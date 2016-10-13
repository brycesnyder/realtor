<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 07/01/16
 * Time: 6:41 PM
 */
if( !function_exists( 'houzez_partner_post_type' ) ){
    function houzez_partner_post_type(){
        $labels = array(
            'name' => __( 'Partners','houzez'),
            'singular_name' => __( 'Partner','houzez' ),
            'add_new' => __('Add New','houzez'),
            'add_new_item' => __('Add New Partner','houzez'),
            'edit_item' => __('Edit Partner','houzez'),
            'new_item' => __('New Partner','houzez'),
            'view_item' => __('View Partner','houzez'),
            'search_items' => __('Search Partner','houzez'),
            'not_found' =>  __('No Partner found','houzez'),
            'not_found_in_trash' => __('No Partner found in Trash','houzez'),
            'parent_item_colon' => ''
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'capability_type' => 'page',
            'hierarchical' => false,
            'menu_icon' => 'dashicons-groups',
            'menu_position' => 22,
            'supports' => array('title','page-attributes','thumbnail','revisions'),
            'rewrite' => array( 'slug' => __('partner', 'houzez') )
        );

        register_post_type('houzez_partner',$args);
    }
}
add_action( 'init', 'houzez_partner_post_type' );

?>