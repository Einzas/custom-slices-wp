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
function cps_register_elementor_widget( $widgets_manager ) {
	// Asegúrate de que Elementor esté cargado.
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return;
	}

	// Incluye el archivo del widget.
	require_once plugin_dir_path( __FILE__ ) . 'widgets/slider-widget.php';

	// Registra el widget usando el nombre completo con namespace.
	$widgets_manager->register( new \Elementor\News_Slides_Widget() );
}
add_action( 'elementor/widgets/register', 'cps_register_elementor_widget' );


// Registrar bloque de Gutenberg
// En el archivo principal del plugin (por ejemplo, new_slides.php)
function cps_register_block() {
	wp_register_script(
		'cps-block',
		plugins_url( 'assets/block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-block-editor' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'assets/block.js' )
	);

	register_block_type( 'cps/slider', array(
		'editor_script'   => 'cps-block',
		'render_callback' => 'cps_render_slider',
		'attributes'      => array(
			'numPosts' => array(
				'type'    => 'number',
				'default' => 5,
			),
			'year' => array(
				'type'    => 'string',
				'default' => '2024',
			),
		),
	) );
}
add_action( 'init', 'cps_register_block' );


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
function cps_enqueue_block_editor_assets() {
	// Encolar el CSS para el editor (si no lo tienes ya)
	wp_enqueue_style(
		'cps-block-editor-style',
		plugin_dir_url(__FILE__) . 'assets/style.css',
		array(),
		filemtime(plugin_dir_path(__FILE__) . 'assets/style.css')
	);

	// Encolar el script del slider en el editor
	wp_enqueue_script(
		'cps-editor-script',
		plugin_dir_url(__FILE__) . 'assets/script.js',
		array('jquery'),
		filemtime(plugin_dir_path(__FILE__) . 'assets/script.js'),
		true
	);
}
add_action('enqueue_block_editor_assets', 'cps_enqueue_block_editor_assets');


// Shortcode para mostrar el slider
// Actualiza la función render_callback para usar los atributos
function cps_render_slider( $attributes ) {
	ob_start();

	// Detecta si estamos en modo edición: admin, Gutenberg (REST con context=edit) o Elementor
	$elementor_edit = false;
	if ( function_exists( 'elementor_editor_mode' ) ) {
		$elementor_edit = elementor_editor_mode();
	}

	if ( is_admin() || ( defined('REST_REQUEST') && REST_REQUEST && isset($_GET['context']) && $_GET['context'] === 'edit' ) || $elementor_edit ) {

		$num_posts = ! empty( $attributes['numPosts'] ) ? intval( $attributes['numPosts'] ) : 5;
		$year      = ! empty( $attributes['year'] ) ? sanitize_text_field( $attributes['year'] ) : '';

		$query_args = array(
			'post_type'      => 'post',
			'posts_per_page' => $num_posts,
			'post_status'    => 'publish',
		);

		if ( $year ) {
			$query_args['date_query'] = array(
				array(
					'year' => $year,
				),
			);
		}

		$query = new WP_Query( $query_args );
		?>
        <div id="cps-slider" class="cps-slider">
            <div class="cps-slides">
				<?php if ( $query->have_posts() ) : $i = 0;
					while ( $query->have_posts() ) : $query->the_post(); ?>
                        <div class="cps-slide <?php echo ( $i === 0 ? 'active' : 'next' ); ?>">
                            <a href="<?php the_permalink(); ?>">
                                <div class="cps-card">
                                    <div class="cps-image">
										<?php if ( has_post_thumbnail() ) :
											the_post_thumbnail( 'medium' );
										else : ?>
                                            <img src="<?php echo plugins_url( 'assets/no-img.png', __FILE__ ); ?>" alt="Imagen por defecto">
										<?php endif; ?>
                                    </div>
                                    <div class="cps-content">
                                        <h3 class="cps-title"><?php the_title(); ?></h3>
                                        <p class="cps-p"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
                                        <a href="<?php the_permalink(); ?>" class="cps-read-more">Ver más</a>
                                    </div>
                                </div>
                            </a>
                        </div>
						<?php $i++; endwhile; endif; wp_reset_postdata(); ?>
            </div>
            <button id="cps-prev">‹</button>
            <button id="cps-next">›</button>
            <div class="cps-dots">
				<?php for ( $j = 0; $j < $num_posts; $j++ ) : ?>
                    <button class="cps-dot <?php echo ( $j === 0 ? 'active' : '' ); ?>"></button>
				<?php endfor; ?>
            </div>
        </div>
		<?php

	} else {
		// En frontend, usa la plantilla que carga posts vía AJAX
		include plugin_dir_path( __FILE__ ) . 'templates/slider-template.php';
	}

	return ob_get_clean();
}


add_shortcode('custom_post_slider', 'cps_render_slider');

// Obtener las últimas entradas con AJAX
function cps_get_latest_posts()
{
	$num_posts = isset($_POST['num_posts']) ? intval($_POST['num_posts']) : 15;

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
