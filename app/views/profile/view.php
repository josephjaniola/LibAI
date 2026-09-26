<?php
  $profileRole = ucfirst((string) ($role ?? 'user'));
  $profileName = trim(preg_replace('/\s+/', ' ', (string) (($user['firstname'] ?? '') . ' ' . ($user['middlename'] ?? '') . ' ' . ($user['lastname'] ?? ''))));
  if ($profileName === '') {
      $profileName = $user['fullname'] ?? $user['username'] ?? 'Library user';
  }
  $profileInitials = getNameInitials($profileName);
  $profilePictureUrl = getProfilePictureUrl($user['profile_picture'] ?? '');
?>
<div class="profile-view-shell">
  <div class="profile-view-header">
    <div class="profile-view-avatar-wrap">
      <?php if ($profilePictureUrl !== ''): ?>
        <img src="<?php echo e($profilePictureUrl); ?>" class="profile-view-avatar-image" alt="<?php echo e($profileName); ?> profile picture" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
      <?php endif; ?>
      <div class="profile-view-avatar-fallback"<?php echo $profilePictureUrl !== '' ? ' style="display:none;"' : ''; ?>><?php echo e($profileInitials); ?></div>
    </div>
    <div class="profile-view-heading">
      <span class="profile-view-eyebrow">LibAI profile</span>
      <h1><?php echo e($profileName); ?></h1>
      <p><?php echo e($profileRole); ?> account</p>
    </div>
    <a href="?url=profile/edit" class="btn btn-primary profile-view-edit">Edit Profile</a>
  </div>

  <div class="profile-details-card">
    <div class="profile-details-heading">
      <div>
        <h2>Personal information</h2>
        <p>Your account details used by the library.</p>
      </div>
    </div>
    <div class="profile-details-grid">
      <?php if (($role ?? '') === 'student'): ?>
        <div class="profile-detail-item"><span>Student ID</span><strong><?php echo e($user['student_id'] ?? 'Not provided'); ?></strong></div>
        <div class="profile-detail-item"><span>Course</span><strong><?php echo e($user['course'] ?? 'Not provided'); ?></strong></div>
        <div class="profile-detail-item"><span>Year level</span><strong><?php echo e($user['year_level'] ?? 'Not provided'); ?></strong></div>
      <?php elseif (($role ?? '') === 'faculty'): ?>
        <div class="profile-detail-item"><span>Faculty ID</span><strong><?php echo e($user['faculty_id'] ?? 'Not provided'); ?></strong></div>
        <div class="profile-detail-item"><span>Department</span><strong><?php echo e($user['department'] ?? 'Not provided'); ?></strong></div>
        <div class="profile-detail-item"><span>Position</span><strong><?php echo e($user['position'] ?? 'Not provided'); ?></strong></div>
      <?php elseif (($role ?? '') === 'librarian'): ?>
        <div class="profile-detail-item"><span>Librarian ID</span><strong><?php echo e($user['librarian_id'] ?? 'Not provided'); ?></strong></div>
        <div class="profile-detail-item"><span>Username</span><strong><?php echo e($user['username'] ?? 'Not provided'); ?></strong></div>
      <?php elseif (($role ?? '') === 'admin'): ?>
        <div class="profile-detail-item"><span>Username</span><strong><?php echo e($user['username'] ?? 'Not provided'); ?></strong></div>
      <?php endif; ?>
      <div class="profile-detail-item"><span>Email address</span><strong><?php echo e($user['email'] ?? 'Not provided'); ?></strong></div>
      <div class="profile-detail-item"><span>Mobile number</span><strong><?php echo e($user['mobile'] ?? 'Not provided'); ?></strong></div>
    </div>
  </div>
</div>
