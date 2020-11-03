<p>
  <?= sprintf(
    __('Requests to images are processed by your server bypassing Apache. When loading images, rules from the .htaccess file are not executed. Occasionally, this only applies to known file extensions: .jpg, .png, etc. and when e.g. .png2 extension is loaded, then the redirections from the .htaccess file work, because the server does not understand this format and does not treat it as image files. Check the redirects for %s.png file%s %s(for which the redirection does not work)%s and for %s.png2 file%s %s(for which the redirection works correctly)%s. %sChange the server settings to stop ignoring the rules from the .htaccess file. Please contact your server administrator.', 'webp-converter-for-media'),
    '<a href="' . WEBPC_URL . 'public/img/debug/icon-before.png" target="_blank">',
    '</a>',
    '<em>',
    '</em>',
    '<a href="' . WEBPC_URL . 'public/img/debug/icon-before.png2" target="_blank">',
    '</a>',
    '<em>',
    '</em>',
    '<br><br>'
  ); ?>
</p>