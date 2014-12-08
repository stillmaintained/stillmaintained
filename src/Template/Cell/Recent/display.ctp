<?php
foreach ($recent as $k => $project) {
	$recent[$k] = $this->Html->link(
		$project->fullname,
		$project->link
	);
}

echo implode(', ', $recent);
