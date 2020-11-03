<?php

  namespace WebpConverter\Plugin;

  class _Core
  {
    public function __construct()
    {
      new Activation();
      new Deactivation();
      new Uninstall();
      new Update();
    }
  }