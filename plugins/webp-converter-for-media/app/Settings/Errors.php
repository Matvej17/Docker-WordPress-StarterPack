<?php

  namespace WebpConverter\Settings;

  use WebpConverter\Media\Htaccess;
  use WebpConverter\Traits\FileLoaderTrait;

  class Errors
  {
    use FileLoaderTrait;

    const PATH_SOURCE_FILE_PNG  = '/public/img/icon-test.png';
    const PATH_SOURCE_FILE_PNG2 = '/public/img/icon-test.png2';
    const PATH_OUTPUT_FILE_PNG  = '/webp-converter-for-media-test.png';
    const PATH_OUTPUT_FILE_PNG2 = '/webp-converter-for-media-test.png2';

    private $cache    = null;
    private $filePath = WEBPC_PATH . '/resources/components/errors/%s.php';

    public function __construct()
    {
      add_filter('webpc_server_errors', [$this, 'getServerErrors']);
    }

    /* ---
      Functions
    --- */

    public function getServerErrors()
    {
      if ($this->cache !== null) return $this->cache;

      $this->cache = $this->loadErrorMessages();
      return $this->cache;
    }

    private function loadErrorMessages()
    {
      $errors = $this->getErrorsList();
      $list   = [];
      foreach ($errors as $error) {
        ob_start();
        include sprintf($this->filePath, str_replace('_', '-', $error));
        $list[$error] = ob_get_contents();
        ob_end_clean();
      }
      return $list;
    }

    private function getErrorsList()
    {
      $errors = [];

      if ($this->ifLibsAreInstalled() !== true) {
        $errors[] = 'libs_not_installed';
      } else if ($this->ifLibsSupportWebp() !== true) {
        $errors[] = 'libs_without_webp_support';
      }
      if ($errors) return $errors;

      if ($this->ifSettingsAreCorrect() !== true) {
        $errors[] = 'settings_incorrect';
      }
      if ($errors) return $errors;

      if ($this->ifRestApiIsEnabled() !== true) {
        $errors[] = 'rest_api_disabled';
      }
      if ($this->ifUploadsPathExists() !== true) {
        $errors[] = 'path_uploads_unavailable';
      } else if ($this->ifHtaccessIsWriteable() !== true) {
        $errors[] = 'path_htaccess_not_writable';
      }
      if ($this->ifPathsAreDifferent() !== true) {
        $errors[] = 'path_webp_duplicated';
      } else if ($this->ifWebpPathIsWriteable() !== true) {
        $errors[] = 'path_webp_not_writable';
      }
      if ($errors) return $errors;

      $this->copyFilesForDebug();
      add_filter('webpc_get_values',        [$this, 'setExtensionsForDebug']);
      add_filter('webpc_gd_create_methods', [$this, 'setMethodsForDebug']);
      do_action(Htaccess::ACTION_NAME, true);

      if ($this->ifRedirectsAreWorks() !== true) {
        if ($this->ifBypassingApacheIsActive() === true) {
          $errors[] = 'bypassing_apache';
        } else {
          $errors[] = 'rewrites_not_working';
        }
      } else if ($this->ifRedirectsAreCached() === true) {
        $errors[] = 'rewrites_cached';
      }

      remove_filter('webpc_get_values',        [$this, 'setExtensionsForDebug']);
      remove_filter('webpc_gd_create_methods', [$this, 'setMethodsForDebug']);
      do_action(Htaccess::ACTION_NAME, true);

      return $errors;
    }

    private function copyFilesForDebug()
    {
      $uploadsDir = apply_filters('webpc_uploads_path', []);
      if (!is_writable($uploadsDir)) return;

      copy(WEBPC_PATH . self::PATH_SOURCE_FILE_PNG, $uploadsDir . self::PATH_OUTPUT_FILE_PNG);
      copy(WEBPC_PATH . self::PATH_SOURCE_FILE_PNG2, $uploadsDir . self::PATH_OUTPUT_FILE_PNG2);
    }

    public function setExtensionsForDebug($settings)
    {
      $settings['extensions'][] = 'png';
      $settings['extensions'][] = 'png2';
      return $settings;
    }

    public function setMethodsForDebug($methods)
    {
      $methods['imagecreatefrompng'][] = 'png2';
      return $methods;
    }

    private function ifLibsAreInstalled()
    {
      return (extension_loaded('gd') || (extension_loaded('imagick') && class_exists('\Imagick')));
    }

    private function ifLibsSupportWebp()
    {
      $methods = apply_filters('webpc_get_methods', []);
      return (count($methods) > 0);
    }

    private function ifSettingsAreCorrect()
    {
      $settings = apply_filters('webpc_get_values', [], true);
      if ((!isset($settings['extensions']) || !$settings['extensions'])
        || (!isset($settings['dirs']) || !$settings['dirs'])
        || (!isset($settings['method']) || !$settings['method'])
        || (!isset($settings['quality']) || !$settings['quality'])) return false;

      return true;
    }

    private function ifRestApiIsEnabled()
    {
      return ((apply_filters('rest_enabled', true) === true)
        && (apply_filters('rest_jsonp_enabled', true) === true)
        && (apply_filters('rest_authentication_errors', true) === true));
    }

    private function ifUploadsPathExists()
    {
      $path = apply_filters('webpc_uploads_path', '');
      return (is_dir($path) && ($path !== ABSPATH));
    }

    private function ifHtaccessIsWriteable()
    {
      $pathDir  = apply_filters('webpc_uploads_path', '');
      $pathFile = $pathDir . '/.htaccess';
      if (file_exists($pathFile)) return (is_readable($pathFile) && is_writable($pathFile));
      else return is_writable($pathDir);
    }

    private function ifPathsAreDifferent()
    {
      $pathUploads = apply_filters('webpc_uploads_path', '');
      $pathWebp    = apply_filters('webpc_uploads_webp', '');
      return ($pathUploads !== $pathWebp);
    }

    private function ifWebpPathIsWriteable()
    {
      $path = apply_filters('webpc_uploads_webp', '');
      return (is_dir($path) || is_writable(dirname($path)));
    }

    private function ifRedirectsAreWorks()
    {
      $uploads    = wp_upload_dir();
      $sourceFile = apply_filters('webpc_uploads_path', []) . self::PATH_OUTPUT_FILE_PNG;

      do_action('webpc_convert_paths', apply_filters('webpc_files_paths', [
        $sourceFile,
      ], true));

      $fileSize = $this->getFileSizeByPath($sourceFile);
      $fileWebp = $this->getFileSizeByUrl($uploads['baseurl'] . self::PATH_OUTPUT_FILE_PNG);

      return ($fileWebp < $fileSize);
    }

    private function ifBypassingApacheIsActive()
    {
      $uploads    = wp_upload_dir();
      $sourceFile = apply_filters('webpc_uploads_path', []) . self::PATH_OUTPUT_FILE_PNG2;

      do_action('webpc_convert_paths', apply_filters('webpc_files_paths', [
        $sourceFile,
      ], true));

      $filePng  = $this->getFileSizeByUrl($uploads['baseurl'] . self::PATH_OUTPUT_FILE_PNG);
      $filePng2 = $this->getFileSizeByUrl($uploads['baseurl'] . self::PATH_OUTPUT_FILE_PNG2);

      return ($filePng > $filePng2);
    }

    private function ifRedirectsAreCached()
    {
      $uploads      = wp_upload_dir();
      $fileWebp     = $this->getFileSizeByUrl($uploads['baseurl'] . self::PATH_OUTPUT_FILE_PNG);
      $fileOriginal = $this->getFileSizeByUrl($uploads['baseurl'] . self::PATH_OUTPUT_FILE_PNG, false);

      return (($fileWebp > 0) && ($fileWebp === $fileOriginal));
    }
  }