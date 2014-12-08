<p class="text-loud">
	<?php
	echo ___("Sorry, :project is abandoned.", [
		'project' => $this->Html->link(
			$project->name,
			$project->source,
			['class' => 'highlight hightlight-' . $project->state]
		)
	]);
	?>
</p>

<p><?= $project->description ?></p>

<div class="alert alert-danger">
	<?php
	echo ___(
		"This project is dead and :user is not looking for a new maintainer",
		['user' => $this->Html->link($project->username, '/' . $project->username)]
	);
	?>
</div>
