<?php

  namespace WebpConverter\Convert;

  class Server
  {
    /* ---
      Functions
    --- */

    public function setSettings()
    {
      ini_set('memory_limit', '1G');

      if (strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
        set_time_limit(120);
      }
    }

    public function checkIfFileExists($path)
    {
      if (is_readable($path)) return true;
      else if (!file_exists($path)) return sprintf('File "%s" does not exist. Please check file path using.', $path);
      else return sprintf('File "%s" is unreadable. Please check file permissions.', $path);
    }
  }