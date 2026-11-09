<?php
/**
 * Native Free Elementor widgets for centrally managed content.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Query controls are editable in Elementor; content remains in the central records. */
class GNN_Elementor_Module extends \Elementor\Widget_Base {
	/** Module identifier.
	 *
	 * @var string
	 */
	protected $module = 'portfolio';

	/** Widget identifier. */
	public function get_name() {
		return 'gnn-' . $this->module;
	}

	/** Visible translated name. */
	public function get_title() {
		$names = array(
			'portfolio' => __( 'GNN Portfolio', 'gnn' ),
			'gallery'   => __( 'GNN Gallery', 'gnn' ),
			'products'  => __( 'GNN Products', 'gnn' ),
			'slider'    => __( 'GNN Slider', 'gnn' ),
		);
		return $names[ $this->module ];
	}

	/** Native library icon. */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/** Available without Pro or another widget library. */
	public function get_categories() {
		return array( 'general' );
	}

	/** Load only the module presentation on the editor canvas. */
	public function get_style_depends() {
		return array( 'gnn-content-modules' );
	}

	/** Native editor loads collection interactions when a widget is inserted dynamically. */
	public function get_script_depends() {
		return 'slider' === $this->module ? array( 'gnn-content-modules', 'gnn-slider' ) : array( 'gnn-content-modules' );
	}

	/** Always query live content; Elementor element caching must not freeze projects. */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/** Shared, server-validated query controls. */
	protected function register_controls() {
		$this->start_controls_section( 'query', array( 'label' => __( 'Central content source', 'gnn' ) ) );
		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns (0 uses theme panel)', 'gnn' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 4,
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Items per page', 'gnn' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 0,
				'max'     => 48,
			)
		);
		$taxonomy = 'slider' === $this->module ? 'gnn_slide_group' : ( 'products' === $this->module ? 'product_cat' : 'gnn_project_category' );
		$options  = array( 0 => __( 'All categories', 'gnn' ) );
		$terms    = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => 100,
			)
		);
		foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
		$this->add_control(
			'category',
			array(
				'label'   => __( 'Category or slider group', 'gnn' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => 0,
			)
		);
		if ( 'portfolio' === $this->module ) {
			$this->add_control(
				'filters',
				array(
					'label'   => __( 'Show category filters', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => 'yes',
				)
			);
		}
		if ( 'products' === $this->module ) {
			$this->add_control(
				'source',
				array(
					'label'   => __( 'Store collection', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'products'   => __( 'Products', 'gnn' ),
						'categories' => __( 'Product categories', 'gnn' ),
					),
					'default' => 'products',
				)
			);
		}
		if ( 'gallery' === $this->module ) {
			$this->add_control(
				'source',
				array(
					'label'   => __( 'Gallery source', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'projects' => __( 'Project category', 'gnn' ),
						'project'  => __( 'Single project', 'gnn' ),
						'album'    => __( 'Independent album', 'gnn' ),
					),
					'default' => 'projects',
				)
			);
			$records = array( 0 => __( 'Choose a published project or album', 'gnn' ) );
			foreach ( get_posts(
				array(
					'post_type'      => array( 'gnn_project', 'gnn_album' ),
					'post_status'    => 'publish',
					'posts_per_page' => 100,
					'orderby'        => 'title',
					'order'          => 'ASC',
				)
			) as $record ) {
				$records[ $record->ID ] = $record->post_title . ' (#' . $record->ID . ')';
			}
			$this->add_control(
				'record',
				array(
					'label'   => __( 'Project or album', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::SELECT2,
					'options' => $records,
					'default' => 0,
				)
			);
		}
		$this->end_controls_section();
	}

	/** Read current records on every render, including editor preview. */
	protected function render() {
		$attrs            = $this->get_settings_for_display();
		$attrs['filters'] = 'yes' === ( $attrs['filters'] ?? 'yes' );
		echo gnn_render_module( $this->module, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the shared renderer.
	}
}
