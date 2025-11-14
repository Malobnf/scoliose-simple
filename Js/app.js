document.addEventListener('DOMContentLoaded', function () {
  const burgerBtn = document.querySelector('#burger-btn');
  const mobileMenu = document.querySelector('#mobile-menu');

  if (!burgerBtn || !mobileMenu) return;

  burgerBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('is-open');
  });

  // Optionnel : fermer le menu au clic sur un lien
  mobileMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      mobileMenu.classList.remove('is-open');
    });
  });
});
