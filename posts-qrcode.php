<?php

/**
 * Plugin Name:       Posts QR COde
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.0
 * Author:            John Smith
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       posts-qrcode
 * Domain Path:       /languages
 */

  /* function wordcount_activation_hook(){}
 register_activation_hook( __FILE__, 'wordcount_activation_hook' );

 function wordcount_deactivation_hook(){}
 register_activation_hook( __FILE__, 'wordcount_deactivation_hook' ); */

 function postsqrcode_load_textdomain(){
    load_plugin_textdomain( 'posts-qrcode', false, dirname(__FILE__)."/languages" );
 }
 add_action( 'plugins_loaded', 'postsqrcode_load_textdomain' );

 function postsqrcode_display_qrcode($content){
    $current_post_id = get_the_ID();
    $current_post_type = get_post_type( $current_post_id );
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode( get_the_permalink( $current_post_id ) );

    // Post Type Check
    $exclude_post_types = apply_filters( 'pqrc_exclude_post_types', array() );
    if(in_array($current_post_type, $exclude_post_types)){
        return $content;
    }

    //QR code image size
    $height = get_option( 'pqrc_height' );
    $height = $height ? $height : 150;
    $width = get_option( 'pqrc_width' );
    $width = $width ? $width : 150;

    $qr_size = apply_filters( 'pqrc_image_size', "{$width}x{$height}" );

    $qr_img_src = sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $qr_size, $current_post_url );
    $content .= sprintf("<div class='post-qrcode'><img src='%s' alt='%s'></div>", $qr_img_src, $current_post_title);

    return $content;

 }
 add_action( 'the_content', 'postsqrcode_display_qrcode', 15 );

 function postsqrcode_settings_init(){
    add_settings_section( 'pqrc_section', __('QR Code Image Dimension','posts-qrcode'), 'pqrc_section_callbak', 'general' );

    add_settings_field( 'pqrc_height', __('QR Code Height','posts-qrcode'), 'pqrc_height_callbak', 'general', 'pqrc_section' );
    add_settings_field( 'pqrc_width', __('QR Code Width','posts-qrcode'), 'pqrc_width_callbak', 'general', 'pqrc_section', );
    add_settings_field( 'pqrc_country', __('Country','posts-qrcode'), 'pqrc_select_callbak', 'general', 'pqrc_section' );
    add_settings_field( 'pqrc_checkbox', __('Checkbox','posts-qrcode'), 'pqrc_checkbox_callbak', 'general', 'pqrc_section' );
    add_settings_field( 'pqrc_toggle', __( 'Toggle Field', 'posts-to-qrcode' ), 'pqrc_display_toggle_field', 'general', 'pqrc_section' );

    register_setting( 'general', 'pqrc_height', array('sanitize_callback' => 'esc_attr') );
    register_setting( 'general', 'pqrc_width', array('sanitize_callback' => 'esc_attr') );
    register_setting( 'general', 'pqrc_country', array('sanitize_callback' => 'esc_attr') );
    register_setting( 'general', 'pqrc_checkbox' );
    register_setting( 'general', 'pqrc_toggle' );
 }

 

 add_action( 'admin_init', 'postsqrcode_settings_init' );

 function pqrc_section_callbak(){
    echo "<p>".__('Settings for Posts QRCode', 'posts-qrcode')."</p>";
 }

 function pqrc_display_callbak($args){
    $option = get_option( $args[0] );
    printf('<input type="text" id="%s" name="%s" value="%s" />', $args[0], $args[0], $option );
 }

 function pqrc_select_callbak(){
    $option = get_option('pqrc_country');
    $countries = array(
        'None',
        'Afganistan',
        'Bangladesh',
        'Bhutan',
        'India',
        'Maldives',
        'Nepal',
        'Pakistan',
        'Sri Langka'
    );
    printf('<select id="%s" name="%s">', 'pqrc_country', 'pqrc_country', $option);

    foreach($countries as $country){
        if($option == $country){
            $selected = 'selected';
            printf('<option value="%s" %s>%s</option>', $country, $selected, $country);
        }else{
            printf('<option value="%s">%s</option>', $country, $country);
        }
    }
    echo "</select>";

 }

 function pqrc_checkbox_callbak(){
    $option = get_option('pqrc_checkbox');
    $countries = array(
        'Afganistan',
        'Bangladesh',
        'Bhutan',
        'India',
        'Maldives',
        'Nepal',
        'Pakistan',
        'Sri Langka'
    );

    foreach($countries as $country){
        if(is_array($option) && in_array($country, $option)){
            $checked = 'checked';
            printf('<input type="checkbox" name="pqrc_checkbox[]" id="%s" value="%s" %s> <label for="%s">%s</label> <br>', $country, $country, $checked, $country, $country);
        }else{
            printf('<input type="checkbox" name="pqrc_checkbox[]" id="%s" value="%s" > <label for="%s">%s</label> <br>', $country, $country, $country, $country);
        }
    }

 }

 function pqrc_height_callbak(){
    $height = get_option( 'pqrc_height' );
    printf('<input type="text" id="%s" name="%s" value="%s" />', 'pqrc_height', 'pqrc_height', $height );

 }

 function pqrc_width_callbak(){
    $width = get_option( 'pqrc_width' );
    printf('<input type="text" id="%s" name="%s" value="%s" />', 'pqrc_width', 'pqrc_width', $width );
 }

 function pqrc_display_toggle_field() {
   $option = get_option('pqrc_toggle');
   echo '<div id="toggle1"></div>';
   echo "<input type='hidden' name='pqrc_toggle' id='pqrc_toggle' value='".$option."'/>";
}


 function pqrc_assets($screen){
    if('options-general.php' == $screen){
      wp_enqueue_style( 'pqrc-minitoggle-css', plugin_dir_url( __FILE__ ) . "/assets/css/minitoggle.css" );
      wp_enqueue_script( 'pqrc-minitoggle-js', plugin_dir_url( __FILE__ ) . "/assets/js/minitoggle.js", array( 'jquery' ), time(), true );
      wp_enqueue_script( 'pqrc-main-js', plugin_dir_url( __FILE__ ) . "/assets/js/pqrc-main.js", array( 'jquery' ), time(), true );
    }
 }

 add_action( 'admin_enqueue_scripts', 'pqrc_assets' );
