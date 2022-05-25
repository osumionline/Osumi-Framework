<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


<?php if ($values['error']!=0): ?>
<?php if ($values['error']==1): ?>
  <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_ERROR') ?>


      <?php echo $values['colors']->getColoredString('ofw plugins remove email', 'light_green') ?>
<?php endif ?>
<?php if ($values['error']==2): ?>
  <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_NOT_INSTALLED') ?>


  <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_CHECK_LIST') ?>

      <?php echo $values['colors']->getColoredString('ofw plugins list', 'light_green') ?>
<?php endif ?>
<?php if ($values['error']==3): ?>
  <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_FOLDER_NOT_FOUND', [$values['plugin_path']]) ?>
<?php endif ?>
<?php else: ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_CONF_REMOVED', [
	  $values['colors']->getColoredString($values['plugin_path'].'/'.$values['plugin_name'].'.json', 'light_green')
  ]) ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_PLUGIN_REMOVED', [
	  $values['colors']->getColoredString($values['plugin_path'].'/'.$values['plugin_file_name'], 'light_green')
  ]) ?>

<?php if (count($values['deps'])>0): ?>
  <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_REMOVING_DEPS') ?>

<?php foreach ($values['deps'] as $dep): ?>
    <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_DEP_REMOVED', [
	  $values['colors']->getColoredString($dep, 'light_green')
  ]) ?>

<?php endforeach ?>
    <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_DEP_FOLDER_REMOVED', [
	  $values['colors']->getColoredString($values['dep_path'], 'light_green')
  ]) ?>

<?php endif ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_FOLDER_REMOVED', [
	  $values['colors']->getColoredString($values['plugin_path'], 'light_green')
  ]) ?>

  <?php echo OTools::getMessage('TASK_PLUGINS_REMOVE_DONE') ?>
<?php endif ?>
