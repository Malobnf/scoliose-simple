document.addEventListener('DOMContentLoaded', () => {
  if (typeof Api === 'undefined' || !Api.getNotifications) {
    return;
  }

  const notifications = Api.getNotifications().slice().sort((a, b) => {
    return new Date(b.createdAt) - new Date(a.createdAt);
  });

  const notifBtn = document.getElementById('notif-btn');
  const notifMenu = document.getElementById('notif-menu');
  const notifMenuList = document.getElementById('notif-menu-list');

  if (notifBtn && notifMenu && notifMenuList) {
    // Affivher uniquement les 5 dernières notifications
    const latestFive = notifications.slice(0, 5);
    notifMenuList.innerHTML = '';

    latestFive.forEach(notif => {
      const li = document.createElement('li');
      li.classList.add('notif-item');

      // Redirection vers la page notifications avec l'id en paramètre
      li.innerHTML = `
        <a href="notifications.html?id=${encodeURIComponent(notif.id)}" class="notif-item__link">
          <div class="notif-item__title">${notif.title}</div>
          <div class="notif-item__meta">${new Date(notif.createdAt).toLocaleString('fr-FR')}</div>
        </a>
      `;

      notifMenuList.appendChild(li);
    });

    // Toggle du menu au clic sur la cloche
    notifBtn.addEventListener('click', () => {
      const isOpen = notifMenu.classList.toggle('is-open');
      notifBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      notifMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    });

    // Fermer le menu si on clique ailleurs
    document.addEventListener('click', (e) => {
      if (!notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
        notifMenu.classList.remove('is-open');
        notifBtn.setAttribute('aria-expanded', 'false');
        notifMenu.setAttribute('aria-hidden', 'true');
      }
    });
  }

  // Page notifications complète
  const notificationsPageList = document.getElementById('notifications-page-list');
  if (notificationsPageList) {
    notificationsPageList.innerHTML = '';

    notifications.forEach(notif => {
      const item = document.createElement('article');
      item.classList.add('notification-card');
      item.id = notif.id;

      item.innerHTML = `
        <h3 class="notification-card__title">${notif.title}</h3>
        <p class="notification-card__meta">${new Date(notif.createdAt).toLocaleString('fr-FR')}</p>
        <p class="notification-card__body">${notif.body}</p>
        <a href="${notif.url}" class="notification-card__link">Aller au contenu</a>
      `;

      notificationsPageList.appendChild(item);
    });

    // Mettre en avant la notification cliquée (paramètre ?id=)
    const params = new URLSearchParams(window.location.search);
    const notifId = params.get('id');

    if (notifId) {
      const target = document.getElementById(notifId);
      if (target) {
        target.classList.add('notification-card--highlight');
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  }
});
