<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


<?php if ($values['error']!=0): ?>
<?php if ($values['error']==1): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_TASK_ERROR') ?>


      <?php echo $values['colors']->getColoredString('ofw add task email', 'light_green') ?>


<?php endif ?>
<?php if ($values['error']==2): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_TASK_EXISTS', [
		$values['colors']->getColoredString($values['task_file'], 'light_green')
	]) ?>



<?php endif ?>
<?php else: ?>
	<?php echo OTools::getMessage('TASK_ADD_TASK_NEW_TASK', [
  	  $values['colors']->getColoredString($values['task_name'], 'light_green')
    ]) ?>

	  <?php echo OTools::getMessage('TASK_ADD_TASK_NEW_FILE', [
  	  $values['colors']->getColoredString($values['task_file'], 'light_green')
    ]) ?>


<?php endif ?>
