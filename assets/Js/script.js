const modal = document.getElementById('contactModal');
const closeBtn = document.getElementById('closeModal');
const joinBtns = document.querySelectorAll('.btn, .joinbtn, .cta button, .valuebtn');
joinBtns.forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    modal.classList.add('modalactive');
    document.body.style.overflow = 'hidden';
  });
});

// Close modal when clicking X button
closeBtn.addEventListener('click', function() {
  modal.classList.remove('modalactive');
  document.body.style.overflow = 'auto';
});

// Close modal when clicking out the box
modal.addEventListener('click', function(e) {
  if (e.target === modal) {
    modal.classList.remove('modalactive');
    document.body.style.overflow = 'auto';
  }
});

const form = document.getElementById('contactForm');
form.addEventListener('submit', function(e) {
  e.preventDefault();
  modal.classList.remove('modalactive');
  document.body.style.overflow = 'auto';
  form.reset();
});

// Close with Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && modal.classList.contains('modalactive')) {
    modal.classList.remove('modalactive');
    document.body.style.overflow = 'auto';
  }
});