<?php

namespace Elementor;

if (! defined('ABSPATH')) exit; // Evitar acceso directo

class News_Slides_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'news_slides';
    }

    public function get_title()
    {
        return __('News Slides Slider', 'news-slides');
    }

    public function get_icon()
    {
        return 'eicon-slider-album';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function render()
    {
        echo do_shortcode('[custom_post_slider]');
    }

    protected function _content_template() {}
}
