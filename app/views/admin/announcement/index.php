<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-3">Homepage Announcement</h4>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($announcement)): ?>
          <div class="alert alert-info mb-4">
            <strong>Current active message:</strong> <?php echo e($announcement['title'] ?? 'Admin Announcement'); ?>
          </div>
        <?php endif; ?>

        <?php
        $announcementSeconds = (int) ($announcement['home_tv_duration_seconds'] ?? 30);
        $announcementDurationValue = $announcementSeconds >= 3600 ? (int) floor($announcementSeconds / 3600) : ($announcementSeconds >= 60 ? (int) floor($announcementSeconds / 60) : $announcementSeconds);
        $announcementDurationUnit = $announcementSeconds >= 3600 ? 'hours' : ($announcementSeconds >= 60 ? 'minutes' : 'seconds');
        ?>

        <form method="post" action="?url=admin/announcement" enctype="multipart/form-data">
          <input type="hidden" name="_csrf" value="<?php echo e(generate_csrf_token()); ?>">

          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo e($announcement['home_tv_title'] ?? 'Admin Announcement'); ?>" placeholder="Admin Announcement">
          </div>

          <div class="mb-3">
            <label class="form-label">Announcement message</label>
            <textarea name="message" class="form-control" rows="6" placeholder="Write what the admin wants to show on the homepage TV panel"><?php echo e($announcement['home_tv_message'] ?? ''); ?></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Display time</label>
              <input type="number" name="duration_value" class="form-control" min="1" max="999" value="<?php echo e((string) $announcementDurationValue); ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Unit</label>
              <select name="duration_unit" class="form-select" required>
                <option value="seconds" <?php echo $announcementDurationUnit === 'seconds' ? 'selected' : ''; ?>>Seconds</option>
                <option value="minutes" <?php echo $announcementDurationUnit === 'minutes' ? 'selected' : ''; ?>>Minutes</option>
                <option value="hours" <?php echo $announcementDurationUnit === 'hours' ? 'selected' : ''; ?>>Hours</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Library image for the homepage screen</label>
            <input type="file" name="announcement_image" class="form-control" accept="image/*">
            <?php if (!empty($announcement['home_tv_image'])): ?>
              <div class="mt-2">
                <img src="<?php echo e(BASE_URL . '/' . $announcement['home_tv_image']); ?>" alt="Current TV image" style="max-width: 220px; max-height: 140px; object-fit: cover; border-radius: 10px;">
              </div>
            <?php endif; ?>
          </div>

          <div class="alert alert-light border mb-3">
            This announcement will automatically disappear after the time you set and then return the library gallery.
          </div>

          <button type="submit" class="btn btn-primary">Publish to Homepage</button>
        </form>
      </div>
    </div>
  </div>
</div>
