<?php

  namespace WebpConverter\Admin;

  use WebpConverter\Plugin\Activation;

  class Notice
  {
    const NOTICE_THANKS_OPTION = 'webpc_notice_hidden';

    public function __construct()
    {
      add_action('admin_init',           [$this, 'showWelcomeNotice']);
      add_action('admin_init',           [$this, 'showThanksNotice']);
      add_action('wp_ajax_webpc_notice', [$this, 'hideThanksNotice']);
    }

    /* ---
      Functions
    --- */

    public function showWelcomeNotice()
    {
      if (get_option(Activation::NEW_INSTALLATION_OPTION) !== '1') return;

      new Assets();
      add_action('admin_notices', ['WebpConverter\Admin\Notice', 'loadWelcomeNotice']);
    }

    public static function loadWelcomeNotice()
    {
      require_once WEBPC_PATH . 'resources/components/notices/welcome.php';
    }

    public function showThanksNotice()
    {
      if (($_SERVER['PHP_SELF'] !== '/wp-admin/index.php') ||
        (get_option(self::NOTICE_THANKS_OPTION, 0) >= time())) return;

      new Assets();
      add_action('admin_notices', [$this, 'loadThanksNotice']);
    }

    public function loadThanksNotice()
    {
      require_once WEBPC_PATH . 'resources/components/notices/thanks.php';
    }

    public function hideThanksNotice()
    {
      $isPermanent = isset($_POST['is_permanently']) && $_POST['is_permanently'];
      $expires     = strtotime($isPermanent ? '+10 years' : '+ 1 month');

      update_option(self::NOTICE_THANKS_OPTION, $expires);
    }
  }