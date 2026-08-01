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
 * - Releases must use a `vX.Y.Z` tag; a `.zip` release asset is preferred,
 *   falling back to the auto-generated zipball.
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
	private $transient_key = 'gnn_github_update_check';

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
			add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );
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
		if ( false !== $cached ) {
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
		if ( empty( $body ) || ! isset( $body->tag_name ) ) {
			set_transient( $this->transient_key, 'no-release', 300 );
			return false;
		}

		$download_url = '';
		if ( ! empty( $body->assets ) ) {
			foreach ( $body->assets as $asset ) {
				if ( false !== strpos( $asset->name, '.zip' ) ) {
					$download_url = $asset->browser_download_url;
					break;
				}
			}
		}
		if ( ! $download_url && isset( $body->zipball_url ) ) {
			$download_url = $body->zipball_url;
		}

		$release = (object) array(
			'version'      => ltrim( $body->tag_name, 'v' ),
			'download_url' => $download_url,
			'changelog'    => isset( $body->body ) ? $body->body : '',
			'published_at' => isset( $body->published_at ) ? $body->published_at : '',
			'html_url'     => isset( $body->html_url ) ? $body->html_url : '',
		);

		set_transient( $this->transient_key, $release, $this->cache_duration );
		return $release;
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
		if ( ! $release || empty( $release->download_url ) ) {
			return $transient;
		}
		$local = wp_get_theme( $this->theme_slug )->get( 'Version' );
		if ( $local && version_compare( $release->version, $local, '>' ) ) {
			$transient->response[ $this->theme_slug ] = array(
				'theme'       => $this->theme_slug,
				'new_version' => $release->version,
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
	 * Rename the extracted release folder back to the theme's own slug.
	 *
	 * GitHub release zips extract to e.g. `gnn-wptheme-1.0.0/` (or a
	 * `BigDesigner-gnn-wptheme-<sha>/` zipball folder); WordPress only
	 * recognizes the theme once it's renamed to its real slug.
	 *
	 * @param bool  $response   Installation response.
	 * @param array $hook_extra Extra info (contains the theme slug).
	 * @param array $result     Installation result with destination paths.
	 * @return array|WP_Error Modified result.
	 */
	public function after_install( $response, $hook_extra, $result ) {
		if ( empty( $hook_extra['theme'] ) || $hook_extra['theme'] !== $this->theme_slug ) {
			return $result;
		}

		global $wp_filesystem;
		$theme_dir = get_theme_root() . '/' . $this->theme_slug;

		if ( $wp_filesystem && $result['destination'] !== $theme_dir ) {
			$wp_filesystem->move( $result['destination'], $theme_dir );
			$result['destination'] = $theme_dir;
		}

		delete_transient( $this->transient_key );
		return $result;
	}
}
