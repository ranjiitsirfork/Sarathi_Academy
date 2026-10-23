  </div><!-- /admin-content -->
</div><!-- /admin-main -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>
// Show sidebar toggle button on mobile
document.addEventListener('DOMContentLoaded', function(){
  var btn = document.getElementById('sidebarToggle');
  if(btn) btn.style.display = window.innerWidth <= 768 ? 'block' : 'none';
  window.addEventListener('resize', function(){
    if(btn) btn.style.display = window.innerWidth <= 768 ? 'block' : 'none';
  });
});
</script>
</body>
</html>
