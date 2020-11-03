<?php

  namespace WebpConverter\Regenerate;

  use WebpConverter\Convert\Directory as Directory;

  class Skip
  {
    public function __construct()
    {
      add_filter('webpc_files_paths', [$this, 'skipExistsImages'], 10, 2); 
    }

    /* ---
      Functions
    --- */

    public function skipExistsImages($paths, $skipExists = true)
    {
      if (!$skipExists) return $paths;

      $directory = new Directory();
      foreach ($paths as $key => $path) {
        $output = $directory->getPath($path, false);
        if (file_exists($output)) unset($paths[$key]);
      }
      return $paths;
    }
  }