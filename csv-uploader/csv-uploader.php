<?php
/**
 * Plugin Name:       CSV Uploader
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       This is my Csv Uploader File Plugin Which i can use in any wordpress website.
 * Version:           1.10.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rabia Gull
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       csv-uploader
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* Plugin path constant */
define( 'CSV_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/* Shortcode */
add_shortcode( 'csv-data-uploader', 'csv_handle_uploader_form' );

function csv_handle_uploader_form() {
    ob_start();
    include CSV_PLUGIN_DIR_PATH . 'template/csv_form.php';
    return ob_get_clean();
}

/* Plugin activation: create DB table */
register_activation_hook( __FILE__, 'csv_create_table' );

function csv_create_table() {
    global $wpdb;

    $table_name      = $wpdb->prefix . 'students_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(50) DEFAULT NULL,
        email VARCHAR(50) DEFAULT NULL,
        age INT(5) DEFAULT NULL,
        phone VARCHAR(30) DEFAULT NULL,
        photo VARCHAR(120) DEFAULT NULL,
        PRIMARY KEY (id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

/* Enqueue scripts */
add_action( 'wp_enqueue_scripts', 'csv_add_script_file' );

function csv_add_script_file() {
    wp_enqueue_script(
        'csv-script-js',
        plugin_dir_url( __FILE__ ) . 'assets/script.js',
        array( 'jquery' ),
        null,
        true
    );

    wp_localize_script(
        'csv-script-js',
        'csv_object',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        )
    );
}

add_action( 'wp_ajax_csv_ajax_handler', 'csv_ajax_handler' );
add_action( 'wp_ajax_nopriv_csv_ajax_handler', 'csv_ajax_handler' );



function csv_ajax_handler() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'students_data';

    /* Security check */
    if ( ! isset( $_FILES['csv_data_file'] ) ) {
        wp_send_json_error( array(
            'message' => 'CSV file not found.'
        ) );
    }

    $file = $_FILES['csv_data_file'];

    /* Validate file type */
    $file_ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
    if ( $file_ext !== 'csv' ) {
        wp_send_json_error( array(
            'message' => 'Invalid file type. Please upload a CSV file.'
        ) );
    }

    /* Open CSV */
    $handle = fopen( $file['tmp_name'], 'r' );
    if ( ! $handle ) {
        wp_send_json_error( array(
            'message' => 'Unable to read CSV file.'
        ) );
    }

    /* Skip header row */
    fgetcsv( $handle );

    $inserted = 0;

    while ( ( $row = fgetcsv( $handle, 1000, ',' ) ) !== false ) {

        /* Sanitize CSV data */
        $name  = sanitize_text_field( $row[0] ?? '' );
        $email = sanitize_email( $row[1] ?? '' );
        $age   = intval( $row[2] ?? 0 );
        $phone = sanitize_text_field( $row[3] ?? '' );
        $photo = sanitize_text_field( $row[4] ?? '' );

        /* Insert into DB */
        $wpdb->insert(
            $table_name,
            array(
                'name'  => $name,
                'email' => $email,
                'age'   => $age,
                'phone' => $phone,
                'photo' => $photo,
            ),
            array( '%s', '%s', '%d', '%s', '%s' )
        );

        if ( $wpdb->insert_id ) {
            $inserted++;
        }
    }

    fclose( $handle );

    wp_send_json_success( array(
        'message'  => 'CSV uploaded successfully.',
        'inserted' => $inserted
    ) );

    wp_die();
}