<div class="card">
  <div class="card-body">
    <h4>Edit Book</h4>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="?url=book/edit/<?php echo $book['id']; ?>">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="mb-3"><input type="text" name="title" class="form-control" value="<?php echo e($book['title']); ?>" required></div>
      <div class="mb-3"><input type="text" name="subtitle" class="form-control" value="<?php echo e($book['subtitle']); ?>"></div>
      <div class="row">
        <div class="col-md-4 mb-3"><input type="text" name="isbn" class="form-control" value="<?php echo e($book['isbn']); ?>"></div>
        <div class="col-md-4 mb-3"><input type="text" name="accession_number" class="form-control" value="<?php echo e($book['accession_number']); ?>"></div>
        <div class="col-md-4 mb-3"><input type="text" name="call_number" class="form-control" value="<?php echo e($book['call_number']); ?>"></div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3"><select name="category_id" class="form-select"><option value="">Select Category</option><?php foreach($categories as $c) echo '<option value="'.$c['id'].'"'.($book['category_id']==$c['id']?' selected':'').'>'.e($c['name']).'</option>'; ?></select></div>
        <div class="col-md-4 mb-3"><select name="publisher_id" class="form-select"><option value="">Select Publisher</option><?php foreach($publishers as $p) echo '<option value="'.$p['id'].'"'.($book['publisher_id']==$p['id']?' selected':'').'>'.e($p['name']).'</option>'; ?></select></div>
        <div class="col-md-4 mb-3"><input type="text" name="year_published" class="form-control" value="<?php echo e($book['year_published']); ?>"></div>
      </div>
      <div class="mb-3"><textarea name="description" class="form-control"><?php echo e($book['description']); ?></textarea></div>
      <div class="mb-3"><input type="text" name="keywords" class="form-control" value="<?php echo e($book['keywords']); ?>"></div>
      <div class="mb-3"><label>Cover Image</label><input type="file" name="cover_image" class="form-control"></div>
      <div class="mb-3"><label>RFID UID</label>
        <div class="input-group">
          <input type="text" id="rfid_uid" name="rfid_uid" class="form-control" value="<?php echo e($book['rfid_uid']); ?>" placeholder="Scan or enter RFID UID">
          <button class="btn btn-outline-secondary" type="button" id="scanRfidBtn">Scan RFID</button>
        </div>
      </div>
      <div class="d-grid"><button class="btn btn-success">Save Book</button></div>
    </form>
  </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/rfid.js"></script>
