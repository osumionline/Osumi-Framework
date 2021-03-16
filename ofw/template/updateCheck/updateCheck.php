<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


  <?php echo OTools::getMessage('TASK_UPDATE_CHECK_INSTALLED_VERSION', [
	  $values['colors']->getColoredString($values['current_version'], 'light_green')
  ]) ?>

  <?php echo OTools::getMessage('TASK_UPDATE_CHECK_CURRENT_VERSION', [
	  $values['colors']->getColoredString($values['repo_version'], 'light_green')
  ]) ?>


<?php if ($values['check']==0): ?>
  <?php echo $values['colors']->getColoredString(OTools::getMessage('TASK_UPDATE_CHECK_UPDATED'), 'light_green') ?>
<?php endif ?>
<?php if ($values['check']==1): ?>
  <?php echo $values['colors']->getColoredString(OTools::getMessage('TASK_UPDATE_CHECK_NEWER'), 'white', 'red') ?>
<?php endif ?>
<?php if ($values['check']==-1): ?>
  <?php echo OTools::getMessage('TASK_UPDATE_CHECK_LIST') ?>

<?php echo $values['messages'] ?>

  <?php echo OTools::getMessage('TASK_UPDATE_CHECK_DO_UPDATE') ?>

      <?php echo $values['colors']->getColoredString('php ofw.php update', 'light_green') ?>
<?php endif ?>


