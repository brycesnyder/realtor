<?php
/**
 * Class Houzez_Post_Type_Agency
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 28/09/16
 * Time: 10:16 PM
 * Since v1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Post_Type_Agency {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'definition' ) );
        add_filter( 'manage_edit-houzez_agency_columns', array( __CLASS__, 'custom_columns' ) );
        add_action( 'manage_houzez_agency_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    public static function definition() {
        $labels = array(
            'name'               => __( 'Agencies', 'houzez' ),
            'singular_name'      => __( 'Agency', 'houzez' ),
            'add_new'            => __( 'Add New Agency', 'houzez' ),
            'add_new_item'       => __( 'Add New Agency', 'houzez' ),
            'edit_item'          => __( 'Edit Agency', 'houzez' ),
            'new_item'           => __( 'New Agency', 'houzez' ),
            'all_items'          => __( 'Agencies', 'houzez' ),
            'view_item'          => __( 'View Agency', 'houzez' ),
            'search_items'       => __( 'Search Agency', 'houzez' ),
            'not_found'          => __( 'No agencies found', 'houzez' ),
            'not_found_in_trash' => __( 'No agencies found in Trash', 'houzez' ),
            'parent_item_colon'  => '',
            'menu_name'          => __( 'Agencies', 'houzez' ),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'supports'        => array( 'title', 'editor', 'thumbnail' ),
            'public'          => true,
            'capability_type' => 'page',
            'show_ui'         => true,
            'menu_position' => 15,
            'has_archive'     => true,
            'rewrite'         => array( 'slug' => __( 'agencies', 'houzez' ) ),
            'categories'      => array(),
        );

        register_post_type('houzez_agency',$args);
    }

    /**
     * Custom admin columns for post type
     *
     * @access public
     * @return array
     */
    public static function custom_columns() {
        $fields = array(
            'cb' 				=> '<input type="checkbox" />',
            'title' 			=> esc_html__( 'Title', 'houzez' ),
            'license' 		    => esc_html__( 'License', 'houzez' ),
            'thumbnail' 		=> esc_html__( 'Thumbnail', 'houzez' ),
            'email'      		=> esc_html__( 'E-mail', 'houzez' ),
            'web'      		    => esc_html__( 'Web', 'houzez' ),
            'phone'      		=> esc_html__( 'Phone', 'houzez' ),
            'agents'         	=> esc_html__( 'Agents', 'houzez' ),
            'author' 			=> esc_html__( 'Author', 'houzez' ),
        );

        return $fields;
    }

    /**
     * Custom admin columns implementation
     *
     * @access public
     * @param string $column
     * @return array
     */
    public static function custom_columns_manage( $column ) {
        switch ( $column ) {
            case 'thumbnail':
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'thumbnail', array(
                        'class'     => 'attachment-thumbnail attachment-thumbnail-small',
                    ) );
                } else {
                    echo '-';
                }
                break;
            case 'license':
                $agency_licenses = get_post_meta( get_the_ID(),  'fave_agency_licenses', true );

                if ( ! empty( $agency_licenses ) ) {
                    echo esc_attr( $agency_licenses );
                } else {
                    echo '-';
                }
                break;
            case 'email':
                $email = get_post_meta( get_the_ID(),  'fave_agency_email', true );

                if ( ! empty( $email ) ) {
                    echo esc_attr( $email );
                } else {
                    echo '-';
                }
                break;
            case 'web':
                $web = get_post_meta( get_the_ID(), 'fave_agency_web', true );

                if ( ! empty( $web ) ) {
                    echo esc_attr( $web );
                } else {
                    echo '-';
                }
                break;
            case 'phone':
                $phone = get_post_meta( get_the_ID(), 'fave_agency_phone', true );

                if ( ! empty( $phone ) ) {
                    echo esc_attr( $phone );
                } else {
                    echo '-';
                }
                break;
            case 'agents':
                $agents_count = Houzez_Query::get_agency_agents( $post_id = get_the_ID() )->post_count;
                echo esc_attr( $agents_count );
                break;
        }
    }
}

Houzez_Post_Type_Agency::init();