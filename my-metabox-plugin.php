<?php
/**
 * Plugin Name: My MetaBox
 * Description: Custom SEO MetaBox for WordPress Pages & Posts
 * Version: 1.0
 * Author: Rabia Gull
 * Text Domain: my-metabox
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Register Meta Box
 */
add_action('add_meta_boxes', 'my_register_page_metabox');
function my_register_page_metabox() {

    add_meta_box(
        'my_metabox_id',
        'My MetaBox – SEO',
        'my_create_page_metabox',
        array('post', 'page'),
        'normal',
        'default'
    );
}

/**
 * Meta Box HTML
 */
function my_create_page_metabox($post) {
    include plugin_dir_path(__FILE__) . 'template/page_metabox.php';
}

/**
 * Save Meta Box Data
 */
add_action('save_post', 'my_save_metabox_data');
function my_save_metabox_data($post_id) {

    // Nonce check
    if ( ! isset($_POST['my_save_pmetabox_nonce']) ) {
        return;
    }

    if ( ! wp_verify_nonce($_POST['my_save_pmetabox_nonce'], 'my_save_metabox_data') ) {
        return;
    }

    // Autosave check
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    // Permission check
    if ( ! current_user_can('edit_post', $post_id) ) {
        return;
    }

    // Save Meta Title
    if ( isset($_POST['pmeta_title']) ) {
        update_post_meta(
            $post_id,
            'pmeta_title',
            sanitize_text_field($_POST['pmeta_title'])
        );
    }

    // Save Meta Description
    if ( isset($_POST['pmeta_description']) ) {
        update_post_meta(
            $post_id,
            'pmeta_description',
            sanitize_textarea_field($_POST['pmeta_description'])
        );
    }
}

/**
 * Output Meta Tags in Head
 */
add_action('wp_head', 'my_add_head_meta_tags');
function my_add_head_meta_tags() {

    if ( is_singular(array('post','page')) ) {

        global $post;
        $post_id = $post->ID;

        $title = get_post_meta($post_id, 'pmeta_title', true);
        $description = get_post_meta($post_id, 'pmeta_description', true);

        if ( ! empty($title) ) {
            echo '<meta name="title" content="' . esc_attr($title) . '">' . "\n";
        }

        if ( ! empty($description) ) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
    }
}
