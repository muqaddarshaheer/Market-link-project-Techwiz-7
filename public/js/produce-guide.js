(function () {
  'use strict';

  var items = window.PG_ITEMS || {};
  var modal = document.getElementById('pgModal');
  var dialog = modal ? modal.querySelector('.pg-modal') : null;
  var closeBtn = document.getElementById('pgModalClose');
  var search = document.getElementById('pgSearch');
  var searchClear = document.getElementById('pgSearchClear');
  var filters = document.querySelectorAll('.pg-filter');
  var cards = Array.prototype.slice.call(document.querySelectorAll('.pg-circle'));
  var floats = document.querySelectorAll('.hw-float');
  var langBtns = document.querySelectorAll('.pg-lang-btn');
  var tabs = document.querySelectorAll('.pg-tab');
  var surpriseBtn = document.getElementById('pgSurprise');
  var scrollBtn = document.getElementById('pgScrollGrid');
  var countEl = document.getElementById('pgCount');
  var emptyEl = document.getElementById('pgEmpty');
  var prevBtn = document.getElementById('pgPrev');
  var nextBtn = document.getElementById('pgNext');
  var speakBtn = document.getElementById('pgSpeakBtn');
  var speakLabel = document.getElementById('pgSpeakLabel');
  var speakStatus = document.getElementById('pgSpeakStatus');
  var assistTitle = document.getElementById('pgAssistTitle');
  var assistHint = document.getElementById('pgAssistHint');
  var activeFilter = 'all';
  var lang = localStorage.getItem('harvestwise-lang') || 'en';
  var currentKey = null;
  var visibleKeys = [];
  var speaking = false;
  var speakToken = 0;
  var speakAudio = null;
  var root = document.getElementById('hwApp');
  var speakUrl = root ? (root.getAttribute('data-speak-url') || '') : '';

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
      assistTitle: 'HarvestWise Assistant',
      assistHint: 'Listen to the full details for this item.',
      read: 'Read aloud',
      stop: 'Stop',
      speaking: 'Reading full details…',
      noSpeak: 'Speech not available on this browser.',
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
      assistTitle: 'HarvestWise Assistant',
      assistHint: 'اس آئٹم کی پوری تفصیل سنیں۔',
      read: 'پوری تفصیل سنیں',
      stop: 'روکیں',
      speaking: 'پوری تفصیل پڑھ رہا ہوں…',
      noSpeak: 'آواز نہیں چل سکی۔ دوبارہ کوشش کریں یا نیٹ چیک کریں۔',
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

  function L() {
    return labels[lang] || labels.en;
  }

  function setLangButtons() {
    langBtns.forEach(function (btn) {
      btn.classList.toggle('is-on', btn.getAttribute('data-lang') === lang);
    });
    if (dialog) dialog.setAttribute('data-lang', lang);
  }

  function setTabLabels() {
    var lab = L();
    tabs.forEach(function (tab) {
      var key = tab.getAttribute('data-tab');
      if (key === 'overview') tab.textContent = lab.overview;
      if (key === 'benefits') tab.textContent = lab.benefitsTab;
      if (key === 'cautions') tab.textContent = lab.cautionsTab;
    });
  }

  function setAssistCopy() {
    var lab = L();
    if (assistTitle) assistTitle.textContent = lab.assistTitle;
    if (assistHint) assistHint.textContent = lab.assistHint;
    if (speakLabel) speakLabel.textContent = speaking ? lab.stop : lab.read;
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

  function stopSpeak() {
    speakToken += 1;
    speaking = false;
    try {
      if (window.speechSynthesis) window.speechSynthesis.cancel();
    } catch (e) {}
    try {
      if (speakAudio) {
        speakAudio.pause();
        speakAudio.removeAttribute('src');
        speakAudio.load();
      }
    } catch (e2) {}
    if (speakBtn) speakBtn.classList.remove('is-playing');
    if (speakStatus) speakStatus.hidden = true;
    setAssistCopy();
  }

  function buildSpeakScript(data) {
    var ur = lang === 'ur';
    var lab = L();
    var name = ur ? (data.ur || data.en) : (data.en || data.ur);
    var body = ur ? (data.body_ur || data.body_en || '') : (data.body_en || data.body_ur || '');
    var benefits = ur ? (data.benefits_ur || data.benefits_en || []) : (data.benefits_en || data.benefits_ur || []);
    var cautions = ur ? (data.cautions_ur || data.cautions_en || []) : (data.cautions_en || data.cautions_ur || []);
    var parts = [];
    parts.push(name + '.');
    parts.push(lab.body + '. ' + body);
    if (benefits.length) parts.push(lab.benefits + '. ' + benefits.join('. '));
    if (cautions.length) parts.push(lab.cautions + '. ' + cautions.join('. '));
    parts.push(lab.disclaimer);
    return parts.join(' ');
  }

  function chunkText(text, maxLen) {
    var clean = String(text || '').replace(/\s+/g, ' ').trim();
    if (!clean) return [];
    var parts = clean.split(/([۔.!?؟]+\s*)/);
    var sentences = [];
    for (var i = 0; i < parts.length; i += 2) {
      var s = (parts[i] || '') + (parts[i + 1] || '');
      s = s.trim();
      if (s) sentences.push(s);
    }
    if (!sentences.length) sentences = [clean];

    var chunks = [];
    var buf = '';
    sentences.forEach(function (part) {
      if ((buf + ' ' + part).trim().length <= maxLen) {
        buf = (buf + ' ' + part).trim();
        return;
      }
      if (buf) chunks.push(buf);
      if (part.length <= maxLen) {
        buf = part;
        return;
      }
      var i = 0;
      while (i < part.length) {
        chunks.push(part.slice(i, i + maxLen));
        i += maxLen;
      }
      buf = '';
    });
    if (buf) chunks.push(buf);
    return chunks;
  }

  function pickVoice(voices, wantUr) {
    var list = voices || [];
    var prefer = wantUr
      ? [/ur[-_]?PK/i, /\bur\b/i, /hindi/i, /hi[-_]?IN/i, /pakistan/i]
      : [/en[-_]?IN/i, /en[-_]?GB/i, /en[-_]?US/i, /english/i];
    for (var i = 0; i < prefer.length; i++) {
      for (var j = 0; j < list.length; j++) {
        var v = list[j];
        var blob = (v.lang || '') + ' ' + (v.name || '');
        if (prefer[i].test(blob)) return v;
      }
    }
    return null;
  }

  function playBlob(audio, blob, token) {
    return new Promise(function (resolve) {
      if (speakToken !== token) {
        resolve(false);
        return;
      }
      var url = URL.createObjectURL(blob);
      var done = false;
      function finish(ok) {
        if (done) return;
        done = true;
        try { URL.revokeObjectURL(url); } catch (e) {}
        resolve(ok && speakToken === token);
      }
      audio.onended = function () { finish(true); };
      audio.onerror = function () { finish(false); };
      audio.src = url;
      var p = audio.play();
      if (p && p.catch) {
        p.catch(function () { finish(false); });
      }
    });
  }

  async function speakViaServer(text, token) {
    if (!speakUrl) return false;
    if (!speakAudio) speakAudio = new Audio();
    var chunks = chunkText(text, 160);
    if (!chunks.length) return false;

    for (var i = 0; i < chunks.length; i++) {
      if (speakToken !== token) return false;
      try {
        var url = speakUrl
          + '?lang=' + encodeURIComponent(lang === 'ur' ? 'ur' : 'en')
          + '&text=' + encodeURIComponent(chunks[i]);
        var res = await fetch(url, {
          method: 'GET',
          credentials: 'same-origin',
          headers: { 'Accept': 'audio/mpeg,*/*' }
        });
        if (!res.ok) return false;
        var blob = await res.blob();
        if (!blob || blob.size < 800) return false;
        var ok = await playBlob(speakAudio, blob, token);
        if (!ok) return false;
      } catch (e) {
        return false;
      }
    }
    return speakToken === token;
  }

  function speakViaBrowser(text, token) {
    return new Promise(function (resolve) {
      if (!window.speechSynthesis || !text) {
        resolve(false);
        return;
      }
      try { window.speechSynthesis.cancel(); } catch (e) {}

      var utter = new SpeechSynthesisUtterance(text);
      var wantUr = lang === 'ur';
      // Most desktops have no Urdu voice — Hindi handles Nastaliq script better than English.
      utter.lang = wantUr ? 'hi-IN' : 'en-IN';
      utter.rate = wantUr ? 0.9 : 1;
      var voice = pickVoice(window.speechSynthesis.getVoices() || [], wantUr);
      if (voice) {
        utter.voice = voice;
        if (voice.lang) utter.lang = voice.lang;
      }

      utter.onend = function () { resolve(speakToken === token); };
      utter.onerror = function () { resolve(false); };
      window.speechSynthesis.speak(utter);
    });
  }

  async function speakDetails() {
    if (!currentKey || !items[currentKey]) return;
    if (speaking) {
      stopSpeak();
      return;
    }

    var text = buildSpeakScript(items[currentKey]);
    if (!text.trim()) return;

    var token = ++speakToken;
    speaking = true;
    setAssistCopy();
    if (speakBtn) speakBtn.classList.add('is-playing');
    if (speakStatus) {
      speakStatus.hidden = false;
      speakStatus.textContent = L().speaking;
    }

    var played = false;
    // Urdu: server TTS first (real ur/hi voice). Browser speech often silent for اردو.
    if (lang === 'ur') {
      played = await speakViaServer(text, token);
      if (!played && speakToken === token) {
        played = await speakViaBrowser(text, token);
      }
    } else {
      played = await speakViaBrowser(text, token);
      if (!played && speakToken === token) {
        played = await speakViaServer(text, token);
      }
    }

    if (speakToken !== token) return;
    speaking = false;
    if (speakBtn) speakBtn.classList.remove('is-playing');
    if (speakStatus) {
      if (!played) {
        speakStatus.hidden = false;
        speakStatus.textContent = L().noSpeak;
      } else {
        speakStatus.hidden = true;
      }
    }
    setAssistCopy();
  }

  function fillModal() {
    if (!currentKey || !modal) return;
    var data = items[currentKey];
    if (!data) return;

    var ur = lang === 'ur';
    var lab = L();

    document.getElementById('pgModalIcon').textContent = data.icon || '';
    document.getElementById('pgModalTitle').textContent = ur ? (data.ur || data.en || currentKey) : (data.en || currentKey);
    document.getElementById('pgModalSub').textContent = ur ? (data.en || '') : (data.ur || '');
    document.getElementById('pgModalType').textContent = data.type === 'fruit' ? lab.fruit : lab.vegetable;

    var sheet = modal.querySelector('.pg-modal');
    if (sheet) {
      sheet.setAttribute('data-type', data.type === 'vegetable' ? 'vegetable' : 'fruit');
      sheet.classList.remove('is-pop');
      void sheet.offsetWidth;
      sheet.classList.add('is-pop');
    }

    ['#pgLabelBody span', '#pgLabelBenefits span', '#pgLabelBenefits2 span', '#pgLabelCautions span', '#pgLabelCautions2 span'].forEach(function (sel, i) {
      var el = document.querySelector(sel);
      if (!el) return;
      if (i === 0) el.textContent = lab.body;
      else if (i === 1 || i === 2) el.textContent = lab.benefits;
      else el.textContent = lab.cautions;
    });

    document.getElementById('pgDisclaimer').textContent = lab.disclaimer;
    setTabLabels();
    setAssistCopy();

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
    stopSpeak();
    currentKey = key;
    setLangButtons();
    showTab('overview');
    fillModal();
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    if (!modal) return;
    stopSpeak();
    modal.hidden = true;
    document.body.style.overflow = '';
    currentKey = null;
  }

  function moveItem(delta) {
    if (!currentKey || !visibleKeys.length) return;
    var i = visibleKeys.indexOf(currentKey);
    if (i < 0) i = 0;
    openItem(visibleKeys[(i + delta + visibleKeys.length) % visibleKeys.length]);
  }

  function updateBadges() {
    document.querySelectorAll('[data-badge]').forEach(function (el) {
      var k = el.getAttribute('data-badge');
      if (k === 'all') el.textContent = String(cards.length);
      else {
        el.textContent = String(cards.filter(function (c) {
          return c.getAttribute('data-type') === k;
        }).length);
      }
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

    updateBadges();

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
    if (countEl) countEl.textContent = L().showing + ' ' + allVisible + ' ' + L().items;
  }

  cards.forEach(function (card) {
    card.addEventListener('click', function () {
      openItem(card.getAttribute('data-key'));
    });
  });

  floats.forEach(function (btn) {
    btn.addEventListener('click', function () {
      openItem(btn.getAttribute('data-key'));
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
      stopSpeak();
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
      openItem(visibleKeys[Math.floor(Math.random() * visibleKeys.length)]);
    });
  }

  if (scrollBtn) {
    scrollBtn.addEventListener('click', function () {
      var el = document.getElementById('pgGridSection');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  if (prevBtn) prevBtn.addEventListener('click', function () { moveItem(-1); });
  if (nextBtn) nextBtn.addEventListener('click', function () { moveItem(1); });
  if (speakBtn) speakBtn.addEventListener('click', speakDetails);

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

  if (window.speechSynthesis) {
    window.speechSynthesis.getVoices();
    window.speechSynthesis.onvoiceschanged = function () {
      window.speechSynthesis.getVoices();
    };
  }

  setLangButtons();
  applyView();
})();
