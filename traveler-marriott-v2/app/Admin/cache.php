<?php

namespace Traveler\V2\Theme\Admin\Cache;


use Dwnload\WpRestApi\RestApi\RestDispatch;
use WP_Post;
use WP_REST_Request;
use WP_REST_Server;
use WpeCommon;
use function class_exists;
use function date;
use function defined;
use function get_lastpostmodified;
use function get_post_type_object;
use function is_object;
use function is_user_logged_in;
use function method_exists;
use function mysql2date;
use function time;
use function wp_schedule_single_event;
use const DAY_IN_SECONDS;
use const HOUR_IN_SECONDS;
use const MINUTE_IN_SECONDS;


if ( class_exists( 'Dwnload\WpRestApi\RestApi\RestDispatch' ) ) {

	if ( ! is_user_logged_in() ) {

		/**
		 *
		 * Set the cache-control and last-modified headers.
		 *
		 * @param array           $headers
		 * @param string          $request_uri
		 * @param WP_REST_Server  $server
		 * @param WP_REST_Request $request
		 *
		 * @return array
		 */

		add_filter( RestDispatch::FILTER_CACHE_HEADERS, 'Traveler\V2\Theme\Admin\Cache\cache_headers', 10, 4 );

		function cache_headers( array $headers, string $request_uri, WP_REST_Server $server, WP_REST_Request $request ) {

			$path = $request->get_route();

			switch ( $path ) {

				case false !== stripos( $path, '/v2/posts' ) :
				case false !== stripos( $path, '/v2/podcasts' ) :
				case false !== stripos( $path, '/v2/relevanssi_search' ) :
				case false !== stripos( $path, '/v2/menu-locations' ) :
					$max_age = DAY_IN_SECONDS;
					break;
				case false !== stripos( $path, '/v2/category-posts' ) :
					$max_age = MINUTE_IN_SECONDS;
					break;
				default:
					$max_age = HOUR_IN_SECONDS;
					break;
			}

			$wp_last_modified = mysql2date( 'D, d M Y H:i:s', get_lastpostmodified( 'GMT' ), false );

			if ( ! $wp_last_modified ) {
				$wp_last_modified = date( 'D, d M Y H:i:s' );
			}

			$wp_last_modified .= ' GMT';

			$headers['Cache-Control'] = 'public, max-age=' . $max_age;
			$headers['Last-Modified'] = $wp_last_modified;

			return $headers;
		}
	}


	add_filter( RestDispatch::FILTER_CACHE_SKIP, 'Traveler\V2\Theme\Admin\Cache\cache_skip', 10, 4 );

	function cache_skip( bool $skip, string $request_uri, WP_REST_Server $server, WP_REST_Request $request ) {

		$path = $request->get_route();

		if ( stripos( $path, '/v2/menu-locations' ) !== false ) {
			$skip = true;
		}

		return $skip;
	}

}


/**
 *
 * Clear the WP and REST cache on post creation/modification
 *
 * @param string  $new_status
 * @param string  $old_status
 * @param WP_Post $post
 *
 */

add_action( 'transition_post_status', 'Traveler\V2\Theme\Admin\Cache\clear_post_cache', 99, 3 );

function clear_post_cache( string $new_status, string $old_status, WP_Post $post ) {

	if ( defined( 'DOING_AUTOSAVE' ) ) {
		return false;
	}

	// Return if $post is not an object.
	if ( ! is_object( $post ) ) {
		return false;
	}

	// No purge for specific conditions.
	if ( 'auto-draft' === $post->post_status || empty( $post->post_type ) || 'nav_menu_item' === $post->post_type || 'attachment' === $post->post_type ) {
		return false;
	}

	// Don't purge if post's post type is not public or not publicly queryable.
	$post_type = get_post_type_object( $post->post_type );
	if ( ! is_object( $post_type ) || true !== $post_type->public ) {
		return false;
	}

	if ( ( 'publish' === $new_status || 'publish' === $old_status ) ) {

		if ( class_exists( 'WpeCommon' ) ) {

			$wpengine = WpeCommon::instance();

			if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {

				$wpengine->purge_memcached();
			}

			if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {

				$wpengine->purge_varnish_cache( (int) $post->ID );
			}

		} else {

			// Purge the cache of the servers we are connected with.
			wp_cache_flush();
		}

		if ( class_exists( 'Traveler\Cron\TravelerCronTasks' ) ) {

			wp_schedule_single_event( time(), 'traveler_init_cron' );
		}
	}

}


/**
 * Clear the Ajax Load More cache files from folder on post creation/modification
 */
add_action( 'created_term', 'Traveler\V2\Theme\Admin\Cache\clear_taxonomy_cache', 10 );
add_action( 'edited_terms', 'Traveler\V2\Theme\Admin\Cache\clear_taxonomy_cache', 10 );

function clear_taxonomy_cache() {


	if ( true === class_exists( 'WpeCommon' ) ) {

		if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {

			WpeCommon::purge_memcached();
		}

		if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {

			WpeCommon::purge_varnish_cache();
		}

	}
}


/**
 */
//add_filter( 'rocket_cache_reject_wp_rest_api', '\__return_false' );
//add_filter( 'rocket_htaccess_mod_expires', 'Traveler\V2\Theme\Admin\Cache\change_expires' );
/**
 * Changes to cache rest api results.
 * @return string
 */
function change_expires() {
	$rules = '# Expires headers (for better cache control)' . PHP_EOL;
	$rules .= '<IfModule mod_expires.c>' . PHP_EOL;
	$rules .= 'ExpiresActive on' . PHP_EOL . PHP_EOL;
	$rules .= '# Perhaps better to whitelist expires rules? Perhaps.' . PHP_EOL;
	$rules .= 'ExpiresDefault                          "access plus 1 month"' . PHP_EOL . PHP_EOL;
	$rules .= '# cache.appcache needs re-requests in FF 3.6 (thanks Remy ~Introducing HTML5)' . PHP_EOL;
	$rules .= 'ExpiresByType text/cache-manifest       "access plus 0 seconds"' . PHP_EOL . PHP_EOL;
	$rules .= '# Your document html' . PHP_EOL;
	$rules .= 'ExpiresByType text/html                 "access plus 1 hour"' . PHP_EOL . PHP_EOL;
	$rules .= '# Data' . PHP_EOL;
	$rules .= 'ExpiresByType text/xml                  "access plus 1 hour"' . PHP_EOL;
	$rules .= 'ExpiresByType application/xml           "access plus 1 hour"' . PHP_EOL;
	$rules .= 'ExpiresByType application/json          "access plus 1 hour"' . PHP_EOL;
	$rules .= 'ExpiresByType application/ld+json       "access plus 1 hour"' . PHP_EOL;
	$rules .= 'ExpiresByType application/schema+json   "access plus 1 hour"' . PHP_EOL . PHP_EOL;
	$rules .= '# Feed' . PHP_EOL;
	$rules .= 'ExpiresByType application/rss+xml       "access plus 1 hour"' . PHP_EOL;
	$rules .= 'ExpiresByType application/atom+xml      "access plus 1 hour"' . PHP_EOL . PHP_EOL;
	$rules .= '# Favicon (cannot be renamed)' . PHP_EOL;
	$rules .= 'ExpiresByType image/x-icon              "access plus 1 week"' . PHP_EOL . PHP_EOL;
	$rules .= '# Media: images, video, audio' . PHP_EOL;
	$rules .= 'ExpiresByType image/gif                 "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType image/png                 "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType image/jpeg                "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType video/ogg                 "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType audio/ogg                 "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType video/mp4                 "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType video/webm                "access plus 1 month"' . PHP_EOL . PHP_EOL;
	$rules .= '# HTC files  (css3pie)' . PHP_EOL;
	$rules .= 'ExpiresByType text/x-component          "access plus 1 month"' . PHP_EOL . PHP_EOL;
	$rules .= '# Webfonts' . PHP_EOL;
	$rules .= 'ExpiresByType application/x-font-ttf    "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType font/opentype             "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType application/x-font-woff   "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType application/x-font-woff2  "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType image/svg+xml             "access plus 1 month"' . PHP_EOL;
	$rules .= 'ExpiresByType application/vnd.ms-fontobject "access plus 1 month"' . PHP_EOL . PHP_EOL;
	$rules .= '# CSS and JavaScript' . PHP_EOL;
	$rules .= 'ExpiresByType text/css                  "access plus 1 year"' . PHP_EOL;
	$rules .= 'ExpiresByType application/javascript    "access plus 1 year"' . PHP_EOL . PHP_EOL;
	$rules .= '</IfModule>' . PHP_EOL . PHP_EOL;

	return $rules;
}
