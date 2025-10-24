document.querySelectorAll('.toggle-submenu').forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    const parent = link.parentElement;
    parent.classList.toggle('open');
  });
});

const userMenu = document.getElementById('userMenu');
const avatarToggle = document.getElementById('avatarToggle');
avatarToggle.addEventListener('click', () => {
  userMenu.classList.toggle('show');
});
document.addEventListener('click', function (event) {
  if (!userMenu.contains(event.target) && !avatarToggle.contains(event.target)) {
    userMenu.classList.remove('show');
  }
});

const toggleMenuBtn = document.getElementById('toggleMenuBtn');
const sidebar = document.getElementById('sidebar');
const contentWrapper = document.getElementById('contentWrapper');

toggleMenuBtn.addEventListener('click', () => {
  sidebar.classList.toggle('show');
  contentWrapper.classList.toggle('expanded');
});

document.addEventListener('click', function(event) {
  const isClickInsideSidebar = sidebar.contains(event.target);
  const isClickOnToggleBtn = toggleMenuBtn.contains(event.target);
  if (!isClickInsideSidebar && !isClickOnToggleBtn) {
    sidebar.classList.remove('show');
    contentWrapper.classList.remove('expanded');
  }
});
