<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


<?php if ($values['error']!=0): ?>
<?php if ($values['error']==1): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODULE_ERROR') ?>


      <?php echo $values['colors']->getColoredString('php ofw.php add module api', 'light_green') ?>


<?php endif ?>
<?php if ($values['error']==2): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODULE_EXISTS', [
		$values['colors']->getColoredString($values['module_file'], 'light_green')
	]) ?>



<?php endif ?>
<?php else: ?>
	<?php echo OTools::getMessage('TASK_ADD_MODULE_NEW_MODULE', [
  	  $values['colors']->getColoredString($values['module_name'], 'light_green')
    ]) ?>

	  <?php echo OTools::getMessage('TASK_ADD_MODULE_NEW_FOLDER', [
  	  $values['colors']->getColoredString($values['module_path'], 'light_green')
    ]) ?>

	  <?php echo OTools::getMessage('TASK_ADD_MODULE_NEW_FOLDER', [
  	  $values['colors']->getColoredString($values['module_templates'], 'light_green')
    ]) ?>

	  <?php echo OTools::getMessage('TASK_ADD_MODULE_NEW_FILE', [
  	  $values['colors']->getColoredString($values['module_file'], 'light_green')
    ]) ?>


<?php endif ?>