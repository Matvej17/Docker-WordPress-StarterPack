<?php

  namespace WebpConverter\Settings;

  use WebpConverter\Action\Cron;
  use WebpConverter\Media\Htaccess;

  class Save
  {
    const SETTINGS_OPTION = 'webpc_settings';

    public function __construct()
    {
      $this->saveConfig();
    }

    /* ---
      Functions
    --- */

    private function saveConfig()
    {
      if (!isset($_POST['webpc_save']) || !isset($_REQUEST['_wpnonce'])
        || !wp_verify_nonce($_REQUEST['_wpnonce'], 'webpc-save')) return;

      $values = $this->getValues();
      update_option(self::SETTINGS_OPTION, $values);
      $settings = apply_filters('webpc_get_values', [], true);

      do_action(Htaccess::ACTION_NAME, true);
      wp_clear_scheduled_hook(Cron::CRON_ACTION);
    }

    private function getValues()
    {
      $options = apply_filters('webpc_get_options', []);
      $values  = [];
      foreach ($options as $key => $option) {
        $name          = $option['name'];
        $values[$name] = (isset($_POST[$name]))
          ? $this->setValuesForOption($_POST[$name], $option['values'])
          : (($option['type'] === 'checkbox') ? [] : null);
      }

      return $values;
    }

    private function setValuesForOption($value, $options)
    {
      $values = array_keys($options);
      if (is_array($value)) return array_intersect($value, $values);
      else return (in_array($value, $values)) ? $value : null;
    }
  }