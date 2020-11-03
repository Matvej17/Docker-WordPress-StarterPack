<?php
  $pageUrl = menu_page_url('webpc_admin_page', false);
  $info    = apply_filters('webpc_server_info', '');
?>
<div class="webpPage__widget">
  <h3 class="webpPage__widgetTitle webpPage__widgetTitle--second">
    <?= __('Your server configuration', 'webp-converter-for-media'); ?>
  </h3>
  <div class="webpContent">
    <div class="webpPage__widgetRow">
      <p>
        <?= sprintf(__('Please compare your configuration with the configuration that is given in the technical requirements in %sthe plugin FAQ%s. If your server does not meet the technical requirements, please contact your server Administrator.', 'webp-converter-for-media'),
        '<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
        '</a>'
      ); ?>
      </p>
      <a href="<?= $pageUrl; ?>" class="webpLoader__button webpButton webpButton--blue">
        <?= __('Back to settings', 'webp-converter-for-media'); ?>
      </a>
    </div>
    <div class="webpPage__widgetRow">
      <div class="webpServerInfo"><?= $info; ?></div>
    </div>
    <div class="webpPage__widgetRow">
      <a href="<?= $pageUrl; ?>" class="webpLoader__button webpButton webpButton--blue">
        <?= __('Back to settings', 'webp-converter-for-media'); ?>
      </a>
    </div>
  </div>
</div>