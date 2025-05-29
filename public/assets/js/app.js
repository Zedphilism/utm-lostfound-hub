// assets/js/app.js
window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mark-returned').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      if (!confirm(`Mark report #${id} as returned?`)) return;
      fetch(window.location.pathname, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'mark_returned',
          id,
          csrf_token: window.csrfToken
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          document.getElementById(`status-${id}`).textContent = 'Resolved';
          btn.remove();
        } else {
          alert('Could not mark returned.');
        }
      });
    });
  });
});
