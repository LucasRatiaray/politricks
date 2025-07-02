import './styles/app.css';

function bindPartnerCardClicks() {
  const partnerCards = document.querySelectorAll('[data-partner-id]');
  const detailContainer = document.getElementById('partner-detail-container');
  partnerCards.forEach(card => {
    card.addEventListener('click', async () => {
      const id = card.getAttribute('data-partner-id');
      const response = await fetch(`/partners/${id}/partial`);
      if (response.ok) {
        const html = await response.text();
        detailContainer.innerHTML = html;
        partnerCards.forEach(c => c.classList.remove('bg-gray-800', 'border-l-4', 'border-blue-600'));
        card.classList.add('bg-gray-800', 'border-l-4', 'border-blue-600');
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  console.log('JS chargé');

  bindPartnerCardClicks();

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

  document.querySelectorAll('[data-open-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      const modalId = btn.getAttribute('data-open-modal');
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.remove('hidden');
    });
  });
  document.querySelectorAll('[data-close-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      const modalId = btn.getAttribute('data-close-modal');
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.add('hidden');
    });
  });

  const filterForm = document.getElementById('filter-form');
  if (filterForm) {
    filterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const params = new URLSearchParams(formData);
      window.location.href = '/medias?' + params.toString();
    });
  }

  const clearFiltersBtn = document.getElementById('clear-filters');
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', function() {
      window.location.href = '/medias';
    });
  }

  const searchInput = document.querySelector('input[name="search"]');
  if (searchInput) {
    let timeout;
    searchInput.addEventListener('input', function() {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        filterForm.dispatchEvent(new Event('submit'));
      }, 500); 
    });
  }

  document.querySelectorAll('[data-edit-id]').forEach(btn => {
    btn.addEventListener('click', async function() {
      const id = this.dataset.editId;
      try {
        const response = await fetch(`/media/edit/${id}`);
        const data = await response.json();
        
        document.getElementById('edit-document-id').value = data.id;
        document.getElementById('edit-nom').value = data.nom;
        document.getElementById('edit-type').value = data.type;
        document.getElementById('edit-confidentialite').value = data.niveauConfidentialite;
        document.getElementById('edit-langue').value = data.langueDocument;
        
        document.getElementById('edit-media-modal').classList.remove('hidden');
      } catch (err) {
        alert('Erreur lors du chargement des données');
      }
    });
  });

  const editMediaForm = document.getElementById('edit-media-form');
  if (editMediaForm) {
    editMediaForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const id = document.getElementById('edit-document-id').value;
      const formData = new FormData();
      formData.append('nom', document.getElementById('edit-nom').value);
      formData.append('type', document.getElementById('edit-type').value);
      formData.append('confidentialite', document.getElementById('edit-confidentialite').value);
      formData.append('langue', document.getElementById('edit-langue').value);
      
      try {
        const response = await fetch(`/media/update/${id}`, {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        if (result.success) {
          document.getElementById('edit-media-modal').classList.add('hidden');
          window.location.reload();
        } else {
          alert('Erreur lors de la modification');
        }
      } catch (err) {
        alert('Erreur réseau ou serveur');
      }
    });
  }

  document.querySelectorAll('.delete-media-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
      const id = this.dataset.id;
      if (!confirm('Supprimer ce document ?')) return;
      try {
        const response = await fetch(`/media/delete/${id}`, { method: 'POST' });
        const result = await response.json();
        if (result.success) {
          window.location.reload();
        } else {
          alert("Erreur lors de la suppression.");
        }
      } catch (err) {
        alert("Erreur réseau ou serveur.");
      }
    });
  });
});