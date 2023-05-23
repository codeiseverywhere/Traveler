<?php

namespace Traveler\V2\Theme\Setup;

/*
|-----------------------------------------------------------
| Theme Actions
|-----------------------------------------------------------
|
| This file purpose is to include your custom
| actions hooks, which process a various
| logic at specific parts of WordPress.
|
*/

use function adrotate_ad;
use function is_search;
use function remove_action;

add_action( 'init', 'Traveler\V2\Theme\Setup\init_internal' );

function init_internal() {
    add_rewrite_rule( 'ads/single/([0-9]{1,})/?$', 'index.php?ad_id=$matches[1]', 'top' );
    add_rewrite_rule( 'ads/group/([0-9]{1,})/?$', 'index.php?group_id=$matches[1]', 'top' );
}


add_action( 'parse_request', 'Traveler\V2\Theme\Setup\parse_request' );

function parse_request( &$wp ) {
    if ( array_key_exists( 'ad_id', $wp->query_vars ) ) {

        echo adrotate_ad( $wp->query_vars['ad_id'] );
        exit();
    } elseif ( array_key_exists( 'group_id', $wp->query_vars ) ) {

        echo adrotate_group( $wp->query_vars['group_id'] );
        exit();
    }

    return;
}

/**
 * Cleanup, removing not needed hooked actions.
 */
remove_action( 'init', 'wp_widgets_init', 1 );
remove_action( 'plugins_loaded', 'wp_maybe_load_widgets', 0 );
remove_action( 'plugins_loaded', '_wp_add_additional_image_sizes', 0 );


add_action( 'init', 'Traveler\V2\Theme\Setup\remove_init_actions', 1 );

function remove_init_actions() {


    if ( ! is_user_logged_in() && ! is_search() ) {

        remove_action( 'init', 'relevanssi_init' );
    }
}


add_action( 'wp_head', 'Traveler\V2\Theme\Setup\add_style_scaffold', 100 );

function add_style_scaffold() {
	echo "<style>.main-header{padding-top:1rem;position:relative;padding-bottom:2.5rem}.menu{list-style:none;position:relative;display:flex;flex-wrap:wrap}.menu.align-right.top-menu{padding-bottom:1rem}.menu.align-right.top-menu{padding-bottom:1rem}.text-uppercase{text-transform:uppercase}.align-right{justify-content:flex-end}.menu,.menu.horizontal{flex-wrap:wrap;flex-direction:row}.menu.align-right a{font-size:.875rem}.menu>li .button{padding:.7rem}.menu.align-right a{font-size:.875rem}.menu .button,.menu a{line-height:1;text-decoration:none;display:block;padding:.7rem 1rem}.hide{display:none}#app{min-height:30rem}.footer-about ul,.list-social{list-style:none}.footer ul li{display:inline-block}</style>";
}
