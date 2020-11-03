<?php if ($option['info']) : ?>
  <p><?= $option['info']; ?></p>
<?php endif; ?>
<table class="webpPage__widgetTable">
  <?php
    foreach ($option['values'] as $value => $label) :
      $isChecked = (isset($values[$option['name']]) && in_array($value, $values[$option['name']]));
  ?>
    <tr>
      <td>
        <input type="checkbox" name="<?= $option['name']; ?>[]" value="<?= $value; ?>"
          id="webpc-<?= $index; ?>-<?= $value; ?>" class="webpPage__checkbox" <?= $isChecked ? 'checked' : ''; ?>
          <?= (in_array($value, $option['disabled'])) ? 'disabled' : ''; ?>>
        <label for="webpc-<?= $index; ?>-<?= $value; ?>"></label>
      </td>
      <td>
        <label for="webpc-<?= $index; ?>-<?= $value; ?>" class="webpPage__checkboxLabel"><?= $label; ?></label>
      </td>
    </tr>
  <?php endforeach; ?>
</table>