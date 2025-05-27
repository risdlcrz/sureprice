document.addEventListener('DOMContentLoaded', () => {
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.getElementById('togglePassword');

  // Exit early if elements don't exist
  if (!passwordInput || !toggleBtn) return;

  const icon = toggleBtn.querySelector('i');

  passwordInput.addEventListener('focus', () => {
    toggleBtn.style.visibility = 'visible';
  });

  passwordInput.addEventListener('blur', () => {
    toggleBtn.style.visibility = passwordInput.value ? 'visible' : 'hidden';
  });

  toggleBtn.addEventListener('click', (e) => {
    e.preventDefault();
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    icon.classList.toggle('fa-eye-slash', isPassword);
    icon.classList.toggle('fa-eye', !isPassword);
  });

  toggleBtn.style.visibility = 'hidden';
});
