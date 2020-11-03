<?php

  namespace WebpConverter\Settings;

  class Values
  {
    private $cache = null;

    public function __construct()
    {
      add_filter('webpc_get_values', [$this, 'getValues'], 10, 2);
    }

    /* ---
      Functions
    --- */

    public function getValues($value, $isForce = false)
    {
      if ($isForce && ($this->cache !== null)) return $this->cache;

      $methods     = apply_filters('webpc_get_methods', []);
      $this->cache = get_option(Save::SETTINGS_OPTION, [
        'extensions' => ['jpg', 'jpeg', 'png'],
        'dirs'       => ['uploads'],
        'method'     => ($methods) ? $methods[0] : '',
        'features'   => ['only_smaller', 'mod_expires', 'debug_enabled'],
        'quality'    => 85,
      ]);
      return $this->cache;
    }
  }