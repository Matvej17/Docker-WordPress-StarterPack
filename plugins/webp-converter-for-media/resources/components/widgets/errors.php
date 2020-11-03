<?php if ($errors = apply_filters('webpc_server_errors', [])) : ?>
  <div class="webpPage__widget">
    <h3 class="webpPage__widgetTitle webpPage__widgetTitle--error">
      <?= __('Server configuration error', 'webp-converter-for-media'); ?>
    </h3>
    <div class="webpContent webpContent--wide"><?= implode(PHP_EOL, $errors); ?></div>
  </div>
<?php endif; ?>