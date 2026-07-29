/**
 * Toko Page Script
 * Handles store location display and selection
 */

document.addEventListener('DOMContentLoaded', () => {
  // Store card interaction
  document.querySelectorAll('.store-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.store-card').forEach(c => c.classList.remove('active'));
      card.classList.add('active');
      
      // Update map detail panel
      document.getElementById('dName').textContent = card.dataset.name;
      document.getElementById('dAddr').textContent = card.dataset.addr;
      document.getElementById('dJarak').textContent = card.dataset.jarak;
      document.getElementById('dJam').textContent = card.dataset.jam;
      document.getElementById('dHp').textContent = card.dataset.hp;
    });
  });

  // Update cart badge
  updateCartBadge();
});
