<?php

namespace Traveler\V2\Theme\Http;

/*
|-----------------------------------------------------------------
| Theme Assets
|-----------------------------------------------------------------
|
| This file is for registering your theme stylesheets and scripts.
| In here you should also deregister all unwanted assets which
| can be shipped with various third-parity plugins.
|
*/

use Traveler\Cron\TravelerCronTasks;
use WP_REST_Request;
use WP_Scripts;
use function antispambot;
use function get_current_blog_id;
use function get_locale;
use function get_permalink;
use function get_registered_nav_menus;
use function get_site_transient;
use function get_template_directory;
use function home_url;
use function intval;
use function is_singular;
use function is_ssl;
use function is_user_logged_in;
use function network_site_url;
use function preg_replace;
use function rest_get_server;
use function rest_url;
use function sprintf;
use function strpos;
use function strtolower;
use function substr;
use function trailingslashit;
use function Traveler\V2\Theme\asset;
use function Traveler\V2\Theme\asset_path;
use function Traveler\V2\Theme\Extras\is_amp;
use function Traveler\V2\Theme\theme;
use function wp_cache_get;
use function wp_cache_set;
use function wp_create_nonce;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_get_current_user;
use function wp_is_mobile;
use function wp_localize_script;
use function wp_login_url;
use const DAY_IN_SECONDS;

function localize_strings() {

	return [
		'site_name'              => get_bloginfo( 'name' ),
		'current_site'           => site_url( '/', 'https' ),
		'rest_base_page'         => rest_url(),
		'nonce'                  => wp_create_nonce( 'wp_rest' ),
		'site_id'                => get_current_blog_id(),
		'site_email'             => 'mailto:' . antispambot( 'traveler@marriott.com', 1 ),
		'routes'                 => theme( 'api/routes' ),
		'menu_cache_url'         => network_site_url( 'wp-content/cache/menu/' . trailingslashit( get_current_blog_id() ), 'https' ),
		'sister_sites_cache_url' => network_site_url( 'wp-content/cache/sister_sites/' . trailingslashit( get_current_blog_id() ), 'https' ),
		'inApp'                  => isset($_GET['inMarriottApp']) ? 'true' : 'false',
		'links'                  => [
			'tos'       => __( 'https://www.marriott.com/about/terms-of-use.mi', 'traveler' ),
			'privacy'   => __( 'https://www.marriott.com/about/privacy.mi', 'traveler' ),
			'bookRoom'  => __( 'https://www.marriott.com/default.mi', 'traveler' ),
			'about'     => __( '/about-us/', 'traveler' ),
			'search'    => __( '/search/', 'traveler' ),
			'signin'    => '#',
			'magazines' => '#',
		],
		'i18n'                   => [
			'recommended'       => __( 'Recommended for you', 'traveler' ),
			'picks'             => __( 'Editor&rsquo;s Picks', 'traveler' ),
			'description'       => __( 'We are Marriott Bonvoy Traveler. We believe that all travel is good travel. We tell the stories that awaken the wanderlust that lives in you; the stories that inspire you to explore the world’s greatest destinations and empower you to travel well. Good travel starts here.', 'traveler' ),
			'notFound'          => sprintf( __( 'We&rsquo;re sorry; it looks like you&rsquo;ve traveled astray. The page you&rsquo;re searching for no longer exists. Try a <a href="#" class="openSearchModal">new search</a> or keep exploring and visit the <a href="%s">Marriott Bonvoy Traveler homepage</a> for travel tips and inspiration.', 'traveler' ), site_url( '/', 'https' ) ),
			'tos'               => __( 'Terms of Use', 'traveler' ),
			'privacy'           => __( 'Privacy Policy', 'traveler' ),
			'contact'           => __( 'Contact Us', 'traveler' ),
			'about'             => __( 'Learn more about us', 'traveler' ),
			'bookRoom'          => __( 'Book a Room', 'traveler' ),
			'signin'            => __( 'Sign In / Join', 'traveler' ),
			'magazines'         => __( 'Other Magazines', 'traveler' ),
			'sponsored'         => __( 'Sponsored', 'traveler' ),
			'byline'            => __( 'By', 'traveler' ),
			'articlesBy'        => __( 'Articles by ', 'traveler' ),
			'articlesTagged'    => __( 'Articles Tagged with', 'traveler' ),
			'follow'            => __( 'Follow us:', 'traveler' ),
			'broughtBy'         => __( 'Brought to you by', 'traveler' ),
			'presentedBy'       => __( 'Presented by', 'traveler' ),
			'more'              => __( 'More', 'traveler' ),
			'moreIn'            => __( 'More In ', 'traveler' ),
			'moreStories'       => __( 'More ', 'traveler' ),
			'stories'           => __( ' Stories', 'traveler' ),
			'morePlaces'        => __( 'More Places', 'traveler' ),
			'tags'              => __( 'Article Tags:', 'traveler' ),
			'next'              => __( 'Next Article', 'traveler' ),
			'thingsToDo'        => __( 'More Things to Do', 'traveler' ),
			'whereTo'           => __( 'Where to Stay', 'traveler' ),
			'whereToStay'       => __( 'Where to stay in', 'traveler' ),
			'relatedArticles'   => __( 'Related Articles', 'traveler' ),
			'readMore'          => __( 'Read More', 'traveler' ),
			'loadMore'          => __( 'Loading More Stories&hellip;', 'traveler' ),
			'video'             => __( 'Video', 'traveler' ),
			'searchPlaceholder' => __( 'Search', 'traveler' ),
			'searchResults'     => __( 'You searched for', 'traveler' ),
			'dates'             => __( 'Dates', 'traveler' ),
			'destination'       => __( 'Destination', 'traveler' ),
			'placeholder'       => __( 'Where to?', 'traveler' ),
			'checkInOut'        => __( 'Check-in  -  Check-out', 'traveler' ),
			'findHotels'        => __( 'Find Hotels', 'traveler' )
		]
	];

}

add_action( 'amp_post_template_css', 'Traveler\V2\Theme\Http\amp_styles' );

function amp_styles() {
	if ( ! is_amp() ) {
		return;
	}
	echo file_get_contents( get_template_directory() . '/amp/public/css/amp.css' );
}

/**
 * Registers theme script files.
 *
 * @return void
 */
add_action( 'wp_enqueue_scripts', 'Traveler\V2\Theme\Http\register_scripts' );

function register_scripts() {


	$localize_strings = localize_strings();

	// Note: using  home_url() instead of admin_url() for ajaxurl to be sure  to get same domain on wpcom when using mapped domains (also works on self-hosted)
	// Also: not hard coding path since there is no guarantee site is running on site root in self-hosted context.
	$is_logged_in         = is_user_logged_in();
	$current_user         = wp_get_current_user();
	$comment_registration = intval( get_option( 'comment_registration' ) );
	$require_name_email   = intval( get_option( 'require_name_email' ) );
	$tp_localize_strings  = [
		'widths'       => [
			370,
			700,
			1000,
			1200,
			1400,
			2000
		],
		'is_logged_in' => $is_logged_in,
		'lang'         => strtolower( substr( get_locale(), 0, 2 ) ),
		'ajaxurl'      => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
		'nonce'        => wp_create_nonce( 'carousel_nonce' ),
		'display_exif' => get_option( 'carousel_display_exif' ),

		'display_geo'      => get_option( 'carousel_display_geo' ),
		'display_comments' => get_option( 'comments_display' ),
		'fullsize_display' => get_option( 'fullsize_display' ),

		'background_color' => get_option( 'carousel_background_color' ),
		'comment'          => __( 'Comment', 'themepacific_gallery' ),
		'post_comment'     => __( 'Post Comment', 'themepacific_gallery' ),
		'loading_comments' => __( 'Loading Comments...', 'themepacific_gallery' ),

		'download_original' => sprintf( __( 'View full size <span class="photo-size">%1$s<span class="photo-size-times">&times;</span>%2$s</span>', 'themepacific_gallery' ), '{0}', '{1}' ),

		'no_comment_text'      => __( 'Please be sure to submit some text with your comment.', 'themepacific_gallery' ),
		'no_comment_email'     => __( 'Please provide an email address to comment.', 'themepacific_gallery' ),
		'no_comment_author'    => __( 'Please provide your name to comment.', 'themepacific_gallery' ),
		'comment_post_error'   => __( 'Sorry, but there was an error posting your comment. Please try again later.', 'themepacific_gallery' ),
		'comment_approved'     => __( 'Your comment was approved.', 'themepacific_gallery' ),
		'comment_unapproved'   => __( 'Your comment is in moderation.', 'themepacific_gallery' ),
		'camera'               => __( 'Camera', 'themepacific_gallery' ),
		'aperture'             => __( 'Aperture', 'themepacific_gallery' ),
		'shutter_speed'        => __( 'Shutter Speed', 'themepacific_gallery' ),
		'focal_length'         => __( 'Focal Length', 'themepacific_gallery' ),
		'comment_registration' => $comment_registration,
		'require_name_email'   => $require_name_email,
		'login_url'            => wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ),
	];

	if ( ! isset( $tp_localize_strings['jetpack_comments_iframe_src'] ) || empty( $tp_localize_strings['jetpack_comments_iframe_src'] ) ) {
		// We're not using Jetpack comments after all, so fallback to standard local comments.
		if ( isset( $tp_localize_strings['display_comments'] ) ) {
			if ( $tp_localize_strings['display_comments'] ) {


				if ( $is_logged_in ) {
					$tp_localize_strings['local_comments_commenting_as'] = '<p id="jp-carousel-commenting-as">' . sprintf( __( 'Commenting as %s', 'themepacific_gallery' ), $current_user->data->display_name ) . '</p>';
				} else {
					if ( $comment_registration ) {
						$tp_localize_strings['local_comments_commenting_as'] = '<p id="jp-carousel-commenting-as">' . __( 'You must be <a href="#" class="jp-carousel-comment-login">logged in</a> to post a comment.', 'themepacific_gallery' ) . '</p>';
					} else {
						$required                                            = ( $require_name_email ) ? __( '%s (Required)', 'themepacific_gallery' ) : '%s';
						$tp_localize_strings['local_comments_commenting_as'] = ''
						                                                       . '<fieldset><label for="email">' . sprintf( $required, __( 'Email', 'themepacific_gallery' ) ) . '</label> '
						                                                       . '<input type="text" name="email" class="jp-carousel-comment-form-field jp-carousel-comment-form-text-field" id="jp-carousel-comment-form-email-field" /></fieldset>'
						                                                       . '<fieldset><label for="author">' . sprintf( $required, __( 'Name', 'themepacific_gallery' ) ) . '</label> '
						                                                       . '<input type="text" name="author" class="jp-carousel-comment-form-field jp-carousel-comment-form-text-field" id="jp-carousel-comment-form-author-field" /></fieldset>'
						                                                       . '<fieldset><label for="url">' . __( 'Website', 'themepacific_gallery' ) . '</label> '
						                                                       . '<input type="text" name="url" class="jp-carousel-comment-form-field jp-carousel-comment-form-text-field" id="jp-carousel-comment-form-url-field" /></fieldset>';
					}
				}
			} else {
				$tp_localize_strings['loading_comments'] = '';
				$tp_localize_strings['comment']          = '';
			}
		}
	}

	register_modern_mix_manifest_assets();
	register_legacy_mix_manifest_assets();

	//wp_enqueue_script( 'traveler/js/manifest', asset_replace( 'js/manifest.js', true ), [], null, true );

	wp_localize_script( 'traveler/js/manifest', 'siteData', $localize_strings );


	if ( false === ( $menu = wp_cache_get( 'traveler_menu', 'traveler-menus-' . get_current_blog_id() ) ) ) :

		$menu      = [ 'plain' => '' ];
		$nav_menus = get_registered_nav_menus();

		foreach ( $nav_menus as $nav_menu => $_menu ) {

			$menu_request  = new WP_REST_Request( 'GET', '/wp-api-menus/v2/menu-locations/' . $nav_menu );
			$menu_server   = rest_get_server();
			$menu_response = $menu_server->dispatch( $menu_request );

			if ( 200 === $menu_response->status ) {

				$menu[ $nav_menu ] = $menu_server->response_to_data( $menu_response, false );
			}

		}

		wp_cache_set( 'traveler_menu', $menu, 'traveler-menus-' . get_current_blog_id(), DAY_IN_SECONDS );

		unset( $menu_request, $menu_response );
	endif;

	wp_localize_script( 'traveler/js/manifest', 'menu', $menu );


	if ( false === ( $magazines = wp_cache_get( 'traveler_magazines', 'traveler-magazines-' . get_current_blog_id() ) ) ) :

		if ( get_field( 'site', 'option' ) ) : while ( has_sub_field( 'site', 'option' ) ) :
			$magazines[] = [
				'url'  => trailingslashit( get_sub_field( 'url' ) ),
				'name' => get_sub_field( 'name' ),
				'src'  => get_sub_field( 'logo' )
			];
		endwhile; endif;

		wp_cache_set( 'traveler_magazines', $magazines, 'traveler-magazines-' . get_current_blog_id(), DAY_IN_SECONDS );

	endif;

	wp_localize_script( 'traveler/js/manifest', 'magazines', $magazines );


	/**
	 * @todo migrate to a way it's only called on homepage
	 */
	if ( 1 === $localize_strings['site_id'] && ( is_front_page() || is_page( 'podcasts' ) ) ) {

		//Podcasts
		if ( ! $podcasts = get_site_transient( 'podcast_index' ) ) {

			if ( class_exists( 'Traveler\Cron\TravelerCronTasks' ) ) {

				$podcasts = TravelerCronTasks::do_podcasts();
			}
		}

		wp_localize_script( 'traveler/js/manifest', 'podcasts', $podcasts );
	}


	if ( is_singular( 'post' ) ) {

		$post_id = get_the_ID();

		$post_request  = new WP_REST_Request( 'GET', '/wp/v2/posts/' . $post_id );
		$post_server   = rest_get_server();
		$post_response = $post_server->dispatch( $post_request );

		if ( 200 === $post_response->status ) {

			$rest_posts[] = $post_server->response_to_data( $post_response, false );

			wp_localize_script( 'traveler/js/manifest', 'posts', $rest_posts );
		}
		unset( $post_request, $post_response );

		$_terms   = get_the_terms( $post_id, 'category' );
		$term_ids = wp_list_pluck( (array) $_terms, 'term_id' );

		if ( $term_ids ) :

			foreach ( $term_ids as $term_id ) {

				localize_ads( $term_id );
			}
		endif;

	} else {

		localize_ads();
	}

}

function register_modern_mix_manifest_assets() {
	register_mix_manifest_assets( get_modern_generated_assets(), true );
}

function register_legacy_mix_manifest_assets() {
	register_mix_manifest_assets( get_legacy_generated_assets(), false );
}

function register_mix_manifest_assets( $assets, $is_modern ) {
	foreach ( $assets as $key => $asset ) {

		$handle = 'traveler/' . $key . ( $is_modern ? '' : '/legacy' );
		//$handle = 'traveler/' . $key;

		if ( 'js/manifest.js' === $key ) {
			//continue;
			$handle = preg_replace( '/\.js/uim', '', $handle );
		}

		if ( false !== strpos( $handle, 'css/' ) ) {
			if ( $is_modern ) {
				wp_enqueue_style( $handle, asset_path( $asset ), null, null, 'all' );
			}
		} else {
			wp_enqueue_script( $handle, asset_path( $asset ), null, null, true );
			if ( $is_modern ) {
				wp_scripts()->add_data( $handle, 'defer', true );
				wp_scripts()->add_data( $handle, 'module', true );
			} else {
				//wp_scripts()->add_data( $handle, 'defer', true );
				wp_scripts()->add_data( $handle, 'nomodule', true );
			}
		}

	}
}


/**
 * Moves front-end jQuery script to the footer.
 *
 * @param WP_Scripts $wp_scripts
 *
 * @return void
 */
add_action( 'wp_default_scripts', 'Traveler\V2\Theme\Http\move_jquery_to_the_footer' );

function move_jquery_to_the_footer( $wp_scripts ) {

	if ( ! is_admin() ) {

		$wp_scripts->add_data( 'jquery', 'group', 1 );
		$wp_scripts->add_data( 'jquery-core', 'group', 1 );
		//$wp_scripts->add_data( 'jquery-migrate', 'group', 1 );
	}
}


function localize_ads( $term_id = 0 ) {

	$ad300 = 'https://ag.yieldoptimizer.com/ag/pt?pt=ivb3m2sjne&t=f&cb=';
	$ad728 = 'https://ag.yieldoptimizer.com/ag/pt?pt=x5mocicye3&t=f&cb=';

	if ( is_numeric( $_ad728 = get_field( 'desktop_banner', 'option' ) ) ) {
		$ad728 = home_url( '/ads/single/' . $_ad728 . '?cb=' );
	}

	if ( is_numeric( $_ad300 = get_field( 'mobile_banner', 'option' ) ) ) {
		$ad300 = home_url( '/ads/single/' . $_ad300 . '?cb=' );
	}

	if ( is_post_type_archive( 'storybooked' ) ) {
		$ad728 = home_url( '/ads/single/78/?cb=' );
	}

	if ( wp_is_mobile() ) {
		$ad728 = $ad300;
	}

	$roots_js_localized = [
		'ad728'     => '',
		'ad300'     => '',
		'adMobile'  => $ad300,
		'adDesktop' => $ad728
	];

	wp_localize_script( 'traveler/js/manifest', 'advertising', $roots_js_localized );

}


function asset_replace( $filename, $is_modern ) {

	$manifest = get_generated_assets( $is_modern );

	if ( array_key_exists( $filename, $manifest ) ) {
		$filename = $manifest[ $filename ];
	}

	return asset_path( $filename );
}


function get_modern_generated_assets() {
	return get_generated_assets( true );
}


function get_legacy_generated_assets() {
	return get_generated_assets( false );
}


function get_generated_assets( $is_modern ) {

	$manifest_path = asset( $is_modern ? 'mix-manifest.json' : 'mix-manifest~legacy.json' )->getPath();
	$manifest      = new JSONManifest( $manifest_path );

	return $manifest->get();
}

add_action( 'wp_enqueue_scripts', 'Traveler\V2\Theme\Http\deregister_front_end_scripts', 999 );


/**
 * Deregister front end scripts
 *
 * @return void
 */
function deregister_front_end_scripts() {
	if ( ! is_admin() ) {
		wp_dequeue_script( 'backbone' );
		wp_dequeue_script( 'jquery' );
		wp_dequeue_script( 'jquery-migrate' );
		wp_dequeue_script( 'mediaelement' );
		wp_dequeue_script( 'underscore' );
//		wp_deregister_script( 'wp-embed' );
		wp_dequeue_script( 'wp-util' );
	}
}

add_action( 'wp_print_styles', 'Traveler\V2\Theme\Http\deregister_front_end_styles', 100 );


/**
 * Deregister front end styles
 *
 * @return void
 */
function deregister_front_end_styles() {
	if ( ! is_admin() ) {
//		wp_dequeue_style( 'wp-components' );
//		wp_dequeue_style( 'wp-block-editor');
//		wp_dequeue_style( 'wp-editor');
//		wp_dequeue_style( 'wp-nux');
		wp_dequeue_style( 'wp-editor-font' );
//		wp_dequeue_style( 'wp-block-library' );
	}
}

add_action( 'wp_enqueue_scripts', 'Traveler\V2\Theme\Http\old_browsers_support' );

function old_browsers_support() {
	wp_add_inline_script( 'traveler/js/manifest', 'document.createElement(\'main\');' );
}
