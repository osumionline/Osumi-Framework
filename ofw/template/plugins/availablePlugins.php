<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


  <?php echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_TITLE') ?>


<?php foreach ($values['list'] as $plugin): ?>
  · <?php echo $values['colors']->getColoredString($plugin['name'], 'light_green') ?> (<?php echo $plugin['version'] ?>): <?php echo $plugin['description'] ?>

<?php endforeach ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_INSTALL') ?>

      <?php echo $values['colors']->getColoredString('php ofw.php plugins install ('.OTools::getMessage('TASK_PLUGINS_AVAILABLE_NAME').')', 'light_green') ?>


  <?php echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_LIST') ?>

      <?php echo $values['colors']->getColoredString('php ofw.php plugins list', 'light_green') ?>


  <?php echo OTools::getMessage('TASK_PLUGINS_AVAILABLE_DELETE') ?>

      <?php echo $values['colors']->getColoredString('php ofw.php plugins remove ('.OTools::getMessage('TASK_PLUGINS_AVAILABLE_NAME').')', 'light_green') ?>


