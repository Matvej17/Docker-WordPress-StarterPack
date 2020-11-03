<p>
  <?= sprintf(
    __('The REST API on your website is not available. Please verify this and try again. Pay special attention to the filters: %s, %s and %s.', 'webp-converter-for-media'),
    '<a href="https://developer.wordpress.org/reference/hooks/rest_enabled/" target="_blank">rest_enabled</a>',
    '<a href="https://developer.wordpress.org/reference/hooks/rest_jsonp_enabled/" target="_blank">rest_jsonp_enabled</a>',
    '<a href="https://developer.wordpress.org/reference/hooks/rest_authentication_errors/" target="_blank">rest_authentication_errors</a>'
  ); ?>
</p>