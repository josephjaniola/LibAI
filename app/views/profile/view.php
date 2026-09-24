<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h4>Profile</h4>
        <?php if (!empty($user['profile_picture'])): ?>
          <img src="<?php echo BASE_URL . '/' . e($user['profile_picture']); ?>" class="img-thumbnail mb-3" style="max-width:150px;">
        <?php endif; ?>
        <p><strong>Name:</strong> <?php echo e($user['firstname'] . ' ' . ($user['middlename'] ?? '') . ' ' . $user['lastname']); ?></p>
        <p><strong>Email:</strong> <?php echo e($user['email']); ?></p>
        <p><strong>Mobile:</strong> <?php echo e($user['mobile']); ?></p>
        <p><a href="?url=profile/edit" class="btn btn-sm btn-primary">Edit Profile</a></p>
      </div>
    </div>
  </div>
</div>
