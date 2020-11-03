<p>
  <?= sprintf(
    __('Redirects on your server are not working. Check the correct configuration for you in %sthe plugin FAQ%s. If your configuration is correct, it means that your server does not support redirects from the .htaccess file or requests to images are processed by your server bypassing Apache. %sIn this case, please contact your server administrator.', 'webp-converter-for-media'),
    '<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
    '</a>',
    '<br><br>'
  ); ?>
</p>