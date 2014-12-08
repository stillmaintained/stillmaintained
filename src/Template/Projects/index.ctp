<?php
if (!empty($this->request->params['username'])) {
    $title = ":count :element by :username";
    $title = ___($title, $this->request->params + compact('count') + ['element' => __n("project", "projects", $count)]);
    printf('<h1 class="block-breather">%s</h1>', $title);
    $this->assign('title', $title);
} else {
    echo $this->element('search');
}

if (empty($projects)) {
    printf(___("Your search ':terms' returned no results.", ['terms' => $this->request->query['q']]));
    return;
}

$this->Html->script('masonry/masonry.pkgd.min.js', ['block' => true]);
$script =
<<<JAVASCRIPT
$(document).ready(function() {
    var projectsContainer = $('#projects');
    projectsContainer.masonry({
        itemSelector: '.masonry-item'
    });
});
JAVASCRIPT
;
$this->Html->scriptBlock($script, ['block' => true]);
?>

<section id="projects">
    <?php foreach ($projects as $project) : ?>
        <div class="masonry-item<?= $project->full_name_length > 20 ? ' masonry-item-lg' : null ?>">
            <div class="result <?= $project->state ?>">
                <?php
                $userUrl = '/' . $project->username;
                $projectUrl = $userUrl . '/' . $project->name;
                echo sprintf(
                    '<strong class="name">%s/%s</strong>',
                    $this->Html->link($project->username, $userUrl),
                    $this->Html->link($project->name, $projectUrl)
                );
                ?>
                <p class="description"><?= $project->description ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</section>
