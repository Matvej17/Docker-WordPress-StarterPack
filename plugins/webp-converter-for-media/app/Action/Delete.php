<?php

  namespace WebpConverter\Action;

  use WebpConverter\Convert\Directory;

  class Delete
  {
    public function __construct()
    {
      add_action('webpc_delete_paths', [$this, 'deleteFilesByPaths']);
    }

    /* ---
      Functions
    --- */

    public function deleteFilesByPaths($paths)
    {
      foreach ($paths as $path) {
        $this->deleteFileByPath($path);
      }
    }

    private function deleteFileByPath($path)
    {
      $source = (new Directory())->getPath($path);
      if (is_writable($source) && (pathinfo($source, PATHINFO_EXTENSION) === 'webp')) unlink($source);
    }
  }