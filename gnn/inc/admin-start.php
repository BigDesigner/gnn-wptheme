<?php
/**
 * Panel start screen: setup progress, quick actions and status.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Link to a panel tab. */
function gnn_start_tab_url( $tab, $args = '' ) {
	return admin_url( 'admin.php?page=gnn-panel' . $args . '#gnn-tab-' . $tab );
}

/**
 * Setup steps with their current state.
 *
 * @return array[] Each item: label, done, url, action.
 */
function gnn_start_steps() {
	$logo     = gnn_option( 'logo_light' ) || gnn_option( 'logo_dark' ) || has_custom_logo();
	$accent   = strtolower( (string) get_theme_mod( 'gnn_accent_color', '#34d399' ) );
	$home     = 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) > 0;
	$contact  = '' !== trim( (string) gnn_option( 'company_email' ) ) || '' !== trim( (string) gnn_option( 'company_phone' ) );
	$design   = 'general' !== (string) gnn_option( 'site_segment' ) && '' !== (string) gnn_option( 'site_segment' );
	return array(
		array(
			'label'  => __( 'Choose a design', 'gnn' ),
			'done'   => $design,
			'url'    => gnn_start_tab_url( 'setup', '&path=ready' ),
			'action' => __( 'Choose', 'gnn' ),
		),
		array(
			'label'  => __( 'Add your logo', 'gnn' ),
			'done'   => (bool) $logo,
			'url'    => gnn_start_tab_url( 'header' ),
			'action' => __( 'Add logo', 'gnn' ),
		),
		array(
			'label'  => __( 'Set colors and fonts', 'gnn' ),
			'done'   => '#34d399' !== $accent,
			'url'    => gnn_start_tab_url( 'styles' ),
			'action' => __( 'Open colors', 'gnn' ),
		),
		array(
			'label'  => __( 'Create the menu', 'gnn' ),
			'done'   => has_nav_menu( 'primary' ),
			'url'    => admin_url( 'nav-menus.php' ),
			'action' => __( 'Open menus', 'gnn' ),
		),
		array(
			'label'  => __( 'Choose a home page', 'gnn' ),
			'done'   => $home,
			'url'    => admin_url( 'options-reading.php' ),
			'action' => __( 'Open settings', 'gnn' ),
		),
		array(
			'label'  => __( 'Add contact details', 'gnn' ),
			'done'   => $contact,
			'url'    => gnn_start_tab_url( 'header' ),
			'action' => __( 'Add details', 'gnn' ),
		),
		array(
			'label'  => __( 'Install Elementor', 'gnn' ),
			'done'   => class_exists( '\Elementor\Plugin' ),
			'url'    => gnn_start_tab_url( 'plugins' ),
			'action' => __( 'Open plugins', 'gnn' ),
		),
	);
}

/** Quick actions: label, short help, tab, icon. */
function gnn_start_actions() {
	return array(
		array( __( 'Header', 'gnn' ), __( 'Logo, menu and top bar', 'gnn' ), gnn_start_tab_url( 'header' ), 'align-center' ),
		array( __( 'Footer', 'gnn' ), __( 'Columns, contact and legal text', 'gnn' ), gnn_start_tab_url( 'footer' ), 'arrow-down-alt2' ),
		array( __( 'Colors', 'gnn' ), __( 'Accent, surfaces and buttons', 'gnn' ), gnn_start_tab_url( 'styles' ), 'art' ),
		array( __( 'Fonts', 'gnn' ), __( 'Headings and body text', 'gnn' ), gnn_start_tab_url( 'typography' ), 'editor-textcolor' ),
		array( __( 'Pages', 'gnn' ), __( 'Width, spacing and titles', 'gnn' ), gnn_start_tab_url( 'pages' ), 'admin-page' ),
		array( __( 'Blog', 'gnn' ), __( 'Post lists and single posts', 'gnn' ), gnn_start_tab_url( 'blog' ), 'admin-post' ),
		array( __( 'Ready-made sites', 'gnn' ), __( 'Install a complete design', 'gnn' ), gnn_start_tab_url( 'setup', '&path=ready' ), 'layout' ),
		array( __( 'Plugins', 'gnn' ), __( 'Elementor, WooCommerce and more', 'gnn' ), gnn_start_tab_url( 'plugins' ), 'admin-plugins' ),
	);
}

/** Status rows: label, state text, ok flag. */
function gnn_start_status() {
	global $wp_version;
	$active = __( 'Active', 'gnn' );
	$none   = __( 'Not active', 'gnn' );
	return array(
		array( __( 'Theme', 'gnn' ), GNN_VERSION, true ),
		array( 'WordPress', (string) $wp_version, true ),
		array( 'Elementor', class_exists( '\Elementor\Plugin' ) ? $active : $none, class_exists( '\Elementor\Plugin' ) ),
		array( 'WooCommerce', class_exists( 'WooCommerce' ) ? $active : $none, class_exists( 'WooCommerce' ) ),
		array( 'GNN Content', defined( 'GNN_CONTENT_VERSION' ) ? $active : $none, defined( 'GNN_CONTENT_VERSION' ) ),
	);
}

/** Render the start screen. */
function gnn_start_content() {
	$steps = gnn_start_steps();
	$done  = count( array_filter( wp_list_pluck( $steps, 'done' ) ) );
	$total = count( $steps );
	$pct   = $total ? (int) round( $done / $total * 100 ) : 0;
	?>
	<div class="gnn-start">
		<section class="gnn-start-card gnn-start-progress" aria-labelledby="gnn-start-setup">
			<div class="gnn-start-head">
				<h2 id="gnn-start-setup"><?php esc_html_e( 'Set up your site', 'gnn' ); ?></h2>
				<span class="gnn-start-count">
					<?php
					/* translators: 1: completed steps, 2: total steps. */
					printf( esc_html__( '%1$d of %2$d done', 'gnn' ), (int) $done, (int) $total );
					?>
				</span>
			</div>
			<div class="gnn-start-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo (int) $pct; ?>" aria-label="<?php esc_attr_e( 'Setup progress', 'gnn' ); ?>"><span style="width:<?php echo (int) $pct; ?>%"></span></div>
			<ul class="gnn-start-steps">
				<?php foreach ( $steps as $step ) : ?>
					<li class="<?php echo $step['done'] ? 'is-done' : ''; ?>">
						<span class="gnn-start-mark" aria-hidden="true"></span>
						<span class="gnn-start-label"><?php echo esc_html( $step['label'] ); ?></span>
						<?php if ( $step['done'] ) : ?>
							<span class="gnn-start-state"><?php esc_html_e( 'Done', 'gnn' ); ?></span>
						<?php else : ?>
							<a class="button" href="<?php echo esc_url( $step['url'] ); ?>"><?php echo esc_html( $step['action'] ); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section class="gnn-start-card" aria-labelledby="gnn-start-actions">
			<h2 id="gnn-start-actions"><?php esc_html_e( 'What do you want to change?', 'gnn' ); ?></h2>
			<ul class="gnn-start-actions">
				<?php foreach ( gnn_start_actions() as $action ) : ?>
					<li>
						<a href="<?php echo esc_url( $action[2] ); ?>">
							<span class="dashicons dashicons-<?php echo esc_attr( $action[3] ); ?>" aria-hidden="true"></span>
							<strong><?php echo esc_html( $action[0] ); ?></strong>
							<span><?php echo esc_html( $action[1] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section class="gnn-start-card" aria-labelledby="gnn-start-status">
			<h2 id="gnn-start-status"><?php esc_html_e( 'Status', 'gnn' ); ?></h2>
			<dl class="gnn-start-status">
				<?php foreach ( gnn_start_status() as $row ) : ?>
					<div>
						<dt><?php echo esc_html( $row[0] ); ?></dt>
						<dd class="<?php echo $row[2] ? 'is-ok' : 'is-off'; ?>"><?php echo esc_html( $row[1] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</section>
	</div>
	<?php
}
