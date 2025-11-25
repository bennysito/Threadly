  const profileBtn = document.getElementById('profileBtn');
  const profileDropdown = document.getElementById('profileDropdown');
  profileBtn.addEventListener('click', () => {
    profileDropdown.classList.toggle('hidden');
  });

  const mobileBtn = document.getElementById('mobileBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  mobileBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });