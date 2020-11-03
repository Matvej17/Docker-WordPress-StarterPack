<?php

  namespace WebpConverter\Method;

  use WebpConverter\Convert\Directory;
  use WebpConverter\Convert\Server;

  class Imagick
  {
    /* ---
      Functions
    --- */

    public function convertImage($path, $settings)
    {
      (new Server())->setSettings();

      try {
        $status = (new Server())->checkIfFileExists($path);
        if ($status !== true) {
          $e         = new \Exception($status);
          $e->status = 'file_unreadable';
          throw $e;
        }

        $response = $this->createImage($path);
        if (!$response['success']) {
          $e         = new \Exception($response['message']);
          $e->status = isset($response['error_code']) ? $response['error_code'] : '';
          throw $e;
        } else {
          $image = $response['data'];
        }

        $image    = apply_filters('webpc_imagick_before_saving', $image, $path);
        $response = $this->convertToWebp($image, $path, $settings);
        if (!$response['success']) {
          $e         = new \Exception($response['message']);
          $e->status = isset($response['error_code']) ? $response['error_code'] : '';
          throw $e;
        } else {
          return [
            'success' => true,
            'data'    => $response['data'],
          ];
        }
      } catch (\Exception $e) {
        if (in_array('debug_enabled', $settings['features'])) {
          error_log(sprintf('WebP Converter for Media: %s', $e->getMessage()));
        }

        return [
          'success' => false,
          'message' => apply_filters('webpc_convert_error', $e->getMessage(), $e->status),
        ];
      }
    }

    private function createImage($path)
    {
      $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      try {
        if (!extension_loaded('imagick') || !class_exists('Imagick')) {
          $e         = new \Exception('Server configuration: Imagick module is not available with this PHP installation.');
          $e->status = 'server_configuration';
          throw $e;
        } else if (!$image = new \Imagick($path)) {
          $e         = new \Exception(sprintf('"%s" is not a valid image file.', $path));
          $e->status = 'invalid_image';
          throw $e;
        }
        if (!isset($image)) {
          $e         = new \Exception(sprintf('Unsupported extension "%s" for file "%s"', $extension, $path));
          $e->status = 'unsupported_extension';
          throw $e;
        }

        return [
          'success' => true,
          'data'    => $image,
        ];
      } catch (\Exception $e) {
        return [
          'success'    => false,
          'message'    => $e->getMessage(),
          'error_code' => (isset($e->status)) ? $e->status : '',
        ];
      }
    }

    private function convertToWebp($image, $path, $settings)
    {
      try {
        $output = (new Directory())->getPath($path, true);
        if (!$output) {
          $e         = new \Exception(sprintf('An error occurred creating destination directory for "%s" file.', $path));
          $e->status = 'output_path';
          throw $e;
        } else if (!in_array('WEBP', $image->queryFormats())) {
          $e         = new \Exception('Server configuration: Imagick does not support WebP format.');
          $e->status = 'server_configuration';
          throw $e;
        }

        $image->setImageFormat('WEBP');
        if (!in_array('keep_metadata', $settings['features'])) {
          $image->stripImage();
        }
        $image->setImageCompressionQuality($settings['quality']);
        $blob = $image->getImageBlob();

        $success = file_put_contents($output, $blob);
        if (!$success) {
          $e         = new \Exception('Error occurred while converting image.');
          $e->status = 'convert_error';
          throw $e;
        }
        do_action('webpc_convert_after', $output, $path);

        return [
          'success' => true,
          'data'    => [
            'size' => [
              'before' => filesize($path),
              'after'  => filesize((file_exists($output)) ? $output : $path),
            ],
          ],
        ];
      } catch (\Exception $e) {
        return [
          'success'    => false,
          'message'    => $e->getMessage(),
          'error_code' => (isset($e->status)) ? $e->status : '',
        ];
      }

      return $image;
    }
  }