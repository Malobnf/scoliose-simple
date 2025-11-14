document.addEventListener('DOMContentLoaded', () => {
  const conversationListEl = document.querySelector('.conversation-list__items');
  const conversationItems = document.querySelectorAll('.conversation-item');
  const messagesContainer = document.getElementById('messages-container');
  const convTitle = document.getElementById('conversation-title');
  const convSubtitle = document.getElementById('conversation-subtitle');

  if (!conversationListEl || !messagesContainer || !convTitle || !convSubtitle) {
    return;
  }

  // 1. Afficher la liste des conversations à partir de l’API
  const conversations = Api.getConversations();

  conversationListEl.innerHTML = '';

  conversations.forEach((conv, index) => {
    const li = document.createElement('li');
    li.classList.add('conversation-item');
    if (index === 0) li.classList.add('conversation-item--active');
    li.dataset.conversation = conv.id;

    li.innerHTML = `
      <div class="conversation-item__title">${conv.title}</div>
      <div class="conversation-item__meta">${conv.subtitle}</div>
    `;

    conversationListEl.appendChild(li);
  });

  // 2. Fonction pour afficher une conversation
  function renderConversation(convId) {
    const conv = conversations.find(c => c.id === convId);
    if (!conv) return;

    convTitle.textContent = conv.title;
    convSubtitle.textContent = conv.subtitle;

    const messages = Api.getMessages(convId);

    messagesContainer.innerHTML = '';

    messages.forEach(m => {
      const wrapper = document.createElement('div');
      wrapper.classList.add('message');
      wrapper.classList.add(m.type === 'sent' ? 'message--sent' : 'message--received');

      const meta = document.createElement('div');
      meta.classList.add('message__meta');
      meta.textContent = `${m.author} · ${m.time}`;

      const bubble = document.createElement('div');
      bubble.classList.add('message__bubble');
      bubble.textContent = m.text;

      wrapper.appendChild(meta);
      wrapper.appendChild(bubble);

      messagesContainer.appendChild(wrapper);
    });
  }

  // 3. Conversation par défaut : la première
  if (conversations.length > 0) {
    renderConversation(conversations[0].id);
  }

  // 4. Gérer les clics sur les conversations
  conversationListEl.addEventListener('click', (e) => {
    const item = e.target.closest('.conversation-item');
    if (!item) return;

    const convId = item.dataset.conversation;
    if (!convId) return;

    // Visuel actif
    document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('conversation-item--active'));
    item.classList.add('conversation-item--active');

    renderConversation(convId);
  });
});
