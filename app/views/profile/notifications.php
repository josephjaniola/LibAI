<?php
$unreadCount = 0;
foreach ($notifications ?? [] as $notification) {
    if (empty($notification['is_read'])) {
        $unreadCount++;
    }
}
?>

<section class="notifications-page profile-view-shell">
  <div class="notifications-header profile-view-header">
    <div class="notifications-avatar"><i class="fa-solid fa-bell"></i></div>
    <div class="profile-view-heading">
      <span class="profile-view-eyebrow">LibAI updates</span>
      <h1>Notifications</h1>
      <p>Recent alerts and updates for your account.</p>
    </div>
    <a href="?url=profile" class="btn btn-primary profile-view-edit">Back to Profile</a>
  </div>

  <div class="notifications-card profile-details-card">
    <div class="notifications-card-heading profile-details-heading">
      <div>
        <h2>Your notifications</h2>
        <p><?php echo count($notifications ?? []); ?> total<?php echo $unreadCount ? ' - ' . $unreadCount . ' unread' : ''; ?></p>
      </div>
      <span class="notifications-count"><i class="fa-solid fa-bell"></i> <?php echo $unreadCount; ?> new</span>
    </div>

    <div class="notifications-list">
      <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $note): ?>
          <?php $notificationType = strtolower((string) ($note['type'] ?? 'general')); ?>
          <article class="notification-item<?php echo empty($note['is_read']) ? ' notification-unread' : ''; ?>">
            <div class="notification-icon notification-icon-<?php echo e($notificationType); ?>"><i class="fa-solid fa-<?php echo $notificationType === 'warning' ? 'triangle-exclamation' : ($notificationType === 'success' ? 'circle-check' : 'bell'); ?>"></i></div>
            <div class="notification-copy">
              <div class="notification-meta"><span class="notification-label"><?php echo e(ucfirst($notificationType)); ?></span><time><?php echo e(date('M j, Y h:i A', strtotime($note['created_at']))); ?></time></div>
              <h3><?php echo e($note['title']); ?></h3>
              <p><?php echo e($note['message']); ?></p>
            </div>
            <?php if (empty($note['is_read'])): ?><span class="notification-unread-dot" aria-label="Unread notification"></span><?php endif; ?>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="notification-empty"><i class="fa-regular fa-bell-slash"></i><strong>You are all caught up</strong><span>New library alerts will appear here.</span></div>
      <?php endif; ?>
    </div>
  </div>
</section>
