<div class="notice notice-success">
  <div class="webpContent webpContent--notice">
    <h4>
      <?= __('Thank you for installing our plugin WebP Converter for Media!', 'webp-converter-for-media'); ?>
    </h4>
    <p>
      <?= sprintf(
        __('Would you like to speed up your website using our plugin? %sGo to plugin settings and convert all your images to WebP with one click! Thank you for being with us! %s', 'webp-converter-for-media'),
        '<br>',
        '<span class="dashicons dashicons-heart"></span>'
      ); ?>
    </p>
    <div class="webpContent__buttons">
      <a href="<?= menu_page_url('webpc_admin_page', false); ?>"
        class="webpContent__button webpButton webpButton--green">
        <?= __('Speed up my website', 'webp-converter-for-media'); ?>
      </a>
    </div>
  </div>
</div>