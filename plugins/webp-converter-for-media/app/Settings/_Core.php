<?php

  namespace WebpConverter\Settings;

  class _Core
  {
    public function __construct()
    {
      new Errors();
      new Methods();
      new Options();
      new Page();
      new Server();
      new Values();
    }
  }