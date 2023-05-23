<?php

namespace Traveler\V2\Theme\Setup;

/*
|-----------------------------------------------------------
| Theme Filters
|-----------------------------------------------------------
|
| This file purpose is to include your theme various
| filters hooks, which changes output or behaviour
| of different parts of WordPress functions.
|
*/

use Traveler\Core\TravelerCore;
use WP_Post;
use WP_REST_Response;
use function add_filter;
use function get_field;
use function get_preview_post_link;
use function is_array;
use function is_singular;
use function preg_replace;
use function str_replace;
use function Traveler\V2\Theme\theme;

add_filter( 'load_default_widgets', '__return_false' );

/**
 * Shortens posts excerpts to 60 words.
 *
 * @return integer
 */
add_filter( 'excerpt_length', 'Traveler\V2\Theme\Setup\excerpt_length', 999 );

function excerpt_length() {
	return 30;
}


add_filter( 'excerpt_more', 'Traveler\V2\Theme\Setup\excerpt_more', 999 );

function excerpt_more() {
	return '&hellip;';
}

add_filter( 'acf/rest_api/post/get_fields', 'Traveler\V2\Theme\Setup\acf_rest', 10, 2 );
add_filter( 'acf/rest_api/page/get_fields', 'Traveler\V2\Theme\Setup\acf_rest', 10, 2 );
add_filter( 'acf/rest_api/storybooked/get_fields', 'Traveler\V2\Theme\Setup\acf_rest', 10, 2 );
function acf_rest( $data, $request ) {
	foreach ( $data['acf'] as $key => $field ) {

		if ( 'hero_slider' === $key && is_array( $data['acf'][ $key ]) ) {
			$data['acf'][ $key ] = TravelerCore::extend_post_object( $data['acf'][ $key ] );
		} elseif ( 'video' === $key && false !== $data['acf'][ $key ] ) {
			$data['acf'][ $key ]['url'] = TravelerCore::cdn_replace( $data['acf'][ $key ]['url'] );
		} elseif ( 'sidebar' === $key ) {
			foreach ( $data['acf'][ $key ] as $_widget ) {
				if ( 'recommended_for_you' === $_widget['acf_fc_layout'] || 'editors_picks' === $_widget['acf_fc_layout'] ) {
					$_widget['posts'] = TravelerCore::extend_post_object( $_widget['posts'] );
				}
			}
		}
	}

	return $data;
}


add_filter( 'query_vars', 'Traveler\V2\Theme\Setup\query_vars' );

function query_vars( $query_vars ) {
	$query_vars[] = 'ad_id';
	$query_vars[] = 'group_id';

	return $query_vars;
}


add_filter( 'amp_content_max_width', 'Traveler\V2\Theme\Setup\content_width' );
add_filter( 'tiled_gallery_content_width', 'Traveler\V2\Theme\Setup\content_width' );

function content_width() {
	return 875;
}


/**
 * Filter the author archive link text
 */


add_filter( 'the_author_posts_link', 'Traveler\V2\Theme\Setup\author_archive_link' );

function author_archive_link( $link ) {

	return str_replace( 'Posts by', '', $link );
}


/**
 *
 * Extends the WHERE clause in the SQL for an adjacent post query.
 *
 * Excludes interactive articles from the regular infinite scroll articles.
 *
 * @param String $where
 *
 * @return string
 */

add_filter( 'get_previous_post_where', 'Traveler\V2\Theme\Setup\skip_adjacent_enhanced_article' );
add_filter( 'get_next_post_where', 'Traveler\V2\Theme\Setup\skip_adjacent_enhanced_article' );

function skip_adjacent_enhanced_article( $where ) {

	if ( is_singular( 'post' ) ) {

		$where .= " AND p.ID NOT IN ( SELECT m.post_id FROM wp_postmeta AS m where m.meta_key = '_wp_page_template' and m.meta_value = 'gutenberg.php' )";
	}

	return $where;
}


/**
 *
 * Add filter to respond with next and previous post in post response.
 *
 * @param $response
 * @param $post
 * @param $request
 *
 * @return mixed
 */

add_filter( 'rest_prepare_post', 'Traveler\V2\Theme\Setup\add_adjacent_links', 10, 3 );

function add_adjacent_links( $response, $post, $request ) {

	// Only do this for single post requests.
	if ( $request->get_param( 'per_page' ) === 1 || $request->get_param( 'id' ) ) {

		// Get the so-called next post.
		$next = get_adjacent_post( false, '', false );

		if ( is_a( $next, 'WP_Post' ) ) {

			// Format them a bit and only send id and slug (or null, if there is no next/previous post).
			$response->data['next'] = [
				'name'  => $next->post_name,
				'title' => $next->post_title,
			];
		}


		// Get the so-called previous post.
		$previous = get_adjacent_post( false, '', true );
		if ( is_a( $previous, 'WP_Post' ) ) {

			// Format them a bit and only send id and slug (or null, if there is no next/previous post).
			$response->data['previous'] = [
				'name'  => $previous->post_name,
				'title' => $previous->post_title,
			];
		}
	}

	return $response;
}


/**
 *
 * Changes the preview link to show it on the Vue view.
 *
 * @param String  $preview_link
 * @param WP_Post $post
 *
 * @return string
 */

add_filter( 'preview_post_link', 'Traveler\V2\Theme\Setup\preview_link', 20, 2 );

function preview_link( $preview_link, WP_Post $post ) {

	$args = [
		'post_type' => $post->post_type,
		'preview'   => 'true',
	];

	switch ( $post->post_type ) {
		case 'taste-of':
			$preview_slug = '/preview-taste-of/';
			$args['p']    = $post->ID;
			break;
		case 'storybooked':
			$preview_slug = '/preview-storybooked/';
			$args['p']    = $post->ID;
			break;
		case 'page':
			$preview_slug    = '/preview-page/';
			$args['page_id'] = $post->ID;
			break;
		default:
			$preview_slug = '/preview/';
			$args['p']    = $post->ID;
	}

	$preview_link = add_query_arg( $args, site_url( $preview_slug ) );

	return $preview_link;
}

/**
 * @param $url
 * @param $post
 *
 * @return string
 */

add_filter( 'post_type_link', 'Traveler\V2\Theme\Setup\post_type_preview_link', 10, 2 );

function post_type_preview_link( $url, $post ) {

	if ( 'draft' === $post->post_status || 'future' === $post->post_status ) {

		return preview_link( $url, $post );
	}

	return $url;
}


/**
 * Includes preview link in post data for a response.
 *
 * @param WP_REST_Response $response The response object.
 * @param WP_Post          $post Post object.
 *
 * @return WP_REST_Response The response object.
 */


add_filter( 'rest_prepare_post', 'Traveler\V2\Theme\Setup\preview_link_in_rest', 5, 2 );
add_filter( 'rest_prepare_page', 'Traveler\V2\Theme\Setup\preview_link_in_rest', 5, 2 );
add_filter( 'rest_prepare_taste-of', 'Traveler\V2\Theme\Setup\preview_link_in_rest', 5, 2 );
add_filter( 'rest_prepare_storybooked', 'Traveler\V2\Theme\Setup\preview_link_in_rest', 5, 2 );

function preview_link_in_rest( $response, $post ) {

	if ( 'draft' === $post->post_status || 'future' === $post->post_status ) {
		$response->data['link']         = get_preview_post_link( $post );
		$response->data['preview_link'] = get_preview_post_link( $post );
	}

	return $response;
}

/**
 * Apple news content, json styles filter.
 */


add_filter( 'apple_news_is_post_in_sync', 'Traveler\V2\Theme\Setup\an_post_sync' );

function an_post_sync() {

	return false;
}


add_filter( 'apple_news_exporter_content_pre', 'Traveler\V2\Theme\Setup\an_content_filter_shortcodes' );

function an_content_filter_shortcodes( $content ) {

	return preg_replace( '/\[sc .*\]/uim', '', $content );
}

add_filter( 'apple_news_component_text_styles', 'Traveler\V2\Theme\Setup\an_component_text_styles', 10, 1 );

function an_component_text_styles( $styles ) {

	$styles['bodyStyle'] = [
		'textAlignment'          => 'left',
		'fontName'               => 'HelveticaNeue',
		'fontSize'               => 16,
		'tracking'               => 0,
		'lineHeight'             => 24,
		'textColor'              => '#1c1c1c',
		'linkStyle'              =>
			[
				'textColor' => '#ff9662',
			],
		'paragraphSpacingBefore' => 18,
		'paragraphSpacingAfter'  => 18
	];

	$styles['whereToStayLayout'] = [
		'fontName'      => 'TimesNewRomanPSMT',
		'fontSize'      => 24,
		'lineHeight'    => 36,
		'tracking'      => 0,
		'textColor'     => '#1C1C1C',
		'textAlignment' => 'left',
	];

	//$styles['activitiesLayout'] = [];

	$styles['activitiesHeadingLayout'] = [
		'fontName'      => 'HelveticaNeue-Bold',
		'fontSize'      => 32,
		'lineHeight'    => 36,
		'textColor'     => '#1c1c1c',
		'textAlignment' => 'left',
		'tracking'      => 0,
	];

	$styles['footerLayout'] = [
		'textAlignment'          => 'center',
		'fontName'               => 'HelveticaNeue',
		'fontSize'               => 16,
		'tracking'               => 0,
		'lineHeight'             => 24,
		'textColor'              => '#1c1c1c',
		'linkStyle'              =>
			[
				'textColor'      => '#ff9662',
				'textDecoration' => 'underline',
			],
		'paragraphSpacingBefore' => 18,
		'paragraphSpacingAfter'  => 18,
	];

	return $styles;
}


/**
 * Apple news json styles filter.
 */


add_filter( 'apple_news_component_styles', 'Traveler\V2\Theme\Setup\an_component_styles', 10, 1 );

function an_component_styles( $styles ) {

	$styles['whereToStayLayout'] = [
		'backgroundColor' => '#FFF3ED',
	];

	$styles['activitiesLayout'] = [
		'backgroundColor' => '#FFF3ED',
	];

	$styles['footerLayout'] = [
		'backgroundColor' => '#FFFFFF',
	];

	$styles['footerLogoLayout'] = [
		'fill' =>
			[
				'type'              => 'image',
				'URL'               => get_field( 'logo', 'option' ),
				'fillMode'          => 'fit',
				'verticalAlignment' => 'center',
			],
	];

	return $styles;
}


/**
 * Apple news json layouts filter.
 */


add_filter( 'apple_news_component_layouts', 'Traveler\V2\Theme\Setup\an_component_layouts', 10, 1 );

function an_component_layouts( $layouts ) {

	$layouts['defaultLayout'] = [
		'columnStart' => 0,
		'columnSpan'  => 7,
		'margin'      =>
			[
				'top'    => 12,
				'bottom' => 30,
			],
	];

	$layouts['activitiesHeadingLayout'] = [
		'columnStart' => 0,
		'columnSpan'  => 6,
		'margin'      =>
			[
				'top'    => 15,
				'bottom' => 15,
			],
	];

	$layouts['activitiesImageLayout'] = [
		'columnStart' => 0,
		'columnSpan'  => 7,
		'margin'      =>
			[
				'top'    => 5,
				'bottom' => 5,
			],
	];

	$layouts['whereToStayLayout'] = [
		'ignoreDocumentMargin' => true,
		'columnStart'          => 0,
		'columnSpan'           => 7,
		'margin'               =>
			[
				'top'    => 20,
				'bottom' => 0,
			],
	];

	$layouts['activitiesLayout'] = [
		'ignoreDocumentMargin' => true,
		'columnStart'          => 0,
		'columnSpan'           => 7,
		'margin'               =>
			[
				'top'    => 0,
				'bottom' => 0,
			],
	];

	$layouts['footerLogoLayout'] = [
		'minimumHeight' => '10vh',
		'columnStart'   => 0,
		'columnSpan'    => 7,
		'margin'        =>
			[
				'top'    => 0,
				'bottom' => 0,
			],
	];

	$layouts['footerLayout'] = [
		'columnStart' => 0,
		'columnSpan'  => 7,
	];

	return $layouts;
}


/**
 * Apple news json content filter.
 */


add_filter( 'apple_news_generate_json', 'Traveler\V2\Theme\Setup\an_json_content_where_to_stay', 10, 2 );

function an_json_content_where_to_stay( $json, $content_id ) {

	if ( $where_to = get_field( 'where_to_stay_links', $content_id ) ) {

		//Older entries of the `where_to_stay_links` field were poorly formatted and include divs tags.
		//This is an attempt to clean data from such tags.
		$remove_div = preg_replace( '/<div[^>]*>.*?<\/div>/uis', '', $where_to );
		$unwrap_ul  = preg_replace( '/<ul[^>]*>(.*?)<\/ul>/uis', '$1', $remove_div );

		$json['components'][] = [
			'role'       => 'container',
			'layout'     => 'whereToStayLayout',
			'style'      => 'whereToStayLayout',
			'textStyle'  => 'whereToStayLayout',
			'components' => [
				[
					'role'      => 'heading2',
					'text'      => 'Where to Stay',
					'format'    => 'html',
					'textStyle' => 'activitiesHeadingLayout',
					'layout'    => 'activitiesHeadingLayout',
				],
				[
					'role'   => 'body',
					'text'   => '<ul>' . $unwrap_ul . '</ul>',
					'format' => 'html',
					'layout' => 'defaultLayout',
				],
			],
		];
	}

	unset( $remove_div, $unwrap_ul, $where_to );

	return $json;
}


add_filter( 'apple_news_generate_json', 'Traveler\V2\Theme\Setup\an_json_content_activities', 15, 2 );

function an_json_content_activities( $json, $content_id ) {

	$activities = false;

	if ( 0 || false !== ( $activities = theme( 'activities', [ 'id' => $content_id ] ) ) ) {

		$_activities_arr = [
			'role'       => 'container',
			'layout'     => 'activitiesLayout',
			'style'      => 'activitiesLayout',
			'textStyle'  => 'activitiesLayout',
			'components' => [
				[
					'role'      => 'heading2',
					'text'      => 'More Things to Do',
					'format'    => 'html',
					'textStyle' => 'activitiesHeadingLayout',
					'layout'    => 'activitiesHeadingLayout',
				]
			]
		];

		foreach ( $activities as $activity ) {

			$image = preg_replace( '/^(http(s?)(:)?(\/\/)?|\/\/){0,9}/uism', 'https://', $activity->ProductImage );

			$_activities_arr['components'][] = [
				'role'   => 'photo',
				'URL'    => $image,
				'layout' => 'activitiesImageLayout',
			];

			$text = '<p><a href="' . $activity->ProductUrl . '">' . $activity->ProductName . '</a><br/>';
			$text .= 'From <strong>$' . $activity->Price . '</strong><br />';
			foreach ( $activity->LoyaltyDetails as $loyalty_detail ) {

				if ( 'MAR' === $loyalty_detail->loyaltyProgramId ) {

					$text .= 'Earn <strong>' . $loyalty_detail->loyaltyPoints . '</strong> ' . $loyalty_detail->loyaltyProgramDisplayName . ' <br/>';
				}

				unset( $loyalty_detail );
			}
			$text .= '</p>';

			$_activities_arr['components'][] = [
				'role'      => 'body',
				'text'      => $text,
				'format'    => 'html',
				'textStyle' => 'bodyStyle',
				'layout'    => 'defaultLayout',
			];

			unset( $text, $activity, $image );
		}

		$json['components'][] = $_activities_arr;

		unset( $_activities_arr );

	}

	unset( $activities );

	return $json;
}


add_filter( 'apple_news_generate_json', 'Traveler\V2\Theme\Setup\an_json_content_footer', 20 );

function an_json_content_footer( $json ) {

	$json['components'][] = [
		'role'   => 'container',
		'layout' => 'footerLogoLayout',
		'style'  => 'footerLogoLayout',
	];

	$json['components'][] = [
		'role'       => 'container',
		'layout'     => 'footerLayout',
		'style'      => 'footerLayout',
		'textStyle'  => 'footerLayout',
		'components' =>
			[

				[
					'role'      => 'body',
					'text'      => '<p>Read more on <br/><a href="' . site_url( '/', 'https' ) . '" target="_blank">' . get_option( 'blogname' ) . '</a></p>',
					'format'    => 'html',
					'layout'    => 'defaultLayout',
					'textStyle' => 'footerLayout',
				],
			],
	];

	return $json;
}


/**
 * Relevanssi filters to remove shortcodes out of excerpt
 */


//add_filter( 'relevanssi_disable_shortcodes_excerpt', 'Traveler\V2\Theme\Setup\relevanssi_disable_shortcodes', 10 );

function relevanssi_disable_shortcodes( $problem_shortcodes ) {

	$problem_shortcodes[] = 'sc';

	return $problem_shortcodes;
}
