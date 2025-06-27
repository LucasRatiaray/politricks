/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

function bindPartnerCardClicks() {
  const partnerCards = document.querySelectorAll('[data-partner-id]');
  const detailContainer = document.getElementById('partner-detail-container');

  partnerCards.forEach(card => {
    card.addEventListener('click', async () => {
      const id = card.getAttribute('data-partner-id');
      const response = await fetch(`/partner/${id}/partial`);
      if (response.ok) {
        const html = await response.text();
        detailContainer.innerHTML = html;
        // Met à jour la sélection visuelle
        partnerCards.forEach(c => c.classList.remove('bg-gray-800', 'border-l-4', 'border-blue-600'));
        card.classList.add('bg-gray-800', 'border-l-4', 'border-blue-600');
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  bindPartnerCardClicks();

  // Menu burger
  const burgerBtn = document.getElementById('burger-menu-btn');
  const sideMenu = document.getElementById('side-menu');
  const closeBtn = document.getElementById('close-menu-btn');
  const overlay = document.getElementById('menu-overlay');

  function openMenu() {
    sideMenu.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  }
  function closeMenu() {
    sideMenu.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  }

  if (burgerBtn && sideMenu && closeBtn && overlay) {
    burgerBtn.addEventListener('click', openMenu);
    closeBtn.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);
  }
});
