<?php

  namespace WebpConverter\Action;

  use WebpConverter\Regenerate\Paths;

  class Regenerate
  {
    public function __construct()
    {
      add_action('webpc_regenerate_all', [$this, 'runRegenerationAllImages']);
    }

    /* ---
      Functions
    --- */

    public function runRegenerationAllImages()
    {
      $paths = (new Paths())->getPaths(true);
      do_action('webpc_convert_paths', $paths);
    }
  }