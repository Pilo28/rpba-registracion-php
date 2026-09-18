(function () {
  const button = document.getElementById('nav-hamburger');
  const mobileNav = document.getElementById('mobile-nav');
  if (!button || !mobileNav) return;

  button.addEventListener('click', () => {
    const isOpen = button.classList.toggle('is-open');
    button.setAttribute('aria-expanded', String(isOpen));
    mobileNav.hidden = !isOpen;
  });

  mobileNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      button.classList.remove('is-open');
      button.setAttribute('aria-expanded', 'false');
      mobileNav.hidden = true;
    });
  });
})();
