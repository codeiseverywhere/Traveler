<?php

namespace Traveler\V2\Theme\Structure;

/*
|----------------------------------------------------------------
| Theme Navigation Areas
|----------------------------------------------------------------
|
| This file is for registering your theme custom navigation areas
| where various menus can be assigned by site administrators.
|
*/

/**
 * Registers navigation areas.
 *
 * @return void
 */
add_action( 'after_setup_theme', 'Traveler\V2\Theme\Structure\register_navigation_areas' );
function register_navigation_areas() {
    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'traveler' ),
        'tasteof'        => __( 'TasteOf Navigation', 'traveler' ),
        'storybooked'        => __( 'StoryBooked Navigation', 'traveler' ),
        'interactive'        => __( 'Interactive Navigation', 'traveler' )
    ] );

}

/**
 * Clean up wp_nav_menu_args
 *
 * Remove the container
 * Use TopBarWalker() by default
 */
add_filter( 'wp_nav_menu_args', 'Traveler\V2\Theme\Structure\menu_args' );
function menu_args( $args = '' ) {
    $roots_nav_menu_args['container'] = false;

    if ( ! isset( $args['items_wrap'] ) ) {
        $roots_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
    }

    if ( ! isset( $args['depth'] ) ) {
        $roots_nav_menu_args['depth'] = 2;
    }

    /*
    if ( ! isset( $args['walker'] ) ) {
        $roots_nav_menu_args['walker'] = new TopBarWalker();
    }
    */

    return array_merge( $args, $roots_nav_menu_args );
}
