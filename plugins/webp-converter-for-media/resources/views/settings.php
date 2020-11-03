<?php
  $path    = sprintf('%s&_wpnonce=%s', menu_page_url('webpc_admin_page', false), wp_create_nonce('webpc-save'));
  $options = apply_filters('webpc_get_options', []);
  $values  = apply_filters('webpc_get_values',  []);
?>
<div class="wrap">
  <h1><?= __('WebP Converter for Media', 'webp-converter-for-media'); ?></h1>
  <form method="post" action="<?= $path; ?>" class="webpPage">
    <div class="webpPage__inner">
      <ul class="webpPage__columns">
        <li class="webpPage__column webpPage__column--large">
          <?php if ($_POST) : ?>
            <div class="webpPage__alert"><?= __('Changes were successfully saved!', 'webp-converter-for-media'); ?></div>
          <?php endif; ?>
          <?php
            if (isset($_GET['action']) && ($_GET['action'] === 'server')) {
              include WEBPC_PATH . '/resources/components/widgets/server.php';
            } else {
              include WEBPC_PATH . '/resources/components/widgets/errors.php';
              include WEBPC_PATH . '/resources/components/widgets/options.php';
              include WEBPC_PATH . '/resources/components/widgets/regenerate.php';
            }
          ?>
        </li>
        <li class="webpPage__column webpPage__column--small">
          <?php
            include WEBPC_PATH . '/resources/components/widgets/about.php';
            include WEBPC_PATH . '/resources/components/widgets/support.php';
            include WEBPC_PATH . '/resources/components/widgets/donate.php';
          ?>
        </li>
      </ul>
    </div>
  </form>
</div>