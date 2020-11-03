<?php

  namespace WebpConverter\Plugin;

  use WebpConverter\Admin\Notice;
  use WebpConverter\Settings\Save;
  use WebpConverter\Settings\Errors;

  class Uninstall
  {
    public function __construct()
    {
      register_uninstall_hook(WEBPC_FILE, ['WebpConverter\Plugin\Uninstall', 'removePluginSettings']);
    }

    /* ---
      Functions
    --- */

    public static function removePluginSettings()
    {
      delete_option(Activation::NEW_INSTALLATION_OPTION);
      delete_option(Save::SETTINGS_OPTION);
      delete_option(Notice::NOTICE_THANKS_OPTION);
      delete_option(Update::VERSION_OPTION);

      self::removeHtaccessFile();
      self::removeWebpFiles();
      self::removeDebugFiles();
    }

    private static function removeHtaccessFile()
    {
      $path = sprintf('%s/.htaccess', apply_filters('webpc_uploads_webp', ''));
      if (is_writable($path)) unlink($path);
    }

    public static function removeWebpFiles()
    {
      $path    = apply_filters('webpc_uploads_webp', '');
      $paths   = self::getPathsFromLocation($path);
      $paths[] = $path;
      self::removeFiles($paths);
    }

    public static function removeDebugFiles()
    {
      $uploadsDir = apply_filters('webpc_uploads_path', []);

      if (is_writable($uploadsDir . Errors::PATH_OUTPUT_FILE_PNG)) {
        unlink($uploadsDir . Errors::PATH_OUTPUT_FILE_PNG);
      }
      if (is_writable($uploadsDir . Errors::PATH_OUTPUT_FILE_PNG2)) {
        unlink($uploadsDir . Errors::PATH_OUTPUT_FILE_PNG2);
      }
    }

    private static function getPathsFromLocation($path, $paths = [])
    {
      if (!file_exists($path)) return $paths;
      $path .= '/';
      $files = glob($path . '*');
      foreach ($files as $file) {
        if (is_dir($file)) $paths = self::getPathsFromLocation($file, $paths);
        $paths[] = $file;
      }
      return $paths;
    }

    private static function removeFiles($paths)
    {
      if (!$paths) return;
      foreach ($paths as $path) {
        if (!is_writable($path) || !is_writable(dirname($path))) continue;
        if (is_file($path) && (pathinfo($path, PATHINFO_EXTENSION) === 'webp')) unlink($path);
        else if (is_dir($path)) rmdir($path);
      }
    }
  }