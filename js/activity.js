// ===== Activity Detail Page =====
document.addEventListener('DOMContentLoaded', function () {
  var params = new URLSearchParams(window.location.search);
  var id = params.get('id');

  if (!id) {
    window.location.href = 'index.html';
    return;
  }

  var activity = activities.find(function (a) { return a.id === id; });

  if (!activity) {
    window.location.href = 'index.html';
    return;
  }

  // Set page title
  document.title = 'مبادرتنا - ' + activity.title;

  // Populate hero
  var heroBg = document.getElementById('activityHeroBg');
  heroBg.style.backgroundImage = "url('" + activity.image + "')";

  document.getElementById('activityIcon').textContent = activity.icon;
  document.getElementById('activityTitle').textContent = activity.title;
  document.getElementById('activityDesc').textContent = activity.description;

  // Populate full description
  document.getElementById('activityFullDesc').textContent = activity.fullDescription;

  // Populate features
  var featuresContainer = document.getElementById('activityFeatures');
  activity.features.forEach(function (feature) {
    var card = document.createElement('div');
    card.className = 'feature-card glass scroll-animate';

    card.innerHTML =
      '<div class="feature-card-icon">' +
        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
          '<path d="M20 6 9 17l-5-5"/>' +
        '</svg>' +
      '</div>' +
      '<h3>' + escapeHtml(feature) + '</h3>';

    featuresContainer.appendChild(card);
  });

  // Populate other activities
  var othersContainer = document.getElementById('otherActivities');
  var otherActivities = activities.filter(function (a) { return a.id !== id; });

  otherActivities.forEach(function (act) {
    var card = document.createElement('div');
    card.className = 'other-activity-card glass scroll-animate';

    card.innerHTML =
      '<a href="activity.html?id=' + encodeURIComponent(act.id) + '">' +
        '<div class="other-activity-img">' +
          '<img src="' + escapeHtml(act.image) + '" alt="' + escapeHtml(act.title) + '">' +
          '<div class="other-activity-img-overlay"></div>' +
        '</div>' +
        '<div class="other-activity-info">' +
          '<div class="other-activity-header">' +
            '<span>' + act.icon + '</span>' +
            '<h3>' + escapeHtml(act.title) + '</h3>' +
          '</div>' +
          '<p class="other-activity-desc">' + escapeHtml(act.description) + '</p>' +
        '</div>' +
      '</a>';

    othersContainer.appendChild(card);
  });

  // Set year
  var yearEl = document.getElementById('activityYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  // Init scroll animations
  initPageScrollAnimations();
});

function escapeHtml(text) {
  var div = document.createElement('div');
  div.appendChild(document.createTextNode(text));
  return div.innerHTML;
}

function initPageScrollAnimations() {
  var animElements = document.querySelectorAll('.scroll-animate');

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  animElements.forEach(function (el) {
    observer.observe(el);
  });
}
