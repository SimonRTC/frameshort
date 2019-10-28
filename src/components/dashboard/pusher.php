    <div class="alert alert-<?= $G['Pusher']->GetType() ?>">
        <p class="h3"><?= $G['Pusher']->GetTypeLabel() ?></p><hr />
        <p><?= $G['Pusher']->GetMessageText() ?></p>
    </div>