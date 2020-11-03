<?php

  namespace WebpConverter\Action;

  class Cron
  {
    const CRON_ACTION = 'webpc_regenerate_all';

    public function __construct()
    {
      add_filter('cron_schedules', [$this, 'addCronInterval']);
      add_action('init',           [$this, 'addCronEvent']);
    }

    /* ---
      Functions
    --- */

    public function addCronInterval($schedules)
    {
      $schedules['webpc_cron'] = [
        'interval' => apply_filters('webpc_cron_interval', HOUR_IN_SECONDS),
        'display'  => 'WebP Converter for Media',
      ];
      return $schedules;
    }

    public function addCronEvent()
    {
      if (wp_next_scheduled(self::CRON_ACTION)
        || (!$settings = apply_filters('webpc_get_values', []))
        || !in_array('cron_enabled', $settings['features'])) return;

      wp_schedule_event(time(), 'webpc_cron', self::CRON_ACTION);
    }
  }