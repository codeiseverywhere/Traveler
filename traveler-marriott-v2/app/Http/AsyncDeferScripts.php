<?php


namespace Traveler\V2\Theme\Http;


if ( ! function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}


/**
 * Class DeferScripts
 * @package wpscholar\WordPress
 */
class AsyncDeferScripts {

    /**
     * Instance of this class.
     *
     * @var object
     */
    public static $instance;

    function __construct() {

        add_filter( 'script_loader_tag', [
            $this,
            'deferScripts'
        ], 10, 2 );
        add_filter( 'script_loader_tag', [
            $this,
            'asyncScripts'
        ], 10, 2 );
    }

    /**
     * Get the singleton instance of this class.
     *
     * @return AsyncDeferScripts
     */
    public static function get_instance() {
        if ( ! ( self::$instance instanceof self ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function deferScripts( $tag, $handle ) {
        if ( wp_scripts()->get_data( $handle, 'defer' ) ) {
            $tag = str_replace( '></', ' defer></', $tag );
        }

        return $tag;
    }

    public static function asyncScripts( $tag, $handle ) {
        if ( wp_scripts()->get_data( $handle, 'async' ) ) {
            $tag = str_replace( '></', ' async></', $tag );
        }

        return $tag;
    }

}


add_action( 'init', 'Traveler\V2\Theme\Http\initialize_async_defer_scripts' );

function initialize_async_defer_scripts() {
    AsyncDeferScripts::get_instance();
}
