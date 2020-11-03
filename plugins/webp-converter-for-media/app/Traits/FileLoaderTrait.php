<?php

  namespace WebpConverter\Traits;

  trait FileLoaderTrait
  {
    /* ---
      Functions
    --- */

    private function getFileSizeByUrl($url, $setHeaders = true)
    {
      $headers = [
        'Accept: image/webp',
        'Referer: ' . WEBPC_URL,
      ];
      return $this->getFileSizeForLoadedFile($url, ($setHeaders) ? $headers : []);
    }

    private function getFileSizeByPath($path)
    {
      if (!file_exists($path)) return 0;
      return filesize($path);
    }

    private function getFileSizeForLoadedFile($url, $headers)
    {
      $headers[] = 'Cache-Control: no-cache';

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($ch);
      $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      return ($code === 200) ? strlen($response) : 0;
    }
  }