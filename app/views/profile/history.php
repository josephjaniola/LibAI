<?php
$borrowedCount = 0;
$overdueCount = 0;
foreach ($borrowHistory ?? [] as $historyItem) {
    if (($historyItem['status'] ?? '') === 'overdue') {
        $overdueCount++;
    }
    if (in_array($historyItem['status'] ?? '', ['borrowed', 'overdue'], true)) {
        $borrowedCount++;
    }
}
$reservationCount = count($reservations ?? []);
?>

<section class="borrow-history-page">
  <div class="borrow-history-hero">
    <div class="borrow-history-avatar"><i class="fa-solid fa-book-open-reader"></i></div>
    <div>
      <span class="borrow-history-kicker">LibAI library activity</span>
      <h1>My Borrowed Books</h1>
      <p>Active loans, due dates, and reservations for your account.</p>
    </div>
    <a href="?url=profile" class="borrow-history-back"><i class="fa-solid fa-arrow-left"></i> Back to profile</a>
  </div>

  <div class="borrow-history-stats">
    <div class="borrow-stat-card borrow-stat-blue">
      <span class="borrow-stat-icon"><i class="fa-solid fa-book"></i></span>
      <div><span class="borrow-stat-label">Active books</span><strong><?php echo $borrowedCount; ?></strong></div>
    </div>
    <div class="borrow-stat-card borrow-stat-red">
      <span class="borrow-stat-icon"><i class="fa-solid fa-clock"></i></span>
      <div><span class="borrow-stat-label">Overdue</span><strong><?php echo $overdueCount; ?></strong></div>
    </div>
    <div class="borrow-stat-card borrow-stat-gold">
      <span class="borrow-stat-icon"><i class="fa-solid fa-bookmark"></i></span>
      <div><span class="borrow-stat-label">Reservations</span><strong><?php echo $reservationCount; ?></strong></div>
    </div>
  </div>

  <div class="borrow-history-grid">
    <section class="borrow-history-panel borrow-history-loans">
      <div class="borrow-panel-heading">
        <div><span class="borrow-panel-eyebrow">Checked out</span><h2>Borrowed books</h2></div>
        <span class="borrow-panel-count"><?php echo count($borrowHistory ?? []); ?> total</span>
      </div>
      <div class="borrow-table-wrap">
        <table class="borrow-history-table">
          <thead><tr><th>Book</th><th>Borrowed</th><th>Due date</th><th>Status</th></tr></thead>
          <tbody>
            <?php if (!empty($borrowHistory)): ?>
              <?php foreach ($borrowHistory as $item): ?>
                <?php $status = strtolower((string) ($item['status'] ?? 'unknown')); ?>
                <tr>
                  <td><div class="borrow-book-title"><span class="borrow-book-icon"><i class="fa-solid fa-book"></i></span><strong><?php echo e($item['book_title'] ?? ('Book #' . ($item['book_id'] ?? 'N/A'))); ?></strong></div></td>
                  <td><?php echo e($item['borrow_date']); ?></td>
                  <td><?php echo e($item['due_date'] ?? '-'); ?></td>
                  <td><span class="borrow-status borrow-status-<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4"><div class="borrow-empty-state"><i class="fa-solid fa-book-open"></i><strong>No borrowed books yet</strong><span>Your active loans will appear here.</span></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="borrow-history-panel borrow-history-reservations">
      <div class="borrow-panel-heading">
        <div><span class="borrow-panel-eyebrow">Coming up</span><h2>Reservations</h2></div>
        <span class="borrow-panel-count"><?php echo $reservationCount; ?> total</span>
      </div>
      <div class="borrow-table-wrap">
        <table class="borrow-history-table">
          <thead><tr><th>Title</th><th>Reserved</th><th>Expires</th><th>Status</th><th></th></tr></thead>
          <tbody>
            <?php if (!empty($reservations)): ?>
              <?php foreach ($reservations as $reservation): ?>
                <?php $reservationStatus = strtolower((string) ($reservation['status'] ?? 'unknown')); ?>
                <tr>
                  <td><strong><?php echo e($reservation['title']); ?></strong></td>
                  <td><?php echo e($reservation['reserved_at']); ?></td>
                  <td><?php echo e($reservation['expires_at'] ?? '-'); ?></td>
                  <td><span class="borrow-status borrow-status-<?php echo e($reservationStatus); ?>"><?php echo e(ucfirst($reservationStatus)); ?></span></td>
                  <td><?php if (in_array($reservationStatus, ['pending', 'approved', 'ready'], true)): ?><a href="?url=reservation/cancel/<?php echo (int) $reservation['id']; ?>" class="borrow-cancel-link" onclick="return confirm('Cancel this reservation?');" aria-label="Cancel reservation" title="Cancel reservation"><i class="fa-solid fa-xmark"></i></a><?php else: ?><span class="text-muted">-</span><?php endif; ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5"><div class="borrow-empty-state"><i class="fa-solid fa-bookmark"></i><strong>No reservations yet</strong><span>Reserved books will appear here.</span></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</section>
