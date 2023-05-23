<?php

namespace Traveler\V2\Theme\Structure;

/*
|-----------------------------------------------------------
| Theme Custom Post Types
|-----------------------------------------------------------
|
| This file is for registering your theme post types.
| Custom post types allow users to easily create
| and manage various types of content.
|
*/

use function register_taxonomy_for_object_type;
use function Traveler\V2\Theme\config;

/**
 * Registers `storybooked` custom post type.
 *
 * @return void
 */
add_action( 'init', 'Traveler\V2\Theme\Structure\register_storybooked_post_type' );

function register_storybooked_post_type() {

	register_post_type( 'storybooked', [
		'description'           => __( 'Collection of StoryBooked articles.', config( 'textdomain' ) ),
		'supports'              => [
			'title',
			'editor',
			'revisions',
			'author',
			'custom-fields',
			'excerpt',
			'page-attributes',
			'thumbnail'
		],
		'taxonomies'            => [
			'category'
		],
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-media-interactive',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'storybooked',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'rewrite'               => [
			'slug'         => 'storybooked',
			'with_front'   => false,
			'hierarchical' => true,
		],
		'labels'                => [
			'name'               => _x( 'StoryBooked', 'post type general name', config( 'textdomain' ) ),
			'singular_name'      => _x( 'Story', 'post type singular name', config( 'textdomain' ) ),
			'menu_name'          => _x( 'StoryBooked', 'admin menu', config( 'textdomain' ) ),
			'name_admin_bar'     => _x( 'Story', 'add new on admin bar', config( 'textdomain' ) ),
			'add_new'            => _x( 'Add New', 'store', config( 'textdomain' ) ),
			'add_new_item'       => __( 'Add New Story', config( 'textdomain' ) ),
			'new_item'           => __( 'New Story', config( 'textdomain' ) ),
			'edit_item'          => __( 'Edit Story', config( 'textdomain' ) ),
			'view_item'          => __( 'View Story', config( 'textdomain' ) ),
			'all_items'          => __( 'All Stories', config( 'textdomain' ) ),
			'search_items'       => __( 'Search Stories', config( 'textdomain' ) ),
			'parent_item_colon'  => __( 'Parent Stories:', config( 'textdomain' ) ),
			'not_found'          => __( 'No stories found.', config( 'textdomain' ) ),
			'not_found_in_trash' => __( 'No stories found in Trash.', config( 'textdomain' ) ),
		],
	] );

	register_taxonomy_for_object_type( 'category', 'storybooked' );

}

/**
 * Registers `taste-of` custom post type.
 *
 * @return void
 */
add_action( 'init', 'Traveler\V2\Theme\Structure\register_taste_of_post_type' );

function register_taste_of_post_type() {

	register_post_type( 'taste-of', [
		'description'           => __( 'Collection of Taste of stories.', config( 'textdomain' ) ),
		'supports'              => [
			'title',
			'editor',
			'revisions',
			'author',
			'custom-fields',
			'excerpt',
			'page-attributes',
			'thumbnail'
		],
		'taxonomies'            => [
			'category'
		],
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-welcome-widgets-menus',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'taste-of',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'rewrite'               => [
			'slug'         => 'a-taste-of',
			'with_front'   => false,
			'hierarchical' => true,
		],
		'labels'                => [
			'name'               => _x( 'A Taste Of', 'post type general name', config( 'textdomain' ) ),
			'singular_name'      => _x( 'A Taste Of', 'post type singular name', config( 'textdomain' ) ),
			'menu_name'          => _x( 'A Taste Of', 'admin menu', config( 'textdomain' ) ),
			'name_admin_bar'     => _x( 'A Taste Of', 'add new on admin bar', config( 'textdomain' ) ),
			'add_new'            => _x( 'Add New', 'store', config( 'textdomain' ) ),
			'add_new_item'       => __( 'Add New Story', config( 'textdomain' ) ),
			'new_item'           => __( 'New Story', config( 'textdomain' ) ),
			'edit_item'          => __( 'Edit Story', config( 'textdomain' ) ),
			'view_item'          => __( 'View Story', config( 'textdomain' ) ),
			'all_items'          => __( 'All Stories', config( 'textdomain' ) ),
			'search_items'       => __( 'Search Stories', config( 'textdomain' ) ),
			'parent_item_colon'  => __( 'Parent Stories:', config( 'textdomain' ) ),
			'not_found'          => __( 'No stories found.', config( 'textdomain' ) ),
			'not_found_in_trash' => __( 'No stories found in Trash.', config( 'textdomain' ) ),
		],
	] );

	register_taxonomy_for_object_type( 'category', 'taste-of' );

}
