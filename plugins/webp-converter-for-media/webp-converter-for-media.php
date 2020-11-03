<?php

  /*
    Plugin Name: WebP Converter for Media
    Description: Speed up your website by serving WebP images instead of standard formats JPEG, PNG and GIF.
    Version: 1.5.1
    Author: Mateusz Gbiorczyk
    Author URI: https://gbiorczyk.pl/
    Text Domain: webp-converter-for-media
  */

  define('WEBPC_VERSION', '1.5.1');
  define('WEBPC_FILE',    __FILE__);
  define('WEBPC_NAME',    plugin_basename(__FILE__));
  define('WEBPC_PATH',    plugin_dir_path(__FILE__));
  define('WEBPC_URL',     plugin_dir_url(__FILE__));

  require_once __DIR__ . '/vendor/autoload.php';
  new WebpConverter\WebpConverter();