<?php
/**
 * Provides helper functions.
 *
 * @since   1.0.0
 *
 * @package TechEd_Directory_Shortcode
 * @subpackage TechEd_Directory_Shortcode/core
 */
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Returns the main plugin object
 *
 * @since   1.0.0
 *
 * @return  TechEd_Directory_Shortcode
 */
function TECHEDDIRECTORYSHORTCODE() {
    return TechEd_Directory_Shortcode::instance();
}

/**
 * Add the shortcode to retun the loop of directory results
 * 
 * @since 1.0.0
 * 
 */
add_shortcode( 'teched_directory', 'teched_directory_shortcode' );

// The shortcode handler function
function teched_directory_shortcode( $attributes ) {

    //Start and object buffer so the query gets buffered for output
    ob_start();

    //Pull in the state and category, state is required but category defaults to the technical directors
	$attributes = shortcode_atts( array( 
		'state' => '',
        'category' => 'state-cte-directors',
	), $attributes, 'teched_directory_shortcode' );

    $args = array(
        'post_type' => 'teched-directory',
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'teched-directory-state',
                'field'    => 'slug',
                'terms'    => $attributes['state'],
            ),
            array(
                'taxonomy' => 'teched-directory-category',
                'field'    => 'slug',
                'terms'    => $attributes['category'],
            ),
        ),
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order'   => 'DESC'
    );

    $directory_query = new  WP_Query( $args );
    
    if ( $directory_query->have_posts() ) :
        ?><div class="directory-list"><?php
            while ( $directory_query->have_posts() ) : $directory_query->the_post();
                include('teched-directory-single.php');
            endwhile;
        ?></div><?php
    endif;
    wp_reset_query();

    // return the buffer contents and delete
    return ob_get_clean();
}

/**
 * Enqueues the necessary JS/CSS on the site
 *
 * @access	public
 * @since	1.0.0
 * @return  void
 */
function teched_directory_shortcode_enqueue_style() {

    wp_enqueue_style( 'teched-directory-shortcode' );
}
 
add_action( 'wp_enqueue_scripts', 'teched_directory_shortcode_enqueue_style' );