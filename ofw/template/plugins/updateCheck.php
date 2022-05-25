<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


<?php if ($values['error']!=0): ?>
<?php if ($values['error']==1): ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_NO_PLUGINS') ?>
<?php endif ?>
<?php else: ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_CHECKING') ?>

<?php foreach ($values['plugins'] as $plugin_update): ?>

  · <?php echo $values['colors']->getColoredString($plugin_update['plugin']->getName(), 'light_green') ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_VERSION', [
	  $plugin_update['plugin']->getVersion()
  ]) ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_CURRENT_VERSION', [
	  $plugin_update['repo_version']
  ]) ?>
<?php if ($plugin_update['update']): ?>

    <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_AVAILABLE') ?>
<?php endif ?>

<?php endforeach ?>
<?php if ($values['updates']): ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECK_UPDATE') ?>

    <?php echo $values['colors']->getColoredString('ofw plugins update', 'light_green') ?>

<?php endif ?>
<?php endif ?>
