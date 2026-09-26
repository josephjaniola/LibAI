<?php
$catalogCategories = [];
foreach ($books as $catalogBook) {
    $categoryName = trim((string) ($catalogBook['category_name'] ?? ''));
    if ($categoryName !== '' && !in_array($categoryName, $catalogCategories, true)) {
        $catalogCategories[] = $categoryName;
    }
}
sort($catalogCategories);
?>

<section class="catalog-page">
<div class="catalog-hero">
  <div>
    <span class="catalog-eyebrow"><i class="fa-solid fa-book-open"></i> Library catalog</span>
    <h1>Search &amp; browse books</h1>
    <p>Find available titles instantly and explore categories with a responsive catalog.</p>
  </div>
  <?php if (!in_array($_SESSION['user_role'], ['student','faculty'], true)): ?>
    <a href="?url=book/add" class="btn btn-primary catalog-add-button"><i class="fa-solid fa-plus"></i> Add Book</a>
  <?php endif; ?>
</div>
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>
<div class="catalog-toolbar">
  <label class="catalog-search-box" for="catalogSearch"><i class="fa-solid fa-magnifying-glass"></i><span class="visually-hidden">Search books</span><input type="search" id="catalogSearch" placeholder="Search by title, category, ISBN, or publisher..." autocomplete="off"></label>
  <label class="catalog-status-filter" for="catalogStatus"><span class="visually-hidden">Filter by availability</span><select id="catalogStatus"><option value="all">All availability</option><option value="available">Available only</option><option value="borrowed">Borrowed</option><option value="reserved">Reserved</option></select></label>
</div>

<div class="catalog-category-row" aria-label="Browse categories">
  <button type="button" class="catalog-category active" data-category="all">All books</button>
  <?php foreach ($catalogCategories as $category): ?>
    <button type="button" class="catalog-category" data-category="<?php echo e(strtolower($category)); ?>"><?php echo e($category); ?></button>
  <?php endforeach; ?>
</div>

<div class="catalog-results-meta"><span><strong id="catalogResultCount"><?php echo count($books); ?></strong> titles</span><span id="catalogNoResults" hidden>No books match your search.</span></div>

<div class="catalog-grid" id="catalogGrid">
  <?php foreach ($books as $b): ?>
    <?php
      $userReservation = $userReservations[(int) $b['id']] ?? null;
      $bookStatus = strtolower((string) ($b['status'] ?? 'unknown'));
      $bookCategory = strtolower((string) ($b['category_name'] ?? 'uncategorized'));
      $bookSearch = strtolower(trim(implode(' ', array_filter([$b['title'] ?? '', $b['category_name'] ?? '', $b['publisher_name'] ?? '', $b['isbn'] ?? '']))));
    ?>
    <article class="catalog-book-card" data-search="<?php echo e($bookSearch); ?>" data-category="<?php echo e($bookCategory); ?>" data-status="<?php echo e($bookStatus); ?>">
      <div class="catalog-cover-wrap">
        <?php if (!empty($b['cover_image'])): ?><img src="<?php echo BASE_URL . '/' . e($b['cover_image']); ?>" alt="<?php echo e($b['title']); ?> cover" class="catalog-cover"><?php else: ?><div class="catalog-cover-placeholder"><i class="fa-solid fa-book"></i></div><?php endif; ?>
        <?php if ($bookStatus === 'available'): ?><span class="catalog-availability available">Available</span><?php else: ?><span class="catalog-availability unavailable"><?php echo e(ucfirst($bookStatus)); ?></span><?php endif; ?>
      </div>
      <div class="catalog-book-body">
        <span class="catalog-book-category"><?php echo e($b['category_name'] ?: 'Uncategorized'); ?></span>
        <h2><?php echo e($b['title']); ?></h2>
        <p class="catalog-book-publisher"><i class="fa-solid fa-building"></i> <?php echo e($b['publisher_name'] ?: 'Publisher not listed'); ?></p>
        <div class="catalog-book-footer">
          <?php if (!in_array($_SESSION['user_role'], ['student','faculty'], true)): ?>
            <a class="catalog-action secondary" href="?url=book/edit/<?php echo $b['id']; ?>">Edit</a>
            <a class="catalog-action danger" href="?url=book/delete/<?php echo $b['id']; ?>" onclick="return confirm('Delete book?');">Delete</a>
          <?php elseif ($userReservation && $userReservation['status'] === 'ready'): ?>
            <span class="catalog-ready">Ready to pick up</span>
            <a class="catalog-action danger" href="?url=reservation/cancel/<?php echo (int) $userReservation['id']; ?>" onclick="return confirm('Cancel this reservation?');">Cancel</a>
          <?php elseif ($bookStatus === 'available' && !$userReservation): ?>
            <a class="catalog-action primary" href="?url=book/reserve/<?php echo $b['id']; ?>" onclick="return confirm('Reserve this book?');">Reserve book</a>
          <?php elseif ($userReservation): ?>
            <a class="catalog-action danger" href="?url=reservation/cancel/<?php echo (int) $userReservation['id']; ?>" onclick="return confirm('Cancel this reservation?');">Cancel reservation</a>
          <?php else: ?>
            <span class="catalog-unavailable-text">Currently unavailable</span>
          <?php endif; ?>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
</div>
</div>
</section>

<script>
(function () {
  const searchInput = document.getElementById('catalogSearch');
  const statusSelect = document.getElementById('catalogStatus');
  const cards = Array.from(document.querySelectorAll('.catalog-book-card'));
  const categoryButtons = Array.from(document.querySelectorAll('.catalog-category'));
  const resultCount = document.getElementById('catalogResultCount');
  const noResults = document.getElementById('catalogNoResults');
  let selectedCategory = 'all';

  function filterCatalog() {
    const query = (searchInput.value || '').trim().toLowerCase();
    const selectedStatus = statusSelect.value;
    let visibleCount = 0;
    cards.forEach((card) => {
      const matchesSearch = !query || card.dataset.search.includes(query);
      const matchesCategory = selectedCategory === 'all' || card.dataset.category === selectedCategory;
      const matchesStatus = selectedStatus === 'all' || card.dataset.status === selectedStatus;
      const visible = matchesSearch && matchesCategory && matchesStatus;
      card.hidden = !visible;
      if (visible) visibleCount += 1;
    });
    resultCount.textContent = visibleCount;
    noResults.hidden = visibleCount !== 0;
  }

  searchInput.addEventListener('input', filterCatalog);
  statusSelect.addEventListener('change', filterCatalog);
  categoryButtons.forEach((button) => button.addEventListener('click', function () {
    categoryButtons.forEach((item) => item.classList.remove('active'));
    button.classList.add('active');
    selectedCategory = button.dataset.category;
    filterCatalog();
  }));
})();
</script>
