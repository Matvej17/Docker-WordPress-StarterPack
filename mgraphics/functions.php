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

