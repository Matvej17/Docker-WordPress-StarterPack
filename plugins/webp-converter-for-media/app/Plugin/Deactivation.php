<?php

  namespace WebpConverter\Plugin;

  use WebpConverter\Action\Cron;
  use WebpConverter\Media\Htaccess;

  class Deactivation
  {
    public function __construct()
    {
      register_deactivation_hook(WEBPC_FILE, [$this, 'refreshRewriteRules']);
      register_deactivation_hook(WEBPC_FILE, [$this, 'resetCronEvent']);
    }

    /* ---
      Functions
    --- */

    public function refreshRewriteRules()
    {
      do_action(Htaccess::ACTION_NAME, false);
      flush_rewrite_rules(true);
    }

    public function resetCronEvent()
    {
      wp_clear_scheduled_hook(Cron::CRON_ACTION);
    }
  }