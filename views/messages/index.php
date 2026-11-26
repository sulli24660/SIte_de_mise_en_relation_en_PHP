<h1>Messagerie</h1>

<div class="messaging">
    <aside class="conversations" aria-label="Mes conversations">
        <?php if ($conversations === []): ?>
            <p>Aucune conversation pour l'instant. Écrivez à un membre depuis la page d'un livre ou son profil.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($conversations as $conversation): ?>
                    <li class="<?= $partner?->getId() === $conversation->partnerId ? 'is-active' : '' ?>">
                        <a href="index.php?action=messages&with=<?= $conversation->partnerId ?>">
                            <strong><?= htmlspecialchars($conversation->partnerPseudo) ?></strong>
                            <time datetime="<?= $conversation->lastMessageAt->format('Y-m-d H:i') ?>"><?= $conversation->lastMessageAt->format('d.m H:i') ?></time>
                            <span><?= htmlspecialchars(mb_strimwidth($conversation->lastMessage, 0, 40, '…')) ?></span>
                            <?php if ($conversation->unreadCount > 0): ?>
                                <span class="badge-count" aria-label="<?= $conversation->unreadCount ?> non lu(s)"><?= $conversation->unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </aside>

    <section class="thread" aria-label="Conversation">
        <?php if ($partner === null): ?>
            <p>Sélectionnez une conversation.</p>
        <?php else: ?>
            <h2><a href="index.php?action=profile&id=<?= $partner->getId() ?>"><?= htmlspecialchars($partner->getPseudo()) ?></a></h2>

            <div class="thread-messages">
                <?php if ($messages === []): ?>
                    <p>Commencez la conversation avec <?= htmlspecialchars($partner->getPseudo()) ?>.</p>
                <?php endif; ?>

                <?php foreach ($messages as $message): ?>
                    <article class="message <?= $message->getSenderId() === $userId ? 'is-mine' : 'is-theirs' ?>">
                        <time datetime="<?= $message->getCreatedAt()->format('Y-m-d H:i') ?>"><?= $message->getCreatedAt()->format('d.m H:i') ?></time>
                        <p><?= nl2br(htmlspecialchars($message->getContent())) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>

            <form action="index.php?action=messages&with=<?= $partner->getId() ?>" method="post" class="message-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <label for="content" class="visually-hidden">Votre message</label>
                <textarea id="content" name="content" rows="3" maxlength="2000" placeholder="Tapez votre message ici" required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
                <?php if ($errors->has('content')): ?>
                    <p class="form-error"><?= htmlspecialchars($errors->get('content')) ?></p>
                <?php endif; ?>
                <?php if ($errors->has('form')): ?>
                    <p class="form-error" role="alert"><?= htmlspecialchars($errors->get('form')) ?></p>
                <?php endif; ?>
                <button type="submit">Envoyer</button>
            </form>
        <?php endif; ?>
    </section>
</div>
