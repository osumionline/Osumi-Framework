<?php use OsumiFramework\OFW\Tools\OTools; ?>


  <?php echo $values['colors']->getColoredString('Osumi Framework', 'white', 'blue') ?>


<?php if ($values['error']!=0): ?>
<?php if ($values['error']==1): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_ERROR') ?>


      <?php echo $values['colors']->getColoredString('php ofw.php add modelComponent User', 'light_green') ?>


<?php endif ?>
<?php if ($values['error']==2): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_NO_MODEL', [
$values['colors']->getColoredString($values['model_name'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==3): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FOLDER_EXISTS', [
$values['colors']->getColoredString($values['list_folder'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==4): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FILE_EXISTS', [
$values['colors']->getColoredString($values['list_folder'].$values['list_file'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==5): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FOLDER_EXISTS', [
$values['colors']->getColoredString($values['component_folder'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==6): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FILE_EXISTS', [
$values['colors']->getColoredString($values['component_folder'].$values['component_file'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==7): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FOLDER_CANT_CREATE', [
$values['colors']->getColoredString($values['list_folder'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==8): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FOLDER_CANT_CREATE', [
$values['colors']->getColoredString($values['component_folder'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==9): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FILE_CANT_CREATE', [
$values['colors']->getColoredString($values['list_folder'].$values['list_file'], 'light_green')
]) ?>



<?php endif ?>
<?php if ($values['error']==10): ?>
    <?php echo $values['colors']->getColoredString('ERROR', 'red') ?>: <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FILE_CANT_CREATE', [
$values['colors']->getColoredString($values['component_folder'].$values['component_file'], 'light_green')
]) ?>



<?php endif ?>
<?php else: ?>
    <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FOLDER_CREATED', [
$values['colors']->getColoredString($values['list_folder'], 'light_green')
]) ?>

    <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FILE_CREATED', [
$values['colors']->getColoredString($values['list_folder'].$values['list_file'], 'light_green')
]) ?>

    <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FOLDER_CREATED', [
$values['colors']->getColoredString($values['component_folder'], 'light_green')
]) ?>

    <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_FILE_CREATED', [
$values['colors']->getColoredString($values['component_folder'].$values['component_file'], 'light_green')
]) ?>


    <?php echo OTools::getMessage('TASK_ADD_MODEL_COMPONENT_USE', [
$values['colors']->getColoredString(strtolower($values['model_name']), 'light_green')
]) ?>


      <?php echo $values['colors']->getColoredString('$'.'this->getTemplate()->addComponent(\'...\', \'model/'.strtolower($values['model_name']).'\', []);', 'light_green') ?>


<?php endif ?>