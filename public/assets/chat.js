(function () {
  const MAX_INPUT_CHARS = 2000;

  const messagesEl = document.getElementById('chat-messages');
  const welcomeEl = document.getElementById('chat-welcome');
  const textarea = document.getElementById('chat-textarea');
  const sendBtn = document.getElementById('chat-send-btn');
  const clearBtn = document.getElementById('chat-clear-btn');
  const sugeridasEl = document.getElementById('chat-sugeridas');

  /** @type {{role: 'user'|'assistant', content: string, isStreaming?: boolean}[]} */
  let messages = [];
  let isLoading = false;
  let errorMsg = null;

  const avatarSvg = `
    <div class="msg__avatar" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2L2 7l10 5 10-5-10-5z" />
        <path d="M2 17l10 5 10-5" />
        <path d="M2 12l10 5 10-5" />
      </svg>
    </div>`;

  function render() {
    const hasMessages = messages.length > 0;
    welcomeEl.hidden = hasMessages;
    clearBtn.hidden = !hasMessages;

    messagesEl.querySelectorAll('.msg, .chat__error').forEach((el) => el.remove());

    for (const msg of messages) {
      messagesEl.appendChild(buildMessageEl(msg));
    }

    if (errorMsg) {
      const div = document.createElement('div');
      div.className = 'chat__error';
      div.setAttribute('role', 'alert');
      div.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10" />
          <line x1="12" y1="8" x2="12" y2="12" />
          <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        ${escapeHtml(errorMsg)}`;
      messagesEl.appendChild(div);
    }

    messagesEl.scrollTop = messagesEl.scrollHeight;
    updateSendState();
  }

  function buildMessageEl(msg) {
    const wrapper = document.createElement('div');
    wrapper.className = 'msg ' + (msg.role === 'user' ? 'msg--user' : 'msg--assistant');

    const isTyping = msg.isStreaming === true && msg.content === '';

    wrapper.innerHTML =
      (msg.role === 'assistant' ? avatarSvg : '') +
      '<div class="msg__bubble">' +
      (isTyping
        ? `<div class="typing" role="status" aria-label="El asistente está escribiendo">
             <span class="typing__dot"></span><span class="typing__dot"></span><span class="typing__dot"></span>
           </div>`
        : `<p class="msg__text${msg.isStreaming ? ' msg__text--streaming' : ''}">${escapeHtml(msg.content)}</p>`) +
      '</div>';

    return wrapper;
  }

  function escapeHtml(str) {
    return str.replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  }

  function updateSendState() {
    const canSend = textarea.value.trim().length > 0 && !isLoading;
    sendBtn.disabled = !canSend;
    sendBtn.classList.toggle('chat__send-btn--active', canSend);
    textarea.disabled = isLoading;
  }

  textarea.addEventListener('input', () => {
    updateSendState();
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 160) + 'px';
  });

  textarea.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      void sendMessage();
    }
  });

  sendBtn.addEventListener('click', () => void sendMessage());
  clearBtn.addEventListener('click', clearHistory);

  sugeridasEl.querySelectorAll('.sugerida-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      textarea.value = btn.textContent.trim();
      void sendMessage();
    });
  });

  async function sendMessage() {
    const text = textarea.value.trim().slice(0, MAX_INPUT_CHARS);
    if (!text || isLoading) return;

    errorMsg = null;
    textarea.value = '';
    textarea.style.height = 'auto';

    messages.push({ role: 'user', content: text });

    const history = messages.map(({ role, content }) => ({ role, content }));

    messages.push({ role: 'assistant', content: '', isStreaming: true });
    isLoading = true;
    render();

    try {
      const response = await fetch('/api/asistente.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ history }),
      });

      if (!response.body) {
        throw new Error('La respuesta no tiene cuerpo de stream.');
      }

      if (!response.ok) {
        const bodyText = await response.text().catch(() => '');
        throw new Error(
          `No se pudo conectar con el asistente (HTTP ${response.status}).` +
            (bodyText ? ` Detalle: ${bodyText.slice(0, 300)}` : ''),
        );
      }

      await processStream(response.body);

      const last = messages[messages.length - 1];
      last.isStreaming = false;
    } catch (err) {
      errorMsg = err instanceof Error ? err.message : 'Error inesperado al conectar con el asistente.';
      messages.pop();
    } finally {
      isLoading = false;
      render();
      textarea.focus();
    }
  }

  async function processStream(body) {
    const reader = body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';
    let eventType = null;

    try {
      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        buffer += decoder.decode(value, { stream: true });
        const lines = buffer.split('\n');
        buffer = lines.pop() ?? '';

        for (const rawLine of lines) {
          const line = rawLine.trim();

          if (line === '') {
            eventType = null;
            continue;
          }

          if (line.startsWith('event: ')) {
            eventType = line.slice(7).trim();
            continue;
          }

          if (!line.startsWith('data: ')) continue;
          const data = line.slice(6);
          if (data === '[DONE]') return;

          if (eventType === 'error') {
            const parsed = JSON.parse(data);
            throw new Error(parsed.error || 'Error del asistente.');
          }

          try {
            const event = JSON.parse(data);
            const text = event.candidates?.[0]?.content?.parts?.[0]?.text;
            if (text) appendToLastMessage(text);
          } catch {
            // Fragmento JSON incompleto — ignorar
          }
        }
      }
    } finally {
      reader.releaseLock();
    }
  }

  function appendToLastMessage(text) {
    const last = messages[messages.length - 1];
    last.content += text;
    render();
  }

  function clearHistory() {
    messages = [];
    errorMsg = null;
    render();
    textarea.focus();
  }

  render();
  textarea.focus();
})();
