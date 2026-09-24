<div class="row">
  <div class="col-12 mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h3>Notifications</h3>
        <p class="text-muted">Recent alerts and updates for your account.</p>
      </div>
      <a href="?url=profile" class="btn btn-secondary">Back to Profile</a>
    </div>
  </div>

  <div class="col-12">
    <div class="list-group">
      <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $note): ?>
          <div class="list-group-item<?php echo $note['is_read'] ? '' : ' list-group-item-warning'; ?>">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="mb-1"><?php echo e($note['title']); ?></h5>
                <p class="mb-1"><?php echo e($note['message']); ?></p>
              </div>
              <small class="text-muted"><?php echo e(date('M j, Y h:i A', strtotime($note['created_at']))); ?></small>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="list-group-item text-center text-muted">You have no notifications at this time.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
