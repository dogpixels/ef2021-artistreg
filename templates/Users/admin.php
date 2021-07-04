<section>
	<h1>Overview</h1>
	
	<div class="uk-card uk-card-default">lvl 0: not enabled</div>
	<div class="uk-card uk-card-primary">lvl 1: enabled</div>
	<div class="uk-card uk-card-secondary">lvl 2: admin</div>
	
	<hr />
	
	<div uk-grid>
		<?php foreach ($users as $user) { ?>
			<?php
				switch($user->level) {
					case 0: $lvlcolor = 'default'; break; // not enabled
					case 1: $lvlcolor = 'primary'; break; // enabled
					case 2: $lvlcolor = 'secondary'; break; // admin
				}
			?>
			
			<div class="uk-card uk-card-body uk-card-<?= $lvlcolor ?> uk-width-1-3">
				<h3 class="uk-card-title"><?= $user->email ?> </h3>
				<p>(level <?= $user->level ?>)</p>
				<a href="<?= $enableurl ?>/<?= $user->id ?>/1">enable</a> | 
				<a href="<?= $enableurl ?>/<?= $user->id ?>/2">make admin</a> | 
				<a href="<?= $editurl ?>/<?= $user->id ?>">edit</a> | 
				<a href="<?= $deleteurl ?>/<?= $user->id ?>" onclick="return confirm('Are you sure?')">delete</a>
			</div>
			
		<?php } // foreach $users ?>
	</div>
</section>