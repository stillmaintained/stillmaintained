<p class="text-loud">
	<?php
	echo ___("Hey! :project is looking for a new maintainer.", [
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
	echo ___("Want to help out? Send :user a :message", [
		'user' => $this->Html->link($project->username, '/' . $project->username),
		'message' => $this->Html->link(___("message"), $project->source . '/issues')
	]);
	?>
</div>
