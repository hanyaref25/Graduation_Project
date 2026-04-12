const activitiesData = {
  volunteer: [
    {
      icon: '🌿',
      badge: 'بيئي',
      color: 'al-img-1',
      title: 'تنظيف الحرم الجامعي',
      desc: 'حملات دورية لتنظيف وتجميل الحرم الجامعي وزراعة الأشجار.',
      date: 'كل جمعة',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '📦',
      badge: 'اجتماعي',
      color: 'al-img-2',
      title: 'جمع التبرعات',
      desc: 'تنظيم حملات لجمع الملابس والمستلزمات للأسر المحتاجة.',
      date: 'شهرياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '📖',
      badge: 'تعليمي',
      color: 'al-img-3',
      title: 'تعليم الأطفال',
      desc: 'مبادرة لتعليم أطفال المجتمع المحيط بالجامعة مجاناً.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '🩺',
      badge: 'صحي',
      color: 'al-img-4',
      title: 'قوافل طبية',
      desc: 'تنظيم قوافل طبية مجانية بالتعاون مع كلية الطب.',
      date: 'كل شهرين',
      target: 'طلاب الطب',
      link: 'https://example.com'
    }
  ],

  sports: [
    {
      icon: '⚽',
      badge: 'كرة قدم',
      color: 'al-img-2',
      title: 'بطولة كرة القدم',
      desc: 'بطولات ومسابقات داخلية وخارجية بين فرق الكليات.',
      date: 'الترم الأول',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '🏃',
      badge: 'جري',
      color: 'al-img-1',
      title: 'سباقات الجري',
      desc: 'تدريبات جماعية ومسابقات جري دورية داخل الحرم الجامعي.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '🎾',
      badge: 'تنس',
      color: 'al-img-3',
      title: 'دروس التنس',
      desc: 'حصص تدريبية ومباريات تنس للمبتدئين والمحترفين.',
      date: 'الترم الثاني',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '🏀',
      badge: 'كرة سلة',
      color: 'al-img-4',
      title: 'بطولة كرة السلة',
      desc: 'تدريبات وبطولات بين الكليات للطلاب المتحمسين.',
      date: 'الترم الأول',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    }
  ],

  cultural: [
    {
      icon: '🎨',
      badge: 'فنون',
      color: 'al-img-3',
      title: 'ورشة الفنون',
      desc: 'ورش عمل فنية لتطوير مهارات الرسم والتصميم والإبداع.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '🎭',
      badge: 'مسرح',
      color: 'al-img-4',
      title: 'فرقة المسرح',
      desc: 'تدريبات مسرحية وعروض فنية دورية داخل الجامعة.',
      date: 'الترم الثاني',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '📸',
      badge: 'تصوير',
      color: 'al-img-1',
      title: 'نادي التصوير',
      desc: 'ورش تصوير فوتوغرافي ومسابقات للمواهب الجديدة.',
      date: 'شهرياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '✍️',
      badge: 'كتابة',
      color: 'al-img-2',
      title: 'نادي الكتابة الإبداعية',
      desc: 'جلسات كتابة إبداعية وتطوير مهارات التعبير الأدبي.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    }
  ],

  educational: [
    {
      icon: '💼',
      badge: 'مهني',
      color: 'al-img-1',
      title: 'ورش سوق العمل',
      desc: 'ورش عمل لتعريف الطلاب بمتطلبات سوق العمل وبناء السيرة الذاتية.',
      date: 'شهرياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '💻',
      badge: 'تقني',
      color: 'al-img-2',
      title: 'دورات البرمجة',
      desc: 'دورات تدريبية في البرمجة وتطوير التطبيقات للمبتدئين.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '🗣️',
      badge: 'لغات',
      color: 'al-img-3',
      title: 'تعلم اللغات',
      desc: 'حصص تعليمية لتطوير مهارات اللغة الإنجليزية والتحدث بثقة.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    },
    {
      icon: '📊',
      badge: 'ريادة',
      color: 'al-img-4',
      title: 'ريادة الأعمال',
      desc: 'برنامج لتطوير مهارات ريادة الأعمال وبناء المشاريع الناشئة.',
      date: 'الترم الثاني',
      target: 'جميع الطلاب',
      link: 'https://example.com'
    }
  ]
};

const params = new URLSearchParams(window.location.search);
const activityId = params.get('id') || 'volunteer';
const grants = activitiesData[activityId] || activitiesData['volunteer'];

const heroTitles = {
  volunteer: { title: 'الأنشطة التطوعية', icon: '🤝' },
  sports:    { title: 'الأنشطة الرياضية', icon: '⚽' },
  cultural:  { title: 'الأنشطة الثقافية', icon: '🎨' },
  educational:{ title: 'الأنشطة التعليمية', icon: '📚' }
};

const hero = heroTitles[activityId];
document.querySelector('.al-hero-icon').textContent = hero.icon;
document.querySelector('.al-hero h1').textContent = hero.title + ' المتاحة';

const grid = document.getElementById('activitiesGrid');
grid.innerHTML = grants.map((g, i) => `
  <div class="al-card scroll-animate">
    <div class="al-card-img ${g.color}">
      <span class="al-card-emoji">${g.icon}</span>
    </div>
    <div class="al-card-body">
      <span class="al-badge">${g.badge}</span>
      <h3 class="al-card-title">${g.title}</h3>
      <p class="al-card-desc">${g.desc}</p>
      <div class="al-card-meta">
        <span>📅 الموعد: ${g.date}</span>
        <span>🎓 الفئة: ${g.target}</span>
      </div>
      <button class="al-btn-more" onclick="openModal(${i})">عرض المزيد</button>
    </div>
  </div>
`).join('');

function openModal(index) {
  const g = grants[index];
  document.getElementById('modalIcon').textContent = g.icon;
  document.getElementById('modalTitle').textContent = g.title;
  document.getElementById('modalDesc').textContent = g.desc;
  document.getElementById('modalDetails').innerHTML = `
    <span>📅 الموعد: ${g.date}</span>
    <span>🎓 الفئة المستهدفة: ${g.target}</span>
  `;
  document.getElementById('modalLink').href = g.link;
  document.getElementById('modalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.scroll-animate').forEach((el, i) => {
  el.style.transitionDelay = `${i * 0.15}s`;
  observer.observe(el);
});