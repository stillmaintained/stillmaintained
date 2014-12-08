<p class="text-loud">
    <?= ___("Hi :username, here's a list of every project you started.", $user); ?>
</p>

<p>
    <?php
    echo __(
        "If you have a project you don't want to have listed, just skip it. " .
        "Also, you can always come back later and edit your project's state."
    );
    ?>
</p>

<?= $this->Form->create(null) ?>
<div class="block-breather">
    <div class="row">
        <?php
        foreach ($projects as $project) {
            echo $this->element('edit_project', compact('user', 'project'));
        }
        ?>
    </div>
</div>

<?= $this->Form->button(__("Update"), ['class' => 'btn btn-primary']) ?>

<span>
    M: <?= __("maintained") ?> | 
    S: <?= __("searching") ?> |
    A: <?= __("abandoned") ?> |
    H: <?= __("hide") ?>
</span>

<?php
echo $this->Form->end();

$scriptBlock =
<<<JAVASCRIPT
$('.btn-group a').on('click', function(){
    var sel = $(this).data('title');
    var tog = $(this).data('toggle');
    $('#'+tog).val(sel);

    $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
    $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
});
JAVASCRIPT
;

$this->Html->scriptBlock($scriptBlock, ['block' => true]);
