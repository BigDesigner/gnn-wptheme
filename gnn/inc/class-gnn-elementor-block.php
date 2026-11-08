<?php
/**
 * Elementor Free shared-block widget.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Display one published shared record in any document. */
class GNN_Elementor_Block extends \Elementor\Widget_Base {
	/** Widget identifier. */
	public function get_name() {
		return 'gnn-block'; }
	/** Visible name. */
	public function get_title() {
		return __( 'GNN Shared Block', 'gnn' ); }
	/** Native icon. */
	public function get_icon() {
		return 'eicon-library-open'; }
	/** Free category. */
	public function get_categories() {
		return array( 'general' ); }
	/** Shared content must never be cached inside its consumer. */
	protected function is_dynamic_content(): bool {
		return true; }
	/** Choose an existing record rather than copying its document. */
	protected function register_controls() {
		$this->start_controls_section( 'block', array( 'label' => __( 'Shared block', 'gnn' ) ) );
		$choices = array( 0 => __( 'Choose', 'gnn' ) );
		foreach ( get_posts(
			array(
				'post_type'   => 'gnn_layout',
				'post_status' => 'publish',
				'numberposts' => 100,
				'orderby'     => 'title',
				'order'       => 'ASC',
			)
		) as $record ) {
			$choices[ $record->ID ] = $record->post_title;
		}
		$this->add_control(
			'shared_block',
			array(
				'label'   => __( 'Shared block', 'gnn' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => $choices,
				'default' => 0,
			)
		);
		$this->add_control(
			'shared_block_id',
			array(
				'label'   => __( 'Other block ID', 'gnn' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'default' => 0,
			)
		);
		$this->end_controls_section();
	}
	/** Render with the shared publication and recursion boundary. */
	protected function render() {
		$settings = $this->get_settings_for_display();
		echo gnn_shared_content( absint( ! empty( $settings['shared_block_id'] ) ? $settings['shared_block_id'] : ( $settings['shared_block'] ?? 0 ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shared native renderer controls publication and markup.
	}
}
