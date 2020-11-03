<?php

  namespace WebpConverter\Convert;

  class Dir
  {
    public function __construct()
    {
      add_filter('webpc_dir_files', [$this, 'getFilesByDirectory'], 10, 3);
    }

    /* ---
      Functions
    --- */

    public function getFilesByDirectory($value, $dirPath, $skipExists = false)
    {
      if (!file_exists($dirPath)) return $value;

      $settings = apply_filters('webpc_get_values', []);
      $excluded = apply_filters('webpc_dir_excluded', []);

      $paths = $this->findFilesInDirectory($dirPath, $settings['extensions'], $excluded);
      return apply_filters('webpc_files_paths', $paths, $skipExists);
    }

    private function findFilesInDirectory($dirPath, $allowedExts, $excludedDirs)
    {
      $paths = scandir($dirPath);
      $list  = [];
      foreach ($paths as $path) {
        if (in_array($path, $excludedDirs)) continue;

        $currentPath = $dirPath . '/' . $path;
        if (is_dir($currentPath)) {
          $list = array_merge($list, $this->findFilesInDirectory($currentPath, $allowedExts, $excludedDirs));
        } else if (in_array(strtolower(pathinfo($currentPath, PATHINFO_EXTENSION)), $allowedExts)) {
          $list[] = $currentPath;
        }
      }
      return $list;
    }
  }