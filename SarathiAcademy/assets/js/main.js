// SarathiAcademy – Main JS
document.addEventListener('DOMContentLoaded', function () {

  // --- Mobile Nav Toggle ---
  const toggle = document.getElementById('navToggle');
  const navLinks = document.getElementById('navLinks');
  if (toggle && navLinks) {
    toggle.addEventListener('click', () => navLinks.classList.toggle('open'));
  }

  // --- Admin Sidebar Toggle ---
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  // --- Flash auto-dismiss ---
  const flashEls = document.querySelectorAll('.flash');
  flashEls.forEach(el => {
    setTimeout(() => { if (el) el.style.display = 'none'; }, 5000);
  });

  // --- Smooth scroll for anchor links ---
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    });
  });

  // --- Confirm delete/action ---
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });

  // --- Active nav based on hash ---
  const hash = window.location.hash;
  if (hash) {
    const link = document.querySelector('.sidebar nav a[href="' + hash + '"]');
    if (link) link.classList.add('active');
  }
});
