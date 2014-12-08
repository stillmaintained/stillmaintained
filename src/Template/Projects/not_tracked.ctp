<div class="block-breather">
	<p class="text-loud">
		<?= ___("Oh no! :username hasn't added that project yet!", $this->request->params) ?>
	</p>
</div>

<?php
echo $this->Html->link(
	___("Show other projects by :username", $this->request->params),
	'/' . $this->request->params['username'],
	['class' => 'btn btn-primary btn-lg']
);
