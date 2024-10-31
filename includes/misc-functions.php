<?php
/**
 * Misc Functions
 *
 * @package     MYC
 * @subpackage  Functions
 * @copyright   Copyright (c) 2017, Daniel Powney
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if (! defined ( 'ABSPATH' ))
	exit ();

/**
 * Helper function which returns current page type
 */
function myc_get_current_page_type() {
    global $wp_query;
    $page_type = null;

    if ( $wp_query->is_page ) {
        $page_type = is_front_page() ? 'front' : 'page';
    } elseif ( $wp_query->is_home ) {
        $page_type = 'home';
    } elseif ( $wp_query->is_single ) {
        $page_type = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
    } elseif ( $wp_query->is_category ) {
        $page_type = 'category';
    } elseif ( $wp_query->is_tag ) {
        $page_type = 'tag';
    } elseif ( $wp_query->is_tax ) {
        $page_type = 'taxonomy';
    } elseif ( $wp_query->is_archive ) {
        if ( $wp_query->is_day ) {
            $page_type = 'archive';
        } elseif ( $wp_query->is_month ) {
            $page_type = 'archive';
        } elseif ( $wp_query->is_year ) {
            $page_type = 'archive';
        } elseif ( $wp_query->is_author ) {
            $page_type = 'author';
        } else {
            $page_type = 'archive';
        }
    } elseif ( $wp_query->is_search ) {
        $page_type = 'search';
    } elseif ( $wp_query->is_404 ) {
        $page_type = 'notfound';
    }

    return $page_type;
}

/**
 * Checks whether function is disabled.
 *
 * @param string  $function Name of the function.
 * @return bool Whether or not function is disabled.
 */
function myc_is_func_disabled( $function ) {
    $disabled = explode( ',',  ini_get( 'disable_functions' ) );

    return in_array( $function, $disabled );
}