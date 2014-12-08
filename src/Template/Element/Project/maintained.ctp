<p class="text-loud">
	<?php
	echo ___("Yay! :project is still being maintained.", [
		'project' => $this->Html->link(
			$project->name,
			$project->source,
			['class' => 'highlight highlight-' . $project->state]
		)
	]);
	?>
</p>

<p><?= $project->description ?></p>
