function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
  }
  
  // Automatically close mobile menu when switching to desktop
  window.addEventListener('resize', () => {
    const menu = document.getElementById('mobileMenu');
    if (window.innerWidth > 768 && menu.style.display === 'flex') {
      menu.style.display = 'none';
    }
  });
  