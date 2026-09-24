document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('form.admin-validate').forEach(function(form){
    form.addEventListener('submit', function(e){
      // simple required check
      let valid = true;
      form.querySelectorAll('[required]').forEach(function(inp){
        if (!inp.value.trim()) { valid = false; inp.classList.add('is-invalid'); }
        else { inp.classList.remove('is-invalid'); }
      });
      if (!valid) {
        e.preventDefault();
        alert('Please fill required fields.');
        return false;
      }
      // disable submit to prevent double-submit
      let btn = form.querySelector('button[type=submit], button');
      if (btn) btn.disabled = true;
    });
  });
});
