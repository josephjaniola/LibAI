<div class="borrow-page-shell">
  <div class="borrow-page-header">
    <div class="borrow-page-icon"><i class="fa-solid fa-book-open" aria-hidden="true"></i></div>
    <div>
      <span class="borrow-page-eyebrow">Circulation desk</span>
      <h1>Borrow a book</h1>
      <p>Verify the borrower, scan the book, and set its return date.</p>
    </div>
  </div>

  <div class="card borrow-card">
  <div class="card-body">
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>

    <div class="borrow-section-heading"><h2>Borrower details</h2><span>Look up a student or faculty member</span></div>
    <div class="row borrow-lookup-panel">
      <div class="col-md-4">
        <select id="borrower_type" class="form-select">
          <option value="student">Student</option>
          <option value="faculty">Faculty</option>
        </select>
      </div>
      <div class="col-md-6"><input id="borrower_id" class="form-control" placeholder="Enter Student ID or Faculty ID"></div>
      <div class="col-md-2"><button id="lookupBorrower" class="btn btn-secondary">Lookup</button></div>
    </div>

    <div id="borrowerInfo" style="display:none;" class="borrower-info-panel mb-3">
      <p><strong>Name:</strong> <span id="bname"></span></p>
      <p><strong>Email:</strong> <span id="bemail"></span></p>
      <p><strong>Phone:</strong> <span id="bphone"></span></p>
    </div>

    <form method="post" action="?url=borrow/create" class="borrow-form">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <input type="hidden" name="borrower_type" id="form_borrower_type" value="student">
      <input type="hidden" name="borrower_ref_id" id="form_borrower_ref_id">

      <div class="borrow-section-heading"><h2>Book and due date</h2><span>Use the RFID tag and librarian-set return time</span></div>
      <div class="borrow-scan-panel mb-3">
        <label class="form-label" for="rfid_uid">Book RFID</label>
        <div class="input-group">
          <input id="rfid_uid" name="rfid_uid" class="form-control" placeholder="Scan or enter RFID" autocomplete="off" required>
          <button type="button" id="scanBtn" class="btn btn-outline-primary"><i class="fa-solid fa-expand" aria-hidden="true"></i> Scan</button>
        </div>
      </div>
      <div class="borrow-date-panel mb-3">
        <label class="form-label" for="due_date">Return date and time</label>
        <input type="datetime-local" id="due_date" name="due_date" class="form-control">
        <small>Choose the exact date and time the book must be returned.</small>
      </div>
      <div class="borrow-form-actions">
        <button class="btn btn-success" type="submit" name="action" value="ready"><i class="fa-solid fa-box" aria-hidden="true"></i> Ready to Pick Up</button>
        <button class="btn btn-primary" type="submit" name="action" value="borrow"><i class="fa-solid fa-check" aria-hidden="true"></i> Confirm Borrow</button>
      </div>
    </form>
  </div>
</div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/borrow.js"></script>
