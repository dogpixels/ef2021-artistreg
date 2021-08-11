<section>
	<h1>Overview</h1>
	<h5 class="uk-text-right">
		<a href="../artists" target="_blank"><span uk-icon="cog"></span>Public API</a> |
		<a href="logout"><span uk-icon="sign-out"></span>Log out</a>
	</h5>

	<div class="admin-stat-0">0 disabled</div>
	<div class="admin-stat-1">1 enabled</div>
	<div class="admin-stat-2">2 admin</div>

	<hr />

	<table class="uk-table" id="admin">
		<tbody>
			<?php foreach ($users as $user) { ?>
			<tr>
				<td class="admin-stat-<?= $user->level ?>"><?= $user->id ?></td>
				<td><?= $user->email ?></td>
				<td><a href="<?= $enableurl ?>/<?= $user->id ?>/0">disable</a></td>
				<td><a href="<?= $enableurl ?>/<?= $user->id ?>/1">enable</a></td>
				<td><a href="<?= $enableurl ?>/<?= $user->id ?>/2">make admin</a></td>
				<td><a href="<?= $editurl ?>/<?= $user->id ?>">edit</a></td>
				<td><a href="<?= $deleteurl ?>/<?= $user->id ?>" onclick="return confirm('Are you sure?')">delete</a></td>
			</tr>

			<?php } // foreach $users ?>
		</tbody>
	</table>
</section>