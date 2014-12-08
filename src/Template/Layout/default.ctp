<?php
/**
 * Default `html` block.
 */
if (!$this->fetch('html')) {
	$this->start('html');
	printf('<html lang="%s" class="no-js">', read('App.language'));
	$this->end();
}

/**
 * Default `title` block.
 */
if (!$this->fetch('title')) {
	$this->start('title');
	echo empty($title) ? read('App.title', env('HTTP_HOST')) : $title;
	$this->end();
}

/**
 * Default `body` block.
 */
$this->prepend('bodyAttributes', ' class="' . implode(' ', array($this->request->controller, $this->request->action)) . '" ');
if (!$this->fetch('body')) {
	$this->start('body');
	echo '<body' . $this->fetch('bodyAttributes') . '>';
	$this->end();
}

/**
 * Prepend `meta` block with `author` and `favicon`.
 */
$this->prepend('meta', $this->Html->meta('author', null, array('name' => 'author', 'content' => read('App.author'))));
$this->prepend('meta', $this->Html->meta('favicon.ico', '/favicon.ico', array('type' => 'icon')));

/**
 * Prepend `css` block with TwitterBootstrap and Bootflat stylesheets and append
 * the `$html5Shim`.
 */
$html5Shim =
<<<HTML
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<script src="//oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
HTML;
$this->prepend('css', $this->AssetCompress->css('app.css'));
$this->append('css', $html5Shim);

$this->prepend('script', $this->Html->script(['jquery/jquery', 'bootstrap/bootstrap']));

?>
<!DOCTYPE html>

<?= $this->fetch('html'); ?>

	<head>

		<?= $this->Html->charset(); ?>

		<title>
			<?php if ($title = $this->fetch('title')) : ?>
				<?= $title ?> -
			<?php endif; ?>
			StillMaintained
		</title>

		<?= $this->fetch('meta'); ?>
		<?= $this->fetch('css'); ?>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-53816225-1', 'auto');
			ga('send', 'pageview');
		</script>
	</head>

	<?= $this->fetch('body'); ?>

		<div class="container">

			<header class="clearfix">
				<h1 class="pull-left">
					<a href="/"><?= __('Still Maintained?') ?></a>
				</h1>
				<?php
				$link = '#';
				$options = [
					'data-toggle' => 'modal',
					'data-target' => '#loginModal'
				];

				if (!empty($user)) {
					$link = "/{$user['username']}/edit";
					$options = [];
				}

				echo $this->Html->link(__("Manage my packages"), $link, [
					'class' => 'btn btn-success btn-lg pull-right block-breather',
				] + $options);
				?>
				<div
					class="modal modal-md fade"
					id="loginModal"
					tabindex="-1"
					role="dialog"
					aria-labelledby="loginModal"
					aria-hidden="true"
				>
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-body">
								<?php
								echo $this->Html->link(
									'<i class="fa fa-github"></i>&nbsp;&nbsp;GitHub',
									'/auth/github',
									['class' => 'btn btn-lg btn-info', 'escape' => false]
								);
								?>
								<?php
								echo $this->Form->button(
									sprintf(
										'<i class="fa fa-bitbucket"></i>&nbsp;&nbsp;Bibucket <em>(%s)</em>',
										__("coming soon")
									),
									['class' => 'btn btn-lg btn-info', 'disabled' => true]
								);
								?>
							</div>
						</div>
					</div>
				</div>
			</header>

			<?= $this->fetch('content'); ?>

		</div>

		<footer class="text-small">

			<div class="container">

				<?php
				$montreal = '<span class="icon-montreal" title="Montreal"></span>';
				$love = '<span class="glyphicon glyphicon-heart" title="%s"></span>';
				echo ___("Built in :montreal, with :love", [
					'montreal' => '<strong>Montreal</strong>' . $montreal,
					'love' => sprintf($love, __("love"))
				]);
				?>


			</div>

		</footer>

		<?= $this->fetch('script'); ?>

	</body>

</html>
