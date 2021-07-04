<section>
	<h1>Login</h1>
	<div uk-grid>
		<div class="uk-width-1-3">
			<fieldset>
				<?= $this->Form->create() ?>
				<?= $this->Form->control('email', ['required' => true, 'class' => 'uk-input', 'placeholder' => 'Email', 'label' => false]) ?>
				<?= $this->Form->control('password', ['required' => true, 'class' => 'uk-input', 'placeholder' => 'Password', 'label' => false]) ?>
				<?= $this->Form->submit('Login', ['class' => 'uk-button uk-button-primary']) ?>
				<?= $this->Form->end() ?>
			</fieldset>
			<?= $this->Html->link("Register", ['action' => 'register']) ?> | 
			<?= $this->Html->link("Lost Password", ['action' => 'recover']) ?>
		</div>
		<div uk-alert class="uk-alert-warning uk-width-expand">
			<h3>Notice</h3>
			<p>This area is for artists and dealers to sign up to be published on the Eurofurence Online 2021 website. This is <strong>not</strong> 
			a registration for a convention, Dealers' Den or Art Show.</p>
			<p>Registrations will not be carried over to the next year. We reserve the right to exclude registrations without further notice.</p>
		</div>
	</div>
</section>