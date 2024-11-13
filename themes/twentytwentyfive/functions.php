<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( get_parent_theme_file_uri( 'assets/css/editor-style.css' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// function to check and redirect based on IP address
function restrict_ip_address() {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    if (strpos($user_ip, '77.29') === 0) {
        // Redirect to another site or page
        wp_redirect('https://abc.com');
        exit; 
    }
}

// Call the function with init hook 
add_action('init', 'restrict_ip_address');

// Ajax Actions for login and logout users
add_action('wp_ajax_get_architecture_projects', 'get_architecture_projects');
add_action('wp_ajax_nopriv_get_architecture_projects', 'get_architecture_projects');

function get_architecture_projects() {
    $posts_per_page = is_user_logged_in() ? 6 : 3;

    $args = array(
        'post_type' => 'projects',
        'posts_per_page' => $posts_per_page,
        'tax_query' => array(
            array(
                'taxonomy' => 'project_type', 
                'field'    => 'slug',
                'terms'    => 'architecture',
            ),
        ),
    );

    $projects = new WP_Query($args);

    $data = array();
    if ($projects->have_posts()) {
        while ($projects->have_posts()) {
            $projects->the_post();
            $data[] = array(
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'link'  => get_the_permalink(),
            );
        }
    }

    // Response in JSON format
    wp_send_json_success($data);
    wp_die(); // End 
}

// Adding Script.js file in function.php
function enqueue_ajax_script() {
    wp_enqueue_script('my-ajax-script', get_template_directory_uri() . '/script.js', array('jquery'), null, true);
    wp_localize_script('my-ajax-script', 'ajaxurl', admin_url('admin-ajax.php')); 
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_script');

// Give me Coffee Function 
function hs_give_me_coffee() {
    // Send a GET request to the Random Coffee API
    $response = wp_remote_get('https://coffee.alexflipnote.dev');
    
    if (is_wp_error($response)) {
        return 'Unable to fetch coffee at the moment.';
    }
    
    $body = wp_remote_retrieve_body($response); // Get the body of the response
    $data = json_decode($body); // Decode the JSON response

    if ($data && isset($data->file)) {
        // Print the URL to confirm
        return '<p>Image URL: ' . $data->file . '</p>';
    } else {
        return 'No coffee found.';
    }
}

// Get Kanye Quotes 
function get_kanye_quotes() {
    $quotes = [];
    for ($i = 0; $i < 5; $i++) {
        
        $response = wp_remote_get('https://api.kanye.rest');
        
        if (is_wp_error($response)) {
            return 'Unable to fetch quotes at the moment.';
        }

        $body = wp_remote_retrieve_body($response); 
        $data = json_decode($body); 

        if ($data && isset($data->quote)) {
            $quotes[] = $data->quote; 
        }
    }


    return implode('<br>', $quotes);
}

function kanye_quotes_shortcode() {
    return get_kanye_quotes(); // Call the function to get the quotes
}
add_shortcode('kanye_quotes', 'kanye_quotes_shortcode');


// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;
