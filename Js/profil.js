document.addEventListener('DOMContentLoaded', () => {
  const profilePage = document.getElementById('profile-page');
  if (!profilePage) {
    return;
  }

  // Fallback
  const setIfEmpty = (el, value) => {
    if (!el) return;
    const current = (el.textContent || '').trim();
    if (!current || current === 'Aucune information disponible') {
      el.textContent = value || 'Aucune information disponible';
    }
  };

  const data = (typeof window !== 'undefined' && window.PROFILE_DATA)
    ? window.PROFILE_DATA
    : null;

  const heroName = document.getElementById('profile-name-hero');
  const heroRoleClass = document.getElementById('profile-role-class');
  const heroTagline = document.getElementById('profile-tagline');
  const avatarImg = document.querySelector('.hero-photo img');

  if (data) {
    if (heroName && data.name) {
      setIfEmpty(heroName, data.name);
    }

    if (heroRoleClass && (data.role || data.className)) {
      const role = data.role || 'Élève';
      const className = data.className || '';
      setIfEmpty(heroRoleClass, className ? `${role} – ${className}` : role);
    }

    if (heroTagline && data.tagline) {
      setIfEmpty(heroTagline, data.tagline);
    }

    if (avatarImg && data.avatarUrl) {
      avatarImg.src = data.avatarUrl;
    }
  } else {
    setIfEmpty(heroName, 'Aucune information disponible');
    setIfEmpty(heroRoleClass, 'Aucune information disponible');
  }

  const nameEl = document.getElementById('profile-name');
  const classEl = document.getElementById('profile-class');
  const schoolEl = document.getElementById('profile-school');
  const emailEl = document.getElementById('profile-email');

  if (data) {
    if (data.name) setIfEmpty(nameEl, data.name);
    if (data.className) setIfEmpty(classEl, data.className);
    if (data.school) setIfEmpty(schoolEl, data.school);
    if (data.email) setIfEmpty(emailEl, data.email);
  } else {

    setIfEmpty(nameEl, null);
    setIfEmpty(classEl, null);
    setIfEmpty(schoolEl, null);
    setIfEmpty(emailEl, null);
  }

  const guardianNameEl = document.getElementById('guardian-name');
  const guardianPhoneEl = document.getElementById('guardian-phone');
  const guardianEmailEl = document.getElementById('guardian-email');

  if (data && data.guardian) {
    if (data.guardian.name) setIfEmpty(guardianNameEl, data.guardian.name);
    if (data.guardian.phone) setIfEmpty(guardianPhoneEl, data.guardian.phone);
    if (data.guardian.email) setIfEmpty(guardianEmailEl, data.guardian.email);
  } else {
    setIfEmpty(guardianNameEl, null);
    setIfEmpty(guardianPhoneEl, null);
    setIfEmpty(guardianEmailEl, null);
  }

  const levelEl = document.getElementById('profile-level');
  const yearEl = document.getElementById('profile-year');
  const mainTeacherEl = document.getElementById('profile-main-teacher');

  if (data) {
    if (data.level) setIfEmpty(levelEl, data.level);
    if (data.schoolYear) setIfEmpty(yearEl, data.schoolYear);
    if (data.mainTeacher) setIfEmpty(mainTeacherEl, data.mainTeacher);
  } else {
    setIfEmpty(levelEl, null);
    setIfEmpty(yearEl, null);
    setIfEmpty(mainTeacherEl, null);
  }

  const gradesList = document.getElementById('profile-grades-list');
  if (data && Array.isArray(data.lastGrades) && gradesList) {
    gradesList.innerHTML = '';

    data.lastGrades.forEach(grade => {
      const li = document.createElement('li');
      li.classList.add('profile-grade-item');

      li.innerHTML = `
        <span class="profile-grade-subject">${grade.subject}</span>
        <span class="profile-grade-value">${grade.value}</span>
        <span class="profile-grade-date">${grade.date}</span>
      `;

      gradesList.appendChild(li);
    });
  }
});
