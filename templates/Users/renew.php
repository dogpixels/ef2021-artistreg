<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users form content">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Recover Lost Password') ?></legend>
        <?php
            echo $this->Form->hidden('email', ['value' => $email]);
            echo $this->Form->control('email_disabled', ['label' => 'eMail', 'value' => $email, 'disabled' => true]);
            echo $this->Form->control('password', ['label' => 'New Password', 'required' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>