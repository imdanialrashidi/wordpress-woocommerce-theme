/* MobileKh app — vanilla JS, no dependencies. */
(function () {
  'use strict';

  /* Theme toggle with persistence (cookie = SSR, localStorage = instant). */
  var toggles = document.querySelectorAll('.theme-toggle');
  toggles.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
      document.documentElement.dataset.theme = next;
      try {
        localStorage.setItem('mobilekh-theme', next);
        document.cookie = 'mobilekh-theme=' + next + ';path=/;max-age=31536000';
      } catch (e) {}
    });
  });

  /* Mobile drawer. */
  var navToggle = document.querySelector('.nav-toggle');
  var drawer = document.querySelector('.mobile-drawer');
  if (navToggle && drawer) {
    navToggle.addEventListener('click', function () {
      var open = drawer.hasAttribute('hidden');
      if (open) {
        drawer.removeAttribute('hidden');
        navToggle.setAttribute('aria-expanded', 'true');
      } else {
        drawer.setAttribute('hidden', '');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* Popup with day-based re-show. */
  var popup = document.getElementById('mobilekh-popup');
  if (popup && window.mobilekhConfig && window.mobilekhConfig.popup.enabled) {
    var days = parseInt(window.mobilekhConfig.popup.days, 10) || 0;
    var key = 'mobilekh-popup-closed';
    var show = true;
    try {
      var last = parseInt(localStorage.getItem(key) || '0', 10);
      if (days > 0 && last && Date.now() - last < days * 86400000) { show = false; }
    } catch (e) {}
    if (show) {
      setTimeout(function () { popup.removeAttribute('hidden'); }, 1200);
    }
    var close = function () {
      popup.setAttribute('hidden', '');
      try { localStorage.setItem(key, String(Date.now())); } catch (e) {}
    };
    var closeBtn = popup.querySelector('.popup-close');
    if (closeBtn) { closeBtn.addEventListener('click', close); }
    popup.addEventListener('click', function (ev) { if (ev.target === popup) { close(); } });
    document.addEventListener('keydown', function (ev) { if (ev.key === 'Escape' && !popup.hasAttribute('hidden')) { close(); } });
  }
  /* Sticky add-to-cart (single product, mobile). */
  var stickyBar = document.querySelector('.sticky-atc');
  var cartForm = document.querySelector('form.cart');
  if (stickyBar && cartForm) {
    var stickyBtn = stickyBar.querySelector('.sticky-atc-btn');
    var onScroll = function () {
      var r = cartForm.getBoundingClientRect();
      if (r.bottom < 0) { stickyBar.removeAttribute('hidden'); }
      else { stickyBar.setAttribute('hidden', ''); }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    if (stickyBtn) {
      stickyBtn.addEventListener('click', function () {
        var submit = cartForm.querySelector('[type="submit"]');
        if (submit) { submit.click(); }
      });
    }
  }
  /* Homepage slider: autoplay, swipe, dots. */
  var slider = document.querySelector('.hero-slider');
  if (slider) {
    var track = slider.querySelector('.hero-track');
    var slides = track ? track.children.length : 0;
    var dots = slider.querySelectorAll('.hero-dots button');
    var idx = 0;
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var go = function (n) {
      if (!slides) { return; }
      idx = (n + slides) % slides;
      track.style.transform = 'translateX(' + (idx * 100) + '%)';
      dots.forEach(function (d, k) { d.classList.toggle('on', k === idx); });
    };
    var prev = slider.querySelector('.hero-prev');
    var next = slider.querySelector('.hero-next');
    if (prev) { prev.addEventListener('click', function () { go(idx - 1); restart(); }); }
    if (next) { next.addEventListener('click', function () { go(idx + 1); restart(); }); }
    dots.forEach(function (d, k) { d.addEventListener('click', function () { go(k); restart(); }); });
    var timer = null;
    var restart = function () {
      if (timer) { clearInterval(timer); }
      if (!reduceMotion && slides > 1) { timer = setInterval(function () { go(idx + 1); }, 6000); }
    };
    slider.addEventListener('mouseenter', function () { if (timer) { clearInterval(timer); timer = null; } });
    slider.addEventListener('mouseleave', restart);
    var startX = null;
    track.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function (e) {
      if (startX === null) { return; }
      var dx = e.changedTouches[0].clientX - startX;
      if (Math.abs(dx) > 40) { go(idx + (dx < 0 ? 1 : -1)); restart(); }
      startX = null;
    }, { passive: true });
    go(0);
    restart();
  }

  /* Deals countdown. */
  var cd = document.querySelector('.countdown[data-until]');
  if (cd) {
    var until = Date.parse(cd.getAttribute('data-until'));
    var set = function (k, v) { var el = cd.querySelector('[data-cd="' + k + '"]'); if (el) { el.textContent = v; } };
    var tick = function () {
      var diff = until - Date.now();
      if (isNaN(until) || diff <= 0) { cd.style.display = 'none'; return; }
      set('d', Math.floor(diff / 86400000));
      set('h', Math.floor(diff / 3600000) % 24);
      set('m', Math.floor(diff / 60000) % 60);
      set('s', Math.floor(diff / 1000) % 60);
      if (typeof faDigits === 'function') { faDigits(cd); }
    };
    tick();
    setInterval(tick, 1000);
  }

  /* Reveal on scroll. */
  var reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.12 });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }
  /* Persian digits: convert 0-9 in visible text (skip code-like contexts). */
  var FA_D = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
  var SKIP_TAGS = /^(SCRIPT|STYLE|PRE|CODE|TEXTAREA|INPUT)$/;
  var faDigits = function (root) {
    var walker = document.createTreeWalker(root || document.body, NodeFilter.SHOW_TEXT);
    var nodes = [];
    while (walker.nextNode()) { nodes.push(walker.currentNode); }
    nodes.forEach(function (n) {
      var p = n.parentElement;
      if (!p || SKIP_TAGS.test(p.tagName)) { return; }
      if (/[0-9]/.test(n.nodeValue)) {
        n.nodeValue = n.nodeValue.replace(/[0-9]/g, function (d) { return FA_D[+d]; });
      }
    });
  };
  faDigits(document.body);
  document.addEventListener('wc_fragments_refreshed', function () { faDigits(document.body); });

  /* Shop mega menu toggle (touch). */
  var megaToggle = document.querySelector('.mega-toggle');
  var megaPanel = document.querySelector('.mega-panel');
  if (megaToggle && megaPanel) {
    megaToggle.addEventListener('click', function (e) {
      e.preventDefault();
      var open = megaPanel.hasAttribute('hidden');
      if (open) { megaPanel.removeAttribute('hidden'); megaToggle.setAttribute('aria-expanded', 'true'); }
      else { megaPanel.setAttribute('hidden', ''); megaToggle.setAttribute('aria-expanded', 'false'); }
    });
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.mega-wrap') && !megaPanel.hasAttribute('hidden')) {
        megaPanel.setAttribute('hidden', '');
        megaToggle.setAttribute('aria-expanded', 'false');
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !megaPanel.hasAttribute('hidden')) {
        megaPanel.setAttribute('hidden', '');
        megaToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

})();

/* Live AJAX search (standalone: header + mobile drawer). */
(function () {
  'use strict';
  if (!window.mobilekhConfig || !window.mobilekhConfig.ajax) { return; }
  /* Persian digits for prices (skip code-like contexts). */
  var FA_D = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
  var SKIP_TAGS = /^(SCRIPT|STYLE|PRE|CODE|TEXTAREA|INPUT)$/;
  var faDigits = function (root) {
    var walker = document.createTreeWalker(root || document.body, NodeFilter.SHOW_TEXT);
    var nodes = [];
    while (walker.nextNode()) { nodes.push(walker.currentNode); }
    nodes.forEach(function (n) {
      var p = n.parentElement;
      if (!p || SKIP_TAGS.test(p.tagName)) { return; }
      if (/[0-9]/.test(n.nodeValue)) {
        n.nodeValue = n.nodeValue.replace(/[0-9]/g, function (d) { return FA_D[+d]; });
      }
    });
  };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
  var forms = document.querySelectorAll('.header-search, .drawer-search');
  if (!forms.length) { return; }
  var closeAll = function () {
    document.querySelectorAll('.header-search .search-results, .drawer-search .search-results').forEach(function (b) { b.setAttribute('hidden', ''); });
  };
  forms.forEach(function (searchForm) {
    var input = searchForm.querySelector('input[type="search"]');
    var box = searchForm.querySelector('.search-results');
    if (!input || !box) { return; }
    var timerS = null;
    var showMsg = function (msg) {
      box.innerHTML = '<div class="search-empty">' + esc(msg) + '</div>';
      box.removeAttribute('hidden');
    };
    input.addEventListener('input', function () {
      var q = input.value.trim();
      if (timerS) { clearTimeout(timerS); }
      if (q.length < 2) { box.setAttribute('hidden', ''); return; }
      showMsg('در حال جستجو…');
      timerS = setTimeout(function () {
        fetch(window.mobilekhConfig.ajax + '?action=mobilekh_search&q=' + encodeURIComponent(q))
          .then(function (r) { if (!r.ok) { throw new Error('http'); } return r.json(); })
          .then(function (items) {
            if (!items || !items.length) {
              showMsg('نتیجه‌ای یافت نشد.');
            } else {
              box.innerHTML = items.map(function (it) {
                return '<a class="search-hit" href="' + esc(it.url) + '">' +
                  (it.img ? '<img src="' + esc(it.img) + '" alt="" loading="lazy">' : '') +
                  '<span class="t" dir="auto">' + esc(it.name) + '</span>' +
                  '<span class="p" dir="auto">' + esc(it.price) + '</span></a>';
              }).join('');
              box.removeAttribute('hidden');
              faDigits(box);
            }
          })
          .catch(function () { showMsg('خطا در جستجو — اینتر بزنید.'); });
      }, 300);
    });
  });
  document.addEventListener('click', function (e) { if (!e.target.closest('.header-search, .drawer-search')) { closeAll(); } });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeAll(); } });
})();

/* FAQ accordion: pair question paragraphs (ending with ؟ or ?) with answers. */
(function () {
  if (!document.body.classList.contains('page-id-9586')) { return; }
  var wrap = document.querySelector('.page-content');
  if (!wrap) { return; }
  var kids = Array.prototype.slice.call(wrap.children);
  var isQ = function (el) {
    return el && el.tagName === 'P' && /[؟?]\s*$/.test(el.textContent.trim());
  };
  var nQ = kids.filter(isQ).length;
  if (nQ < 2) { return; }
  var acc = document.createElement('div');
  acc.className = 'faq-list';
  kids.forEach(function (el) {
    if (isQ(el)) {
      var d = document.createElement('details');
      d.className = 'faq-item';
      var s = document.createElement('summary');
      s.textContent = el.textContent.trim();
      var b = document.createElement('div');
      b.className = 'faq-a';
      d.appendChild(s); d.appendChild(b);
      acc.appendChild(d);
      wrap.insertBefore(acc, el);
      el.remove();
    } else if (el.tagName === 'P' && acc.parentNode) {
      var last = acc.lastChild;
      if (last) { last.querySelector('.faq-a').appendChild(el); }
    }
  });
  var first = acc.querySelector('.faq-item');
  if (first) { first.setAttribute('open', ''); }
})();

/* Latin digits for phone/username inputs (gateways reject Persian digits). */
(function () {
  var FA = '[\u06F0-\u06F9\u0660-\u0669]';
  var toLatin = function (s) {
    return String(s)
      .replace(/[\u06F0-\u06F9]/g, function (d) { return String(d.charCodeAt(0) - 0x06F0); })
      .replace(/[\u0660-\u0669]/g, function (d) { return String(d.charCodeAt(0) - 0x0660); });
  };
  var hasFa = function (s) { return new RegExp(FA).test(s || ''); };
  /* Hidden register email must not block HTML5 validation (JS + server fill it). */
  document.querySelectorAll('.woocommerce-form-register #reg_email').forEach(function (el) {
    el.removeAttribute('required');
    el.setAttribute('aria-required', 'false');
  });
  document.addEventListener('submit', function (ev) {
    var f = ev.target;
    if (!f || !f.matches) { return; }
    if (!f.matches('form.checkout, form.register, form.login, .woocommerce-form-login, .woocommerce-form-register')) { return; }
    var fields = f.querySelectorAll('input[type="tel"], input[name="billing_phone"], input[name="username"]');
    fields.forEach(function (el) {
      if (el.type !== 'password' && hasFa(el.value)) { el.value = toLatin(el.value); }
    });
    /* Phone-only registration: fill a placeholder email when it is empty. */
    if (f.matches('form.register, .woocommerce-form-register')) {
      var phoneEl = f.querySelector('input[name="billing_phone"]');
      var emailEl = f.querySelector('input[type="email"], input[name="email"]');
      if (phoneEl && emailEl && !emailEl.value.trim()) {
        var ph = toLatin(phoneEl.value).replace(/[\s\-()+]/g, '');
        if (ph.indexOf('0098') === 0) { ph = '0' + ph.slice(4); }
        else if (ph.indexOf('+98') === 0) { ph = '0' + ph.slice(3); }
        else if (ph.indexOf('98') === 0 && ph.length === 12) { ph = '0' + ph.slice(2); }
        if (/^09\d{9}$/.test(ph)) { emailEl.value = ph + '@' + window.location.hostname; }
      }
    }
  });
})();
