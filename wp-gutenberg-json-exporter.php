<?php
/**
 * Plugin Name: WP Gutenberg JSON Exporter
 * Description: Exposes REST API endpoints to retrieve post content as parsed Gutenberg block JSON, with optional ACF and Yoast SEO metadata.
 * Version: 1.1
 * Author: Temi Kolawole
 * License: GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', function () {
    register_rest_route( 'gutenberg-json/v1', '/post/(?P<id>\\d+)', array(
        'methods' => 'GET',
        'callback' => 'wpgje_get_block_json_by_id',
        'permission_callback' => '__return_true'
    ));

    register_rest_route( 'gutenberg-json/v1', '/slug/(?P<slug>[a-zA-Z0-9-_]+)', array(
        'methods' => 'GET',
        'callback' => 'wpgje_get_block_json_by_slug',
        'permission_callback' => '__return_true'
    ));
});

function wpgje_get_block_json_by_id( $data ) {
    $post = get_post( $data['id'] );
    return wpgje_format_post_response( $post );
}

function wpgje_get_block_json_by_slug( $data ) {
    $posts = get_posts(array(
        'name' => $data['slug'],
        'post_type' => 'any',
        'post_status' => 'publish',
        'numberposts' => 1,
    ));

    $post = !empty($posts) ? $posts[0] : null;
    return wpgje_format_post_response( $post );
}

function wpgje_format_post_response( $post ) {
    if ( ! $post || $post->post_status !== 'publish' ) {
        return new WP_REST_Response( array( 'error' => 'Post not found.' ), 404 );
    }

    $response = array(
        'id' => $post->ID,
        'slug' => $post->post_name,
        'title' => get_the_title( $post ),
        'blocks' => has_blocks( $post->post_content ) ? parse_blocks( $post->post_content ) : [],
        'raw_content' => $post->post_content,
    );

    // Include ACF fields if available
    if ( function_exists( 'get_fields' ) ) {
        $acf = get_fields( $post->ID );
        if ( $acf ) {
            $response['acf'] = $acf;
        }
    }

    // Include Yoast SEO data if available
    if ( defined( 'WPSEO_VERSION' ) && function_exists( 'wpseo_replace_vars' ) ) {
        $yoast_meta = array(
            'seo_title'       => get_post_meta( $post->ID, '_yoast_wpseo_title', true ),
            'seo_description' => get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true ),
            'focus_keyword'   => get_post_meta( $post->ID, '_yoast_wpseo_focuskw', true ),
        );
        $response['yoast'] = $yoast_meta;
    }

    return new WP_REST_Response( $response, 200 );
}
