<?php
declare(strict_types=1);

use function App\Core\e;
?>
<div class="grid">
  <div class="card">
    <div class="profile-header">
      <div class="profile-avatar">
        <?= mb_strtoupper(mb_substr($profile['first_name'], 0, 1)) ?>
      </div>
      <div>
        <h1 class="h1 mt-0"><?= e($profile['first_name'] . ' ' . $profile['last_name']) ?></h1>
        <div class="muted"><?= e($profile['email']) ?></div>
      </div>
    </div>

    <div class="profile-stats">
      <div class="stat">
        <div class="stat-value"><?= $topic_count ?></div>
        <div class="stat-label">Тем создано</div>
      </div>
      <div class="stat">
        <div class="stat-value"><?= $comment_count ?></div>
        <div class="stat-label">Комментариев</div>
      </div>
      <div class="stat">
        <div class="stat-value"><?= e(date('d.m.Y', strtotime($profile['created_at']))) ?></div>
        <div class="stat-label">Дата регистрации</div>
      </div>
    </div>

    <?php if ($is_owner): ?>
      <div class="actions mt-3">
        <a class="btn primary" href="/profile/edit">Редактировать профиль</a>
      </div>
    <?php endif; ?>
  </div>
</div>
