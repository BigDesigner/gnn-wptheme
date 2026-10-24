<?php
/**
 * GNN_GitHub_Updater class.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Self-contained GitHub-based theme updater.
 *
 * Checks the GitHub Releases API for new versions and integrates with
 * WordPress's native theme update pipeline, so a newer release shows up
 * in Appearance > Themes / Dashboard > Updates like a wordpress.org theme.
 *
 * Requirements:
 * - The GitHub repository must be PUBLIC (or set $access_token below).
 * - Releases must use a `vX.Y.Z` tag and an installable gnn-X.Y.Z.zip asset.
 */
class GNN_GitHub_Updater {

	/**
	 * GitHub "owner/repo".
	 *
	 * @var string
	 */
	private $repo = 'BigDesigner/gnn-wptheme';

	/**
	 * Theme stylesheet slug — always the running theme's own directory name,
	 * never hardcoded, so it can't drift from the actual install.
	 *
	 * @var string
	 */
	private $theme_slug;

	/**
	 * Transient key used to cache the GitHub API response.
	 *
	 * @var string
	 */
	private $transient_key = 'gnn_github_update_check_v2';

	/**
	 * Cache duration in seconds (12 hours).
	 *
	 * @var int
	 */
	private $cache_duration = 43200;

	/**
	 * Personal access token for private repos. Leave empty for public repos.
	 *
	 * @var string
	 */
	private $access_token = '';

	/**
	 * Wire up the WP update-pipeline hooks (only while updates are enabled).
	 */
	public function __construct() {
		$this->theme_slug = get_template();

		if ( gnn_updates_enabled() ) {
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
			add_filter( 'themes_api', array( $this, 'theme_info' ), 20, 3 );
			add_filter( 'upgrader_source_selection', array( $this, 'validate_source' ), 10, 4 );
			add_action( 'upgrader_process_complete', array( $this, 'clear_update_cache' ), 10, 2 );
		}
	}

	/**
	 * Fetch (and cache) the latest GitHub release.
	 *
	 * A failed lookup is cached too, briefly, so a flaky/rate-limited API
	 * doesn't get hit on every admin page load. get_transient() returning
	 * false is ambiguous between "not cached" and "cached failure", so a
	 * distinct sentinel string is stored instead of a bare `false`.
	 *
	 * @return object|false Release data, or false when unavailable.
	 */
	public function get_remote_release() {
		$cached = get_transient( $this->transient_key );
		if ( 'no-release' === $cached ) {
			return false;
		}
		if ( is_object( $cached ) && isset( $cached->version, $cached->download_url ) && $this->valid_package_url( $cached->download_url, $cached->version ) ) {
			return $cached;
		}

		$url  = sprintf( 'https://api.github.com/repos/%s/releases/latest', $this->repo );
		$args = array(
			'headers' => array(
				'Accept'     => 'application/vnd.github.v3+json',
				'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
			),
			'timeout' => 10,
		);
		if ( $this->access_token ) {
			$args['headers']['Authorization'] = 'token ' . $this->access_token;
		}

		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			set_transient( $this->transient_key, 'no-release', 300 );
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_object( $body ) || empty( $body->tag_name ) || ! is_string( $body->tag_name )
			|| ! preg_match( '/^v?(\d+\.\d+\.\d+)$/', $body->tag_name, $matches )
			|| ! empty( $body->draft ) || ! empty( $body->prerelease ) ) {
			set_transient( $this->transient_key, 'no-release', 300 );
			return false;
		}

		$download_url = '';
		$version      = $matches[1];
		if ( ! empty( $body->assets ) && is_array( $body->assets ) ) {
			foreach ( $body->assets as $asset ) {
				if ( isset( $asset->name, $asset->browser_download_url ) && 'gnn-' . $version . '.zip' === $asset->name
					&& $this->valid_package_url( $asset->browser_download_url, $version ) ) {
					$download_url = $asset->browser_download_url;
					break;
				}
			}
		}
		if ( ! $download_url ) {
			set_transient( $this->transient_key, 'no-release', 300 );
			return false;
		}

		$release = (object) array(
			'version'      => $version,
			'download_url' => $download_url,
			'changelog'    => isset( $body->body ) ? $body->body : '',
			'published_at' => isset( $body->published_at ) ? $body->published_at : '',
			'html_url'     => isset( $body->html_url ) ? $body->html_url : '',
		);

		set_transient( $this->transient_key, $release, $this->cache_duration );
		return $release;
	}

	/**
	 * Only release assets from this repository are executable update packages.
	 *
	 * @param string $url Package URL.
	 * @param string $version Release version.
	 * @return bool
	 */
	private function valid_package_url( $url, $version ) {
		if ( ! is_string( $url ) || ! is_string( $version ) || ! preg_match( '/^\d+\.\d+\.\d+$/', $version ) ) {
			return false;
		}
		$parts = wp_parse_url( $url );
		return is_array( $parts ) && isset( $parts['scheme'], $parts['host'], $parts['path'] )
			&& 'https' === $parts['scheme'] && 'github.com' === $parts['host']
			&& ! isset( $parts['user'] ) && ! isset( $parts['pass'] ) && ! isset( $parts['port'] )
			&& ! isset( $parts['query'] ) && ! isset( $parts['fragment'] )
			&& in_array(
				$parts['path'],
				array(
					'/' . $this->repo . '/releases/download/v' . $version . '/gnn-' . $version . '.zip',
					'/' . $this->repo . '/releases/download/' . $version . '/gnn-' . $version . '.zip',
				),
				true
			);
	}

	/**
	 * Inject update data into WP's theme-update transient when GitHub has a newer tag.
	 *
	 * @param object $transient The update_themes transient.
	 * @return object
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		$release = $this->get_remote_release();
		unset( $transient->response[ $this->theme_slug ], $transient->no_update[ $this->theme_slug ] );
		if ( ! $release || empty( $release->download_url ) ) {
			return $transient;
		}
		$local = wp_get_theme( $this->theme_slug )->get( 'Version' );
		if ( ! $local ) {
			return $transient;
		}
		if ( version_compare( $release->version, $local, '>' ) ) {
			$transient->response[ $this->theme_slug ] = array(
				'theme'       => $this->theme_slug,
				'new_version' => $release->version,
				'url'         => $release->html_url,
				'package'     => $release->download_url,
			);
			unset( $transient->no_update[ $this->theme_slug ] );
		} else {
			// WordPress only offers the "enable auto-updates" link for themes
			// present in `no_update`; without this the theme silently opts out
			// of the auto-update UI whenever it's already current.
			$transient->no_update[ $this->theme_slug ] = array(
				'theme'       => $this->theme_slug,
				'new_version' => $local,
				'url'         => $release->html_url,
				'package'     => $release->download_url,
			);
		}
		return $transient;
	}

	/**
	 * Supply the "View version details" popup data for our theme.
	 *
	 * @param false|object|array $result Default result.
	 * @param string             $action Requested action.
	 * @param object             $args   Request args.
	 * @return false|object
	 */
	public function theme_info( $result, $action, $args ) {
		if ( 'theme_information' !== $action || empty( $args->slug ) || $this->theme_slug !== $args->slug ) {
			return $result;
		}
		$release = $this->get_remote_release();
		if ( ! $release ) {
			return $result;
		}
		$theme = wp_get_theme( $this->theme_slug );
		return (object) array(
			'name'          => $theme->get( 'Name' ),
			'slug'          => $this->theme_slug,
			'version'       => $release->version,
			'author'        => $theme->get( 'Author' ),
			'homepage'      => $theme->get( 'ThemeURI' ),
			'download_link' => $release->download_url,
			'sections'      => array(
				'description' => $theme->get( 'Description' ),
				'changelog'   => wpautop( wp_kses_post( $release->changelog ) ),
			),
		);
	}

	/**
	 * Validate the extracted theme before WordPress touches the installed theme.
	 *
	 * @param string|WP_Error $source Extracted source directory.
	 * @param string          $remote_source Temporary extraction root.
	 * @param WP_Upgrader     $upgrader Upgrader instance (unused).
	 * @param array           $hook_extra Current upgrade context.
	 * @return string|WP_Error
	 */
	public function validate_source( $source, $remote_source, $upgrader, $hook_extra ) {
		if ( is_wp_error( $source ) || empty( $hook_extra['theme'] ) || $hook_extra['theme'] !== $this->theme_slug ) {
			return $source;
		}
		global $wp_filesystem;
		$source = trailingslashit( $source );
		$root   = trailingslashit( $remote_source );
		if ( ! $wp_filesystem || 0 !== strpos( $source, $root ) || preg_match( '#(?:^|/)\.\.(?:/|$)#', $source )
			|| ! $wp_filesystem->exists( $source . 'style.css' ) || ! $wp_filesystem->exists( $source . 'index.php' ) ) {
			return new WP_Error( 'gnn_invalid_package', __( 'The update does not contain an installable GNN theme.', 'gnn' ) );
		}
		$header = $wp_filesystem->get_contents( $source . 'style.css' );
		if ( ! is_string( $header ) || ! preg_match( '/^Theme Name:\s*GNN\s*$/m', $header ) ) {
			return new WP_Error( 'gnn_invalid_package', __( 'The update does not contain an installable GNN theme.', 'gnn' ) );
		}
		$destination = $root . $this->theme_slug . '/';
		if ( $source !== $destination ) {
			if ( $wp_filesystem->exists( $destination ) || ! $wp_filesystem->move( $source, $destination ) ) {
				return new WP_Error( 'gnn_package_move_failed', __( 'The update package could not be prepared. The installed theme has not been changed.', 'gnn' ) );
			}
		}
		return $destination;
	}

	/**
	 * Clear release data after this theme has been updated.
	 *
	 * @param WP_Upgrader $upgrader Upgrader instance (unused).
	 * @param array       $options Upgrade context.
	 */
	public function clear_update_cache( $upgrader, $options ) {
		$themes = isset( $options['themes'] ) ? (array) $options['themes'] : array();
		if ( isset( $options['theme'] ) ) {
			$themes[] = $options['theme'];
		}
		if ( 'theme' !== ( $options['type'] ?? '' ) || ! in_array( $this->theme_slug, $themes, true ) ) {
			return;
		}
		delete_transient( $this->transient_key );
	}
}
