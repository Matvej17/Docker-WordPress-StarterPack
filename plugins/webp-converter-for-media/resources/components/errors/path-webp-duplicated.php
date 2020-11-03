<p>
  <?= sprintf(
    __('The paths for /uploads files and for saving converted WebP files are the same. Change them using filters %s or %s. The current path for them is: %s.', 'webp-converter-for-media'),
    '<strong>webpc_uploads_path</strong>',
    '<strong>webpc_uploads_webp</strong>',
    '<strong>' . apply_filters('webpc_uploads_path', '') . '</strong>'
  ); ?>
</p>