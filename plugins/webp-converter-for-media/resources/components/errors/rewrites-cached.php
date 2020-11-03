<p>
  <?= sprintf(
    __('Your server uses the cache for HTTP requests. The rules from .htaccess file or from Nginx configuration are not executed every time when the image is loaded, but the last redirect from cache is performed. With each request to image, your server should execute the rules from .htaccess file or from Nginx configuration. Now it only does this the first time and then uses cache. This means that if your server redirected image to WebP format the first time, it does so on every request. It should check the rules from .htaccess file or from Nginx configuration each time during request to image and redirect only when the conditions are met. %sIf you have enabled caching HTTP reverse proxy or another HTTP caching, you must disable it. Otherwise the plugin cannot work properly. In this case, please contact your server administrator.', 'webp-converter-for-media'),
    '<br><br>'
  ); ?>
</p>