const activitiesData = {
  volunteer: [
    {
      icon: '🌿',
      image: 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=400&q=80',
      badge: 'بيئي',
      color: 'al-img-1',
      title: 'تنظيف الحرم الجامعي',
      desc: 'حملات دورية لتنظيف وتجميل الحرم الجامعي وزراعة الأشجار.',
      date: 'كل جمعة',
      target: 'جميع الطلاب',
      link: 'https://shorturl.at/xB2NU'
    },
    {
      icon: '📦',
      image: 'https://plus.unsplash.com/premium_photo-1733306621909-1d63c088a93e?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
      badge: 'اجتماعي',
      color: 'al-img-2',
      title: ' مؤسسات خيرية',
      desc: 'المساهمة في تنفيذ مبادرات المؤسسة لتوصيل الدعم للمستحقين.',
      date: 'شهرياً',
      target: 'جميع الطلاب',
      link: 'https://shorturl.at/b1tmv'
    },
    {
      icon: '📖',
      image: 'https://plus.unsplash.com/premium_photo-1681830431271-d740702ec63f?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
      badge: 'انساني',
      color: 'al-img-3',
      title: 'المبادرات الإنسانية المستدامة',
      desc: 'دورة أونلاين مجانية تتناول التنمية المستدامة في سياق العمل الإنساني.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://shorturl.at/ZvKRx'
    },
    {
      icon: '🩺',
      image: 'https://images.unsplash.com/photo-1584515933487-779824d29309?w=400&q=80',
      badge: 'صحي',
      color: 'al-img-4',
      title: 'قوافل طبية',
      desc: 'تنظيم قوافل طبية مجانية بالتعاون مع كلية الطب.',
      date: 'كل شهرين',
      target: 'طلاب الطب',
      link: 'https://shorturl.at/NfD7V'
    }
  ],

  sports: [
    {
      icon: '⚽',
      image: 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=400&q=80',
      badge: 'كرة قدم',
      color: 'al-img-2',
      title: 'بطولة كرة القدم',
      desc: 'بطولات ومسابقات داخلية وخارجية بين فرق الكليات.',
      date: 'الترم الأول',
      target: 'جميع الطلاب',
      link: 'https://n9.cl/7i4xe'
    },
    {
      icon: '🏃',
      image: 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=400&q=80',
      badge: 'جري',
      color: 'al-img-1',
      title: 'سباقات الجري',
      desc: 'تدريبات جماعية ومسابقات جري دورية داخل الحرم الجامعي.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://n9.cl/h7vk8h'
    },
    {
      icon: '🎾',
      image: 'https://images.unsplash.com/photo-1542144582-1ba00456b5e3?w=400&q=80',
      badge: 'تنس',
      color: 'al-img-3',
      title: 'دروس التنس',
      desc: 'حصص تدريبية ومباريات تنس للمبتدئين والمحترفين.',
      date: 'الترم الثاني',
      target: 'جميع الطلاب',
      link: 'https://etf.tournamentsoftware.com/'
    },
    {
      icon: '🏀',
      image: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400&q=80',
      badge: 'كرة سلة',
      color: 'al-img-4',
      title: 'بطولة كرة السلة',
      desc: 'تدريبات وبطولات بين الكليات للطلاب المتحمسين.',
      date: 'الترم الأول',
      target: 'جميع الطلاب',
      link: 'https://n9.cl/8hrsc'
    }
  ],

  cultural: [
    {
      icon: '🎨',
      image: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&q=80',
      badge: 'فنون',
      color: 'al-img-3',
      title: 'ورشة الفنون',
      desc: 'ورش عمل فنية لتطوير مهارات الرسم والتصميم والإبداع.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://n9.cl/nnu84'
    },
    {
      icon: '🎭',
      image: 'https://images.unsplash.com/photo-1503095396549-807759245b35?w=400&q=80',
      badge: 'مسرح',
      color: 'al-img-4',
      title: 'فرقة المسرح',
      desc: 'تدريبات مسرحية وعروض فنية دورية داخل الجامعة.',
      date: 'الترم الثاني',
      target: 'جميع الطلاب',
      link: 'https://2u.pw/LqkoWE'
    },
    {
      icon: '📸',
      image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=400&q=80',
      badge: 'تصوير',
      color: 'al-img-1',
      title: 'نادي التصوير',
      desc: 'ورش تصوير فوتوغرافي ومسابقات للمواهب الجديدة.',
      date: 'شهرياً',
      target: 'جميع الطلاب',
      link: 'https://n9.cl/5myos'
    },
    {
      icon: '✍️',
      image: 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=400&q=80',
      badge: 'كتابة',
      color: 'al-img-2',
      title: 'نادي الكتابة الإبداعية',
      desc: 'جلسات كتابة إبداعية وتطوير مهارات التعبير الأدبي.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://2u.pw/g1ATna'
    }
  ],

  educational: [
    {
      icon: '💼',
      image: 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=400&q=80',
      badge: 'مهني',
      color: 'al-img-1',
      title: 'ورش سوق العمل',
      desc: 'ورش عمل لتعريف الطلاب بمتطلبات سوق العمل وبناء السيرة الذاتية.',
      date: 'شهرياً',
      target: 'جميع الطلاب',
      link: 'https://2u.pw/KCPDpg'
    },
    {
      icon: '💻',
      image: 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400&q=80',
      badge: 'تقني',
      color: 'al-img-2',
      title: 'دورات البرمجة',
      desc: 'دورات تدريبية في البرمجة وتطوير التطبيقات للمبتدئين.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://2u.pw/FNTxIf'
    },
    {
      icon: '🗣️',
      image: 'https://images.unsplash.com/photo-1543269865-cbf427effbad?w=400&q=80',
      badge: 'لغات',
      color: 'al-img-3',
      title: 'تعلم اللغات',
      desc: 'حصص تعليمية لتطوير مهارات اللغة الإنجليزية والتحدث بثقة.',
      date: 'أسبوعياً',
      target: 'جميع الطلاب',
      link: 'https://2u.pw/dNSf9D'
    },
    {
      icon: '📊',
      image: 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=400&q=80',
      badge: 'ريادة',
      color: 'al-img-4',
      title: 'ريادة الأعمال',
      desc: 'برنامج لتطوير مهارات ريادة الأعمال وبناء المشاريع الناشئة.',
      date: 'الترم الثاني',
      target: 'جميع الطلاب',
      link: 'https://2u.pw/Yp3PH5'
    }
  ]
};

const params = new URLSearchParams(window.location.search);
const activityId = params.get('id') || 'volunteer';
const grants = activitiesData[activityId] || activitiesData['volunteer'];

const heroTitles = {
  volunteer:   { title: 'الأنشطة التطوعية',  icon: '🤝' },
  sports:      { title: 'الأنشطة الرياضية',  icon: '⚽' },
  cultural:    { title: 'الأنشطة الثقافية',  icon: '🎨' },
  educational: { title: 'الأنشطة التعليمية', icon: '📚' }
};

const hero = heroTitles[activityId];
document.getElementById('alHeroIcon').textContent = hero.icon;
document.getElementById('alHeroTitle').textContent = hero.title + ' المتاحة';

document.getElementById('backBtn').href = `activity.html?id=${activityId}`;

const grid = document.getElementById('activitiesGrid');
grid.innerHTML = grants.map((g, i) => `
  <div class="al-card scroll-animate">
    <div class="al-card-img ${g.color}">
      <img src="${g.image}" alt="${g.title}" 
           onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
           style="width:100%; height:100%; object-fit:cover; position:absolute; inset:0;">
      <span class="al-card-emoji" style="display:none;">${g.icon}</span>
      <div class="al-card-overlay"></div>
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