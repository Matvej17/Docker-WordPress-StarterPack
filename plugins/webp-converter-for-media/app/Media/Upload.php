<?php

  namespace WebpConverter\Media;

  class Upload
  {
    private $convertedPaths = [];

    public function __construct()
    {
      add_filter('wp_update_attachment_metadata', [$this, 'initAttachmentConvert'], 10, 2);
    }

    /* ---
      Functions
    --- */

    public function initAttachmentConvert($data, $attachmentId)
    {
      if (!$data || !isset($data['file']) || !isset($data['sizes'])) return $data;

      $paths = $this->getSizesPaths($data);
      $paths = apply_filters('webpc_attachment_paths', $paths, $attachmentId);
      $paths = apply_filters('webpc_files_paths', $paths, false);

      $paths = array_diff($paths, $this->convertedPaths);
      $this->convertedPaths = array_merge($this->convertedPaths, $paths);

      do_action('webpc_convert_paths', $paths);
      return $data;
    }

    private function getSizesPaths($data)
    {
      $directory = $this->getAttachmentDirectory($data['file']);
      $list      = [];

      $list[] = $directory . basename($data['file']);
      foreach ($data['sizes'] as $key => $size) {
        $path = $directory . $size['file'];
        if (!in_array($path, $list)) $list[] = $path;
      }
      return array_values(array_unique($list));
    }

    private function getAttachmentDirectory($path)
    {
      $upload = wp_upload_dir();
      $source = rtrim($upload['basedir'], '/\\') . '/' . rtrim(dirname($path), '/\\') . '/';
      $source = str_replace('\\', '/', $source);
      return $source;
    }
  }