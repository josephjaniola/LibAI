<?php $role = $_SESSION['user_role'] ?? 'admin'; ?>
<?php $displayName = trim((string) ($_SESSION['user_name'] ?? '')); ?>
<?php $displayName = $displayName !== '' ? $displayName : 'there'; ?>
<div class="dashboard-shell role-<?php echo htmlspecialchars($role); ?>">
    <div class="dashboard-header mb-4 d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
        <div>
            <p class="text-muted mb-1">Welcome back, <?php echo htmlspecialchars($displayName); ?></p>
            <h1 class="h3 mb-0">
                <?php echo $role === 'admin' ? 'Admin Dashboard' : ($role === 'librarian' ? 'Librarian Dashboard' : ($role === 'student' ? 'Student Dashboard' : 'Faculty Dashboard')); ?>
            </h1>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="?url=profile" class="btn btn-outline-primary">Profile</a>
            <a href="?url=auth/logout" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <?php if (in_array($role, ['student', 'faculty'], true) && (($stats['due_soon'] ?? 0) > 0 || ($stats['overdue_books'] ?? 0) > 0)): ?>
        <div class="alert <?php echo ($stats['overdue_books'] ?? 0) > 0 ? 'alert-danger' : 'alert-warning'; ?> d-flex justify-content-between align-items-center gap-3" role="alert">
            <div>
                <strong><?php echo ($stats['overdue_books'] ?? 0) > 0 ? 'Book return warning' : 'Due date reminder'; ?></strong>
                <div>
                    <?php if (($stats['overdue_books'] ?? 0) > 0): ?>
                        You have <?php echo (int) $stats['overdue_books']; ?> overdue book<?php echo $stats['overdue_books'] == 1 ? '' : 's'; ?>. Please return it as soon as possible.
                    <?php else: ?>
                        You have <?php echo (int) $stats['due_soon']; ?> book<?php echo $stats['due_soon'] == 1 ? '' : 's'; ?> due within 3 days.
                    <?php endif; ?>
                </div>
            </div>
            <a href="?url=profile/history" class="btn btn-sm btn-outline-dark">View Due Dates</a>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <?php if ($role === 'admin'): ?>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Total Books</span></div>
                <p class="metric-value"><?php echo e($stats['total_books'] ?? 0); ?></p>
                <p class="metric-label">Catalog size across all collections.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Available Books</span></div>
                <p class="metric-value"><?php echo e($stats['available_books'] ?? 0); ?></p>
                <p class="metric-label">Items available for checkout.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Borrowed Books</span></div>
                <p class="metric-value"><?php echo e($stats['borrowed_books'] ?? 0); ?></p>
                <p class="metric-label">Currently circulating items.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Overdue Books</span></div>
                <p class="metric-value"><?php echo e($stats['overdue_books'] ?? 0); ?></p>
                <p class="metric-label">Items waiting for return.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Unread Notifications</span></div>
                <p class="metric-value"><?php echo e($stats['unread_notifications'] ?? 0); ?></p>
                <p class="metric-label">Messages from users and system alerts.</p>
            </div>
        <?php elseif ($role === 'librarian'): ?>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Borrowed Today</span></div>
                <p class="metric-value"><?php echo e($stats['borrowed_today'] ?? 0); ?></p>
                <p class="metric-label">Loans processed today.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Returned Today</span></div>
                <p class="metric-value"><?php echo e($stats['returned_today'] ?? 0); ?></p>
                <p class="metric-label">Returns processed today.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Available Books</span></div>
                <p class="metric-value"><?php echo e($stats['available_books'] ?? 0); ?></p>
                <p class="metric-label">Items available for checkout.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Pending Reservations</span></div>
                <p class="metric-value"><?php echo e($stats['pending_reservations'] ?? 0); ?></p>
                <p class="metric-label">Reservations awaiting review.</p>
            </div>
        <?php elseif ($role === 'student'): ?>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Borrowed Books</span></div>
                <p class="metric-value"><?php echo e($stats['borrowed_books'] ?? 0); ?></p>
                <p class="metric-label">Books currently checked out.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Due Soon</span></div>
                <p class="metric-value"><?php echo e($stats['due_soon'] ?? 0); ?></p>
                <p class="metric-label">Returns due within 3 days.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Reservations</span></div>
                <p class="metric-value"><?php echo e($stats['reserved_books'] ?? 0); ?></p>
                <p class="metric-label">Active reservations.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Unread Notifications</span></div>
                <p class="metric-value"><?php echo e($stats['unread_notifications'] ?? 0); ?></p>
                <p class="metric-label">Library alerts and reminders.</p>
            </div>
        <?php else: ?>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Borrowed Books</span></div>
                <p class="metric-value"><?php echo e($stats['borrowed_books'] ?? 0); ?></p>
                <p class="metric-label">Books currently checked out.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Available Books</span></div>
                <p class="metric-value"><?php echo e($stats['available_books'] ?? 0); ?></p>
                <p class="metric-label">Books available in the catalog.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Reservations</span></div>
                <p class="metric-value"><?php echo e($stats['reserved_books'] ?? 0); ?></p>
                <p class="metric-label">Active book reservations.</p>
            </div>
            <div class="dashboard-card">
                <div class="card-header"><span class="label">Unread Notifications</span></div>
                <p class="metric-value"><?php echo e($stats['unread_notifications'] ?? 0); ?></p>
                <p class="metric-label">Library alerts and reminders.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (in_array($role, ['student', 'faculty'], true)): ?>
        <div class="mt-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div>
                        <h2 class="h5 mb-1">Reserve a Book</h2>
                        <p class="mb-0 text-muted">Browse available books and reserve the ones you want before they are taken.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="?url=book/index" class="btn btn-primary">Go to Catalog</a>
                        <a href="?url=profile/history" class="btn btn-outline-primary">My Borrowed Books</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($recommendations)): ?>
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">AI Recommendations</h2>
                <span class="text-muted small">Smart suggestions based on your activity</span>
            </div>
            <div class="row g-3">
                <?php foreach ($recommendations as $book): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <h3 class="h6 mb-1"><?php echo e($book['title'] ?? 'Untitled Book'); ?></h3>
                                        <p class="text-muted small mb-2"><?php echo e($book['category_name'] ?? 'General'); ?></p>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary"><?php echo !empty($book['status']) ? e($book['status']) : 'available'; ?></span>
                                </div>
                                <p class="mb-2 small text-secondary"><?php echo e(substr(strip_tags($book['description'] ?? ''), 0, 140)); ?><?php echo !empty($book['description']) && strlen(strip_tags($book['description'])) > 140 ? '...' : ''; ?></p>
                                <div class="d-flex gap-2 align-items-center text-muted small">
                                    <span><?php echo e($book['publisher_name'] ?? 'Unknown publisher'); ?></span>
                                    <span>•</span>
                                    <span><?php echo e($book['year_published'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
