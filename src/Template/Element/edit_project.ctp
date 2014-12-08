<?php
$states = [
	'maintained' => __("Still maintained"),
	'searching' => __("Looking for maintainer"),
	'abandoned' => __("Abandoned"),
	'hide' => __("Don't list this project")
];
$states = [
	'maintained' => 'M',
	'searching' => 'S',
	'abandoned' => 'A',
	'hide' => 'H'
];
?>

<div class="col-lg-4 col-md-6">
	<fieldset class="result">
		<strong>
			<?= $project->name ?>
		</strong>
		<div class="input-group pull-right">
			<div class="btn-group">
				<?php foreach ($states as $state => $label) : ?>
					<a
						class="btn btn-primary btn-xs <?= ($project->state == $state) ? 'active' : 'notActive' ?>"
						data-toggle="<?= $project['name'] ?>"
						data-title="<?= $state ?>"
						title="<?= $state ?>"
					>
						<?= $label ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		echo $this->Form->hidden("{$user['username']}[{$project['name']}]", [
			'value' => $project->state,
			'id' => $project->name
		]);
		?>

	</fieldset>
</div>
