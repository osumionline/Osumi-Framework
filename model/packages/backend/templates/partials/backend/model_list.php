<?php foreach ($values['list'] as $i => $model): ?>
  {
    "name": "<?php echo $model->getModelName() ?>",
    "tablename": "<?php echo $model->getTableName() ?>",
    "fields": <?php echo $model->generate('json') ?>
  }<?php if ($i<count($values['list'])-1): ?>,<?php endif ?>
<?php endforeach ?>