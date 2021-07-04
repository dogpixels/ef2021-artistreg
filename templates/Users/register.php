<section>
	<h1>Register</h1>
	<?= $this->Form->create() ?>
	<fieldset>
		<?= $this->Form->control('email', ['label' => 'eMail', 'class' => 'uk-input', 'placeholder' => 'Email', 'label' => false]) ?>
		<?= $this->Form->control('email2', ['label' => 'repeat eMail', 'class' => 'uk-input', 'placeholder' => 'Repeat Email', 'label' => false]) ?>
		<?= $this->Form->control('password', ['class' => 'uk-input', 'placeholder' => 'password', 'label' => false]) ?>
		<?= $this->Form->submit('register', ['class' => 'uk-button uk-button-primary']) ?>
	</fieldset>
	<?= $this->Form->end() ?>
</section>