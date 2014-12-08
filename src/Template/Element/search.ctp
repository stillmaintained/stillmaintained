<?php
echo $this->Form->create(null, [
	'url' => ['controller' => 'Projects', 'action' => 'index'],
	'type' => 'GET',
	'class' => 'block-breather',
	'role' => 'form',
	'templates' => ['input' =>
		'<div class="input-group">' .
		'<input type="{{type}}" name="{{name}}"{{attrs}}>' .
		'<span class="input-group-btn">' .
		$this->Form->button(__("Search"), ['class' => 'btn btn-primary btn-lg']) .
		'</span></div><em class="text-muted">' .
		__("i.e. jadb/cakephp-monolog") .
		'</em>'
	]
]);

echo $this->Form->input('q', [
	'label' => false,
	'class' => 'form-control input-lg',
	'type' => 'search',
	'value' => !empty($this->request->query['q']) ? $this->request->query['q'] : null,
]);

echo $this->Form->end();
