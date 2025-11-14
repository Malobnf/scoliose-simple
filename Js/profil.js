document.addEventListener('DOMContentLoaded', () => {
  const profilePage = document.getElementById('profile-page');
  if (!profilePage || typeof Api === 'undefined') {
    return;
  }

  const data = Api.getProfile();

  const heroName = document.getElementById('profile-name-hero');
  const heroRoleClass = document.getElementById('profile-role-class');
  const heroTagline = document.getElementById('profile-tagline');
  const avatarImg = document.getElementById('profile-avatar');

  if (heroName) heroName.textContent = data.name;
  if (heroRoleClass) heroRoleClass.textContent = `${data.role} – ${data.className}`;
  if (heroTagline && data.tagline) heroTagline.textContent = data.tagline;
  if (avatarImg && data.avatarUrl) avatarImg.src = data.avatarUrl;

  // Infos élève
  const nameEl = document.getElementById('profile-name');
  const classEl = document.getElementById('profile-class');
  const schoolEl = document.getElementById('profile-school');
  const emailEl = document.getElementById('profile-email');

  if (nameEl) nameEl.textContent = data.name;
  if (classEl) classEl.textContent = data.className;
  if (schoolEl) schoolEl.textContent = data.school;
  if (emailEl) emailEl.textContent = data.email;

  // Parents
  const guardianNameEl = document.getElementById('guardian-name');
  const guardianPhoneEl = document.getElementById('guardian-phone');
  const guardianEmailEl = document.getElementById('guardian-email');

  if (guardianNameEl) guardianNameEl.textContent = data.guardian.name;
  if (guardianPhoneEl) guardianPhoneEl.textContent = data.guardian.phone;
  if (guardianEmailEl) guardianEmailEl.textContent = data.guardian.email;

  // Scolarité
  const levelEl = document.getElementById('profile-level');
  const yearEl = document.getElementById('profile-year');
  const mainTeacherEl = document.getElementById('profile-main-teacher');

  if (levelEl) levelEl.textContent = data.level;
  if (yearEl) yearEl.textContent = data.schoolYear;
  if (mainTeacherEl) mainTeacherEl.textContent = data.mainTeacher;

  // Notes
  const gradesList = document.getElementById('profile-grades-list');
  if (gradesList && Array.isArray(data.lastGrades)) {
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
