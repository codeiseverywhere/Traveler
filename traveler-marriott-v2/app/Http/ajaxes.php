<?php

namespace Traveler\V2\Theme\Http;

/*
|-----------------------------------------------------------
| Theme AJAX Actions
|-----------------------------------------------------------
|
| This file is for registering your theme AJAX actions,
| which can be hit with HTTP requests in order to make
| smooth and dynamic JavaScript components.
|
*/

/**
 * My AJAX action callback.
 *
 * @return void
 */
function action_callback()
{
    // Validate nonce token.
    check_ajax_referer('my_action_nonce', 'nonce');

    // Action logic...

    die();
}
add_action('wp_ajax_my_action', 'Traveler\V2\Theme\Http\action_callback');
add_action('wp_ajax_nopriv_my_action', 'Traveler\V2\Theme\Http\action_callback');


/**
 * newsletter_session set a cookie via ajax that will prevent the newsletter module to show
 * @var int $expire set with 0 so it will expire after the session expires
 */
add_action( 'wp_ajax_nopriv_traveler_newsletter', 'Traveler\V2\Theme\Http\newsletter_session' );
add_action( 'wp_ajax_traveler_newsletter', 'Traveler\V2\Theme\Http\newsletter_session' );

function newsletter_session() {
    if ( ! isset( $_COOKIE['traveler_newsletter'] ) ) {
        $expire = 0;
        setcookie( 'traveler_newsletter', 'hidden', $expire, COOKIEPATH, COOKIE_DOMAIN );
    }
    echo 'hidden';
    die();
}
