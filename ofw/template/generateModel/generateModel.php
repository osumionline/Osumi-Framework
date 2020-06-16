
  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


  <?php echo OTools::getMessage('TASK_GENERATE_MODEL_MODEL') ?>

<?php if ($values['file_exists']): ?>

    <?php echo OTools::getMessage('TASK_GENERATE_MODEL_EXISTS') ?>

<?php endif ?>

  <?php echo OTools::getMessage('TASK_GENERATE_MODEL_GENERATED', [
	  	$values['colors']->getColoredString($values['file'], 'light_green')
	  ]) ?>


