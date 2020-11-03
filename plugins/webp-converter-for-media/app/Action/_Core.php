<?php

  namespace WebpConverter\Action;

  class _Core
  {
    public function __construct()
    {
      new Convert();
      new Cron();
      new Delete();
      new Regenerate();
    }
  }