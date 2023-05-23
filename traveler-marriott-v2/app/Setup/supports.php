<?php

namespace Traveler\V2\Theme\Setup;

/*
|-----------------------------------------------------------
| Theme Supports
|-----------------------------------------------------------
|
| This file enables theme supports, which activates various
| WordPress functions used or required by this theme.
| By default we enabled most common for you.
|
*/

use function remove_post_type_support;
use function Traveler\V2\Theme\config;

/**
 * Adds various theme supports.
 *
 * @return void
 */
add_action( 'after_setup_theme', 'Traveler\V2\Theme\Setup\add_theme_supports' );


function add_theme_supports() {

    /**
     * Add support for head <title> tag. By adding `title-tag` support, we
     * declare that this theme does not use a hard-coded <title> tag in
     * the document head, and expect WordPress to provide it for us.
     *
     * @see https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support( 'title-tag' );

    /**
     * Enable support for Post Thumbnails on posts and pages. Note that you
     * can optionally pass a second argument, $args, with an array of
     * the Post Types for which you want to enable this feature.
     *
     * @see https://developer.wordpress.org/reference/functions/add_theme_support/#post-thumbnails
     */
    add_theme_support( 'post-thumbnails' );

    /**
     * Switch default core markup for search forms, comment forms, comment
     * lists, gallery, and captions to output valid HTML5 markup.
     *
     * @see https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support( 'html5', [
        'search-form',
        'gallery',
        'caption',
    ] );

    add_post_type_support( 'page', 'excerpt' );

    /**
     * Add post formats
     *
     * @see http://codex.wordpress.org/Post_Formats
     */
    add_theme_support( 'post-formats', [ 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio' ] );

    // Add support for responsive embedded content.
    add_theme_support( 'responsive-embeds' );

	add_theme_support( 'amp' );
}



add_action( 'wp_loaded', 'Traveler\V2\Theme\Setup\init_wploaded_filters' );


function init_wploaded_filters() {

    remove_post_type_support( 'page', 'comments');
    remove_post_type_support( 'page', 'trackbacks');

    remove_post_type_support( 'post', 'comments');
    remove_post_type_support( 'post', 'trackbacks');

    //add_filter( 'pre_option_default_pingback_flag', '__return_zero' );

}

/**
 * Loads theme textdomain language files.
 *
 * @return void
 */
add_action( 'after_setup_theme', 'Traveler\V2\Theme\Setup\load_textdomain' );


function load_textdomain() {
    $paths       = config( 'paths' );
    $directories = config( 'directories' );

    load_theme_textdomain( config( 'textdomain' ), "{$paths['directory']}/{$directories['languages']}" );
}


