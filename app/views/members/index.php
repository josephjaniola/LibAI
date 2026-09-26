<div class="members-page row">
  <div class="col-12 mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <h3>Members</h3>
      <span class="text-muted">Viewing student and faculty accounts</span>
    </div>
    <p class="text-muted">This page is available to librarians and administrators for quick borrower lookup and account review.</p>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <strong>Students</strong>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
          <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($students)): ?>
              <?php foreach ($students as $user): ?>
                <tr>
                  <td><?php echo e($user['student_id']); ?></td>
                  <td><?php echo e($user['firstname'] . ' ' . ($user['lastname'] ?? '')); ?></td>
                  <td><?php echo e($user['email']); ?></td>
                  <td><?php echo e($user['status'] ?? 'active'); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center">No students found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <strong>Faculty</strong>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
          <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($faculty)): ?>
              <?php foreach ($faculty as $user): ?>
                <tr>
                  <td><?php echo e($user['faculty_id']); ?></td>
                  <td><?php echo e($user['firstname'] . ' ' . ($user['lastname'] ?? '')); ?></td>
                  <td><?php echo e($user['email']); ?></td>
                  <td><?php echo e($user['department'] ?? 'N/A'); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center">No faculty accounts found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
