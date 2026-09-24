<div class="card">
  <div class="card-body">
    <h4>Add Book</h4>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="?url=book/add">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="mb-3"><input type="text" name="title" class="form-control" placeholder="Title" required></div>
      <div class="mb-3"><input type="text" name="subtitle" class="form-control" placeholder="Subtitle"></div>
      <div class="row">
        <div class="col-md-4 mb-3"><input type="text" name="isbn" class="form-control" placeholder="ISBN"></div>
        <div class="col-md-4 mb-3"><input type="text" name="accession_number" class="form-control" placeholder="Accession Number"></div>
        <div class="col-md-4 mb-3"><input type="text" name="call_number" class="form-control" placeholder="Call Number"></div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3"><select name="category_id" class="form-select"><option value="">Select Category</option><?php foreach($categories as $c) echo '<option value="'.$c['id'].'">'.e($c['name']).'</option>'; ?></select></div>
        <div class="col-md-4 mb-3"><select name="publisher_id" class="form-select"><option value="">Select Publisher</option><?php foreach($publishers as $p) echo '<option value="'.$p['id'].'">'.e($p['name']).'</option>'; ?></select></div>
        <div class="col-md-4 mb-3"><input type="text" name="year_published" class="form-control" placeholder="Year Published"></div>
      </div>
      <div class="mb-3"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
      <div class="mb-3"><input type="text" name="keywords" class="form-control" placeholder="Keywords"></div>
      <div class="mb-3"><label>Cover Image</label><input type="file" name="cover_image" class="form-control"></div>
      <div class="mb-3"><label>RFID UID</label>
        <div class="input-group">
          <input type="text" id="rfid_uid" name="rfid_uid" class="form-control" placeholder="Scan or enter RFID UID">
          <button class="btn btn-outline-secondary" type="button" id="scanRfidBtn">Scan RFID</button>
        </div>
      </div>
      <div class="d-grid"><button class="btn btn-success">Save Book</button></div>
    </form>
  </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/rfid.js"></script>
