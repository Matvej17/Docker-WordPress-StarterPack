<p>
  <?= sprintf(
    __('On your server installed GD or Imagick library %sdoes not support WebP format%s. Please read %sthe plugin FAQ%s and check your server configuration %shere%s. Compare it with the configuration given in the requirements of plugin in the FAQ. Please contact your server administrator.', 'webp-converter-for-media'),
    '<strong>',
    '</strong>',
    '<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
    '</a>',
    '<a href="' . sprintf('%s&action=server', menu_page_url('webpc_admin_page', false)) . '">',
    '</a>'
  ); ?>
</p>