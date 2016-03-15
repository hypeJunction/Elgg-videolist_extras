<?php

$entity = elgg_extract('entity', $vars);

?>
<div>
	<label><?php echo elgg_echo('videolist:extras:domains') ?></label>
	<?php
	echo elgg_view('input/plaintext', array(
		'name' => 'params[domains]',
		'value' => $entity->domains,
	));
	?>
</div>