<?php

  namespace WebpConverter\Media;

  class _Core
  {
    public function __construct()
    {
      new Delete();
      new Htaccess();
      new Upload();
    }
  }