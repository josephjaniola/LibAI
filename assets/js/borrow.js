document.addEventListener('DOMContentLoaded', function(){
  // Lookup borrower
  var lookupBtn = document.getElementById('lookupBorrower');
  if (lookupBtn) {
    lookupBtn.addEventListener('click', function(){
      var type = document.getElementById('borrower_type').value;
      var id = document.getElementById('borrower_id').value.trim();
      if (!id) return alert('Please enter a student or faculty ID/email first.');
      fetch('?url=borrow/lookupBorrower&type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id)).then(function(response){
        if (!response.ok) throw new Error('Lookup request failed');
        return response.json();
      }).then(data=>{
        if (data.ok) {
          document.getElementById('borrowerInfo').style.display='block';
          document.getElementById('bname').textContent = data.user.firstname + ' ' + (data.user.lastname || '');
          document.getElementById('bemail').textContent = data.user.email || '';
          document.getElementById('bphone').textContent = data.user.mobile || '';
          document.getElementById('form_borrower_type').value = type;
          document.getElementById('form_borrower_ref_id').value = id;
        } else {
          document.getElementById('borrowerInfo').style.display='none';
          document.getElementById('form_borrower_ref_id').value='';
          alert('Borrower not found. Please check the ID or email and try again.');
        }
      }).catch(function(){
        alert('Unable to check the borrower right now. Please try again.');
      });
    });
  }

  var borrowForm = document.querySelector('form[action*="borrow/create"]');
  if (borrowForm) {
    borrowForm.addEventListener('submit', function(event){
      var borrowerRef = document.getElementById('form_borrower_ref_id').value.trim();
      var rfid = document.getElementById('rfid_uid').value.trim();
      if (!borrowerRef) {
        event.preventDefault();
        alert('Please look up a valid student or faculty borrower before borrowing.');
        return;
      }
      if (!rfid) {
        event.preventDefault();
        alert('Please scan or enter a valid book RFID before borrowing.');
      }
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
