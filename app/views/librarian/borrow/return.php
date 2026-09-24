<div class="card">
  <div class="card-body">
    <h4>Return Book</h4>
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>

    <form method="post" action="?url=borrow/confirmReturn">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="mb-3">
        <label>Scan Returned Book (RFID)</label>
        <div class="input-group">
          <input id="rfid_uid" name="rfid_uid" class="form-control" placeholder="Scan RFID or enter UID" required>
          <button type="button" id="scanBtn" class="btn btn-outline-secondary">Scan</button>
        </div>
      </div>
      <div class="d-grid"><button class="btn btn-success">Confirm Return</button></div>
    </form>
  </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/borrow.js"></script>
