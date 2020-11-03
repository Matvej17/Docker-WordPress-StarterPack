<?php if ($option['info']) : ?>
  <p><?= $option['info']; ?></p>
<?php endif; ?>
<div class="webpPage__quality">
  <?php
    foreach ($option['values'] as $value => $label) :
      $isChecked = (isset($values[$option['name']]) && ($value == $values[$option['name']]));
  ?>
    <div class="webpPage__qualityItem">
      <input type="radio" name="<?= $option['name']; ?>" value="<?= $value; ?>"
        id="webpc-<?= $index; ?>-<?= $value; ?>" class="webpPage__qualityItemInput" <?= $isChecked ? 'checked' : ''; ?>
        <?= (in_array($value, $option['disabled'])) ? 'disabled' : ''; ?>>
      <label for="webpc-<?= $index; ?>-<?= $value; ?>" class="webpPage__qualityItemLabel"><?= $label; ?></label>
    </div>
  <?php endforeach; ?>
</div>