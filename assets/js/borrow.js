document.addEventListener('DOMContentLoaded', function(){
  // Lookup borrower
  var lookupBtn = document.getElementById('lookupBorrower');
  if (lookupBtn) {
    lookupBtn.addEventListener('click', function(){
      var type = document.getElementById('borrower_type').value;
      var id = document.getElementById('borrower_id').value.trim();
      if (!id) return alert('Enter borrower ID');
      fetch('?url=borrow/lookupBorrower&type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id)).then(r=>r.json()).then(data=>{
        if (data.ok) {
          document.getElementById('borrowerInfo').style.display='block';
          document.getElementById('bname').textContent = data.user.firstname + ' ' + (data.user.lastname || '');
          document.getElementById('bemail').textContent = data.user.email || '';
          document.getElementById('bphone').textContent = data.user.mobile || '';
          document.getElementById('form_borrower_type').value = type;
          document.getElementById('form_borrower_ref_id').value = id;
        } else {
          alert('Borrower not found');
        }
      });
    });
  }

  // Scan buttons for borrow/return and book add/edit
  var scanBtns = document.querySelectorAll('#scanBtn, #scanRfidBtn');
  scanBtns.forEach(function(btn){
    btn.addEventListener('click', function(){
      var uid = prompt('Place RFID tag on reader or enter UID manually:');
      if (!uid) return;
      // put into nearest input
      var input = btn.closest('.input-group').querySelector('input');
      if (input) input.value = uid;
    });
  });
});
