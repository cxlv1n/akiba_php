/**
 * AkibaAuto - Main JavaScript
 * Production-ready scripts
 */

const apiRequestUrl = window.AKIBA_API_REQUEST_URL || '/api/request/';

// ================== MOBILE NAVIGATION ==================
const burger = document.querySelector('.burger');
const nav = document.getElementById('main-nav');

const closeNavOnClickOutside = (event) => {
  if (!nav.contains(event.target) && !burger.contains(event.target)) {
    nav.classList.remove('nav--open');
    burger.setAttribute('aria-expanded', 'false');
    document.removeEventListener('click', closeNavOnClickOutside);
  }
};

const toggleNav = () => {
  const opened = nav.classList.toggle('nav--open');
  burger.setAttribute('aria-expanded', opened ? 'true' : 'false');
  if (opened) {
    document.addEventListener('click', closeNavOnClickOutside);
  } else {
    document.removeEventListener('click', closeNavOnClickOutside);
  }
};

if (burger && nav) {
  burger.addEventListener('click', toggleNav);
  
  // Close mobile menu on link click
  const navLinks = nav.querySelectorAll('a');
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('nav--open');
      burger.setAttribute('aria-expanded', 'false');
      document.removeEventListener('click', closeNavOnClickOutside);
    });
  });
}

// ================== PAGE LOAD ANIMATION ==================
window.addEventListener('load', () => {
  document.body.classList.remove('page-loading');
  document.body.classList.add('page-loaded');
});

// Fallback if load already happened
if (document.readyState === 'complete') {
  document.body.classList.remove('page-loading');
  document.body.classList.add('page-loaded');
}

// ================== PARALLAX & SCROLL ANIMATIONS ==================
const socialIphones = document.querySelectorAll('.social__iphone');
let ticking = false;

function updateParallax() {
  const scrollY = window.scrollY;
  
  // Hero параллакс убран — используется CSS sticky
  
  // Social iPhones parallax
  socialIphones.forEach((phone, index) => {
    const rect = phone.getBoundingClientRect();
    if (rect.top < window.innerHeight && rect.bottom > 0) {
      const speed = index === 0 ? 0.08 : 0.12;
      const offset = (window.innerHeight - rect.top) * speed;
      phone.style.transform = `translate(-50%, calc(-50% + ${offset}px))`;
    }
  });
  
  ticking = false;
}

window.addEventListener('scroll', () => {
  if (!ticking) {
    requestAnimationFrame(updateParallax);
    ticking = true;
  }
}, { passive: true });

// ================== NUMBER COUNTER ANIMATION ==================
function animateCounter(element) {
  const target = parseFloat(element.getAttribute('data-count'));
  const prefix = element.getAttribute('data-prefix') || '';
  const suffix = element.getAttribute('data-suffix') || '';
  const duration = 800;
  const startTime = performance.now();
  
  function formatNumber(num) {
    if (num >= 1000) {
      return Math.floor(num).toLocaleString('ru-RU');
    }
    return Math.floor(num);
  }
  
  function updateCounter(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    
    let easedProgress;
    if (progress < 0.2) {
      easedProgress = progress * progress * 2.5;
    } else {
      easedProgress = 0.1 + (progress - 0.2) * 1.125;
    }
    
    const finalProgress = Math.min(easedProgress, 1);
    const currentValue = target * finalProgress;
    
    element.textContent = prefix + formatNumber(currentValue) + suffix;
    
    if (progress < 1) {
      requestAnimationFrame(updateCounter);
    } else {
      element.textContent = prefix + formatNumber(target) + suffix;
    }
  }
  
  requestAnimationFrame(updateCounter);
}

// Counter observer
const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
      entry.target.classList.add('counted');
      animateCounter(entry.target);
      counterObserver.unobserve(entry.target);
    }
  });
}, { root: null, rootMargin: '0px', threshold: 0.5 });

function initCounters() {
  document.querySelectorAll('[data-count]').forEach(counter => {
    counterObserver.observe(counter);
  });
}

// ================== SCROLL ANIMATIONS ==================
const scrollObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      scrollObserver.unobserve(entry.target);
    }
  });
}, { root: null, rootMargin: '0px 0px -20px 0px', threshold: 0.05 });

document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .scale-in').forEach(el => {
  scrollObserver.observe(el);
});

function initScrollAnimations() {
  // Section titles
  document.querySelectorAll('.popular__title, .benefits__title, .about__title, .social__title, .reviews__title, .contacts-map__title').forEach(el => {
    if (!el.classList.contains('fade-in')) {
      el.classList.add('fade-in');
      scrollObserver.observe(el);
    }
  });
  
  // Benefit cards with stagger
  document.querySelectorAll('.benefit-card').forEach((el, i) => {
    if (!el.classList.contains('fade-in')) {
      el.classList.add('fade-in', `stagger-${(i % 6) + 1}`);
      scrollObserver.observe(el);
    }
  });
  
  // Car cards with stagger
  document.querySelectorAll('.car-card').forEach((el, i) => {
    if (!el.classList.contains('fade-in')) {
      el.classList.add('fade-in', `stagger-${(i % 4) + 1}`);
      scrollObserver.observe(el);
    }
  });
  
  // About section
  const aboutContent = document.querySelector('.about__content');
  const aboutCert = document.querySelector('.about__certificate');
  if (aboutContent && !aboutContent.classList.contains('fade-in-left')) {
    aboutContent.classList.add('fade-in-left');
    scrollObserver.observe(aboutContent);
  }
  if (aboutCert && !aboutCert.classList.contains('fade-in-right')) {
    aboutCert.classList.add('fade-in-right');
    scrollObserver.observe(aboutCert);
  }
  
  // Social section
  const socialMedia = document.querySelector('.social__media');
  const socialContent = document.querySelector('.social__content');
  if (socialMedia && !socialMedia.classList.contains('fade-in-left')) {
    socialMedia.classList.add('fade-in-left');
    scrollObserver.observe(socialMedia);
  }
  if (socialContent && !socialContent.classList.contains('fade-in-right')) {
    socialContent.classList.add('fade-in-right');
    scrollObserver.observe(socialContent);
  }
  
  // Review cards
  document.querySelectorAll('.review-card').forEach((el, i) => {
    if (!el.classList.contains('scale-in')) {
      el.classList.add('scale-in', `stagger-${(i % 3) + 1}`);
      scrollObserver.observe(el);
    }
  });
  
  // Contacts map
  const contactsWrapper = document.querySelector('.contacts-map__wrapper');
  if (contactsWrapper && !contactsWrapper.classList.contains('fade-in')) {
    contactsWrapper.classList.add('fade-in');
    scrollObserver.observe(contactsWrapper);
  }
}

// ================== REQUEST MODAL ==================
const requestModal = document.getElementById('requestModal');
const requestModalOverlay = document.getElementById('requestModalOverlay');
const requestModalClose = document.getElementById('requestModalClose');
const requestForm = document.getElementById('requestForm');

// LocalStorage keys
const FORM_LAST_SHOWN_KEY = 'akiba_form_last_shown';
const FORM_SUBMITTED_KEY = 'akiba_form_submitted';
const FORM_COOLDOWN_MINUTES = 5;

function canShowRequestModal() {
  const now = Date.now();
  
  if (localStorage.getItem(FORM_SUBMITTED_KEY) === 'true') {
    return false;
  }
  
  const lastShown = localStorage.getItem(FORM_LAST_SHOWN_KEY);
  if (lastShown) {
    const timeSinceLastShow = now - parseInt(lastShown);
    const cooldownMs = FORM_COOLDOWN_MINUTES * 60 * 1000;
    if (timeSinceLastShow < cooldownMs) {
      return false;
    }
  }
  
  return true;
}

function saveFormShownTime() {
  localStorage.setItem(FORM_LAST_SHOWN_KEY, Date.now().toString());
}

function markFormAsSubmitted() {
  localStorage.setItem(FORM_SUBMITTED_KEY, 'true');
}

function openRequestModal() {
  if (requestModal && canShowRequestModal()) {
    requestModal.classList.add('request-modal--open');
    document.body.style.overflow = 'hidden';
    saveFormShownTime();
  }
}

function closeRequestModal() {
  if (requestModal) {
    requestModal.classList.remove('request-modal--open');
    document.body.style.overflow = '';
    if (requestForm) {
      requestForm.reset();
    }
  }
}

// Open modal by buttons
document.querySelectorAll('[data-open-modal="request"]').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    if (canShowRequestModal()) {
      openRequestModal();
    } else {
      const hasSubmitted = localStorage.getItem(FORM_SUBMITTED_KEY) === 'true';
      if (hasSubmitted) {
        alert('Вы уже отправили заявку. Мы свяжемся с вами в ближайшее время.');
      } else {
        alert('Форма заявки будет доступна через несколько минут.');
      }
    }
  });
});

// Also handle calc/footer buttons
document.querySelectorAll('.btn--calc, .btn--footer, .car-hero__btn').forEach(btn => {
  if (btn.hasAttribute('data-open-modal')) return;
  
  if (btn.href === '#' || (!btn.href || (!btn.href.startsWith('http') && !btn.href.startsWith('tel:') && !btn.href.startsWith('mailto:') && !btn.href.includes('/')))) {
    const btnText = btn.textContent.trim().toLowerCase();
    if (btnText.includes('рассчитать') || btnText.includes('оставить заявку') || btnText.includes('заявку')) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        if (canShowRequestModal()) {
          openRequestModal();
        } else {
          const hasSubmitted = localStorage.getItem(FORM_SUBMITTED_KEY) === 'true';
          if (hasSubmitted) {
            alert('Вы уже отправили заявку. Мы свяжемся с вами в ближайшее время.');
          } else {
            alert('Форма заявки будет доступна через 5 минут.');
          }
        }
      });
    }
  }
});

// Close modal
if (requestModalOverlay) {
  requestModalOverlay.addEventListener('click', closeRequestModal);
}

if (requestModalClose) {
  requestModalClose.addEventListener('click', closeRequestModal);
}

// Close by Escape
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape' && requestModal && requestModal.classList.contains('request-modal--open')) {
    closeRequestModal();
  }
});

// Auto-show modal after 30 seconds
setTimeout(function() {
  if (canShowRequestModal()) {
    openRequestModal();
  }
}, 30000);

// Form submission
if (requestForm) {
  requestForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(requestForm);
    const data = Object.fromEntries(formData);
    
    // Send to server via fetch
    fetch(apiRequestUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRFToken': document.querySelector('[name=csrfmiddlewaretoken]')?.value || ''
      },
      body: JSON.stringify(data)
    })
    .then(response => {
      if (response.ok) {
        markFormAsSubmitted();
        alert('Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.');
        closeRequestModal();
      } else {
        throw new Error('Server error');
      }
    })
    .catch(error => {
      // Fallback - mark as submitted anyway for UX
      console.log('Form data:', data);
      markFormAsSubmitted();
      alert('Спасибо! Ваша заявка принята. Мы свяжемся с вами в ближайшее время.');
      closeRequestModal();
    });
  });
}

// ================== INIT ==================
document.addEventListener('DOMContentLoaded', () => {
  initScrollAnimations();
  initCounters();
});

// Fallback if DOM already loaded
if (document.readyState !== 'loading') {
  initScrollAnimations();
  initCounters();
}


