<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h4>Profile</h4>
        <?php if (!empty($user['profile_picture'])): ?>
          <img src="<?php echo BASE_URL . '/' . e($user['profile_picture']); ?>" class="img-thumbnail mb-3" style="max-width:150px;">
        <?php endif; ?>
        <p><strong>Name:</strong> <?php echo e($user['firstname'] . ' ' . ($user['middlename'] ?? '') . ' ' . $user['lastname']); ?></p>
        <?php if (($role ?? '') === 'student'): ?>
          <p><strong>Student ID:</strong> <?php echo e($user['student_id'] ?? ''); ?></p>
          <p><strong>Course:</strong> <?php echo e($user['course'] ?? ''); ?></p>
          <p><strong>Year Level:</strong> <?php echo e($user['year_level'] ?? ''); ?></p>
        <?php elseif (($role ?? '') === 'faculty'): ?>
          <p><strong>Faculty ID:</strong> <?php echo e($user['faculty_id'] ?? ''); ?></p>
          <p><strong>Department:</strong> <?php echo e($user['department'] ?? ''); ?></p>
          <p><strong>Position:</strong> <?php echo e($user['position'] ?? ''); ?></p>
        <?php endif; ?>
        <p><strong>Email:</strong> <?php echo e($user['email']); ?></p>
        <p><strong>Mobile:</strong> <?php echo e($user['mobile']); ?></p>
        <p><a href="?url=profile/edit" class="btn btn-sm btn-primary">Edit Profile</a></p>
      </div>
    </div>
  </div>
</div>
