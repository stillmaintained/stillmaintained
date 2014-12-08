<?php $this->assign('title', __('Home')); ?>

<p class="text-loud">
    <?php
    $link = function($text, $state) {
        $url = ['controller' => 'Projects', 'action' => 'index', 'state' => $state];
        $opts = ['class' => 'highlight highlight-' . $state];
        return $this->Html->link($text, $url, $opts);
    };
    echo ___(
        "Finally a place where to check if the packages you are " .
        "using are still :maintained, :looking for a maintainer or " .
        ":abandoned.",
        [
            'maintained' => $link(__("maintained"), 'maintained'),
            'looking' => $link(__("searching"), 'searching'),
            'abandoned' => $link(__("abandoned"), 'abandoned'),
        ]
    );
    ?>
</p>

<?= $this->element('search') ?>

<hr>

<div class="row">
    <div class="col-md-6">
        <div id="recent">
            <strong class="text-success"><?= __("Most recent") ?>:</strong>
            <?= $this->cell('Recent') ?>
        </div>
    </div>
    <div class="col-md-6">
        <div id="popular">
            <strong class="text-success"><?= __("Most popular") ?>:</strong>
            <?= $this->cell('Popular') ?>
        </div>
    </div>
</div>
