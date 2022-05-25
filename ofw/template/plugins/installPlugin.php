<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


<?php if ($values['error']!=0): ?>
<?php if ($values['error']==1): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_ERROR') ?>


      <?php echo $values['colors']->getColoredString('ofw plugins install email', 'light_green') ?>


<?php endif ?>
<?php if ($values['error']==2): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_NOT_AVAILABLE') ?>


      <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_CHECK_LIST') ?>


      <?php echo $values['colors']->getColoredString('ofw plugins', 'light_green') ?>


<?php endif ?>
<?php if ($values['error']==3): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_FOLDER_EXISTS', [$values['error_path']]) ?>


<?php endif ?>
<?php else: ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_CREATE_FOLDER', [
	  $values['colors']->getColoredString($values['plugin_path'], 'light_green')
  ]) ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_CREATE_CONFIG', [
	  $values['colors']->getColoredString($values['plugin_path'].'/'.$values['plugin_name'].'.json', 'light_green')
  ]) ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_CREATE_FILE', [
	  $values['colors']->getColoredString($values['plugin_path'].'/'.$values['plugin_file'], 'light_green')
  ]) ?>

<?php if (count($values['deps'])>0): ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_DOWNLOAD_DEPS') ?>

<?php foreach ($values['deps'] as $dep): ?>
    <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_NEW_DEP', [
		$values['colors']->getColoredString($values['plugin_path'].'/dependencies/'.$dep, 'light_green')
	]) ?>

<?php endforeach ?>
<?php endif ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_INSTALL_DONE') ?>


<?php endif ?>
