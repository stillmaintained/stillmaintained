<?php $this->assign('title', ___(":name by :username", $project->toArray())); ?>

<div class="block-breather">
	<?= $this->element('Project/' . $project->state) ?>
</div>

<?php
echo $this->Html->link(
	___("Show other projects by :username", $this->request->params),
	'/' . $this->request->params['username'],
	['class' => 'btn btn-primary']
);

echo $this->element('instructions', compact('project'));
