<?php

/**
 * Enqueue scripts and styles.
 */
function mgraphics_scripts() {
  // load assets (prod)
  // wp_enqueue_style('mgraphics-style', get_template_directory_uri() . '/dist/site.css');
  // wp_enqueue_script('mgraphics-scripts', get_template_directory_uri() . '/dist/site.js');

  // load assets (dev)
  wp_enqueue_script('mgraphics-scripts-dev', 'http://localhost:8080/site.js');
}
add_action( 'wp_enqueue_scripts', 'mgraphics_scripts' );


/**
 * Vypnutie Wysivyg editora v administrácii.
 */
function remove_editor() {
  remove_post_type_support('page', 'editor');
}

add_action('admin_init', 'remove_editor');


/**
 * Custom thumbnails
 */
function wpse_setup_theme() {
  add_theme_support( 'post-thumbnails' );
  
  add_image_size( 'custom', 180, 180, true );
}

add_action( 'after_setup_theme', 'wpse_setup_theme' );