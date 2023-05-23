<?php

namespace Traveler\V2\Theme\Setup;

/*
|-----------------------------------------------------------
| Theme Custom Services
|-----------------------------------------------------------
|
| This file is for registering your third-parity services
| or custom logic within theme container, so they can
| be easily used for a theme template files later.
|
*/

use Tonik\Gin\Foundation\Theme;
use Traveler\Core\TravelerCore;
use WP_Post;
use WP_Query;
use function basename;
use function count;
use function get_current_blog_id;
use function get_field;
use function get_post_meta;
use function get_terms;
use function is_array;
use function is_wp_error;
use function preg_replace;
use function str_replace;
use function Traveler\V2\Theme\theme;
use function urlencode;
use function wp_cache_get;
use function wp_cache_set;
use function wp_remote_retrieve_body;
use function wp_reset_postdata;
use function wp_safe_remote_get;
use function wp_safe_remote_post;
use const HOUR_IN_SECONDS;
use const MINUTE_IN_SECONDS;


/**
 * Service handler for retrieving categories to be used on our router.
 *
 * @return void
 */

add_action( 'init', 'Traveler\V2\Theme\Setup\bind_routes_service' );

function bind_routes_service() {

	/**
	 * Binds service for retrieving categories.
	 *
	 * @param Theme $theme Instance of the service container
	 * @param array $parameters Parameters passed on service resolving
	 *
	 * @return WP_Post[]
	 */
	theme()->bind( 'api/routes', function ( Theme $theme, $parameters ) {

		$blog_id         = get_current_blog_id();
		$cache_key       = 'traveler/api/routes/';
		$cache_group     = 'Traveler\V2\Structure\routes_' . $blog_id;
		$main_categories = TravelerCore::get_main_categories();

		if ( 0 || false === ( $routes = wp_cache_get( $cache_key, $cache_group ) ) ) {

			$routes = [];

			$args = [
				'taxonomy'     => 'category',
				'exclude'      => [ TravelerCore::city_parent() ],
				'orderby'      => 'parent',
				'cache_domain' => 'traveler_routes',
			];

			$_terms = get_terms( $args );

			if ( $_terms ) :
				$categories = [];

				foreach ( $_terms as $key => $term ) {

					$is_child = (bool) $term->parent;
					$is_city  = TravelerCore::is_city( $term->term_id );
					$template = 'Category';

					if ( $is_city ) {
						$template = 'CategoryCity';
					} elseif ( $is_child ) {
						$template = 'CategoryChild';
					}

					$categories[] = [
						'id'       => $term->term_id,
						'name'     => $term->name,
						'template' => $template,
						'is_city'  => $is_city,
						'is_child' => $is_child,
						'slug'     => basename( $term->slug ),
					];

					if ( $is_city ) {
						foreach ( $main_categories as $main_category ) {

							$categories[] = [
								'id'       => $term->term_id,
								'name'     => $term->name . ' ' . $main_category->name,
								'template' => 'CategoryCityChild',
								'is_city'  => true,
								'is_child' => true,
								'slug'     => $term->slug . '/' . $main_category->slug,
							];

						}
					}

				}

				$routes['categories'] = $categories;

			endif;

			//StoryBooked

			$args = [
				'post_type'      => [
					'storybooked'
				],
				'posts_per_page' => '500',
				'post_status'    => 'publish',
			];

			$page_query = new WP_Query( $args );

			if ( $page_query->have_posts() ) :

				while ( $page_query->have_posts() ) : $page_query->the_post();

					global $post;

					$_template = get_post_meta( $post->ID, '_wp_page_template', true );

					switch ( $_template ) {

						case 'gutenberg.php' :
							$template = 'PostBlock';
							break;

						case 'interactive.php' :
							$template = 'PostACF';
							break;

						default:
							$template = 'Post';
							break;
					}

					$routes['storybooked'][] = [
						'id'       => $post->ID,
						'name'     => $post->post_title,
						'slug'     => basename( $post->post_name ),
						'template' => $template,
						'type'     => $post->post_type,
					];

				endwhile;

			endif;

			wp_reset_postdata();

			//A Taste of

			$args = [
				'post_type'      => [
					'taste-of'
				],
				'posts_per_page' => '500',
				'post_status'    => 'publish',
			];

			$page_query = new WP_Query( $args );

			if ( $page_query->have_posts() ) :

				while ( $page_query->have_posts() ) : $page_query->the_post();

					global $post;

					$_template = get_post_meta( $post->ID, '_wp_page_template', true );

					switch ( $_template ) {

						case 'gutenberg.php' :
							$template = 'PostBlock';
							break;

						case 'interactive.php' :
							$template = 'PostACF';
							break;

						default:
							$template = 'Post';
							break;
					}

					$routes['a_taste_of'][] = [
						'id'       => $post->ID,
						'name'     => $post->post_title,
						'slug'     => basename( $post->post_name ),
						'template' => $template,
						'type'     => $post->post_type,
					];


				endwhile;

			endif;

			wp_reset_postdata();

			//Hubs

			$args = [
				'post_type'              => [
					'page',
				],
				'posts_per_page'         => '500',
				'post_status'            => 'publish',
				'post_parent'            => 0,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'cache_results'          => false,
				'meta_query'             => [
					[
						'key'   => '_wp_page_template',
						'value' => 'gutenberg.php'
					]
				]
			];

			$page_query = new WP_Query( $args );

			if ( $page_query->have_posts() ) :

				while ( $page_query->have_posts() ) : $page_query->the_post();

					global $post;

					if ( 'gutenberg.php' !== get_post_meta( $post->ID, '_wp_page_template', true ) ) {
						continue;
					}

					$routes['hubs'][] = [
						'id'       => $post->ID,
						'name'     => $post->post_title,
						'slug'     => basename( $post->post_name ),
						'template' => 'PageHub',
						'type'     => $post->post_type,
					];

				endwhile;

			endif;

			wp_reset_postdata();

			//lgbtq

			$args = [
				'post_type'              => [
					'post',
				],
				'posts_per_page'         => '10',
				'post_status'            => 'publish',
				'post_parent'            => 0,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'cache_results'          => false,
				'meta_query'             => [
					[
						'key'   => '_wp_page_template',
						'value' => 'lgbtq.php'
					]
				]
			];

			$post_query = new WP_Query( $args );

			if ( $post_query->have_posts() ) :

				while ( $post_query->have_posts() ) : $post_query->the_post();

					global $post;

					if ( 'lgbtq.php' !== get_post_meta( $post->ID, '_wp_page_template', true ) ) {
						continue;
					}

					$routes['lgbtq'][] = [
						'id'       => $post->ID,
						'name'     => $post->post_title,
						'slug'     => basename( $post->post_name ),
						'template' => 'PostLGBTQ',
						'type'     => 'posts',
					];

				endwhile;

			endif;

			wp_reset_postdata();

			//Posts

			$args = [
				'post_type'              => [
					'post',
				],
				'posts_per_page'         => '500',
				'post_status'            => 'publish',
				'post_parent'            => 0,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'cache_results'          => false,
				'meta_query'             => [
					[
						'key'   => '_wp_page_template',
						'value' => 'gutenberg.php'
					]
				]
			];

			$post_query = new WP_Query( $args );

			if ( $post_query->have_posts() ) :

				while ( $post_query->have_posts() ) : $post_query->the_post();

					global $post;

					if ( 'gutenberg.php' !== get_post_meta( $post->ID, '_wp_page_template', true ) ) {
						continue;
					}

					$routes['posts'][] = [
						'id'       => $post->ID,
						'name'     => $post->post_title,
						'slug'     => basename( $post->post_name ),
						'template' => 'PostBlock',
						'type'     => 'posts',
					];

				endwhile;

			endif;

			wp_reset_postdata();

			wp_cache_set( $cache_key, $routes, $cache_group, MINUTE_IN_SECONDS );
		}

		return $routes;
	} );
}


/**
 * Service handler for retrieving posts of specific post type.
 *
 * @return void
 */

add_action( 'init', 'Traveler\V2\Theme\Setup\bind_city_code_service' );

function bind_city_code_service() {
	/**
	 * Binds service for retrieving posts of specific post type.
	 *
	 * @param Theme $theme Instance of the service container
	 * @param array $parameters Parameters passed on service resolving
	 *
	 * @return WP_Post[]
	 */
	theme()->bind( 'city/code', function ( Theme $theme, $parameters ) {

		$blog_id     = get_current_blog_id();
		$cache_key   = 'traveler/api/city_code_' . $parameters['id'];
		$cache_group = 'Traveler\V2\Structure\posts_' . $blog_id;
		$post_id     = $parameters['id'];

		if ( ! $city_code = wp_cache_get( $cache_key, $cache_group ) ) {

			$city = TravelerCore::get_city( $post_id, false );

			if ( $city ) {

				$city = str_replace( 'new york city', 'new york', $city );
				$city = str_replace( 'Washington, D.C.', 'Washington, DC', $city );

				$args = [
					'headers' => [
						'Content-Type'              => 'application/json',
						'partner-id'                => '84389150-8f48-43b7-ac6b-25c5f1fb955a',
						'Ocp-Apim-Subscription-Key' => '1109c10c28f54db6a7018a5f4b23727a'
					]
				];

				$request = wp_safe_remote_get( 'https://placepass.azure-api.net/data-api/partners/places?q=' . urlencode( $city ) . '&count=1', $args );

				if ( ! is_wp_error( $request ) ) {

					$body = wp_remote_retrieve_body( $request );
					$json = json_decode( $body );

					if ( is_array( $json ) && ! is_wp_error( $json ) ) {

						$city_code = $json[0]->PlaceId;
						wp_cache_set( $cache_key, $city_code, $cache_group, HOUR_IN_SECONDS );
					}
				}
			}

		}

		return $city_code;

	} );
}


/**
 * Service handler for retrieving posts of specific post type.
 *
 * @return void
 */

add_action( 'init', 'Traveler\V2\Theme\Setup\bind_activities_service' );

function bind_activities_service() {
	/**
	 * Binds service for retrieving posts of specific post type.
	 *
	 * @param Theme $theme Instance of the service container
	 * @param array $parameters Parameters passed on service resolving
	 *
	 * @return WP_Post[]
	 */
	theme()->bind( 'activities', function ( Theme $theme, $parameters ) {

		$blog_id     = get_current_blog_id();
		$cache_key   = 'traveler/api/activities_' . $parameters['id'];
		$cache_group = 'Traveler\V2\Structure\activities_' . $blog_id;
		$post_id     = $parameters['id'];

		$city_code = theme( 'city/code', [ 'id' => $post_id ] );

		if ( ! $hits = wp_cache_get( $cache_key, $cache_group ) ) {

			$query = get_field( 'moments', $post_id ) ? get_field( 'moments', $post_id ) : '';

			$headers = [
				'Content-Type'              => 'application/json',
				'partner-id'                => '84389150-8f48-43b7-ac6b-25c5f1fb955a',
				'Ocp-Apim-Subscription-Key' => '1109c10c28f54db6a7018a5f4b23727a'
			];

			$body = [
				'hitsPerPage' => 3,
				'isBookable'  => true,
				'pageNumber'  => 0,
				'query'       => $query,
				'sortBy'      => 'topRated'
			];

			if ( $city_code ) {
				$body['location'] = [
					'geoLocationId' => $city_code
				];
			}

			$args = [
				'headers' => $headers,
				'body'    => json_encode( $body )
			];


			$request = wp_safe_remote_post( 'https://placepass.azure-api.net/searches/search', $args );

			if ( ! is_wp_error( $request ) ) {

				$body = wp_remote_retrieve_body( $request );
				$json = json_decode( $body );

				if ( $json && 1 <= count( $json->hits ) ) {

					$hits = $json->hits;

					foreach ( $hits as $key => $hit ) {

						$hits[ $key ]->ProductUrl   = $hit->ProductUrl . '?scid=2446299e-e416-480f-9392-409f6df1ad2d';
						$hits[ $key ]->ProductImage = preg_replace( '(http://|//)', 'https://', $hit->ProductImage );
					}

					wp_cache_set( $cache_key, $hits, $cache_group, HOUR_IN_SECONDS );
				}

			}

		}

		return $hits;

	} );
}
