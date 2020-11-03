<?php

  namespace WebpConverter\Regenerate;

  class Paths
  {
    /* ---
      Functions
    --- */

    public function getPaths($skipExists = false, $chunkSize = null)
    {
      $settings = apply_filters('webpc_get_values', []);
      $dirs     = array_filter(array_map(function($dirName) {
        return apply_filters('webpc_dir_path', '', $dirName);
      }, $settings['dirs']));

      $list = [];
      foreach ($dirs as $dirPath) {
        $paths = apply_filters('webpc_dir_files', [], $dirPath, $skipExists);
        $list  = array_merge($list, $paths);
      }

      if ($chunkSize === null) return $list;
      return array_chunk($list, $chunkSize);
    }
  }