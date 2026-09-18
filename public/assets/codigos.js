(function () {
  const input = document.getElementById('codigos-filtro');
  const status = document.getElementById('codigos-status');
  const table = document.getElementById('codigos-table');
  const emptyMsg = document.getElementById('codigos-empty');
  if (!input || !table) return;

  const rows = Array.from(table.querySelectorAll('.codigos-row'));
  const total = rows.length;

  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();
    let visible = 0;

    rows.forEach((row) => {
      const matches = q === '' || row.dataset.search.includes(q);
      row.hidden = !matches;
      if (matches) visible++;
    });

    table.hidden = visible === 0;
    emptyMsg.hidden = visible !== 0;

    if (q === '') {
      status.innerHTML = `${total} actos registrales en total`;
    } else {
      const palabra = visible === 1 ? 'acto' : 'actos';
      status.innerHTML = `<strong>${visible}</strong> de ${total} ${palabra} coinciden con "<strong>${escapeHtml(q)}</strong>"`;
    }
  });

  table.addEventListener('click', (event) => {
    const row = event.target.closest('.codigos-row');
    if (row && !event.target.closest('a')) {
      window.location.href = row.dataset.href;
    }
  });

  table.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' && event.key !== ' ') return;
    const row = event.target.closest('.codigos-row');
    if (row) {
      event.preventDefault();
      window.location.href = row.dataset.href;
    }
  });

  function escapeHtml(str) {
    return str.replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  }
})();
