<?php

  namespace WebpConverter\Plugin;

  use WebpConverter\Media\Htaccess;
  use WebpConverter\Settings\Save;

  class Update
  {
    const VERSION_OPTION = 'webpc_latest_version';

    public function __construct()
    {
      add_action('admin_init', [$this, 'runActionsAfterUpdate']);
    }

    /* ---
      Functions
    --- */

    public function runActionsAfterUpdate()
    {
      $version = get_option(self::VERSION_OPTION, null);
      if ($version === WEBPC_VERSION) return;

      if ($version !== null) {
        update_option(Save::SETTINGS_OPTION, $this->updateSettingsForOldVersions($version));
        $this->moveFilesToUploadsSubdirectory(self::VERSION_OPTION);

        update_option(Activation::NEW_INSTALLATION_OPTION, '0');
        remove_action('admin_notices', ['WebpConverter\Admin\Notice', 'loadWelcomeNotice']);
      }

      do_action(Htaccess::ACTION_NAME, true);
      flush_rewrite_rules(true);
      update_option(self::VERSION_OPTION, WEBPC_VERSION);
    }

    private function updateSettingsForOldVersions($version)
    {
      $settings = apply_filters('webpc_get_values', []);

      if (version_compare($version, '1.1.2', '<=')) {
        $settings['features'][] = 'only_smaller';
      }

      if (version_compare($version, '1.2.7', '<=') && !isset($settings['dirs'])) {
        $settings['dirs'] = ['uploads'];
      }

      if (version_compare($version, '1.3.1', '<=')) {
        $settings['features'][] = 'debug_enabled';
      }

      $settings['features'] = array_unique($settings['features']);
      return $settings;
    }

    private function moveFilesToUploadsSubdirectory($version)
    {
      if (version_compare($version, '1.2.7', '>')) return;

      $webpRoot = apply_filters('webpc_uploads_webp', '');
      if (!is_writable($webpRoot)) return;

      $pathParts = explode('/', apply_filters('webpc_uploads_dir', ''));
      $oldPaths  = scandir(apply_filters('webpc_uploads_webp', ''));
      for ($i = 1; $i <= count($pathParts); $i++) {
        $dirPath = $webpRoot . '/' . implode('/', array_slice($pathParts, 0, $i));
        if (!file_exists($dirPath)) mkdir($dirPath);
      }

      foreach ($oldPaths as $path) {
        if (in_array($path, ['.', '..', '.htaccess', $pathParts[0]])) continue;
        rename($webpRoot . '/'. $path, $dirPath . '/'. $path);
      }
    }
  }