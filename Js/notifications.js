document.addEventListener('DOMContentLoaded', () => {
  // 1. On récupère les notifications SI on en a, sinon on laisse un tableau vide
  let notifications = [];

  if (typeof window !== 'undefined' && Array.isArray(window.NOTIFICATIONS)) {
    notifications = window.NOTIFICATIONS.slice();
  } else if (typeof Api !== 'undefined' && typeof Api.getNotifications === 'function') {
    notifications = Api.getNotifications().slice();
  }
  // ❌ surtout pas de "return" ici :
  // même avec 0 notif, on veut que la cloche ouvre / ferme le menu

  // Tri par date décroissante
  notifications.sort((a, b) => {
    return new Date(b.createdAt) - new Date(a.createdAt);
  });

  const notifBtn = document.getElementById('notif-btn');
  const notifMenu = document.getElementById('notif-menu');
  const notifMenuList = document.getElementById('notif-menu-list');

  // Si pas de bouton ou pas de menu, on ne fait rien
  if (notifBtn && notifMenu) {
    // Remplir la liste SI on a un UL
    if (notifMenuList) {
      notifMenuList.innerHTML = '';
      const latestFive = notifications.slice(0, 5);

      latestFive.forEach(notif => {
        const li = document.createElement('li');
        li.classList.add('notif-item');

        const notifId = encodeURIComponent(notif.id);

        li.innerHTML = `
          <a href="notifications.php?id=${notifId}" class="notif-item__link">
            <div class="notif-item__title">${notif.title}</div>
            <div class="notif-item__meta">${new Date(notif.createdAt).toLocaleString('fr-FR')}</div>
          </a>
        `;

        notifMenuList.appendChild(li);
      });
    }

    // Toggle du menu au clic sur la cloche (même avec 0 notif)
    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
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

  // ----- PARTIE PAGE notifications complète (optionnelle) -----
  const notificationsPageList = document.getElementById('notifications-page-list');
  if (notificationsPageList) {
    notificationsPageList.innerHTML = '';

    notifications.forEach(notif => {
      const item = document.createElement('article');
      item.classList.add('notification-card');
      item.id = String(notif.id);

      item.innerHTML = `
        <h3 class="notification-card__title">${notif.title}</h3>
        <p class="notification-card__meta">${new Date(notif.createdAt).toLocaleString('fr-FR')}</p>
        <p class="notification-card__body">${notif.body}</p>
        <a href="${notif.url}" class="notification-card__link">Aller au contenu</a>
      `;

      notificationsPageList.appendChild(item);
    });

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
