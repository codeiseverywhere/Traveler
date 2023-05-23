<?php

namespace Traveler\V2\Theme\Plugins\AdRotate;
/**
 * Cleanup, removes clutter added by "AdRotate" plugin
 */

add_action( 'wp_head', 'Traveler\V2\Theme\Plugins\AdRotate\remove_actions', 1 );
function remove_actions() {
	remove_action( 'wp_head', 'adrotate_header' );
	remove_action( 'wp_head', 'adrotate_custom_javascript');
}

add_action( 'admin_init', 'Traveler\V2\Theme\Plugins\AdRotate\remove_meta_boxes' );
function remove_meta_boxes() {
	remove_action( 'admin_notices','adrotate_notifications_dashboard' );
}
