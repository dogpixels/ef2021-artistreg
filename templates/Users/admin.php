<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

?>

 <?php foreach ($users as $user) { ?>
	<article>
		<h3><?= $user->email ?> (level <?= $user->level ?>)</h3>
		<a href="<?= $enableurl ?>/<?= $user->id ?>/1">enable</a> | 
		<a href="<?= $enableurl ?>/<?= $user->id ?>/2">make admin</a> | 
		<a href="<?= $editurl ?>/<?= $user->id ?>">edit</a> | 
		<a href="<?= $deleteurl ?>/<?= $user->id ?>">delete</a>
	</article>
 <?php } // foreach $users ?>
	 
