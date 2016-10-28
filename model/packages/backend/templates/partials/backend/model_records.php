<?php foreach ($values['list'] as $i => $row): ?>
  <?php echo json_encode($row) ?>
  <?php if ($i<count($values['list'])-1): ?>,<?php endif ?>
<?php endforeach ?>