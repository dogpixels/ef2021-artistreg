<section>
	<h1>Password Recovery</h1>
	<div uk-alert class="uk-alert-primary">
		<p>Welcome back <strong><?= $email ?></strong>, please enter a new password.</p>
	</div>
    <?= $this->Form->create() ?>
    <fieldset>
		<?= $this->Form->hidden('email', ['value' => $email]) ?>
		<?= $this->Form->control('password', ['class' => 'uk-input', 'label' => false, 'placeholder' => 'New Password', 'required' => true]) ?>			
    </fieldset>
    <?= $this->Form->submit('submit', ['class' => 'uk-button uk-button-primary']) ?>
    <?= $this->Form->end() ?>
</section>