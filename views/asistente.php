<div class="chat">

  <!-- Barra superior -->
  <div class="chat__topbar">
    <div class="chat__title-group">
      <span class="chat__title">Asistente IA</span>
      <span class="chat__subtitle">Manual de Registración</span>
    </div>
    <button type="button" class="chat__clear-btn" id="chat-clear-btn" aria-label="Limpiar conversación" hidden>
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="3 6 5 6 21 6" />
        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
        <path d="M10 11v6M14 11v6" />
        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
      </svg>
      Limpiar
    </button>
  </div>

  <!-- Área de mensajes -->
  <div id="chat-messages" class="chat__messages" role="log" aria-live="polite" aria-relevant="additions" aria-label="Historial de conversación">
    <div id="chat-welcome" class="chat__welcome">
      <div class="chat__welcome-icon" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2L2 7l10 5 10-5-10-5z" />
          <path d="M2 17l10 5 10-5" />
          <path d="M2 12l10 5 10-5" />
        </svg>
      </div>
      <h2 class="chat__welcome-titulo">¿En qué puedo ayudarte?</h2>
      <p class="chat__welcome-desc">
        Preguntame sobre el Manual de Registración: requisitos documentales,
        códigos de acto, trámites registrales y más.
      </p>
      <div class="sugeridas" aria-labelledby="sugeridas-titulo">
        <p id="sugeridas-titulo" class="sugeridas__titulo">Preguntas frecuentes</p>
        <ul class="sugeridas__lista" role="list" id="chat-sugeridas">
          <li><button type="button" class="sugerida-btn">¿Qué documentos necesito para una compraventa?</button></li>
          <li><button type="button" class="sugerida-btn">¿Cuál es el código de acto para una hipoteca?</button></li>
          <li><button type="button" class="sugerida-btn">¿Qué es el tracto abreviado?</button></li>
          <li><button type="button" class="sugerida-btn">¿Se necesita certificado catastral para una donación?</button></li>
          <li><button type="button" class="sugerida-btn">¿Cómo se registra un embargo?</button></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Área de input -->
  <div class="chat__input-area">
    <div class="chat__input-wrap">
      <textarea
        id="chat-textarea"
        class="chat__textarea"
        placeholder="Escribí tu pregunta sobre el manual..."
        rows="1"
        aria-label="Escribir mensaje al asistente de IA"
        aria-multiline="true"
      ></textarea>
      <button type="button" class="chat__send-btn" id="chat-send-btn" disabled aria-label="Enviar mensaje">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="22" y1="2" x2="11" y2="13" />
          <polygon points="22 2 15 22 11 13 2 9 22 2" />
        </svg>
      </button>
    </div>
    <p class="chat__hint">
      <kbd>Enter</kbd> para enviar &nbsp;·&nbsp;
      <kbd>Shift+Enter</kbd> para nueva línea
    </p>
  </div>

</div>

<script src="/assets/chat.js" defer></script>
