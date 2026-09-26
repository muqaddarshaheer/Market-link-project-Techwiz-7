(function () {
  'use strict';

  var items = window.PG_ITEMS || {};
  var modal = document.getElementById('pgModal');
  var dialog = modal ? modal.querySelector('.pg-modal') : null;
  var closeBtn = document.getElementById('pgModalClose');
  var search = document.getElementById('pgSearch');
  var searchClear = document.getElementById('pgSearchClear');
  var filters = document.querySelectorAll('.pg-filter');
  var cards = Array.prototype.slice.call(document.querySelectorAll('.pg-card'));
  var langBtns = document.querySelectorAll('.pg-lang-btn');
  var tabs = document.querySelectorAll('.pg-tab');
  var surpriseBtn = document.getElementById('pgSurprise');
  var countEl = document.getElementById('pgCount');
  var emptyEl = document.getElementById('pgEmpty');
  var prevBtn = document.getElementById('pgPrev');
  var nextBtn = document.getElementById('pgNext');
  var activeFilter = 'all';
  var lang = localStorage.getItem('harvestwise-lang') || 'en';
  var currentKey = null;
  var visibleKeys = [];

  var labels = {
    en: {
      body: 'For the body',
      benefits: 'Benefits',
      cautions: 'Caution / Downsides',
      fruit: 'Fruit',
      vegetable: 'Vegetable',
      overview: 'Overview',
      benefitsTab: 'Benefits',
      cautionsTab: 'Cautions',
      showing: 'Showing',
      items: 'items',
      disclaimer: 'General food notes only — not medical advice. Ask a doctor or nutritionist for personal health decisions.'
    },
    ur: {
      body: 'جسم کے لیے',
      benefits: 'فائدے',
      cautions: 'احتیاط / نقصان',
      fruit: 'پھل',
      vegetable: 'سبزی',
      overview: 'خلاصہ',
      benefitsTab: 'فائدے',
      cautionsTab: 'احتیاط',
      showing: 'دکھ رہے ہیں',
      items: 'آئٹمز',
      disclaimer: 'یہ صرف عمومی غذائی نوٹس ہیں — طبی مشورہ نہیں۔ ذاتی صحت کے لیے ڈاکٹر یا ماہر غذائیت سے مشورہ لیں۔'
    }
  };

  function escapeHtml(s) {
    return String(s || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function listHtml(arr) {
    return (arr || []).map(function (t) {
      return '<li>' + escapeHtml(t) + '</li>';
    }).join('');
  }

  function setLangButtons() {
    langBtns.forEach(function (btn) {
      btn.classList.toggle('is-on', btn.getAttribute('data-lang') === lang);
    });
    if (dialog) dialog.setAttribute('data-lang', lang);
  }

  function setTabLabels() {
    var L = labels[lang] || labels.en;
    tabs.forEach(function (tab) {
      var key = tab.getAttribute('data-tab');
      if (key === 'overview') tab.textContent = L.overview;
      if (key === 'benefits') tab.textContent = L.benefitsTab;
      if (key === 'cautions') tab.textContent = L.cautionsTab;
    });
  }

  function showTab(name) {
    tabs.forEach(function (tab) {
      var on = tab.getAttribute('data-tab') === name;
      tab.classList.toggle('is-on', on);
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    document.querySelectorAll('.pg-panel').forEach(function (panel) {
      var on = panel.getAttribute('data-panel') === name;
      panel.classList.toggle('is-on', on);
      panel.hidden = !on;
    });
  }

  function fillModal() {
    if (!currentKey || !modal) return;
    var data = items[currentKey];
    if (!data) return;

    var ur = lang === 'ur';
    var L = labels[lang] || labels.en;

    document.getElementById('pgModalIcon').textContent = data.icon || '';
    document.getElementById('pgModalTitle').textContent = ur ? (data.ur || data.en || currentKey) : (data.en || currentKey);
    document.getElementById('pgModalSub').textContent = ur ? (data.en || '') : (data.ur || '');
    document.getElementById('pgModalType').textContent = data.type === 'fruit' ? L.fruit : L.vegetable;

    ['#pgLabelBody span', '#pgLabelBenefits span', '#pgLabelBenefits2 span', '#pgLabelCautions span', '#pgLabelCautions2 span'].forEach(function (sel, i) {
      var el = document.querySelector(sel);
      if (!el) return;
      if (i === 0) el.textContent = L.body;
      else if (i === 1 || i === 2) el.textContent = L.benefits;
      else el.textContent = L.cautions;
    });

    document.getElementById('pgDisclaimer').textContent = L.disclaimer;
    setTabLabels();

    document.getElementById('pgModalBody').textContent = ur
      ? (data.body_ur || data.body_en || '')
      : (data.body_en || data.body_ur || '');

    var benefits = ur ? (data.benefits_ur || data.benefits_en || []) : (data.benefits_en || data.benefits_ur || []);
    var cautions = ur ? (data.cautions_ur || data.cautions_en || []) : (data.cautions_en || data.cautions_ur || []);
    var htmlB = listHtml(benefits);
    var htmlC = listHtml(cautions);

    document.getElementById('pgModalBenefits').innerHTML = htmlB;
    document.getElementById('pgModalCautions').innerHTML = htmlC;
    var b2 = document.getElementById('pgModalBenefits2');
    var c2 = document.getElementById('pgModalCautions2');
    if (b2) b2.innerHTML = htmlB;
    if (c2) c2.innerHTML = htmlC;
  }

  function openItem(key) {
    if (!items[key]) return;
    currentKey = key;
    setLangButtons();
    showTab('overview');
    fillModal();
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    if (closeBtn) closeBtn.focus();
  }

  function closeModal() {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
    currentKey = null;
  }

  function moveItem(delta) {
    if (!currentKey || !visibleKeys.length) return;
    var i = visibleKeys.indexOf(currentKey);
    if (i < 0) i = 0;
    var next = visibleKeys[(i + delta + visibleKeys.length) % visibleKeys.length];
    openItem(next);
  }

  function updateBadges(totals) {
    document.querySelectorAll('[data-badge]').forEach(function (el) {
      var k = el.getAttribute('data-badge');
      el.textContent = String(totals[k] || 0);
    });
  }

  function applyView() {
    var q = (search && search.value ? search.value : '').trim().toLowerCase();
    if (searchClear) searchClear.hidden = !q;

    var fruitVisible = 0;
    var vegVisible = 0;
    var allVisible = 0;
    visibleKeys = [];

    cards.forEach(function (card) {
      var type = card.getAttribute('data-type');
      var en = (card.getAttribute('data-en') || '').toLowerCase();
      var ur = card.getAttribute('data-ur') || '';
      var key = card.getAttribute('data-key') || '';
      var matchFilter = activeFilter === 'all' || activeFilter === type;
      var matchSearch = !q || en.indexOf(q) !== -1 || ur.indexOf(q) !== -1 || key.indexOf(q) !== -1;
      var show = matchFilter && matchSearch;
      card.hidden = !show;
      if (show) {
        allVisible += 1;
        visibleKeys.push(key);
        if (type === 'fruit') fruitVisible += 1;
        if (type === 'vegetable') vegVisible += 1;
      }
    });

    updateBadges({
      all: cards.length,
      fruit: cards.filter(function (c) { return c.getAttribute('data-type') === 'fruit'; }).length,
      vegetable: cards.filter(function (c) { return c.getAttribute('data-type') === 'vegetable'; }).length
    });

    document.querySelectorAll('.pg-h[data-group="fruit"]').forEach(function (h) {
      h.hidden = activeFilter === 'vegetable' || fruitVisible === 0;
    });
    document.querySelectorAll('.pg-h[data-group="vegetable"]').forEach(function (h) {
      h.hidden = activeFilter === 'fruit' || vegVisible === 0;
    });

    var fg = document.getElementById('pgFruits');
    var vg = document.getElementById('pgVeggies');
    if (fg) fg.hidden = activeFilter === 'vegetable' || fruitVisible === 0;
    if (vg) vg.hidden = activeFilter === 'fruit' || vegVisible === 0;

    if (emptyEl) emptyEl.hidden = allVisible > 0;
    if (countEl) {
      var L = labels.en;
      countEl.textContent = L.showing + ' ' + allVisible + ' ' + L.items;
    }
  }

  cards.forEach(function (card) {
    card.addEventListener('click', function () {
      openItem(card.getAttribute('data-key'));
    });
  });

  filters.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filters.forEach(function (b) {
        b.classList.remove('is-on');
        b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('is-on');
      btn.setAttribute('aria-pressed', 'true');
      activeFilter = btn.getAttribute('data-filter') || 'all';
      applyView();
    });
  });

  langBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      lang = btn.getAttribute('data-lang') || 'en';
      localStorage.setItem('harvestwise-lang', lang);
      setLangButtons();
      fillModal();
    });
  });

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      showTab(tab.getAttribute('data-tab') || 'overview');
    });
  });

  if (search) search.addEventListener('input', applyView);
  if (searchClear) {
    searchClear.addEventListener('click', function () {
      if (!search) return;
      search.value = '';
      search.focus();
      applyView();
    });
  }

  if (surpriseBtn) {
    surpriseBtn.addEventListener('click', function () {
      applyView();
      if (!visibleKeys.length) return;
      var key = visibleKeys[Math.floor(Math.random() * visibleKeys.length)];
      openItem(key);
    });
  }

  if (prevBtn) prevBtn.addEventListener('click', function () { moveItem(-1); });
  if (nextBtn) nextBtn.addEventListener('click', function () { moveItem(1); });

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal();
    });
  }

  document.addEventListener('keydown', function (e) {
    if (!modal || modal.hidden) return;
    if (e.key === 'Escape') closeModal();
    if (e.key === 'ArrowLeft') { e.preventDefault(); moveItem(-1); }
    if (e.key === 'ArrowRight') { e.preventDefault(); moveItem(1); }
  });

  setLangButtons();
  applyView();
})();
