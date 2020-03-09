<?php if (count($values['list'])>0): ?>
	<ul class="photos">
<?php foreach ($values['list'] as $photo): ?>
		<li>
			<img src="/photo/<?php echo $photo ?>">
			<p>
				Tags: <?php echo implode(', ', $photo->getTags()) ?>
			</p>
		</li>
<?php endforeach ?>
	</ul>
<?php else: ?>
	El usuario todavía no tiene ninguna foto.
<?php endif ?>