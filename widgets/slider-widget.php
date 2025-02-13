<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Evitar acceso directo.
}

class News_Slides_Widget extends Widget_Base {

	public function get_name() {
		return 'news_slides';
	}

	public function get_title() {
		return __( 'News Slides Slider', 'news-slides' );
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Contenido', 'news-slides' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'numPosts',
			[
				'label'   => __( 'Número de posts', 'news-slides' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'max'     => 20,
			]
		);

		$this->add_control(
			'year',
			[
				'label'       => __( 'Año', 'news-slides' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Ejemplo: 2022', 'news-slides' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		// Se pasan los valores a través del shortcode
		echo do_shortcode( '[custom_post_slider numPosts="' . $settings['numPosts'] . '" year="' . $settings['year'] . '"]' );
	}

	protected function _content_template() {}
}
