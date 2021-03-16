<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


  <?php echo $values['colors']->getColoredString(OTools::getMessage('TASK_EXTRACTOR_EXPORTING'), 'light_green') ?>


<?php if ($values['file_exists']): ?>
    <?php echo OTools::getMessage('TASK_EXTRACTOR_EXISTS') ?>


<?php endif ?>
  <?php echo OTools::getMessage('TASK_EXTRACTOR_GETTING_FILES') ?>

  <?php echo OTools::getMessage('TASK_EXTRACTOR_EXPORTING_FILES', [$values['num_files']]) ?>

  <?php echo OTools::getMessage('TASK_EXTRACTOR_EXPORTING_FOLDERS', [$values['num_folders']]) ?>

  <?php echo OTools::getMessage('TASK_EXTRACTOR_GETTING_READY') ?>

  <?php echo $values['colors']->getColoredString(OTools::getMessage('TASK_EXTRACTOR_END'), 'light_green') ?>


