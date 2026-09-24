<div class="card">
  <div class="card-body">
    <h4>New Reservation</h4>
    <?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>
    <form method="post" action="?url=reservation/create">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Borrower Type</label>
          <select name="borrower_type" class="form-select" required>
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
          </select>
        </div>
        <div class="col-md-8">
          <label class="form-label">Student/Faculty ID or Email</label>
          <input type="text" name="borrower_ref_id" class="form-control" placeholder="Enter student or faculty identifier" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Select Book</label>
        <select name="book_id" class="form-select" required>
          <option value="">Choose an available book</option>
          <?php foreach ($books as $book): ?>
            <option value="<?php echo $book['id']; ?>"><?php echo e($book['title'] . ' (' . $book['rfid_uid'] . ')'); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="d-grid"><button class="btn btn-primary">Submit Reservation</button></div>
    </form>
  </div>
</div>
