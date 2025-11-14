const Api = {
  getConversations() {
    return [
      { id: 'maths', title: 'Maths – 4ème B', subtitle: 'Prof. Martin · Chapitre 3 – Fractions' },
      { id: 'francais', title: 'Français – Devoir d’écriture', subtitle: 'Prof. Dupont · Récit d’imagination' },
      { id: 'histoire', title: 'Groupe projet Histoire', subtitle: 'Projet Révolution française · Groupe A' },
    ];
  },

  getMessages(conversationId) {
    const allMessages = {
      maths: [
        { type: 'received', author: 'Prof. Martin', time: '14:05', text: "N’oubliez pas..." },
        { type: 'sent', author: 'Vous', time: '14:12', text: "Bonjour, est-ce que..." },
      ],
      francais: [
        { type: 'received', author: 'Prof. Dupont', time: 'Hier · 10:15', text: "Pensez à relire..." },
      ],
      histoire: [
        { type: 'sent', author: 'Vous', time: 'Lundi · 17:00', text: "On se répartit les parties..." },
      ]
    };

    return allMessages[conversationId] || [];
  },

  getProfile() {
    return {
      name: 'Alex Martin',
      role: 'Élève',
      className: '4ème B',
      level: 'Collège',
      school: 'Collège Jean Moulin',
      schoolYear: '2024–2025',
      mainTeacher: 'Prof. Martin',
      tagline: 'Suivi personnalisé de votre scolarité : notes, devoirs, messagerie et ressources.',
      avatarUrl: 'assets/images/avatar-1577909.svg',

      email: 'alex.martin@example.com',

      guardian: {
        name: 'Mme Martin',
        phone: '06 12 34 56 78',
        email: 'parent.martin@example.com'
      },

      lastGrades: [
        { subject: 'Maths', value: '16/20', date: '12/11' },
        { subject: 'Français', value: '14/20', date: '08/11' },
        { subject: 'Histoire', value: '15/20', date: '05/11' }
      ]
    };
  },

  getNotifications() {
    return [
      {
        id: 'notif-5',
        title: 'Nouvelle note en Maths',
        body: 'Vous avez obtenu 16/20 au contrôle de fractions.',
        createdAt: '2025-11-14T09:15:00',
        url: 'profil.html#notes'
      },
      {
        id: 'notif-4',
        title: 'Devoir d’Histoire à rendre',
        body: 'Le devoir sur la Révolution française est à rendre pour lundi.',
        createdAt: '2025-11-13T18:30:00',
        url: 'index.html#devoirs'
      },
      {
        id: 'notif-3',
        title: 'Nouveau message de votre professeur principal',
        body: 'Votre professeur principal a ajouté un message dans la messagerie.',
        createdAt: '2025-11-13T10:05:00',
        url: 'messagerie.html'
      },
      {
        id: 'notif-2',
        title: 'Rappel : sortie scolaire',
        body: 'Pensez à rapporter l’autorisation signée pour la sortie de vendredi.',
        createdAt: '2025-11-12T16:00:00',
        url: 'index.html#ressources'
      },
      {
        id: 'notif-1',
        title: 'Nouveau document partagé',
        body: 'Un cours de sciences a été ajouté dans vos ressources.',
        createdAt: '2025-11-11T14:20:00',
        url: 'index.html#ressources'
      }
    ];
  }
};
