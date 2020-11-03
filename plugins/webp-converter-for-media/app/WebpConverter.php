<?php

  namespace WebpConverter;

  class WebpConverter
  {
    public function __construct()
    {
      new Action\_Core();
      new Admin\_Core();
      new Convert\_Core();
      new Media\_Core();
      new Plugin\_Core();
      new Regenerate\_Core();
      new Settings\_Core();
    }
  }