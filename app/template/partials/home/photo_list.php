<?php if (count($values['list'])>0): ?>
  <ul>
<?php foreach ($values['list'] as $photo): ?>
    <li>
      <strong>Foto <?php echo $photo ?></strong>
      <p>
        Tags: <?php echo implode(', ', $photo->getTags()) ?>
      </p>
    </li>
<?php endforeach ?>
  </ul>
<?php else: ?>
  El usuario todavía no tiene ninguna foto.
<?php endif ?>