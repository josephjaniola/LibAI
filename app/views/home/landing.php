<?php
$videoFiles = array_values(array_filter(glob(__DIR__ . '/../../../video/*.mp4'), 'is_file'));
sort($videoFiles);
$heroVideos = array_map('basename', $videoFiles);
$libraryImages = [];
foreach (glob(__DIR__ . '/../../../image/*') as $imagePath) {
    if (!is_file($imagePath)) {
        continue;
    }
    $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $libraryImages[] = basename($imagePath);
    }
}
sort($libraryImages);
$db = Database::getInstance();
$settingsStmt = $db->query("SELECT `key`, `value` FROM system_settings WHERE `key` IN ('home_tv_message', 'home_tv_image', 'home_tv_until', 'home_tv_title')");
$tvSettings = [];
foreach ($settingsStmt as $row) {
    $tvSettings[$row['key']] = $row['value'];
}

$sessionAnnouncement = trim((string) ($_SESSION['tv_announcement'] ?? ''));
$sessionExpiresAt = $_SESSION['tv_announcement_expires_at'] ?? null;

if ($sessionExpiresAt !== null && (int) $sessionExpiresAt <= time()) {
    unset($_SESSION['tv_announcement'], $_SESSION['tv_announcement_expires_at']);
    $sessionAnnouncement = '';
}

$tvAnnouncement = trim((string) ($tvSettings['home_tv_message'] ?? $sessionAnnouncement));
$tvImage = trim((string) ($tvSettings['home_tv_image'] ?? ''));
$tvUntil = trim((string) ($tvSettings['home_tv_until'] ?? ''));
$tvExpiresAt = 0;

if ($tvUntil !== '') {
    $tvExpiresAt = (int) strtotime($tvUntil);
}
if ($tvExpiresAt <= 0 && !empty($_SESSION['tv_announcement_expires_at'])) {
    $tvExpiresAt = (int) $_SESSION['tv_announcement_expires_at'];
}

if ($tvExpiresAt > 0 && $tvExpiresAt <= time()) {
    $db->prepare("UPDATE system_settings SET `value` = NULL WHERE `key` IN ('home_tv_message', 'home_tv_image', 'home_tv_until', 'home_tv_title')")->execute();
    unset($_SESSION['tv_announcement'], $_SESSION['tv_announcement_expires_at']);
    $tvAnnouncement = '';
    $tvImage = '';
    $tvUntil = '';
    $tvExpiresAt = 0;
}

if ($tvAnnouncement === '' && $tvImage === '' && $sessionAnnouncement !== '') {
    $tvAnnouncement = $sessionAnnouncement;
    unset($_SESSION['tv_announcement'], $_SESSION['tv_announcement_expires_at']);
}

$showAnnouncementOverride = ($tvAnnouncement !== '' || $tvImage !== '') && ($tvExpiresAt > time() || (!empty($_SESSION['tv_announcement_expires_at']) && (int)$_SESSION['tv_announcement_expires_at'] > time()));

$renderHeroVideo = function ($videos) {
    if (empty($videos)) {
        return;
    }

    $videoList = array_map(function ($video) {
        return BASE_URL . '/video/' . $video;
    }, array_slice($videos, 0, 8));

    echo '<div class="video-bg" data-videos="' . e(json_encode($videoList)) . '">';
    foreach ($videoList as $index => $src) {
        echo '<video class="layered-video layered-video-' . ($index + 1) . '" autoplay muted loop playsinline preload="auto" src="' . e($src) . '"></video>';
    }
    echo '<div class="hero-cloud-glow"></div></div>';
};
?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.layered-video').forEach(function (video, index) {
      video.playbackRate = 0.35 + (index * 0.05);
      video.play().catch(function () {});
    });

    const tvScreen = document.querySelector('.tv-screen');
    if (tvScreen && tvScreen.dataset.expireAt) {
      const expireAt = Number(tvScreen.dataset.expireAt) * 1000;
      const remaining = Math.max(0, expireAt - Date.now());
      setTimeout(function () {
        const overridePanel = tvScreen.querySelector('.tv-override-panel');
        const galleryPanel = tvScreen.querySelector('.tv-gallery-panel');
        if (overridePanel) overridePanel.classList.remove('active');
        if (galleryPanel) galleryPanel.classList.add('active');
      }, remaining);
    }

    const previewSlides = Array.from(document.querySelectorAll('.library-preview-slide'));
    const previewDots = Array.from(document.querySelectorAll('.library-preview-dots .slider-dot'));

    let previewIndex = 0;
    if (previewSlides.length > 1) {
      setInterval(function () {
        previewIndex = (previewIndex + 1) % previewSlides.length;
        previewSlides.forEach(function (slide, index) {
          slide.classList.toggle('active', index === previewIndex);
        });
        previewDots.forEach(function (dot, index) {
          dot.classList.toggle('active', index === previewIndex);
        });
      }, 3500);
    }
  });
</script>

<section class="landing-hero py-5 video-section" id="home">
  <?php $renderHeroVideo($heroVideos); ?>
  <div class="container landing-hero-content">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-success mb-4"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger mb-4"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <div class="row align-items-center g-4 hero-layout">
      <div class="col-lg-6">
        <p class="eyebrow">Cebu Eastern College, Incorporated</p>
        <h1 class="display-5 fw-bold">LibAI an AI-powered smart library with RFID-based inventory management</h1>
        <p class="lead">A smarter library experience for Cebu Eastern College, designed to streamline borrowing, improve inventory management, and help students and faculty access resources with ease.</p>

        <div class="hero-actions">
          <a href="<?php echo e(BASE_URL . '/?url=register'); ?>" class="btn btn-primary btn-lg px-4">Explore LibAI</a>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="tv-shell" aria-label="LibAI announcement screen">
          <div class="tv-bezel">
            <div class="tv-header">
              <span class="tv-brand">LibAI TV</span>
              <span class="tv-live">LIVE</span>
            </div>
            <div class="tv-screen" data-expire-at="<?php echo e((string) ($showAnnouncementOverride ? $tvExpiresAt : 0)); ?>">
              <div class="tv-override-panel <?php echo $showAnnouncementOverride ? 'active' : ''; ?>">
                <?php if ($tvAnnouncement !== ''): ?>
                  <div class="tv-label"><?php echo e($tvSettings['home_tv_title'] ?? 'Admin Announcement'); ?></div>
                  <p><?php echo nl2br(e($tvAnnouncement)); ?></p>
                <?php elseif ($tvImage !== ''): ?>
                  <div class="tv-label">Library Preview</div>
                  <img src="<?php echo e(BASE_URL . '/' . $tvImage); ?>" alt="Library preview" class="img-fluid rounded-3 shadow-sm" style="max-height: 240px; width: 100%; object-fit: cover;">
                <?php endif; ?>
              </div>

              <div class="tv-gallery-panel <?php echo $showAnnouncementOverride ? '' : 'active'; ?>">
                <div class="tv-label">Library Preview</div>
                <div class="library-preview-slider" aria-label="Library image gallery">
                  <?php foreach ($libraryImages as $index => $image): ?>
                    <div class="library-preview-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                      <img src="<?php echo e(BASE_URL . '/image/' . $image); ?>" alt="Library preview image <?php echo $index + 1; ?>">
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="library-preview-dots">
                  <?php foreach ($libraryImages as $index => $image): ?>
                    <span class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" aria-label="Slide <?php echo $index + 1; ?>"></span>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="landing-section py-5" id="features">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Features</p>
      <h2 class="h3 mb-3">What users can do</h2>
      <p class="text-muted">LibAI provides an intelligent library experience for students, faculty, librarians, and administrators.</p>
    </div>
    <div class="row gy-4">
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>Search & browse books</h5>
          <p>Find available titles instantly and explore categories with a responsive catalog.</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>Reserve & borrow</h5>
          <p>Reserve books from your device, then borrow or return with RFID-assisted processing.</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>Receive alerts</h5>
          <p>Get due-date reminders, reservation updates, and library notifications in one place.</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card p-4 h-100">
          <h5>View recommendations</h5>
          <p>See AI-powered suggestions based on your borrowing history and academic interests.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="landing-section py-5" id="about">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">About</p>
      <h2 class="h3 mb-3">One connected system for the whole library</h2>
      <p class="text-muted">LibAI manages the complete library journey: discover a book, reserve it, borrow it with RFID, return it, and receive timely reminders before or after the librarian-set due date.</p>
    </div>
    <div class="row gy-4">
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>For students</h5>
          <p>Browse the catalog, reserve available books, view borrowing history, manage your profile, and receive in-app and Gmail due-date reminders.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>For faculty</h5>
          <p>Find research and teaching materials, reserve books, follow pickup updates, manage current loans, and receive return notifications.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>For librarians</h5>
          <p>Verify borrowers, scan RFID tags to lend and return books, set exact return dates and times, approve reservations, and manage circulation.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>For administrators</h5>
          <p>Manage books, categories, authors, publishers, librarians, members, announcements, activity logs, reports, and overdue records.</p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>Personalized recommendations</h5>
          <p>Students and faculty see recommendations based on their course or department, interests, borrowing history, and the books most often borrowed.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="landing-section py-5" id="contact">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Contact</p>
      <h2 class="h3 mb-3">Send a message to the admin</h2>
      <p class="text-muted">Use this section to request help, report issues, or ask questions about the library system.</p>
    </div>
    <div class="row gy-4">
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>What you can do</h5>
          <ul>
            <li>Ask for assistance with login or registration.</li>
            <li>Report missing or damaged books.</li>
            <li>Request reservations or book recommendations.</li>
            <li>Send general feedback to the library admin team.</li>
          </ul>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card p-4 h-100">
          <h5>Contact form</h5>
          <form action="?url=home/contact" method="post">
            <input type="hidden" name="_csrf" value="<?php echo e(generate_csrf_token()); ?>">
            <div class="mb-3">
              <label class="form-label">Your Name</label>
              <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Your Email</label>
              <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea name="message" class="form-control" rows="4" placeholder="Write your message to the admin" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

