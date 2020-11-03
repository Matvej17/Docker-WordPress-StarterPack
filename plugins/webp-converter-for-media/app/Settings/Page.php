<?php

  namespace WebpConverter\Settings;

  use WebpConverter\Admin\Assets;
  use WebpConverter\Plugin\Activation;

  class Page
  {
    private $filePath = WEBPC_PATH . '/resources/views/settings.php';

    public function __construct()
    {
      add_action('admin_menu', [$this, 'addSettingsPage']);
    }

    /* ---
      Functions
    --- */

    public function addSettingsPage()
    {
      if (is_network_admin()) return;

      $page = add_submenu_page(
        'options-general.php',
        'WebP Converter for Media',
        'WebP Converter',
        'manage_options',
        'webpc_admin_page',
        [$this, 'showSettingsPage']
      );
      add_action('load-' . $page, [$this, 'loadScriptsForPage']);
    }

    public function showSettingsPage()
    {
      new Save();
      require_once $this->filePath;
    }

    public function loadScriptsForPage()
    {
      update_option(Activation::NEW_INSTALLATION_OPTION, '0');
      remove_action('admin_notices', ['WebpConverter\Admin\Notice', 'loadWelcomeNotice']);

      new Assets();
    }
  }