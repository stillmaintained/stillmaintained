<div class="block-breather">
	<p class="text-loud">
		<?= ___("Oh no! :username hasn't added any project yet!", $this->request->params) ?>
	</p>
</div>

<?php
echo $this->Html->link(
	___("Tell :username about us", $this->request->params),
	'https://github.com/' . $this->request->params['username'],
	['class' => 'btn btn-primary btn-lg']
);
