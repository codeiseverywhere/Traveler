<?php

namespace Traveler\V2\Theme\Plugins\WordPressSEO;

use const WEEK_IN_SECONDS;
use function get_current_blog_id;

/**
 * Cleanup, removes rel="prev" and rel="next" links from header
 */

add_filter( 'wpseo_prev_rel_link', '__return_empty_string' );
add_filter( 'wpseo_next_rel_link', '__return_empty_string' );

/**
 * Limit the number of sitemap entries for Yoast SEO
 * Credit: Yoast Developers
 * Last Tested: Jan 31 2019 using Yoast SEO 9.5 on WordPress 5.0.3
 * Yoast SEO defaults to 1000
 * Google allows up to 50000 URLs or 50MB (uncompressed)
 */
add_filter( 'wpseo_sitemap_entries_per_page', 'Traveler\V2\Theme\Plugins\WordPressSEO\max_entries_per_sitemap' );

function max_entries_per_sitemap() {

    return 100;
}


/**
 * Adds hreflang tag to the head of Traveler EN
 */
add_action( 'wpseo_head', 'Traveler\V2\Theme\Plugins\WordPressSEO\hreflang' );

function hreflang() {

    global $blog_id, $post;

    if ( false === ( $output = wp_cache_get( 'traveler_hreflang_' . $post->ID, 'traveler-postdata-' . $blog_id ) ) ) :
    //if ( false === ( $output = get_site_transient( 'traveler_hreflang_' . $post->ID ) ) ) :

        $origin_id = absint( 1 );
        $output    = '';
        $posts     = get_field( 'translated_post' );
        $lang      = $origin_id === $blog_id ? 'es' : 'en';

        if ( $posts ) :

            switch_to_blog( $posts['site_id'] );
            foreach ( $posts['selected_posts'] as $post ) :
                setup_postdata( $post );
                $output = '<link rel="alternate" hreflang="' . $lang . '" href="' . get_permalink() . '" />' . "\n";
                wp_reset_postdata();
            endforeach;
            restore_current_blog();
        endif;

        wp_cache_set( 'traveler_hreflang_' . $post->ID, $output, 'traveler-postdata-' . $blog_id, WEEK_IN_SECONDS );
        //set_site_transient( 'traveler_hreflang_' . $post->ID, $output, WEEK_IN_SECONDS );

    endif;

    echo $output;
}


add_filter( 'wpseo_sitemap_entry', 'Traveler\V2\Theme\Plugins\WordPressSEO\exclude_from_sitemap', 10, 3 );

function exclude_from_sitemap( $url, $type, $term ) {

    if ( ! is_wp_error( $term ) && 'term' === $type ) :

        $parent = isset( $term->parent ) ? $term->parent : '';

        if ( ! empty( $parent ) && ( 3 === $parent || 821 === $parent ) && 'status-live' !== get_term_meta( $term->term_id, 'ba_city_status', true ) ) {

            $url = '';
        }

    endif;

    return $url;
}


add_filter( 'wpseo_json_ld_search_url', 'Traveler\V2\Theme\Plugins\WordPressSEO\search_url' );

function search_url() {

    return network_site_url( '/search/' ) . '?s={search_term_string}';
}
