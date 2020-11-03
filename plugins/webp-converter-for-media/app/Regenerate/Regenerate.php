<?php

  namespace WebpConverter\Regenerate;

  use WebpConverter\Method;

  class Regenerate
  {
    /* ---
      Functions
    --- */

    public function convertImages($paths)
    {
      $settings   = apply_filters('webpc_get_values', []);
      $errors     = [];
      $sizeBefore = 0;
      $sizeAfter  = 0;

      if ($settings['method'] === 'gd') $convert = new Method\Gd();
      else if ($settings['method'] === 'imagick') $convert = new Method\Imagick();
      if (!isset($convert)) return false;

      foreach ($paths as $path) {
        $response = $convert->convertImage($path, $settings);

        if ($response['success'] !== true) {
          $errors[] = $response['message'];
        } else {
          $sizeBefore += $response['data']['size']['before'];
          $sizeAfter  += $response['data']['size']['after'];
        }
      }
      $errors = array_filter($errors);

      return [
        'errors' => apply_filters('webpc_convert_errors', $errors),
        'size'   => [
          'before' => $sizeBefore,
          'after'  => $sizeAfter,
        ],
      ];
    }
  }