<?php

namespace Traveler\V2\Theme\Admin;

use function is_array;

/**
 * Output menu links.
 */
function nav_menu_links() { ?>
    <div id="traveler-city-endpoints" class="posttypediv">
        <div id="tabs-panel-traveler-city-endpoints" class="tabs-panel tabs-panel-active">
            <ul id="traveler-city-endpoints-checklist" class="categorychecklist form-no-clear">
                <?php
                $city_parent = 1 === get_current_blog_id() ? 3 : 821;

                $i    = - 1;
                $args = [
                    'hide_empty' => false,
                    'taxonomy'   => 'category',
                    'parent'     => $city_parent,
                    'meta_query' => [
                        [
                            'key'     => 'ba_city_status',
                            'value'   => 'status-live',
                            'compare' => '==',
                        ]
                    ]
                ];

                $cities = get_terms( $args );
                foreach ( $cities as $city ) :
                    ?>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox"
                                   name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]"
                                   value="<?php echo esc_attr( $i ); ?>"/>
                            <?php echo esc_html( $city->name ); ?>
                        </label>
                        <input type="hidden" class="menu-item-type"
                               name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom"/>
                        <input type="hidden" class="menu-item-title"
                               name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]"
                               value="<?php echo esc_html( $city->name ); ?>"/>
                        <input type="hidden" class="menu-item-url"
                               name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]"
                               value="<?php echo esc_url( get_term_link( $city ) ); ?>"/>
                        <input type="hidden" class="menu-item-classes"
                               name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]"/>
                    </li>
                    <?php
                    $i --;
                endforeach;
                ?>
            </ul>
        </div>
        <p class="button-controls">
            <span class="list-controls">
                <a href="<?php echo esc_url( admin_url( 'nav-menus.php?page-tab=all&selectall=1#traveler-city-endpoints' ) ); ?>"
                   class="select-all"><?php esc_html_e( 'Select all', 'traveler' ); ?></a>
            </span>
            <span class="add-to-menu">
                <button type="submit" class="button-secondary submit-add-to-menu right"
                        value="<?php esc_attr_e( 'Add to menu', 'traveler' ); ?>" name="add-post-type-menu-item"
                        id="submit-traveler-city-endpoints"><?php esc_html_e( 'Add to menu', 'traveler' ); ?></button>
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}

/**
 * Add custom nav meta box.
 *
 * Adapted from http://www.johnmorrisonline.com/how-to-add-a-fully-functional-custom-meta-box-to-wordpress-navigation-menus/.
 */
add_action( 'admin_head-nav-menus.php', 'Traveler\V2\Theme\Admin\add_nav_menu_meta_boxes' );
function add_nav_menu_meta_boxes() {
    add_meta_box( 'traveler_city_nav_link', __( 'Cities', 'traveler' ), 'Traveler\V2\Theme\Admin\nav_menu_links', 'nav-menus', 'side', 'low' );
}

/**
 * Removes default widgets.
 */
//add_action( 'wp_dashboard_setup', 'Traveler\V2\Theme\Admin\remove_dashboard_widgets' );
function remove_dashboard_widgets() {

    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );      //WordPress.com Blog
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );      //Other WordPress News
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );    //Incoming Links

}

/**
 * Remove WP-Admin cleanup
 */
add_action( 'admin_init', 'Traveler\V2\Theme\Admin\admin_cleanup' );
function admin_cleanup() {
    remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );

    if ( is_user_logged_in() ) :
        $current_user_id     = get_current_user_id();
        $adrotate_pointer    = (string) 'adrotate_pro';
        $amp_pointer         = (string) 'ampforwp_subscribe_pointer';
        $dismissed           = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );

        if ( is_array( $dismissed ) && ! in_array( $amp_pointer, $dismissed ) ) {
            $dismissed[] = $amp_pointer;
            $dismissed   = implode( ',', $dismissed );

            update_user_meta( $current_user_id, 'dismissed_wp_pointers', $dismissed );
        }


        if ( is_array( $dismissed ) && ! in_array( $adrotate_pointer, $dismissed ) ) {
            $dismissed[] = $adrotate_pointer;
            $dismissed   = implode( ',', $dismissed );

            update_user_meta( $current_user_id, 'dismissed_wp_pointers', $dismissed );
        }
    endif;
}

/**
 * Add query argument for selecting pages to add to a menu
 */
add_filter( 'nav_menu_meta_box_object', 'Traveler\V2\Theme\Admin\menu_pages_status' );
function menu_pages_status( $args ) {
    if ( $args->name == 'page' || $args->name == 'taste-of' ) {
        $args->_default_query['post_status'] = [
            'publish',
            'private',
            'draft',
        ];
    }

    return $args;
}

add_action( 'admin_menu', 'Traveler\V2\Theme\Admin\remove_menu_pages' );
function remove_menu_pages() {
    remove_menu_page( 'themepacific_jp_gallery' );
}
