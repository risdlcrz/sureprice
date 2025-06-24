document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenu = document.getElementById('mobileMenu');
    const toggleButtons = document.querySelectorAll('[onclick="toggleMobileMenu()"]');

    function toggleMobileMenu() {
        mobileMenu.classList.toggle('active');
        document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
    }

    // Attach click event to all toggle buttons
    toggleButtons.forEach(button => {
        button.onclick = toggleMobileMenu;
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (mobileMenu.classList.contains('active') && 
            !event.target.closest('.mobile-menu') && 
            !event.target.closest('.mobile-topbar')) {
            toggleMobileMenu();
        }
    });

    // Handle escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && mobileMenu.classList.contains('active')) {
            toggleMobileMenu();
        }
    });
});

// Automatically close mobile menu when switching to desktop
window.addEventListener('resize', () => {
    const menu = document.getElementById('mobileMenu');
    if (window.innerWidth > 768 && menu.style.display === 'flex') {
        menu.style.display = 'none';
    }
});
  