<?php


namespace Traveler\V2\Theme\Http;


if ( ! function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}


/**
 * Class ModuleNoModuleScripts
 * @package wpscholar\WordPress
 */
class ModuleNoModuleScripts {

    /**
     * Instance of this class.
     *
     * @var object
     */
    public static $instance;

    function __construct() {

        add_filter( 'script_loader_tag', [
            $this,
            'moduleScripts'
        ], 10, 2 );
        add_filter( 'script_loader_tag', [
            $this,
            'noModuleScripts'
        ], 10, 2 );
    }

    /**
     * Get the singleton instance of this class.
     *
     * @return ModuleNoModuleScripts
     */
    public static function get_instance() {
        if ( ! ( self::$instance instanceof self ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function moduleScripts( $tag, $handle ) {
        if ( wp_scripts()->get_data( $handle, 'module' ) ) {
            $tag = str_replace( 'type=\'text/javascript\'', 'type=\'module\'', $tag );
        }

        return $tag;
    }

    public static function noModuleScripts( $tag, $handle ) {
        if ( wp_scripts()->get_data( $handle, 'nomodule' ) ) {
            $tag = str_replace( '></', ' nomodule></', $tag );
        }

        return $tag;
    }

}


add_action( 'init', 'Traveler\V2\Theme\Http\initialize_module_no_module_scripts' );

function initialize_module_no_module_scripts() {
    ModuleNoModuleScripts::get_instance();
}
