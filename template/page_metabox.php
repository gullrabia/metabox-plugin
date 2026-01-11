<?php
if ( ! defined('ABSPATH') ) {
    exit;
}

$post_id = $post->ID;

$title       = get_post_meta($post_id, 'pmeta_title', true);
$description = get_post_meta($post_id, 'pmeta_description', true);

// Nonce
wp_nonce_field('my_save_metabox_data', 'my_save_pmetabox_nonce');
?>

<p>
    <label for="pmeta_title"><strong>Meta Title</strong></label><br>
    <input
        type="text"
        name="pmeta_title"
        id="pmeta_title"
        value="<?php echo esc_attr($title); ?>"
        style="width:100%;"
        placeholder="Meta Title..."
    >
</p>

<p>
    <label for="pmeta_description"><strong>Meta Description</strong></label><br>
    <textarea
        name="pmeta_description"
        id="pmeta_description"
        rows="4"
        style="width:100%;"
        placeholder="Meta Description..."
    ><?php echo esc_textarea($description); ?></textarea>
</p>
