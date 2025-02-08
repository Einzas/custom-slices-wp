<?php

/*
Plugin Name: News Slides
Plugin URI: https:/einzas.online
Description: Plugin para mostrar noticias en un slider
Version: 1.0
Author: Einzas
Author URI: http://einzas.online
License:  GPL2
*/

if (!defined('ABSPATH')) {
	exit; // Evitar acceso directo
}

// Cargar scripts y estilos
function cps_enqueue_scripts() {
	wp_enqueue_style('cps-style', plugin_dir_url(__FILE__) . 'assets/style.css');
	wp_enqueue_script('cps-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);

	// Enviar URL de AJAX al JavaScript
	wp_localize_script('cps-script', 'cps_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'cps_enqueue_scripts');

// Shortcode para mostrar el slider
function cps_render_slider() {
	ob_start();
	include plugin_dir_path(__FILE__) . 'templates/slider-template.php';
	return ob_get_clean();
}
add_shortcode('custom_post_slider', 'cps_render_slider');

// Obtener las últimas entradas con AJAX
function cps_get_latest_posts() {
	$num_posts = isset($_POST['num_posts']) ? intval($_POST['num_posts']) : 5;

	$query = new WP_Query(array(
		'post_type'      => 'post',
		'posts_per_page' => $num_posts,
		'post_status'    => 'publish'
	));

	if ($query->have_posts()) :
		while ($query->have_posts()) : $query->the_post(); ?>
            <div class="cps-slide">
                <a href="<?php the_permalink(); ?>">
                    <div class="cps-card">
                        <div class="cps-image">
							<?php if (has_post_thumbnail()) : ?>
								<?php the_post_thumbnail('medium'); ?>
							<?php else : ?>
                                <img src="<?php echo plugin_dir_url(__FILE__) . 'assets/no-img.png'; ?>" width="300" height="300" alt="Imagen por defecto">
							<?php endif; ?>
                        </div>

                        <div class="cps-content">
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo wp_trim_words(get_the_excerpt(), 8); ?></p>
                            <a href="<?php the_permalink(); ?>" class="cps-read-more">Ver más</a>
                        </div>
                    </div>
                </a>
            </div>
		<?php endwhile;
	endif;



	wp_reset_postdata();
	wp_die();
}
add_action('wp_ajax_nopriv_cps_get_latest_posts', 'cps_get_latest_posts');
add_action('wp_ajax_cps_get_latest_posts', 'cps_get_latest_posts');