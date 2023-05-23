<?php

namespace Traveler\V2\Theme\Plugins\AdvancedCustomFields;

use function function_exists;

add_filter( 'acf/settings/save_json', 'Traveler\V2\Theme\Plugins\AdvancedCustomFields\json_save_point' );

function json_save_point( $path ) {

	// update path
	return get_template_directory() . '/resources/assets/acf-json';
}

add_filter( 'acf/settings/load_json', 'Traveler\V2\Theme\Plugins\AdvancedCustomFields\json_load_point' );

function json_load_point( $paths ) {

	// remove original path (optional)
	unset( $paths[0] );

	// append path
	$paths[] = get_template_directory() . '/resources/assets/acf-json';

	// return
	return $paths;
}

if ( function_exists( 'acf_add_options_page' ) ) {

	acf_add_options_page( [
		'page_title' => __( 'Options', 'traveler' ),
		'menu_title' => __( 'Options', 'traveler' ),
		'menu_slug'  => 'acf-options',
		'capability' => 'edit_posts',
		'redirect'   => false
	] );
}

if ( function_exists( 'acf_add_local_field_group' ) ):
	global $blog_id;
	$origin_id = absint( 1 );

	if ( $origin_id ===
	     $blog_id ) :
		acf_add_local_field_group( [
			'key'                   => 'group_59b19748ea787',
			'title'                 => 'Hreflang',
			'fields'                => [
				[
					'key'               => 'field_59b19759421a4',
					'label'             => 'Translated Post',
					'name'              => 'translated_post',
					'type'              => 'relationship_multisite',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'site'              => 2,
					'post_type'         => [
						0 => 'post',
					],
					'taxonomy'          => '',
					'filters'           => [
						0 => 'search',
					],
					'elements'          => '',
					'min'               => 1,
					'max'               => 1,
					'return_format'     => 'id',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					],
				],
			],
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
		] );
	else :
		acf_add_local_field_group( [
			'key'                   => 'group_59b19748ea787',
			'title'                 => 'Hreflang',
			'fields'                => [
				[
					'key'               => 'field_59b19759421a4',
					'label'             => 'Translated Post',
					'name'              => 'translated_post',
					'type'              => 'relationship_multisite',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'site'              => 1,
					'post_type'         => [
						0 => 'post',
					],
					'taxonomy'          => '',
					'filters'           => [
						0 => 'search',
					],
					'elements'          => '',
					'min'               => 1,
					'max'               => 1,
					'return_format'     => 'id',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					],
				],
			],
			'menu_order'            => 1,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => 1,
			'description'           => '',
		] );
	endif;
endif;

