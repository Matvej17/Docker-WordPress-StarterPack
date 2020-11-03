<?php

  namespace WebpConverter\Admin;

  class Assets
  {
    private $pathCss = WEBPC_URL . 'public/build/css/styles.css';
    private $pathJs  = WEBPC_URL . 'public/build/js/scripts.js';

    public function __construct()
    {
      add_filter('admin_enqueue_scripts', [$this, 'loadStyles']);
      add_filter('admin_enqueue_scripts', [$this, 'loadScripts']);
    }

    /* ---
      Functions
    --- */

    public function loadStyles()
    {
      wp_register_style('webp-converter', $this->pathCss, '', WEBPC_VERSION);
      wp_enqueue_style('webp-converter');
    }

    public function loadScripts()
    {
      wp_register_script('webp-converter', $this->pathJs, 'jquery', WEBPC_VERSION, true);
      wp_enqueue_script('webp-converter');
    }
  }