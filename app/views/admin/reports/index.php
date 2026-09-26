<div class="reports-shell">
<div class="reports-header">
  <div>
    <span class="reports-eyebrow">Library insights</span>
    <h1>Reports</h1>
    <p>Track borrowing activity, inventory trends, and return reminders.</p>
  </div>
  <div class="reports-overdue-actions">
    <span class="reports-overdue-count">Overdue loans <strong><?php echo e($overdue); ?></strong></span>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a class="btn btn-sm btn-outline-danger" href="?url=reports/overdue">View Records</a>
      <form method="post" action="?url=overdue/process" class="d-inline">
        <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
        <button type="submit" class="btn btn-sm btn-danger">Send Due and Overdue Reminders</button>
      </form>
    </div>
  </div>
</div>
<div class="reports-toolbar">
    <span class="reports-toolbar-label">Report range</span>
    <form class="reports-filter-form" method="get" action="?url=reports/index">
      <input type="hidden" name="url" value="reports/index">
      <div class="col-auto">
        <input type="date" name="start" class="form-control form-control-sm" value="<?php echo e($_GET['start'] ?? ''); ?>">
      </div>
      <div class="col-auto">
        <input type="date" name="end" class="form-control form-control-sm" value="<?php echo e($_GET['end'] ?? ''); ?>">
      </div>
      <div class="col-auto">
        <button class="btn btn-sm btn-primary">Filter</button>
      </div>
      <div class="col-auto">
        <div class="btn-group">
          <a class="btn btn-sm btn-outline-secondary" href="?url=reports/export&format=csv&type=most&start=<?php echo urlencode($_GET['start'] ?? ''); ?>&end=<?php echo urlencode($_GET['end'] ?? ''); ?>">Export Most</a>
          <a class="btn btn-sm btn-outline-secondary" href="?url=reports/export&format=csv&type=monthly&start=<?php echo urlencode($_GET['start'] ?? ''); ?>&end=<?php echo urlencode($_GET['end'] ?? ''); ?>">Export Monthly</a>
          <a class="btn btn-sm btn-outline-secondary" href="?url=reports/export&format=csv&type=bycat">Export By Category</a>
          <a class="btn btn-sm btn-outline-secondary" href="?url=reports/export&format=csv&type=all&start=<?php echo urlencode($_GET['start'] ?? ''); ?>&end=<?php echo urlencode($_GET['end'] ?? ''); ?>">Export All</a>
        </div>
      </div>
    </form>
</div>
<div class="report-chart-card report-chart-wide">
  <div class="report-chart-heading">
    <div><h2>Most borrowed books</h2><p>Top titles for the selected date range.</p></div>
  </div>
  <canvas id="mostBorrowedChart" height="120"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('mostBorrowedChart');
  const labels = <?php echo json_encode(array_column($most,'title')); ?>;
  const data = <?php echo json_encode(array_column($most,'borrow_count')); ?>;
  new Chart(ctx, { type: 'bar', data: { labels: labels, datasets: [{ label: 'Borrow Count', data: data, backgroundColor: 'rgba(54,162,235,0.6)' }] } });
</script>

<div class="row g-4 mt-1">
  <div class="col-md-8">
    <div class="report-chart-card h-100">
    <div class="report-chart-heading"><div><h2>Monthly borrows</h2><p>Borrowing activity across the selected period.</p></div></div>
    <canvas id="monthlyTrend" height="120"></canvas>
    </div>
  </div>
  <div class="col-md-4">
    <div class="report-chart-card h-100">
    <div class="report-chart-heading"><div><h2>Inventory by category</h2><p>Current catalog distribution.</p></div></div>
    <canvas id="byCategory" height="200"></canvas>
    </div>
  </div>
</div>

<script>
  // Monthly trend data
  const monthlyLabels = <?php echo json_encode(array_column($monthly,'ym')); ?>;
  const monthlyData = <?php echo json_encode(array_column($monthly,'cnt')); ?>;
  new Chart(document.getElementById('monthlyTrend'), { type: 'line', data: { labels: monthlyLabels, datasets: [{ label: 'Borrows', data: monthlyData, borderColor: 'rgba(75,192,192,1)', fill:false }] }, options:{responsive:true} });

  // Inventory by category
  const catLabels = <?php echo json_encode(array_column($bycat,'name')); ?>;
  const catData = <?php echo json_encode(array_column($bycat,'cnt')); ?>;
  new Chart(document.getElementById('byCategory'), { type: 'pie', data: { labels: catLabels, datasets: [{ data: catData, backgroundColor: ['#36a2eb','#ff6384','#ffcd56','#4bc0c0','#9966ff','#c9cbcf'] }] }, options:{responsive:true} });
</script>
</div>
