<?php
/**
 * GNN GitHub Theme Updater
 *
 * Checks the GitHub Releases API for new versions and integrates
 * with the WordPress theme update system. When a newer release is
 * detected, it appears in Appearance > Themes and Dashboard > Updates
 * just like themes from wordpress.org.
 *
 * Features:
 * - Enable/disable via Customizer toggle (GNN Theme Options > Updates).
 * - Manual "Check Now" button under Appearance > Theme Updates.
 * - 12-hour API response caching to respect GitHub rate limits.
 * - Automatic folder renaming after update extraction.
 *
 * Requirements:
 * - The GitHub repository must be PUBLIC (or a personal access token must be provided).
 * - GitHub Releases must have a tag matching `vX.Y.Z` format.
 * - Each release must include a `.zip` asset named `gnn-wptheme-X.Y.Z.zip`.
 *
 * @package GNN
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class GNN_GitHub_Updater
 *
 * Self-contained GitHub-based theme updater. Hooks into WordPress's
 * native update pipeline so the user can update from WP Admin.
 */
class GNN_GitHub_Updater {

    /**
     * GitHub repository owner/name.
     *
     * @var string
     */
    private $repo = 'BigDesigner/gnn-wptheme';

    /**
     * WordPress theme slug (directory name).
     *
     * @var string
     */
    private $theme_slug = 'gnn-wptheme';

    /**
     * Transient key for caching the GitHub API response.
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
     * Personal access token for private repos (optional).
     * Leave empty for public repositories.
     *
     * @var string
     */
    private $access_token = '';

    /**
     * Initialize hooks.
     */
    public function __construct() {
        // Only register update hooks if auto-update is enabled.
        if ( get_theme_mod( 'enable_github_updates', true ) ) {
            add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
            add_filter( 'themes_api', array( $this, 'theme_info' ), 20, 3 );
            add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );
            add_action( 'load-update-core.php', array( $this, 'clear_cache' ) );
        }

        // Always register the admin page and Customizer setting (so user can toggle it).
        add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
        add_action( 'admin_init', array( $this, 'handle_manual_check' ) );
        add_action( 'customize_register', array( $this, 'register_customizer_settings' ) );
    }

    // =========================================================================
    // Customizer Settings
    // =========================================================================

    /**
     * Register update-related settings in the Customizer.
     *
     * @param WP_Customize_Manager $wp_customize Customizer manager instance.
     * @return void
     */
    public function register_customizer_settings( $wp_customize ) {
        // Section
        $wp_customize->add_section( 'gnn_updates', array(
            'title'       => esc_html__( 'Theme Updates', 'gnn-wptheme' ),
            'panel'       => 'gnn_theme_panel',
            'priority'    => 90,
            'description' => esc_html__( 'Control how the theme checks for updates from GitHub.', 'gnn-wptheme' ),
        ) );

        // Toggle: Enable/Disable auto-update
        $wp_customize->add_setting( 'enable_github_updates', array(
            'default'           => true,
            'sanitize_callback' => 'gnn_sanitize_checkbox',
        ) );
        $wp_customize->add_control( 'enable_github_updates', array(
            'label'       => esc_html__( 'Enable Automatic Update Checks', 'gnn-wptheme' ),
            'description' => esc_html__( 'When enabled, the theme will periodically check GitHub for new releases and show update notifications in the WordPress dashboard.', 'gnn-wptheme' ),
            'section'     => 'gnn_updates',
            'type'        => 'checkbox',
        ) );
    }

    // =========================================================================
    // Admin Page — Manual Check
    // =========================================================================

    /**
     * Register the "Theme Updates" page under Appearance menu.
     *
     * @return void
     */
    public function register_admin_page() {
        add_theme_page(
            esc_html__( 'Theme Updates', 'gnn-wptheme' ),
            esc_html__( 'Theme Updates', 'gnn-wptheme' ),
            'update_themes',
            'gnn-theme-updates',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Render the Theme Updates admin page.
     *
     * Shows current version, latest remote version, update status,
     * and a "Check Now" button.
     *
     * @return void
     */
    public function render_admin_page() {
        $local_version  = wp_get_theme( $this->theme_slug )->get( 'Version' );
        $is_enabled     = get_theme_mod( 'enable_github_updates', true );
        $release        = $is_enabled ? $this->get_remote_release() : false;
        $has_update     = ( $release && version_compare( $release->version, $local_version, '>' ) );
        $last_checked   = get_transient( $this->transient_key ) ? true : false;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'GNN Theme Updates', 'gnn-wptheme' ); ?></h1>

            <div class="card" style="max-width: 600px; padding: 1.5em;">
                <h2 style="margin-top: 0;"><?php esc_html_e( 'Update Status', 'gnn-wptheme' ); ?></h2>

                <table class="form-table" role="presentation">
                    <tr>
                        <th><?php esc_html_e( 'Installed Version', 'gnn-wptheme' ); ?></th>
                        <td><code><?php echo esc_html( $local_version ); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Latest Version', 'gnn-wptheme' ); ?></th>
                        <td>
                            <?php if ( ! $is_enabled ) : ?>
                                <em><?php esc_html_e( 'Auto-updates are disabled.', 'gnn-wptheme' ); ?></em>
                            <?php elseif ( $release ) : ?>
                                <code><?php echo esc_html( $release->version ); ?></code>
                                <?php if ( $has_update ) : ?>
                                    <span style="color: #d63638; font-weight: 600; margin-left: 8px;">
                                        ⬆ <?php esc_html_e( 'Update available!', 'gnn-wptheme' ); ?>
                                    </span>
                                <?php else : ?>
                                    <span style="color: #00a32a; font-weight: 600; margin-left: 8px;">
                                        ✓ <?php esc_html_e( 'You are up to date.', 'gnn-wptheme' ); ?>
                                    </span>
                                <?php endif; ?>
                            <?php else : ?>
                                <em><?php esc_html_e( 'Could not reach GitHub. Try again later.', 'gnn-wptheme' ); ?></em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Auto-Updates', 'gnn-wptheme' ); ?></th>
                        <td>
                            <?php if ( $is_enabled ) : ?>
                                <span style="color: #00a32a;">● <?php esc_html_e( 'Enabled', 'gnn-wptheme' ); ?></span>
                            <?php else : ?>
                                <span style="color: #d63638;">● <?php esc_html_e( 'Disabled', 'gnn-wptheme' ); ?></span>
                                — <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=gnn_updates' ) ); ?>">
                                    <?php esc_html_e( 'Enable in Customizer', 'gnn-wptheme' ); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ( $release && ! empty( $release->published_at ) ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'Release Date', 'gnn-wptheme' ); ?></th>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $release->published_at ) ) ); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <hr>

                <p class="submit" style="display: flex; gap: 12px; align-items: center;">
                    <?php
                    $check_url = wp_nonce_url(
                        admin_url( 'themes.php?page=gnn-theme-updates&gnn_check_update=1' ),
                        'gnn_manual_update_check'
                    );
                    ?>
                    <a href="<?php echo esc_url( $check_url ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Check for Updates Now', 'gnn-wptheme' ); ?>
                    </a>

                    <?php if ( $has_update ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>" class="button button-secondary">
                            <?php esc_html_e( 'Go to WordPress Updates', 'gnn-wptheme' ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( $release && ! empty( $release->html_url ) ) : ?>
                        <a href="<?php echo esc_url( $release->html_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-link">
                            <?php esc_html_e( 'View on GitHub ↗', 'gnn-wptheme' ); ?>
                        </a>
                    <?php endif; ?>
                </p>
            </div>

            <?php if ( $release && ! empty( $release->changelog ) ) : ?>
            <div class="card" style="max-width: 600px; padding: 1.5em; margin-top: 1em;">
                <h2 style="margin-top: 0;"><?php esc_html_e( 'Changelog', 'gnn-wptheme' ); ?></h2>
                <div style="background: #f6f7f7; padding: 1em; border-radius: 4px; font-size: 13px; line-height: 1.6;">
                    <?php echo nl2br( esc_html( $release->changelog ) ); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Handle the manual "Check Now" button click.
     *
     * Clears the cached transient and redirects back to the page
     * with a fresh API check.
     *
     * @return void
     */
    public function handle_manual_check() {
        if ( ! isset( $_GET['gnn_check_update'] ) || '1' !== $_GET['gnn_check_update'] ) {
            return;
        }

        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'gnn_manual_update_check' ) ) {
            wp_die( esc_html__( 'Security check failed.', 'gnn-wptheme' ) );
        }

        if ( ! current_user_can( 'update_themes' ) ) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'gnn-wptheme' ) );
        }

        // Clear cache to force a fresh API call.
        delete_transient( $this->transient_key );

        // Also delete WP's own theme update transient so it re-checks immediately.
        delete_site_transient( 'update_themes' );

        // Redirect back (without the query params) to trigger a clean page load.
        wp_safe_redirect( admin_url( 'themes.php?page=gnn-theme-updates&checked=1' ) );
        exit;
    }

    // =========================================================================
    // GitHub API
    // =========================================================================

    /**
     * Fetch the latest release data from GitHub API.
     *
     * Uses a WordPress transient to cache results and avoid
     * exceeding GitHub's unauthenticated rate limit (60 req/hr).
     *
     * @return object|false Release data or false on failure.
     */
    private function get_remote_release() {
        $cached = get_transient( $this->transient_key );
        if ( false !== $cached ) {
            return $cached;
        }

        $url = sprintf( 'https://api.github.com/repos/%s/releases/latest', $this->repo );

        $args = array(
            'headers' => array(
                'Accept'     => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
            ),
            'timeout' => 10,
        );

        // Add authorization header for private repos.
        if ( ! empty( $this->access_token ) ) {
            $args['headers']['Authorization'] = 'token ' . $this->access_token;
        }

        $response = wp_remote_get( $url, $args );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            // Cache the failure too, so we don't spam the API.
            set_transient( $this->transient_key, false, 300 );
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ) );

        if ( empty( $body ) || ! isset( $body->tag_name ) ) {
            return false;
        }

        // Extract version from tag (strip leading 'v').
        $remote_version = ltrim( $body->tag_name, 'v' );

        // Find the zip asset in the release.
        $download_url = '';
        if ( ! empty( $body->assets ) ) {
            foreach ( $body->assets as $asset ) {
                if ( strpos( $asset->name, '.zip' ) !== false ) {
                    $download_url = $asset->browser_download_url;
                    break;
                }
            }
        }

        // Fallback to the auto-generated zipball if no asset found.
        if ( empty( $download_url ) ) {
            $download_url = $body->zipball_url;
        }

        $release_data = (object) array(
            'version'      => $remote_version,
            'download_url' => $download_url,
            'changelog'    => isset( $body->body ) ? $body->body : '',
            'published_at' => isset( $body->published_at ) ? $body->published_at : '',
            'html_url'     => isset( $body->html_url ) ? $body->html_url : '',
        );

        set_transient( $this->transient_key, $release_data, $this->cache_duration );

        return $release_data;
    }

    // =========================================================================
    // WordPress Update Pipeline Hooks
    // =========================================================================

    /**
     * Hook into the theme update check transient.
     *
     * Compares the remote GitHub version with the local theme version.
     * If the remote version is newer, injects update data into the
     * transient so WordPress shows the update notification.
     *
     * @param object $transient The update_themes transient object.
     * @return object Modified transient.
     */
    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $release = $this->get_remote_release();

        if ( false === $release || empty( $release->version ) ) {
            return $transient;
        }

        $local_version = wp_get_theme( $this->theme_slug )->get( 'Version' );

        if ( version_compare( $release->version, $local_version, '>' ) ) {
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
     * Provide theme information for the update details popup.
     *
     * When the user clicks "View version X.Y.Z details" in the
     * WordPress dashboard, this method supplies the changelog and
     * other metadata.
     *
     * @param false|object|array $result Default result.
     * @param string             $action API action ('theme_information').
     * @param object             $args   Arguments including slug.
     * @return false|object Theme info or false.
     */
    public function theme_info( $result, $action, $args ) {
        if ( 'theme_information' !== $action || $this->theme_slug !== $args->slug ) {
            return $result;
        }

        $release = $this->get_remote_release();

        if ( false === $release ) {
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
                'changelog'   => nl2br( esc_html( $release->changelog ) ),
            ),
        );
    }

    /**
     * Rename the extracted directory after theme update.
     *
     * GitHub release zips extract to a folder like `gnn-wptheme-1.0.0/`
     * (from our workflow) or `BigDesigner-gnn-wptheme-abc1234/` (from
     * zipball). This hook renames it back to `gnn-wptheme/` so WordPress
     * recognizes the theme.
     *
     * @param bool  $response   Installation response.
     * @param array $hook_extra Extra info (contains the theme slug).
     * @param array $result     Installation result with destination paths.
     * @return array|WP_Error Modified result.
     */
    public function after_install( $response, $hook_extra, $result ) {
        // Only act on our theme.
        if ( ! isset( $hook_extra['theme'] ) || $hook_extra['theme'] !== $this->theme_slug ) {
            return $result;
        }

        global $wp_filesystem;

        $theme_dir = get_theme_root() . '/' . $this->theme_slug;

        // Move from extracted dir to proper theme directory.
        $wp_filesystem->move( $result['destination'], $theme_dir );
        $result['destination'] = $theme_dir;

        // Re-activate the theme if it was active before the update.
        if ( wp_get_theme()->get_stylesheet() === $this->theme_slug ) {
            switch_theme( $this->theme_slug );
        }

        // Clear the cache so the next check fetches fresh data.
        delete_transient( $this->transient_key );

        return $result;
    }

    /**
     * Clear the cached GitHub API response.
     *
     * Triggered when user visits the Updates page, ensuring
     * a fresh check is performed.
     *
     * @return void
     */
    public function clear_cache() {
        delete_transient( $this->transient_key );
    }
}

// Initialize the updater.
new GNN_GitHub_Updater();
