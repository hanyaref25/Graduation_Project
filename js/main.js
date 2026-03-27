// ===== Mobile Menu =====
function toggleMobileMenu() {
  var menu = document.getElementById('mobileMenu');
  var menuIcon = document.getElementById('menuIcon');
  var closeIcon = document.getElementById('closeIcon');
  menu.classList.toggle('open');
  menuIcon.classList.toggle('hidden');
  closeIcon.classList.toggle('hidden');
}

function closeMobileMenu() {
  var menu = document.getElementById('mobileMenu');
  var menuIcon = document.getElementById('menuIcon');
  var closeIcon = document.getElementById('closeIcon');
  menu.classList.remove('open');
  menuIcon.classList.remove('hidden');
  closeIcon.classList.add('hidden');
}

// ===== Navbar Scroll Effect =====
window.addEventListener('scroll', function () {
  var navbar = document.getElementById('navbar');
  if (window.scrollY > 50) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

// ===== Modal =====
function openModal() {
  var overlay = document.getElementById('modalOverlay');
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  var overlay = document.getElementById('modalOverlay');
  overlay.classList.remove('open');
  document.body.style.overflow = '';
  // Reset form state
  var form = document.getElementById('contactForm');
  var formContainer = document.getElementById('formContainer');
  var successMessage = document.getElementById('successMessage');
  if (form) form.reset();
  formContainer.classList.remove('hidden');
  successMessage.classList.add('hidden');
}

async function handleFormSubmit(e) {
  e.preventDefault();
  var form = document.getElementById('contactForm');
  var formContainer = document.getElementById('formContainer');
  var successMessage = document.getElementById('successMessage');
  var submitButton = form.querySelector('button[type="submit"]');
  var formData = new FormData(form);
  var selectedSlug = formData.get('activity_slug');

  var payload = {
    full_name: formData.get('full_name'),
    email: formData.get('email'),
    college: formData.get('college'),
    phone: formData.get('phone'),
    message: formData.get('message')
  };

  try {
    submitButton.disabled = true;
    submitButton.textContent = 'جاري الإرسال...';

    if (selectedSlug) {
      var activitiesResponse = await apiGet('/activities');
      var activity = (activitiesResponse.data || []).find(function (item) {
        return item.slug === selectedSlug;
      });

      if (activity) {
        payload.activity_id = activity.id;
      }
    }

    await apiPost('/applications', payload);

    formContainer.classList.add('hidden');
    successMessage.classList.remove('hidden');
    setTimeout(function () {
      closeModal();
    }, 2000);
  } catch (error) {
    alert(error.message || 'تعذر إرسال الطلب حاليًا. تأكد من تشغيل الباك إند.');
  } finally {
    submitButton.disabled = false;
    submitButton.innerHTML = '<span>✉️</span> إرسال';
  }
}

// ===== Scroll Animations =====
function initScrollAnimations() {
  var animElements = document.querySelectorAll('.scroll-animate, .scroll-animate-left, .scroll-animate-right');

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        var delay = entry.target.getAttribute('data-delay');
        if (delay) {
          setTimeout(function () {
            entry.target.classList.add('visible');
          }, parseInt(delay));
        } else {
          entry.target.classList.add('visible');
        }
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  animElements.forEach(function (el) {
    observer.observe(el);
  });
}

// ===== Add scroll-animate classes dynamically =====
function setupAnimations() {
  // About section
  document.querySelectorAll('.about-text').forEach(function (el, i) {
    el.classList.add(i % 2 === 0 ? 'scroll-animate-left' : 'scroll-animate-right');
  });
  document.querySelectorAll('.about-img').forEach(function (el, i) {
    el.classList.add(i % 2 === 0 ? 'scroll-animate-right' : 'scroll-animate-left');
  });

  // Activities
  document.querySelectorAll('.activities-header').forEach(function (el) {
    el.classList.add('scroll-animate');
  });
  document.querySelectorAll('.activity-card').forEach(function (el, i) {
    el.classList.add('scroll-animate');
    el.setAttribute('data-delay', String(i * 150));
  });

  // Why Join
  document.querySelectorAll('.reason-item').forEach(function (el, i) {
    el.classList.add('scroll-animate-left');
    el.setAttribute('data-delay', String(i * 150));
  });
  document.querySelectorAll('.whyjoin-center').forEach(function (el) {
    el.classList.add('scroll-animate');
  });

  // Mission & Vision
  document.querySelectorAll('.mv-header').forEach(function (el) {
    el.classList.add('scroll-animate');
  });
  document.querySelectorAll('.mv-card').forEach(function (el, i) {
    el.classList.add('scroll-animate');
    el.setAttribute('data-delay', String(i * 200));
  });

  // Goals
  document.querySelectorAll('.goals-header').forEach(function (el) {
    el.classList.add('scroll-animate');
  });
  document.querySelectorAll('.goal-card').forEach(function (el, i) {
    el.classList.add(i === 0 ? 'scroll-animate-left' : 'scroll-animate-right');
  });

  // Values
  document.querySelectorAll('.values-header').forEach(function (el) {
    el.classList.add('scroll-animate');
  });
  document.querySelectorAll('.values-banner').forEach(function (el) {
    el.classList.add('scroll-animate');
  });
  document.querySelectorAll('.values-cta-heading').forEach(function (el) {
    el.classList.add('scroll-animate');
  });

  // Steps
  document.querySelectorAll('.steps-header').forEach(function (el) {
    el.classList.add('scroll-animate');
  });
  document.querySelectorAll('.step-card').forEach(function (el, i) {
    el.classList.add('scroll-animate');
    el.setAttribute('data-delay', String(i * 200));
  });

  // Testimonials
  document.querySelectorAll('.testimonials-heading').forEach(function (el) {
    el.classList.add('scroll-animate');
  });
  document.querySelectorAll('.testimonial-card').forEach(function (el, i) {
    el.classList.add('scroll-animate');
    el.setAttribute('data-delay', String(i * 150));
  });
}

// ===== Footer Year =====
function setYear() {
  var el = document.getElementById('currentYear');
  if (el) el.textContent = new Date().getFullYear();
}

// ===== Init =====
document.addEventListener('DOMContentLoaded', function () {
  setYear();
  setupAnimations();
  initScrollAnimations();
});
