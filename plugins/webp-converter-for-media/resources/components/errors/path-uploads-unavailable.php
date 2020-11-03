<p>
  <?= sprintf(
    __('The path for /uploads files does not exist %s(function is_dir() returns false)%s. Use filters %s or %s to set the correct path. The current using path is: %s. Please read the plugin FAQ to learn more.', 'webp-converter-for-media'),
    '<em>',
    '</em>',
    '<strong>webpc_uploads_root</strong>',
    '<strong>webpc_uploads_path</strong>',
    '<strong>' . apply_filters('webpc_uploads_path', '') . '</strong>'
  ); ?>
</p>