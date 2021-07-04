<section>
	<h1>Password Recovery</h1>
	<div uk-alert class="uk-alert-primary">
		<p>Enter the eMail address that you registered with and you'll receive an automated eMail with a link to change your password.<br />If that recovery eMail doesn't seem to arrive within 24 hours, check your spam folder and contact tech support.</p>
	</div>
    <?= $this->Form->create() ?>
    <fieldset>
        <?= $this->Form->control('email', ['label' => 'eMail', 'class' => 'uk-input', 'placeholder' => 'Email', 'label' => false]) ?>
    </fieldset>
    <?= $this->Form->submit('submit', ['class' => 'uk-button uk-button-primary']) ?>
    <?= $this->Form->end() ?>
</section>