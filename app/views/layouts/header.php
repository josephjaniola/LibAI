<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($title) ? $title . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p0x0S1uYq5bX7c1xOKpF2kwN1Vnl3N9Lv+hzQ2HXzbDJmE7CZXVbzl+8Le5EHXb0EIN6VC9i7um2Gol8Qg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <?php if (!empty($_SESSION['user_role'])): ?>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo htmlspecialchars($_SESSION['user_role']); ?>.css">
    <?php endif; ?>
</head>
<?php
$currentRoute = trim($_GET['url'] ?? 'home');
function isNavActive($route, $currentRoute)
{
    if ($route === 'home' && in_array($currentRoute, ['home', 'home/index'], true)) {
        return true;
    }

    return $currentRoute === $route || strpos($currentRoute, $route . '/') === 0;
}
?>
<body class="<?php echo !empty($_SESSION['user_role']) ? 'has-sidebar role-' . htmlspecialchars($_SESSION['user_role']) : ''; ?>">
<?php if (empty($_SESSION['user_role'])): ?>
<header class="app-topnav">
  <div class="topnav-left">
    <a class="brand-link" href="<?php echo BASE_URL; ?>">LibAI</a>
  </div>
  <nav class="topnav-menu">
    <a href="<?php echo BASE_URL; ?>">Home</a>
    <a href="<?php echo BASE_URL; ?>#features">Features</a>
    <a href="<?php echo BASE_URL; ?>#about">About</a>
    <a href="<?php echo BASE_URL; ?>#contact">Contact</a>
  </nav>
  <div class="topnav-actions">
    <a class="btn btn-outline-primary" href="?url=auth/login">Login</a>
    <a class="btn btn-outline-primary" href="?url=register">Register</a>
  </div>
</header>
<?php else: ?>
<nav class="app-sidebar">
  <div class="sidebar-header">
    <div class="sidebar-brand-row">
      <a class="brand-link" href="<?php echo BASE_URL; ?>">LibAI</a>
      <button class="sidebar-toggle" type="button" aria-label="Toggle navigation">
        <i class="fa-solid fa-bars"></i>
      </button>
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
      <a class="sidebar-link <?php echo isNavActive('admincategories', $currentRoute) ? 'active' : ''; ?>" href="?url=admincategories/index">Categories</a>
      <a class="sidebar-link <?php echo isNavActive('adminauthors', $currentRoute) ? 'active' : ''; ?>" href="?url=adminauthors/index">Authors</a>
      <a class="sidebar-link <?php echo isNavActive('adminpublishers', $currentRoute) ? 'active' : ''; ?>" href="?url=adminpublishers/index">Publishers</a>
      <a class="sidebar-link <?php echo isNavActive('adminactivity', $currentRoute) ? 'active' : ''; ?>" href="?url=adminactivity/index">Activity Logs</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile', $currentRoute) ? 'active' : ''; ?>" href="?url=profile">Profile</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link" href="?url=auth/logout">Logout</a>
    <?php elseif ($role === 'student'): ?>
      <a class="sidebar-link <?php echo isNavActive('home', $currentRoute) ? 'active' : ''; ?>" href="?url=home">Dashboard</a>
      <a class="sidebar-link <?php echo isNavActive('book', $currentRoute) ? 'active' : ''; ?>" href="?url=book/index">Catalog</a>
      <a class="sidebar-link <?php echo isNavActive('profile/history', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/history">My Borrowed Books</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile', $currentRoute) ? 'active' : ''; ?>" href="?url=profile">Profile</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link" href="?url=auth/logout">Logout</a>
    <?php elseif ($role === 'faculty'): ?>
      <a class="sidebar-link <?php echo isNavActive('home', $currentRoute) ? 'active' : ''; ?>" href="?url=home">Dashboard</a>
      <a class="sidebar-link <?php echo isNavActive('book', $currentRoute) ? 'active' : ''; ?>" href="?url=book/index">Catalog</a>
      <a class="sidebar-link <?php echo isNavActive('profile/favorites', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/favorites">Reading Lists</a>
      <a class="sidebar-link <?php echo isNavActive('profile/history', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/history">Borrowed Books</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile', $currentRoute) ? 'active' : ''; ?>" href="?url=profile">Profile</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link" href="?url=auth/logout">Logout</a>
    <?php elseif ($role === 'librarian'): ?>
      <a class="sidebar-link <?php echo isNavActive('home', $currentRoute) ? 'active' : ''; ?>" href="?url=home">Dashboard</a>
      <a class="sidebar-link <?php echo isNavActive('book', $currentRoute) ? 'active' : ''; ?>" href="?url=book/index">Catalog</a>
      <a class="sidebar-link <?php echo isNavActive('inventory', $currentRoute) ? 'active' : ''; ?>" href="?url=inventory/index">Inventory</a>
      <a class="sidebar-link <?php echo isNavActive('borrow', $currentRoute) ? 'active' : ''; ?>" href="?url=borrow/index">Borrow</a>
      <a class="sidebar-link <?php echo isNavActive('borrow/return', $currentRoute) ? 'active' : ''; ?>" href="?url=borrow/return">Return</a>
      <a class="sidebar-link <?php echo isNavActive('reservation', $currentRoute) ? 'active' : ''; ?>" href="?url=reservation/index">Reservations</a>
      <a class="sidebar-link <?php echo isNavActive('members', $currentRoute) ? 'active' : ''; ?>" href="?url=members/index">Members</a>
      <a class="sidebar-link <?php echo isNavActive('reports', $currentRoute) ? 'active' : ''; ?>" href="?url=reports">Reports</a>
      <a class="sidebar-link <?php echo isNavActive('profile', $currentRoute) ? 'active' : ''; ?>" href="?url=profile">Profile</a>
      <a class="sidebar-link <?php echo isNavActive('profile/notifications', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/notifications">Notifications</a>
      <a class="sidebar-link <?php echo isNavActive('profile/edit', $currentRoute) ? 'active' : ''; ?>" href="?url=profile/edit">Settings</a>
      <a class="sidebar-link" href="?url=auth/logout">Logout</a>
    <?php endif; ?>
  </div>
</nav>
<div class="sidebar-overlay" aria-hidden="true"></div>
<?php endif; ?>
<main class="container py-4 page-content">
