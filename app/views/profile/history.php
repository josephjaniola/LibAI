<div class="row">
  <div class="col-12 mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h3>Borrowing History</h3>
        <p class="text-muted">A record of all checkout and reservation activity for your account.</p>
      </div>
      <a href="?url=profile" class="btn btn-secondary">Back to Profile</a>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header"><strong>Borrowed Books</strong></div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
          <thead>
            <tr><th>Book</th><th>Borrow Date</th><th>Due Date</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($borrowHistory)): ?>
              <?php foreach ($borrowHistory as $item): ?>
                <tr>
                  <td><?php echo e($item['book_title'] ?? ('Book #' . ($item['book_id'] ?? 'N/A'))); ?></td>
                  <td><?php echo e($item['borrow_date']); ?></td>
                  <td><?php echo e($item['due_date'] ?? '-'); ?></td>
                  <td><?php echo e(ucfirst($item['status'])); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center">No borrow history available.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header"><strong>Reservations</strong></div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
          <thead>
            <tr><th>Title</th><th>Reserved At</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($reservations)): ?>
              <?php foreach ($reservations as $reservation): ?>
                <tr>
                  <td><?php echo e($reservation['title']); ?></td>
                  <td><?php echo e($reservation['reserved_at']); ?></td>
                  <td><?php echo e(ucfirst($reservation['status'])); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center">No reservation history available.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
