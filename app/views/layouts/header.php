<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/logo/logo1.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=20260926.15">
    <?php if (!empty($_SESSION['user_role'])): ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo htmlspecialchars($_SESSION['user_role']); ?>.css">
    <?php endif; ?>
</head>
<?php
$currentRoute = trim($_GET['url'] ?? 'home');
$isAuthPage = in_array($currentRoute, ['auth/login', 'register', 'register/student', 'register/faculty'], true);
function isNavActive($route, $currentRoute)
{
    if ($route === 'home' && in_array($currentRoute, ['home', 'home/index'], true)) {
        return true;
    }

    return $currentRoute === $route || strpos($currentRoute, $route . '/') === 0;
}
?>
<body class="<?php echo !empty($_SESSION['user_role']) ? 'has-sidebar role-' . htmlspecialchars($_SESSION['user_role']) : ''; ?>">
<?php if (empty($_SESSION['user_role']) && !$isAuthPage): ?>
<header class="app-topnav">
  <div class="topnav-left">
    <a class="brand-link" href="<?php echo BASE_URL; ?>" style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
      <img src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo" style="width: 28px; height: 28px; object-fit: contain; border-radius: 8px; display: block;">
      <span>LibAI</span>
    </a>
  </div>
  <nav class="topnav-menu">
    <a href="<?php echo BASE_URL; ?>">Home</a>
    <a href="<?php echo BASE_URL; ?>#features">Features</a>
    <a href="<?php echo BASE_URL; ?>#about">About</a>
    <a href="<?php echo BASE_URL; ?>#contact">Contact</a>
  </nav>
  <div class="topnav-actions">
    <a class="btn btn-outline-primary" href="<?php echo e(rtrim(BASE_URL, '/') . '/?url=auth/login'); ?>" target="_self">Login</a>
    <a class="btn btn-outline-primary" href="<?php echo e(rtrim(BASE_URL, '/') . '/?url=register'); ?>" target="_self">Register</a>
  </div>
</header>
<?php elseif (!empty($_SESSION['user_role'])): ?>
<?php
  $dashboardRole = $_SESSION['user_role'] ?? '';
  $dashboardName = trim((string) ($_SESSION['user_name'] ?? ''));
  $dashboardName = $dashboardName !== '' ? $dashboardName : 'there';
  $dashboardInitials = getNameInitials($dashboardName);
  $dashboardProfilePicture = getProfilePictureUrl($_SESSION['user_profile_picture'] ?? '');
  $dashboardTitle = $dashboardRole === 'admin' ? 'Admin Dashboard' : ($dashboardRole === 'librarian' ? 'Librarian Dashboard' : ($dashboardRole === 'student' ? 'Student Dashboard' : 'Faculty Dashboard'));
?>
<div class="mobile-dashboard-bar">
  <button class="sidebar-toggle" type="button" aria-label="Toggle navigation">
    <img src="<?php echo BASE_URL; ?>/menu/MENU.png" alt="Menu" />
  </button>
  <div class="mobile-dashboard-identity" aria-label="LibAI dashboard identity">
    <div class="mobile-dashboard-brand">
      <img src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo" />
      <span>LibAI</span>
    </div>
    <div class="mobile-dashboard-welcome">
      <span>Welcome back, <?php echo htmlspecialchars($dashboardName); ?></span>
      <strong><?php echo htmlspecialchars($dashboardTitle); ?></strong>
    </div>
  </div>
</div>
<nav class="app-sidebar">
  <div class="sidebar-header">
    <div class="sidebar-brand-row">
      <a class="brand-link" href="<?php echo BASE_URL; ?>" style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
        <img src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo" style="width: 28px; height: 28px; object-fit: contain; border-radius: 8px; display: block;">
        <span>LibAI</span>
      </a>
    </div>
  </div>
  <div class="sidebar-menu">
    <?php $role = $_SESSION['user_role']; ?>
    <?php if ($role === 'admin'): ?>
      <a class="sidebar-link <?php echo isNavActive('home', $currentRoute) ? 'active' : ''; ?>" href="?url=home">Dashboard</a>
      <a class="sidebar-link <?php echo isNavActive('book', $currentRoute) ? 'active' : ''; ?>" href="?url=book/index">Catalog</a>
      <a class="sidebar-link <?php echo isNavActive('inventory', $currentRoute) ? 'active' : ''; ?>" href="?url=inventory/index">Inventory</a>
      <a class="sidebar-link <?php echo isNavActive('borrow', $currentRoute) ? 'active' : ''; ?>" href="?url=borrow/index">Borrow</a>
      <a class="sidebar-link <?php echo isNavActive('borrow/return', $currentRoute) ? 'active' : ''; ?>" href="?url=borrow/return">Return</a>
      <a class="sidebar-link <?php echo isNavActive('reservation', $currentRoute) ? 'active' : ''; ?>" href="?url=reservation/index">Reservations</a>
      <a class="sidebar-link <?php echo isNavActive('members', $currentRoute) ? 'active' : ''; ?>" href="?url=members/index">Members</a>
      <a class="sidebar-link <?php echo isNavActive('reports', $currentRoute) ? 'active' : ''; ?>" href="?url=reports">Reports</a>
      <a class="sidebar-link <?php echo isNavActive('admin/librarians', $currentRoute) ? 'active' : ''; ?>" href="?url=admin/librarians">Librarians</a>
      <a class="sidebar-link <?php echo isNavActive('admin/announcement', $currentRoute) ? 'active' : ''; ?>" href="?url=admin/announcement">Announcements</a>
      <a class="sidebar-link <?php echo isNavActive('admincategories', $currentRoute) ? 'active' : ''; ?>" href="?url=admincategories/index">Categories</a>
      <a class="sidebar-link <?php echo isNavActive('adminauthors', $currentRoute) ? 'active' : ''; ?>" href="?url=adminauthors/index">Authors</a>
      <a class="sidebar-link <?php echo isNavActive('adminpublishers', $currentRoute) ? 'active' : ''; ?>" href="?url=adminpublishers/index">Publishers</a>
      <a class="sidebar-link <?php echo isNavActive('adminactivity', $currentRoute) ? 'active' : ''; ?>" href="?url=adminactivity/index">Activity Logs</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link sidebar-profile-link <?php echo $currentRoute === 'profile' ? 'active' : ''; ?>" href="?url=profile"><?php if ($dashboardProfilePicture !== ''): ?><img class="dashboard-profile-avatar" src="<?php echo e($dashboardProfilePicture); ?>" alt=""><?php else: ?><span class="dashboard-profile-initials"><?php echo e($dashboardInitials); ?></span><?php endif; ?><span>Profile</span></a>
      <a class="sidebar-link dashboard-logout-link" href="?url=auth/logout">Logout</a>
    <?php elseif ($role === 'student'): ?>
      <a class="sidebar-link <?php echo isNavActive('home', $currentRoute) ? 'active' : ''; ?>" href="?url=home">Dashboard</a>
      <a class="sidebar-link <?php echo isNavActive('book', $currentRoute) ? 'active' : ''; ?>" href="?url=book/index">Catalog</a>
      <a class="sidebar-link <?php echo isNavActive('profile/history', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/history">My Borrowed Books</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link sidebar-profile-link <?php echo $currentRoute === 'profile' ? 'active' : ''; ?>" href="?url=profile"><?php if ($dashboardProfilePicture !== ''): ?><img class="dashboard-profile-avatar" src="<?php echo e($dashboardProfilePicture); ?>" alt=""><?php else: ?><span class="dashboard-profile-initials"><?php echo e($dashboardInitials); ?></span><?php endif; ?><span>Profile</span></a>
      <a class="sidebar-link dashboard-logout-link" href="?url=auth/logout">Logout</a>
    <?php elseif ($role === 'faculty'): ?>
      <a class="sidebar-link <?php echo isNavActive('home', $currentRoute) ? 'active' : ''; ?>" href="?url=home">Dashboard</a>
      <a class="sidebar-link <?php echo isNavActive('book', $currentRoute) ? 'active' : ''; ?>" href="?url=book/index">Catalog</a>
      <a class="sidebar-link <?php echo isNavActive('profile/history', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/history">My Borrowed Books</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link sidebar-profile-link <?php echo $currentRoute === 'profile' ? 'active' : ''; ?>" href="?url=profile"><?php if ($dashboardProfilePicture !== ''): ?><img class="dashboard-profile-avatar" src="<?php echo e($dashboardProfilePicture); ?>" alt=""><?php else: ?><span class="dashboard-profile-initials"><?php echo e($dashboardInitials); ?></span><?php endif; ?><span>Profile</span></a>
      <a class="sidebar-link dashboard-logout-link" href="?url=auth/logout">Logout</a>
    <?php elseif ($role === 'librarian'): ?>
      <a class="sidebar-link <?php echo isNavActive('home', $currentRoute) ? 'active' : ''; ?>" href="?url=home">Dashboard</a>
      <a class="sidebar-link <?php echo isNavActive('book', $currentRoute) ? 'active' : ''; ?>" href="?url=book/index">Catalog</a>
      <a class="sidebar-link <?php echo isNavActive('inventory', $currentRoute) ? 'active' : ''; ?>" href="?url=inventory/index">Inventory</a>
      <a class="sidebar-link <?php echo isNavActive('borrow', $currentRoute) ? 'active' : ''; ?>" href="?url=borrow/index">Borrow</a>
      <a class="sidebar-link <?php echo isNavActive('borrow/return', $currentRoute) ? 'active' : ''; ?>" href="?url=borrow/return">Return</a>
      <a class="sidebar-link <?php echo isNavActive('reservation', $currentRoute) ? 'active' : ''; ?>" href="?url=reservation/index">Reservations</a>
      <a class="sidebar-link <?php echo isNavActive('members', $currentRoute) ? 'active' : ''; ?>" href="?url=members/index">Members</a>
      <a class="sidebar-link <?php echo isNavActive('reports', $currentRoute) ? 'active' : ''; ?>" href="?url=reports">Reports</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link sidebar-profile-link <?php echo $currentRoute === 'profile' ? 'active' : ''; ?>" href="?url=profile"><?php if ($dashboardProfilePicture !== ''): ?><img class="dashboard-profile-avatar" src="<?php echo e($dashboardProfilePicture); ?>" alt=""><?php else: ?><span class="dashboard-profile-initials"><?php echo e($dashboardInitials); ?></span><?php endif; ?><span>Profile</span></a>
      <a class="sidebar-link dashboard-logout-link" href="?url=auth/logout">Logout</a>
    <?php endif; ?>
  </div>
</nav>
<div class="sidebar-overlay" aria-hidden="true"></div>
<?php endif; ?>
<main class="container py-4 page-content">
