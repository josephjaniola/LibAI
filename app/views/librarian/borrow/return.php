<div class="return-page-shell">
  <div class="return-page-header">
    <div class="return-page-icon"><i class="fa-solid fa-arrow-rotate-left" aria-hidden="true"></i></div>
    <div>
      <span class="return-page-eyebrow">Circulation desk</span>
      <h1>Return a book</h1>
      <p>Scan the returned book's RFID tag to complete the return.</p>
    </div>
  </div>

  <div class="card return-card">
  <div class="card-body">
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>

    <form method="post" action="?url=borrow/confirmReturn">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="return-scan-panel">
        <label class="form-label" for="rfid_uid">Returned book RFID</label>
        <p class="return-scan-help">Use the scanner or enter the RFID number manually.</p>
        <div class="input-group">
          <input id="rfid_uid" name="rfid_uid" class="form-control" placeholder="Enter or scan RFID" autocomplete="off" required>
          <button type="button" id="scanBtn" class="btn btn-outline-primary"><i class="fa-solid fa-expand" aria-hidden="true"></i> Scan</button>
        </div>
      </div>
      <div class="return-form-actions">
        <span><i class="fa-solid fa-circle-info" aria-hidden="true"></i> The book status will update after confirmation.</span>
        <button class="btn btn-success"><i class="fa-solid fa-check" aria-hidden="true"></i> Confirm Return</button>
      </div>
    </form>
  </div>
  </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/borrow.js"></script>
