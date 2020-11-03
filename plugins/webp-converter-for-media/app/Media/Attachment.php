<?php

  namespace WebpConverter\Media;

  class Attachment
  {
    private $uploadDir, $imageSizes;

    public function __construct()
    {
      $this->uploadDir  = wp_upload_dir();
      $this->imageSizes = get_intermediate_image_sizes();
    }

    /* ---
      Functions
    --- */

    public function getAttachmentPaths($attachmentId)
    {
      $settings = apply_filters('webpc_get_values', []);
      $paths    = $this->getPathsByAttachment($attachmentId, $settings);
      return $paths;
    }

    private function getPathsByAttachment($postId, $settings)
    {
      $list     = [];
      $metadata = wp_get_attachment_metadata($postId);
      if (!$metadata) return $list;

      $extension = strtolower(pathinfo($metadata['file'], PATHINFO_EXTENSION));
      if (!isset($metadata['file'])
        || !in_array($extension, $settings['extensions'])) return $list;

      $paths = $this->getPathsBySizes($postId, $metadata['file']);
      $paths = apply_filters('webpc_attachment_paths', $paths, $postId);
      $paths = apply_filters('webpc_files_paths', $paths, false);
      return $paths;
    }

    private function getPathsBySizes($postId, $path)
    {
      $list   = [];
      $list[] = str_replace('\\', '/', implode('/', [$this->uploadDir['basedir'], $path]));

      foreach ($this->imageSizes as $size) {
        $src    = wp_get_attachment_image_src($postId, $size);
        $url    = str_replace($this->uploadDir['baseurl'], $this->uploadDir['basedir'], $src[0]);
        $url    = str_replace('\\', '/', $url);
        $list[] = $url;
      }
      return array_values(array_unique($list));
    }
  }