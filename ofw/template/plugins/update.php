
  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


<?php if ($values['error']!=0): ?>
<?php if ($values['error']==1): ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_NO_PLUGINS') ?>
<?php endif ?>
<?php else: ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CHECKING') ?>


<?php foreach ($values['plugins'] as $plugin_update): ?>
  · <?php echo $values['colors']->getColoredString($plugin_update['plugin']->getName(), 'light_green') ?>

    <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_INSTALLED_VERSION', [
	  $values['colors']->getColoredString($plugin_update['plugin']->getVersion(), 'light_green')
  ]) ?>

    <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_CURRENT_VERSION', [
	  $values['colors']->getColoredString($plugin_update['repo_version'], 'light_green')
  ]) ?>
<?php if ($plugin_update['update']): ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_UPDATING') ?>
      <?php echo $plugin_update['update_message'] ?>
<?php endif ?>

<?php foreach ($plugin_update['deletes'] as $plugin_update_delete): ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_TO_BE_DELETED', [
	  $values['colors']->getColoredString($plugin_update_delete['delete'], 'light_green')
  ]) ?>
<?php if ($plugin_update_delete['error']): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_FILE_NOT_FOUND', [
		$values['colors']->getColoredString($plugin_update_delete['file'], 'light_green')
  ]) ?>
<?php else: ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_TO_BE_DELETED', [
	  $values['colors']->getColoredString($plugin_update_delete['delete'], 'light_green')
  ]) ?>
<?php endif ?>
<?php endforeach ?>

<?php foreach ($plugin_update['files'] as $plugin_update_file): ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_DOWNLOADING', [
	  $values['colors']->getColoredString($plugin_update_file['url'], 'light_green')
  ]) ?>

<?php if ($plugin_update_file['exists']): ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_FILE_EXISTS') ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_FILE_UPDATED') ?>

<?php else: ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_NEW_FILE') ?>
<?php endif ?>
<?php endforeach ?>
<?php if ($plugin_update['update']): ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_VERSION_UPDATED') ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_UPDATE_DONE') ?>


<?php endif ?>
<?php endforeach ?>
<?php endif ?>
