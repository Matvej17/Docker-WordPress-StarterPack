<p>
  <?= sprintf(
    __('The path for saving converted WebP files does not exist and cannot be created %s(function is_writable() returns false)%s. Use filters %s or %s to set the correct path. The current using path is: %s. Please read the plugin FAQ to learn more.', 'webp-converter-for-media'),
    '<em>',
    '</em>',
    '<strong>webpc_uploads_root</strong>',
    '<strong>webpc_uploads_webp</strong>',
    '<strong>' . apply_filters('webpc_uploads_webp', '') . '</strong>'
  ); ?>
</p>