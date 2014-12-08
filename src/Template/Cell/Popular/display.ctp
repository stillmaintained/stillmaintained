<?php
foreach ($popular as $k => $project) {
	$popular[$k] = $this->Html->link(
		$project->fullname,
		$project->link
	);
}

echo implode(', ', $popular);
