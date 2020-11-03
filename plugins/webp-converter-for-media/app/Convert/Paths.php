<?php

  namespace WebpConverter\Convert;

  class Paths
  {
    const PATH_UPLOADS  = 'wp-content/uploads';
    const PATH_OUTPUT   = 'wp-content/uploads-webpc';
    const DIRS_EXCLUDED = ['.', '..', '.git', '.svn', 'node_modules'];

    public function __construct()
    {
      add_filter('webpc_uploads_path',   [$this, 'getUploadsPath'],  0,   2);
      add_filter('webpc_uploads_webp',   [$this, 'getOutputPath'],   0,   2);
      add_filter('webpc_uploads_path',   [$this, 'parsePath'],       100, 2);
      add_filter('webpc_uploads_webp',   [$this, 'parsePath'],       100, 2);
      add_filter('webpc_uploads_prefix', [$this, 'getPrefixPath'],   0);
      add_filter('webpc_uploads_dir',    [$this, 'getUploadsDir'],   0);
      add_filter('webpc_dir_path',       [$this, 'getPathOfDir'],    0,   2);
      add_filter('webpc_dir_excluded',   [$this, 'getExcludedDirs'], 0);
    }

    /* ---
      Functions
    --- */

    public function getUploadsPath($value, $skipRoot = false)
    {
      return self::PATH_UPLOADS;
    }

    public function getOutputPath($value, $skipRoot = false)
    {
      return self::PATH_OUTPUT;
    }

    public function parsePath($value, $skipRoot = false)
    {
      if ($skipRoot) return trim($value, '\/');

      $path = apply_filters('webpc_uploads_root', ABSPATH);
      return $path . '/' . trim($value, '\/');
    }

    public function getPrefixPath($value)
    {
      $docDir   = realpath($_SERVER['DOCUMENT_ROOT']);
      $wpDir    = realpath(ABSPATH);
      $diffDir  = trim(str_replace($docDir, '', $wpDir), '\/');
      $diffPath = sprintf('/%s/', $diffDir);

      return str_replace('//', '/', $diffPath);
    }

    public function getUploadsDir($value)
    {
      $uploadsDir    = apply_filters('webpc_uploads_path', '');
      $uploadsParent = dirname($uploadsDir);
      $webpParent    = dirname(apply_filters('webpc_uploads_webp', ''));
      if ((!$uploadsDir = realpath($uploadsDir)) || (!$uploadsParent = realpath($uploadsParent))
        || ($uploadsParent !== realpath($webpParent))) {
        return $value;
      }

      $path = str_replace($uploadsParent, '', $uploadsDir);
      return trim($path, '\/');
    }

    public function getPathOfDir($value, $directory)
    {
      switch ($directory) {
        case 'plugins':
          return dirname(WEBPC_PATH);
          break;
        case 'themes':
          return get_theme_root();
          break;
        case 'uploads':
          return apply_filters('webpc_uploads_path', '');
          break;
      }
      return null;
    }

    public function getExcludedDirs($value)
    {
      return self::DIRS_EXCLUDED;
    }
  }