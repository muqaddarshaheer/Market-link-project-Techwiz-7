/**
 * Smart Crop Guide — crop cards + weather advice modal
 */
(function () {
  'use strict';

  var app = document.getElementById('scgApp');
  if (!app) return;

  var base = (app.getAttribute('data-show-base') || '').replace(/\/$/, '');
  var modal = document.getElementById('scgModal');
  var loading = document.getElementById('scgLoading');
  var content = document.getElementById('scgContent');
  var lang = localStorage.getItem('scg-lang') || localStorage.getItem('farmer-lang') || 'en';
  var abortCtrl = null;

  setLang(lang);

  document.getElementById('scgGrid')?.addEventListener('click', function (e) {
    var card = e.target.closest('.scg-card');
    if (!card) return;
    openCrop(card.getAttribute('data-slug'));
  });

  modal?.querySelectorAll('[data-scg-close]').forEach(function (el) {
    el.addEventListener('click', closeModal);
  });

  modal?.querySelectorAll('[data-scg-lang]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      setLang(btn.getAttribute('data-scg-lang'));
      if (content && content._payload) {
        content.innerHTML = renderPayload(content._payload);
      }
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal && !modal.hidden) closeModal();
  });

  function setLang(next) {
    lang = next === 'ur' ? 'ur' : 'en';
    localStorage.setItem('scg-lang', lang);
    app.setAttribute('data-lang', lang);
    if (modal) {
      modal.setAttribute('data-lang', lang);
      modal.setAttribute('dir', lang === 'ur' ? 'rtl' : 'ltr');
    }
    modal?.querySelectorAll('[data-scg-lang]').forEach(function (btn) {
      btn.classList.toggle('is-active', btn.getAttribute('data-scg-lang') === lang);
    });
    app.querySelectorAll('.scg-en').forEach(function (el) {
      el.hidden = lang === 'ur';
    });
    app.querySelectorAll('.scg-ur').forEach(function (el) {
      el.hidden = lang !== 'ur';
    });
  }

  function openCrop(slug) {
    if (!slug || !modal) return;
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    loading.hidden = false;
    content.hidden = true;
    content.innerHTML = '';
    content._payload = null;

    if (abortCtrl) abortCtrl.abort();
    abortCtrl = new AbortController();

    fetch(base + '/' + encodeURIComponent(slug), {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      signal: abortCtrl.signal,
    })
      .then(function (res) {
        if (!res.ok) throw new Error('Failed');
        return res.json();
      })
      .then(function (data) {
        content._payload = data;
        content.innerHTML = renderPayload(data);
        loading.hidden = true;
        content.hidden = false;
      })
      .catch(function (err) {
        if (err.name === 'AbortError') return;
        loading.hidden = true;
        content.hidden = false;
        content.innerHTML =
          '<p class="scg-empty">' +
          (lang === 'ur' ? 'ڈیٹا لوڈ نہیں ہو سکا۔ دوبارہ کوشش کریں۔' : 'Could not load crop details. Please try again.') +
          '</p>';
      });
  }

  function closeModal() {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
    if (abortCtrl) abortCtrl.abort();
  }

  function t(obj) {
    if (!obj) return '';
    if (typeof obj === 'string') return obj;
    return (obj[lang] != null ? obj[lang] : obj.en) || '';
  }

  function detail(details, section, field) {
    var pack = (details && details[lang]) || (details && details.en) || {};
    var sec = pack[section] || {};
    return sec[field] || '';
  }

  function labels() {
    if (lang === 'ur') {
      return {
        amount: 'مقدار',
        frequency: 'فریکونسی',
        best: 'بہترین وقت',
        warning: 'انتباہ',
        type: 'قسم',
        req: 'ضروریات',
        range: 'حد',
        period: 'مدت',
        harvest: 'کٹائی',
        care: 'دیکھ بھال',
        pests: 'کیڑے',
        diseases: 'بیماریاں',
        prevention: 'بچاؤ',
        weather: 'موجودہ موسم',
        watering: 'آبپاشی',
        soil: 'مٹی',
        temp: 'درجہ حرارت',
        growth: 'نشوونما و کٹائی',
        problems: 'کیڑے و بیماریاں',
        today: 'آج کی کارروائی',
        doToday: 'آج کریں',
        careful: 'احتیاط',
        avoid: 'آج نہ کریں',
        advice: 'آج کی کھیتی مشورہ',
        humidity: 'نمی',
        rain: 'بارش',
        wind: 'ہوا',
        forecast: 'آگے کے دن',
        tempL: 'درجہ',
        place: 'جگہ',
        condition: 'حالت',
        detail: 'تفصیل',
        value: 'معلومات',
        action: 'عمل',
      };
    }
    return {
      amount: 'Amount',
      frequency: 'Frequency',
      best: 'Best time',
      warning: 'Warning',
      type: 'Type',
      req: 'Needs',
      range: 'Range',
      period: 'Period',
      harvest: 'Harvest',
      care: 'Care',
      pests: 'Pests',
      diseases: 'Diseases',
      prevention: 'Prevention',
      weather: 'Current weather',
      watering: 'Watering',
      soil: 'Soil',
      temp: 'Temperature',
      growth: 'Growth & harvest',
      problems: 'Pests & diseases',
      today: "Today's action",
      doToday: 'Do today',
      careful: 'Be careful',
      avoid: 'Avoid today',
      advice: "Today's farming advice",
      humidity: 'Humidity',
      rain: 'Rain',
      wind: 'Wind',
      forecast: 'Next days',
      tempL: 'Temp',
      place: 'Place',
      condition: 'Condition',
      detail: 'Detail',
      value: 'Info',
      action: 'Action',
    };
  }

  function renderPayload(data) {
    var L = labels();
    var name = t(data.name);
    var other = lang === 'ur' ? data.name.en : data.name.ur;
    var emoji = data.emoji || '🌱';
    var d = data.details || {};
    var w = data.weather;
    var a = data.advice || {};
    var watering = a.watering || {};
    var wLoc = watering[lang] || watering.en || {};
    var status = watering.status || 'careful';
    var headline = a.headline || {};
    var today = a.today || {};
    var summary = (a.summary && (a.summary[lang] || a.summary.en)) || {};

    var html = '';
    html += '<div class="scg-head">';
    html += '<div class="scg-head-emoji">' + esc(emoji) + '</div>';
    html += '<h2 id="scgModalTitle">' + esc(name) + (other ? ' / ' + esc(other) : '') + '</h2>';
    html += '<p class="scg-head-line">' + esc((headline.icon || '') + ' ' + (headline[lang] || headline.en || '')) + '</p>';
    html += '</div>';

    // TODAY advice first — highlighted ledger
    html += '<section class="scg-advice">';
    html += '<h3>🌾 ' + L.advice + '</h3>';
    html += table(L, [
      [L.watering, (watering.icon || '') + ' ' + (wLoc.label || '')],
      [lang === 'ur' ? 'وجہ' : 'Reason', wLoc.reason || ''],
      [L.soil, summary.soil ? String(summary.soil).replace(/^🌱\s*/, '') : ''],
      [L.weather, summary.weather ? String(summary.weather).replace(/^☀️\s*/, '') : ''],
    ]);
    html += '</section>';

    // Weather table
    html += '<section class="scg-section"><h3>🌤️ ' + L.weather + '</h3>';
    if (w) {
      html += table(L, [
        [L.tempL, w.temp != null ? w.temp + '°C' : '—'],
        [L.humidity, w.humidity != null ? w.humidity + '%' : '—'],
        [L.rain, w.rain_chance != null ? w.rain_chance + '%' : '—'],
        [L.wind, w.wind != null ? w.wind + ' km/h' : '—'],
        [L.condition, lang === 'ur' ? (w.summary_ur || w.summary) : w.summary],
        [L.place, w.place || '—'],
      ]);
      if (w.days && w.days.length) {
        html += '<div class="scg-forecast" aria-label="' + L.forecast + '">';
        w.days.slice(0, 5).forEach(function (day, i) {
          html +=
            '<div class="scg-day' +
            (i === 0 ? ' is-today' : '') +
            '"><span>' +
            esc(lang === 'ur' ? day.label_ur || day.label : day.label) +
            '</span><strong class="scg-day-date">' +
            esc(day.day_num || '') +
            (day.month ? ' ' + esc(lang === 'ur' ? day.month_ur || day.month : day.month) : '') +
            '</strong><i class="bi ' +
            esc(day.icon || 'bi-cloud') +
            '"></i><strong>' +
            (day.temp_max != null ? day.temp_max + '°' : '—') +
            '</strong><div>' +
            (day.rain_chance != null ? day.rain_chance + '%' : '') +
            '</div></div>';
        });
        html += '</div>';
      }
    } else {
      html += '<p class="scg-muted">' + (lang === 'ur' ? 'موسم دستیاب نہیں۔' : 'Weather unavailable.') + '</p>';
    }
    html += '</section>';

    // Watering
    html += '<section class="scg-section"><h3>💧 ' + L.watering + '</h3>';
    html +=
      '<div class="scg-water-banner status-' +
      esc(status) +
      '"><span class="scg-w-icon">' +
      esc(watering.icon || '') +
      '</span><div><strong>' +
      esc(wLoc.label || '') +
      '</strong><p>' +
      esc(wLoc.reason || '') +
      '</p></div></div>';
    html += table(L, [
      [L.amount, detail(d, 'water', 'amount')],
      [L.frequency, detail(d, 'water', 'frequency')],
      [L.best, detail(d, 'water', 'best_time')],
      [L.warning, detail(d, 'water', 'warning')],
    ]);
    html += '</section>';

    html += '<section class="scg-section"><h3>🌱 ' + L.soil + '</h3>';
    html += table(L, [
      [L.type, detail(d, 'soil', 'type')],
      [L.req, detail(d, 'soil', 'requirements')],
    ]);
    html += '</section>';

    html += '<section class="scg-section"><h3>☀️ ' + L.temp + '</h3>';
    html += table(L, [
      [L.range, detail(d, 'temperature', 'range')],
      [L.warning, detail(d, 'temperature', 'warning')],
    ]);
    html += '</section>';

    html += '<section class="scg-section"><h3>📅 ' + L.growth + '</h3>';
    html += table(L, [
      [L.period, detail(d, 'growth', 'period')],
      [L.harvest, detail(d, 'growth', 'harvest')],
      [L.care, detail(d, 'growth', 'care')],
    ]);
    html += '</section>';

    html += '<section class="scg-section"><h3>🐛 ' + L.problems + '</h3>';
    html += table(L, [
      [L.pests, detail(d, 'problems', 'pests')],
      [L.diseases, detail(d, 'problems', 'diseases')],
      [L.prevention, detail(d, 'problems', 'prevention')],
    ]);
    html += '</section>';

    // Actions as table
    html += '<section class="scg-section"><h3>📋 ' + L.today + '</h3>';
    var actionRows = [];
    listLang(today.do).forEach(function (item) {
      actionRows.push(['✅ ' + L.doToday, item]);
    });
    listLang(today.careful).forEach(function (item) {
      actionRows.push(['⚠️ ' + L.careful, item]);
    });
    listLang(today.avoid).forEach(function (item) {
      actionRows.push(['❌ ' + L.avoid, item]);
    });
    html += table(L, actionRows, true);
    html += '</section>';

    return html;
  }

  function listLang(block) {
    if (!block) return [];
    if (Array.isArray(block)) return block;
    return block[lang] || block.en || [];
  }

  function table(L, rows, actionMode) {
    var html =
      '<table class="scg-ledger"><thead><tr><th>' +
      esc(actionMode ? L.action : L.detail) +
      '</th><th>' +
      esc(L.value) +
      '</th></tr></thead><tbody>';
    var any = false;
    rows.forEach(function (row) {
      if (!row[1]) return;
      any = true;
      html +=
        '<tr><th scope="row">' +
        esc(row[0]) +
        '</th><td>' +
        esc(row[1]) +
        '</td></tr>';
    });
    if (!any) {
      html +=
        '<tr><td colspan="2" class="scg-muted">' +
        (lang === 'ur' ? 'تفصیل دستیاب نہیں' : 'No details available') +
        '</td></tr>';
    }
    html += '</tbody></table>';
    return html;
  }

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
})();
