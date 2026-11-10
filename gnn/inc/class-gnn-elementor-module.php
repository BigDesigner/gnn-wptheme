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
		if ( 'portfolio' === $this->module ) {
			$this->start_controls_section( 'selection', array( 'label' => __( 'Project selection', 'gnn' ) ) );
			$this->add_control(
				'orderby',
				array(
					'label'   => __( 'Order by', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						''           => __( 'Theme default', 'gnn' ),
						'date'       => __( 'Date', 'gnn' ),
						'title'      => __( 'Title', 'gnn' ),
						'menu_order' => __( 'Menu order', 'gnn' ),
					),
				)
			);
			$this->add_control(
				'order',
				array(
					'label'   => __( 'Order', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => array(
						'ASC'  => __( 'Ascending', 'gnn' ),
						'DESC' => __( 'Descending', 'gnn' ),
					),
				)
			);
			$this->add_control(
				'exclude_ids',
				array(
					'label'   => __( 'Excluded project IDs', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$this->add_control(
				'related',
				array(
					'label'   => __( 'Related projects', 'gnn' ),
					'type'    => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
				)
			);
			$this->end_controls_section();
		}
		if ( in_array( $this->module, array( 'portfolio', 'gallery' ), true ) ) {
			$this->start_controls_section(
				'appearance',
				array(
					'label' => __( 'Appearance', 'gnn' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);
			$this->add_responsive_control(
				'display_columns',
				array(
					'label'     => __( 'Columns', 'gnn' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						''  => __( 'Theme default', 'gnn' ),
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
					),
					'selectors' => array( '{{WRAPPER}} .gnn-module-grid' => 'grid-template-columns:repeat({{VALUE}},minmax(0,1fr));' ),
				)
			);
			$this->add_responsive_control(
				'item_gap',
				array(
					'label'     => __( 'Gap', 'gnn' ),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors' => array( '{{WRAPPER}} .gnn-module-grid' => 'gap:{{SIZE}}{{UNIT}};' ),
				)
			);
			$this->add_control(
				'image_ratio',
				array(
					'label'     => __( 'Image ratio', 'gnn' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						''     => __( 'Theme default', 'gnn' ),
						'auto' => __( 'Natural', 'gnn' ),
						'1/1'  => '1:1',
						'4/3'  => '4:3',
						'16/9' => '16:9',
						'3/4'  => '3:4',
					),
					'selectors' => array( '{{WRAPPER}} .gnn-module-project-image, {{WRAPPER}} .gnn-module-gallery img' => 'aspect-ratio:{{VALUE}};' ),
				)
			);
			$this->add_control(
				'image_fit',
				array(
					'label'     => __( 'Image fit', 'gnn' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						''        => __( 'Theme default', 'gnn' ),
						'cover'   => __( 'Cover', 'gnn' ),
						'contain' => __( 'Contain', 'gnn' ),
					),
					'selectors' => array( '{{WRAPPER}} .gnn-module-project-image img, {{WRAPPER}} .gnn-module-gallery img' => 'object-fit:{{VALUE}};' ),
				)
			);
			$this->add_control(
				'card_radius',
				array(
					'label'     => __( 'Corner radius', 'gnn' ),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors' => array( '{{WRAPPER}} .gnn-module-project-image, {{WRAPPER}} .gnn-module-gallery img' => 'border-radius:{{SIZE}}{{UNIT}};' ),
				)
			);
			if ( 'portfolio' === $this->module ) {
				$this->add_control(
					'title_color',
					array(
						'label'     => __( 'Title color', 'gnn' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array( '{{WRAPPER}} .gnn-module-project h3, {{WRAPPER}} .gnn-module-project h3 a' => 'color:{{VALUE}};' ),
					)
				);
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					array(
						'name'     => 'card_title',
						'selector' => '{{WRAPPER}} .gnn-module-project h3',
					)
				);
			}
			$this->end_controls_section();
		}
	}

	/** Read current records on every render, including editor preview. */
	protected function render() {
		$attrs            = $this->get_settings_for_display();
		$attrs['filters'] = 'yes' === ( $attrs['filters'] ?? 'yes' );
		echo gnn_render_module( $this->module, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the shared renderer.
	}
}
