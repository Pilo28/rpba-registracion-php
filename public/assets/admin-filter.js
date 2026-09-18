/**
 * Filtro client-side genérico para tablas del panel de admin.
 * Cada <tr> debe tener un atributo data-search con el texto ya en minúsculas.
 */
function initAdminFilter({ input, table, status, empty, label, labelPlural }) {
  const inputEl = document.getElementById(input);
  const tableEl = document.getElementById(table);
  const statusEl = document.getElementById(status);
  const emptyEl = document.getElementById(empty);
  if (!inputEl || !tableEl) return;

  const rows = Array.from(tableEl.querySelectorAll('tbody tr'));
  const total = rows.length;

  inputEl.addEventListener('input', () => {
    const q = inputEl.value.trim().toLowerCase();
    let visible = 0;

    rows.forEach((row) => {
      const matches = q === '' || (row.dataset.search || '').includes(q);
      row.hidden = !matches;
      if (matches) visible++;
    });

    tableEl.hidden = visible === 0;
    if (emptyEl) emptyEl.hidden = visible !== 0;

    if (statusEl) {
      if (q === '') {
        statusEl.textContent = `${total} ${labelPlural} en total`;
      } else {
        const palabra = visible === 1 ? label : labelPlural;
        statusEl.textContent = `${visible} de ${total} ${palabra} coinciden con "${q}"`;
      }
    }
  });

  inputEl.focus();
}
