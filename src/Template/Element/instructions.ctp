<?php
use \Cake\Utility\String;
$uri = String::insert('https\://:host/:username/:name', ['host' => $_SERVER['HTTP_HOST']] + $project->toArray());

$tpl = [
	'image' => String::insert(':uri.svg', compact('uri')),
	'markdown' => String::insert('[![Project Status](:uri.svg)](:uri)', compact('uri')),
	'textile' => String::insert('"!:uri.svg!"::uri', compact('uri')),
	'rdoc' => String::insert('{&lt;img src=":uri.svg" /&gt;}[:uri]', compact('uri')),
	'json_request' => String::insert(':uri.json', compact('uri')),
	'json_response' => json_encode(['status' => $project['state'], 'project' => $project->toArray()], JSON_PRETTY_PRINT)
];
?>

<hr>

<div class="block-breather">

	<h3><?= __('Usage') ?></h3>

	<p>
		<?= __("Embed the status badge into your project's README or general documentation.") ?>
	</p>

	<p class="input-group">
		<span class="input-group-btn"><button class="btn btn-info"><?= __('Image') ?>:</button></span>
		<span class="form-control"><?= $tpl['image'] ?></span>
	</p>

	<p class="input-group">
		<span class="input-group-btn"><button class="btn btn-info"><?= __('Markdown') ?>:</button></span>
		<span class="form-control"><?= $tpl['markdown'] ?></span>
	</p>

	<p class="input-group">
		<span class="input-group-btn"><button class="btn btn-info"><?= __('Textile') ?>:</button></span>
		<span class="form-control"><?= $tpl['textile'] ?></span>
	</p>

	<p class="input-group">
		<span class="input-group-btn"><button class="btn btn-info"><?= __('RDoc') ?>:</button></span>
		<span class="form-control"><?= $tpl['rdoc'] ?></span>
	</p>

	<p>
		<em>This project's badge:</em>
		<img alt="<?= __('Still Maintained?') ?>" src="<?= $tpl['image'] ?>" />
	</p>

	<p>
		<?= __("Call the JSON API") ?>
	</p>

	<p class="input-group">
		<span class="input-group-btn"><button class="btn btn-info"><?= __('Request') ?>:</button></span>
		<span class="form-control"><?= $tpl['json_request'] ?></span>
	</p>

	<p>
		<em>This project's JSON response:</em>
		<textarea class="form-control" rows="10"><?= $tpl['json_response'] ?></textarea>
	</p>

</div>
