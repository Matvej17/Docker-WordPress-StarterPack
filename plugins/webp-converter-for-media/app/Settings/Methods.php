<?php

  namespace WebpConverter\Settings;

  class Methods
  {
    private $cache = null;

    public function __construct()
    {
      add_filter('webpc_get_methods', [$this, 'getAvaiableMethods']);
    }

    /* ---
      Functions
    --- */

    public function getAvaiableMethods()
    {
      if ($this->cache !== null) return $this->cache;

      $this->cache = [];
      if (extension_loaded('gd') && function_exists('imagewebp')) {
        $this->cache[] = 'gd';
      }
      if (extension_loaded('imagick') && class_exists('\Imagick')) {
        $formats = (new \Imagick)->queryformats();
        if (in_array('WEBP', $formats)) $this->cache[] = 'imagick';
      }
      return $this->cache;
    }
  }