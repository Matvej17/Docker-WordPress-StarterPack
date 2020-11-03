<?php

  namespace WebpConverter\Method;

  use WebpConverter\Convert\Directory;
  use WebpConverter\Convert\Server;

  class Gd
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

        $response = $this->convertColorPalette($image, $path);
        if (!$response['success']) {
          $e         = new \Exception($response['message']);
          $e->status = isset($response['error_code']) ? $response['error_code'] : '';
          throw $e;
        } else {
          $image = $response['data'];
        }

        $image    = apply_filters('webpc_gd_before_saving', $image, $path);
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
      $methods   = apply_filters('webpc_gd_create_methods', [
        'imagecreatefromjpeg' => ['jpg', 'jpeg'],
        'imagecreatefrompng'  => ['png'],
        'imagecreatefromgif'  => ['gif'],
      ]);
      try {
        foreach ($methods as $method => $extensions) {
          if (!in_array($extension, $extensions)) {
            continue;
          } else if (!function_exists($method)) {
            $e         = new \Exception(sprintf('Server configuration: "%s" function is not available.', $method));
            $e->status = 'server_configuration';
            throw $e;
          } else if (!$image = @$method($path)) {
            $e         = new \Exception(sprintf('"%s" is not a valid image file.', $path));
            $e->status = 'invalid_image';
            throw $e;
          }
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

    private function convertColorPalette($image)
    {
      try {
        if (!function_exists('imageistruecolor')) {
          $e         = new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imageistruecolor'));
          $e->status = 'server_configuration';
          throw $e;
        } else if (!imageistruecolor($image)) {
          if (!function_exists('imagepalettetotruecolor')) {
            $e         = new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imagepalettetotruecolor'));
            $e->status = 'server_configuration';
            throw $e;
          }
          imagepalettetotruecolor($image);
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
        } else if (!function_exists('imagewebp')) {
          $e         = new \Exception(sprintf('Server configuration: "%s" function is not available.', 'imagewebp'));
          $e->status = 'server_configuration';
          throw $e;
        } else if ((imagesx($image) > 8192) || (imagesy($image) > 8192)) {
          $e         = new \Exception(sprintf('Image is larger than maximum 8K resolution: "%s".', $path));
          $e->status = 'max_resolution';
          throw $e;
        } else if (!$success = imagewebp($image, $output, $settings['quality'])) {
          $e         = new \Exception(sprintf('Error occurred while converting image: "%s".', $path));
          $e->status = 'convert_error';
          throw $e;
        }

        if (filesize($output) % 2 === 1) file_put_contents($output, "\0", FILE_APPEND);
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