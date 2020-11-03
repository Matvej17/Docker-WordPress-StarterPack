<?php

  namespace WebpConverter\Media;

  class Delete
  {
    public function __construct()
    {
      add_filter('wp_delete_file', [$this, 'deleteAttachmentFile']);
    }

    /* ---
      Functions
    --- */

    public function deleteAttachmentFile($path)
    {
      do_action('webpc_delete_paths', [$path]);
      return $path;
    }
  }