<?php

  namespace WebpConverter\Plugin;

  use WebpConverter\Action\Cron;
  use WebpConverter\Admin\Notice;
  use WebpConverter\Media\Htaccess;

  class Activation
  {
    const PHP_REQUIRED_VERSION    = '7.0.0';
    const NEW_INSTALLATION_OPTION = 'webpc_is_new_installation';

    public function __construct()
    {
      register_activation_hook(WEBPC_FILE, [$this, 'disablePluginForOldPhp']);
      register_activation_hook(WEBPC_FILE, [$this, 'createDirectoryForUploadsWebp']);
      register_activation_hook(WEBPC_FILE, [$this, 'addDefaultOptions']);
      register_activation_hook(WEBPC_FILE, [$this, 'refreshRewriteRules']);
    }

    /* ---
      Functions
    --- */

    public function disablePluginForOldPhp()
    {
      if (version_compare(PHP_VERSION,self::PHP_REQUIRED_VERSION, '>=')) return;

      deactivate_plugins(basename(WEBPC_FILE));
      wp_die(sprintf(
        __('%sWebP Converter for Media%s plugin requires a minimum PHP %s version. Sorry about that!', 'webp-converter-for-media'),
        '<strong>',
        '</strong>',
        self::PHP_REQUIRED_VERSION
      ));
    }

    public function createDirectoryForUploadsWebp()
    {
      $path = apply_filters('webpc_uploads_webp', '');
      if (!file_exists($path) && is_writable(dirname($path))) mkdir($path);
    }

    public function addDefaultOptions()
    {
      add_option(Notice::NOTICE_THANKS_OPTION, strtotime('+ 1 week'));
      add_option(self::NEW_INSTALLATION_OPTION, '1');
    }

    public function refreshRewriteRules()
    {
      do_action(Htaccess::ACTION_NAME, true);
      flush_rewrite_rules(true);
    }
  }