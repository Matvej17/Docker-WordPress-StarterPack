<?php

  namespace WebpConverter\Convert;

  class _Core
  {
    public function __construct()
    {
      new Dir();
      new Paths();
      new Size();
    }
  }