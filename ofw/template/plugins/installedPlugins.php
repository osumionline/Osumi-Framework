
  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


  <?php echo OTools::getMessage('TASK_PLUGINS_INSTALLED') ?>

<?php if (count($values['plugins'])>0): ?>
<?php foreach ($values['plugins'] as $plugin): ?>
  · <?php echo $values['colors']->getColoredString($plugin->getName(), 'light_green') ?> (<?php echo $plugin->getVersion() ?>): <?php echo $plugin->getDescription() ?>

<?php endforeach ?>
<?php else: ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_INSTALLED_NONE') ?>

<?php endif ?>

