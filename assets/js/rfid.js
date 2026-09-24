// Simple RFID scan helper: prompts for UID and sends to scan endpoint
document.addEventListener('DOMContentLoaded', function(){
  var btn = document.getElementById('scanRfidBtn');
  if (!btn) return;
  btn.addEventListener('click', function(){
    var uid = prompt('Place RFID tag on reader or enter UID manually:');
    if (!uid) return;
    // Fill input if present
    var input = document.getElementById('rfid_uid');
    if (input) input.value = uid;
    // Optional: fetch book info
    fetch('?url=book/scanRfid&uid=' + encodeURIComponent(uid)).then(res=>res.json()).then(data=>{
      if (data.ok) {
        alert('Book found: ' + data.book.title);
      } else {
        alert('No book found for UID: ' + uid);
      }
    }).catch(()=>{});
  });
});
