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
// Agregar página de opciones al menú de administración
function cps_add_admin_menu()
{
	add_menu_page(
		'Opciones News Slides', // Título de la página
		'News Slides',          // Título del menú
		'manage_options',       // Permiso
		'news-slides',          // Slug
		'cps_options_page',     // Función que muestra la página
		'dashicons-images-alt2', // Icono
		80                      // Posición
	);
}
add_action('admin_menu', 'cps_add_admin_menu');

// Registrar opciones
function cps_register_settings()
{
	register_setting('cps_options_group', 'cps_option_example');
}
add_action('admin_init', 'cps_register_settings');

// Registrar widget en Elementor
function cps_register_elementor_widget($widgets_manager)
{
	require_once plugin_dir_path(__FILE__) . 'widgets/slider-widget.php';
	$widgets_manager->register(new \News_Slides_Widget());
}
add_action('elementor/widgets/register', 'cps_register_elementor_widget');


// Registrar bloque de Gutenberg
function cps_register_block()
{
	wp_register_script(
		'cps-block',
		plugins_url('assets/block.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
		filemtime(plugin_dir_path(__FILE__) . 'assets/block.js')
	);

	register_block_type('cps/slider', array(
		'editor_script'   => 'cps-block',
		'render_callback' => 'cps_render_slider' // Usa la función que ya tienes
	));
}
add_action('init', 'cps_register_block');


// Vista de la página de opciones
function cps_options_page()
{ ?>
	<div class="wrap">
		<h1>Seccion en construcción</h1>

	</div>
	<?php }


// Cargar scripts y estilos
function cps_enqueue_scripts()
{
	wp_enqueue_style('cps-style', plugin_dir_url(__FILE__) . 'assets/style.css');
	wp_enqueue_script('cps-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);

	// Enviar URL de AJAX al JavaScript
	wp_localize_script('cps-script', 'cps_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'cps_enqueue_scripts');

// Shortcode para mostrar el slider
function cps_render_slider()
{
	ob_start();
	include plugin_dir_path(__FILE__) . 'templates/slider-template.php';
	return ob_get_clean();
}
add_shortcode('custom_post_slider', 'cps_render_slider');

// Obtener las últimas entradas con AJAX
function cps_get_latest_posts()
{
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
								<img src="<?php echo plugin_dir_url(__FILE__) . 'assets/no-img.png'; ?>" width="200" height="200" alt="Imagen por defecto">
							<?php endif; ?>
						</div>

						<div class="cps-content">
							<h3 class="cps-title"><?php the_title(); ?></h3>
							<p class="cps-p"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
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
