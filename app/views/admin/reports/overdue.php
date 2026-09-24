<div class="card">
  <div class="card-body">
    <h4>Overdue Processing</h4>
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <p>Click the button below to mark all overdue borrowed loans as overdue and send email notifications.</p>
    <form method="post" action="?url=overdue/process">
      <div class="d-grid"><button class="btn btn-danger">Process Overdue Loans</button></div>
    </form>
  </div>
</div>
