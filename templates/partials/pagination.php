<?php
declare(strict_types=1);

use function App\Core\url;

/**
 * @var array $pagination Pagination data (page, total_pages)
 * @var string|null $search Search query
 */

$page = $pagination['page'];
$totalPages = $pagination['total_pages'];
$search = $search ?? null;

function pageUrl(int $page, ?string $search): string {
    $params = ['page' => $page];
    if ($search) {
        $params['q'] = $search;
    }
    return '/' . ($page > 1 ? '?' . http_build_query($params) : ($search ? '?' . http_build_query(['q' => $search]) : ''));
}
?>
<div class="pagination">
  <?php if ($page > 1): ?>
    <a href="<?= e(pageUrl($page - 1, $search)) ?>">← Назад</a>
  <?php else: ?>
    <span class="disabled">← Назад</span>
  <?php endif; ?>

  <?php
  $start = max(1, $page - 2);
  $end = min($totalPages, $page + 2);
  
  if ($start > 1): ?>
    <a href="<?= e(pageUrl(1, $search)) ?>">1</a>
    <?php if ($start > 2): ?><span>...</span><?php endif; ?>
  <?php endif; ?>

  <?php for ($i = $start; $i <= $end; $i++): ?>
    <?php if ($i === $page): ?>
      <span class="current"><?= $i ?></span>
    <?php else: ?>
      <a href="<?= e(pageUrl($i, $search)) ?>"><?= $i ?></a>
    <?php endif; ?>
  <?php endfor; ?>

  <?php if ($end < $totalPages): ?>
    <?php if ($end < $totalPages - 1): ?><span>...</span><?php endif; ?>
    <a href="<?= e(pageUrl($totalPages, $search)) ?>"><?= $totalPages ?></a>
  <?php endif; ?>

  <?php if ($page < $totalPages): ?>
    <a href="<?= e(pageUrl($page + 1, $search)) ?>">Вперёд →</a>
  <?php else: ?>
    <span class="disabled">Вперёд →</span>
  <?php endif; ?>
</div>
