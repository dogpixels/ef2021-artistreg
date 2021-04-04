<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $subject
 */
?>
<div class="users form content">
    <?= $this->Form->create($subject) ?>
    <fieldset>
        <legend><?= __('Editing') ?>: <?= $subject->email ?></legend>
        <?php
            echo $this->Form->control('data');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
