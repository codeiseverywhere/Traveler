<?php

namespace Traveler\V2\Theme\Structure;

/*
|--------------------------------------------------------------------------
| Custom Shortcodes
|--------------------------------------------------------------------------
|
| This file is for registering your shortcodes. Shortcodes allows to facade
| a code logic behind readable piece of text. Below you have an example
| which facilitates outputting markup with template() helper function.
|
*/

use Traveler\Core\TravelerCore;
use function add_shortcode;
use function get_field;
use function preg_replace;
use function Traveler\V2\Theme\Extras\is_apple_news_exporting;
use function Traveler\V2\Theme\template;
use function urlencode;

add_shortcode( 'irp', 'Traveler\V2\Theme\Structure\inline_related_posts' );

function inline_related_posts() {
	return false;
}


add_shortcode( 'sc', 'Traveler\V2\Theme\Structure\shortcoder' );

function shortcoder( $attr ) {

	if ( is_apple_news_exporting() || "/wp-json/wp/v2/relevanssi_search" === $GLOBALS['path'] ) {
		return false;
	}

	$post_id = get_the_ID();
	$city    = TravelerCore::get_city( $post_id, true );
	$query   = get_field( 'moments', $post_id ) ? get_field( 'moments', $post_id ) : '';

	if ( $city ) {
		$city = str_ireplace( 'New York City', 'new york', $city );
		$city = str_ireplace( 'Washington, D.C.', 'Washington, DC', $city );
	}

	return '<div class="moments-data" data-city-code="' . urlencode( $city ) . '" data-query="' . $query . '"></div>';
}


add_shortcode( 'lead', 'Traveler\V2\Theme\Structure\lead' );

function lead( $attr, $content = "" ) {

	return '<div class="lead">' . $content . '</div>';
}


add_shortcode( 'sidebar', 'Traveler\V2\Theme\Structure\sidebar' );

function sidebar( $attr ) {

	$attributes = shortcode_atts( [
		'align' => 'right'
	], $attr );

	$output = '';

	if ( get_sub_field( 'show_sidebar' ) ) {
		$output = '<div class="sidebar align' . $attributes['align'] . '">' . get_sub_field( 'sidebar' ) . '</div><br class="clearfix"/>';
	}


	return $output;
}


add_shortcode( 'heading', 'Traveler\V2\Theme\Structure\heading' );

function heading( $attr, $content = "" ) {

	return '<div class="interactive-heading text-center">' . $content . '</div>';
}

add_shortcode( 'quote', 'Traveler\V2\Theme\Structure\quotes' );

function quotes( $attributes ) {

	ob_start();

	template( 'shortcodes/quote', $attributes );

	return ob_get_clean();

}


add_shortcode( 'moments', 'Traveler\V2\Theme\Structure\moments' );

function moments() {

	$post_id = get_the_ID();

	if ( ! $city_code = wp_cache_get( 'city_code_' . $post_id, 'Traveler\V2\Structure\posts_' . get_current_blog_id() ) ) {

		$city = TravelerCore::get_city( $post_id, true );

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
					wp_cache_set( 'city_code_' . $post_id, $city_code, 'Traveler\V2\Structure\posts_' . get_current_blog_id(), HOUR_IN_SECONDS );
				}
			}
		}

	}

	if ( ! $experiences = wp_cache_get( 'experiences_' . $post_id, 'Traveler\V2\Structure\posts_' . get_current_blog_id() ) ) {

		$query = get_field( 'moments', $post_id ) ? get_field( 'moments', $post_id ) : '';

		$headers = [
			'Content-Type'              => 'application/json',
			'partner-id'                => '84389150-8f48-43b7-ac6b-25c5f1fb955a',
			'Ocp-Apim-Subscription-Key' => '1109c10c28f54db6a7018a5f4b23727a'
		];
		$body    = [
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

				$experiences .= '<h2>'.__( 'More Things to Do', 'traveler' ).'</h2>';
				$experiences .= '<div class="grid-x grid-margin-x medium-up-3 related-moments">';

				foreach ( $hits as $hit ) {
					$experiences .= '<div class="cell related-moment">';
					$experiences .= '<a href="' . $hit->ProductUrl . '?scid=2446299e-e416-480f-9392-409f6df1ad2d" target="_blank" rel="noreferrer noopener">';
					$experiences .= '<span class="aspect_ratio-16_9"><img loading="lazy" class="native-lazyload-js-fallback" data-src="' . preg_replace( '(http://|//)', 'https://', $hit->ProductImage ) . '" alt="' . $hit->ProductName . '" /></span><br/>';
					$experiences .= '</a>';
					$experiences .= '<p>';
					$experiences .= '<a href="' . $hit->ProductUrl . '?scid=2446299e-e416-480f-9392-409f6df1ad2d" target="_blank" rel="noreferrer noopener">';
					$experiences .= '<span class="moment_title">' . $hit->ProductName . '</span>';
					$experiences .= 'From <strong>$' . $hit->Price . '</strong> <br/>';

					foreach ( $hit->LoyaltyDetails as $loyalty_detail ) {

						if ( 'MAR' === $loyalty_detail->loyaltyProgramId ) {

							$experiences .= 'Earn <strong>' . $loyalty_detail->loyaltyPoints . '</strong> ' . $loyalty_detail->loyaltyProgramDisplayName . ' <br/>';
						}
					}
					$experiences .= '</a>';
					$experiences .= '</p>';
					$experiences .= '</div>';
				}
				$experiences .= '</div>';

				wp_cache_set( 'experiences_' . $post_id, $experiences, 'Traveler\V2\Structure\posts_' . get_current_blog_id(), HOUR_IN_SECONDS );
			}

		}
	}

	return '<div class="moments">' . $experiences . '</div>';
}


add_shortcode( 'morebox', 'Traveler\V2\Theme\Structure\more_box' );

function more_box( $attr ) {

	$attributes = shortcode_atts( [
		'number' => '4'
	], $attr );

	return '<div class="more-box" data-number="' . $attributes['number'] . '"></div>';
}
