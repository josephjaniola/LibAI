<div class="card">
  <div class="card-body">
    <h4>Borrow Book</h4>
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>

    <div class="row mb-3">
      <div class="col-md-4">
        <select id="borrower_type" class="form-select">
          <option value="student">Student</option>
          <option value="faculty">Faculty</option>
        </select>
      </div>
      <div class="col-md-6"><input id="borrower_id" class="form-control" placeholder="Enter Student ID or Faculty ID"></div>
      <div class="col-md-2"><button id="lookupBorrower" class="btn btn-secondary">Lookup</button></div>
    </div>

    <div id="borrowerInfo" style="display:none;" class="mb-3">
      <p><strong>Name:</strong> <span id="bname"></span></p>
      <p><strong>Email:</strong> <span id="bemail"></span></p>
      <p><strong>Phone:</strong> <span id="bphone"></span></p>
    </div>

    <form method="post" action="?url=borrow/create">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <input type="hidden" name="borrower_type" id="form_borrower_type" value="student">
      <input type="hidden" name="borrower_ref_id" id="form_borrower_ref_id">

      <div class="mb-3">
        <label>Scan Book (RFID)</label>
        <div class="input-group">
          <input id="rfid_uid" name="rfid_uid" class="form-control" placeholder="Scan RFID or enter UID" required>
          <button type="button" id="scanBtn" class="btn btn-outline-secondary">Scan</button>
        </div>
      </div>
      <div class="mb-3">
        <label>Due Date</label>
        <input type="datetime-local" name="due_date" class="form-control">
      </div>
      <div class="d-grid"><button class="btn btn-primary">Borrow</button></div>
    </form>
  </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/borrow.js"></script>
