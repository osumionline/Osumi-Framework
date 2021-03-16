<?php use OsumiFramework\OFW\Tools\OTools; ?>

<?php if (!$values['hasDB']): ?>

  <?php echo $values['colors']->getColoredString(OTools::getMessage('TASK_BACKUP_DB_NO_DB'), 'white', 'red') ?>

<?php else: ?>
<?php if (!$values['from_all']): ?>

  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>

<?php endif ?>

  <?php echo OTools::getMessage('TASK_BACKUP_DB_EXPORTING', [
				$values['colors']->getColoredString($values['db_name'], 'light_green'),
				$values['colors']->getColoredString($values['dump_file'], 'light_green')
			]);
?>

<?php if ($values['dump_exists']): ?>
    <?php echo OTools::getMessage('TASK_BACKUP_DB_EXISTS') ?>

<?php endif ?>
<?php if ($values['success']): ?>
  <?php echo OTools::getMessage('TASK_BACKUP_DB_SUCCESS') ?>
<?php endif ?>
<?php endif ?>


