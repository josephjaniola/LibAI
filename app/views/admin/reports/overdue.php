<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
      <h4 class="mb-0">Due and Overdue Records</h4>
      <a class="btn btn-sm btn-outline-secondary" href="?url=reports/index">Back to Reports</a>
    </div>
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>
    <p>These records use the exact return date and time entered by the librarian. Automatic emails are sent to each borrower from the configured admin Gmail.</p>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Borrower</th><th>Email</th><th>Book</th><th>Due date/time</th><th>Status</th><th>Email</th></tr></thead>
        <tbody>
          <?php if (empty($records)): ?>
            <tr><td colspan="6" class="text-center text-muted">No due or overdue records.</td></tr>
          <?php else: ?>
            <?php foreach ($records as $record): ?>
              <tr>
                <td><?php echo e($record['borrower_name']); ?></td>
                <td><?php echo e($record['borrower_email'] ?: 'No email'); ?></td>
                <td><?php echo e($record['book_title']); ?></td>
                <td><?php echo e($record['due_date']); ?></td>
                <td><span class="badge <?php echo $record['status'] === 'overdue' ? 'bg-danger' : 'bg-warning text-dark'; ?>"><?php echo e(ucfirst($record['status'])); ?></span></td>
                <td><?php echo !empty($record['due_notice_sent_at']) ? 'Sent' : 'Pending'; ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
