(function () {
  'use strict';

  function boot() {
    var root = document.getElementById('sccApp');
    var form = document.getElementById('sccForm');
    if (!root || !form) return;

    var calcUrl = root.getAttribute('data-calc-url');
    var csrf = root.getAttribute('data-csrf')
      || (document.querySelector('meta[name="csrf-token"]') || {}).content
      || '';

    var crop = document.getElementById('sccCrop');
    var cropOtherWrap = document.getElementById('sccCropOtherWrap');
    var budget = document.getElementById('sccBudget');
    var otherCost = document.getElementById('sccOtherCost');
    var prodUnit = document.getElementById('sccProdUnit');
    var priceUnit = document.getElementById('sccPriceUnit');
    var errorsEl = document.getElementById('sccErrors');
    var submitBtn = document.getElementById('sccSubmit');
    var resetBtn = document.getElementById('sccReset');
    var modal = document.getElementById('sccModal');
    var modalClose = document.getElementById('sccModalClose');
    var modalOk = document.getElementById('sccModalOk');
    var printBtn = document.getElementById('sccPrintBtn');
    var resultHero = document.getElementById('sccResultHero');
    var resultLabel = document.getElementById('sccResultLabel');
    var resultAmt = document.getElementById('sccResultAmt');
    var resultList = document.getElementById('sccResultList');
    var modalTitle = document.getElementById('sccModalTitle');
    var assistBtn = document.getElementById('sccAssistBtn');
    var urduBtn = document.getElementById('sccUrduBtn');
    var urduLabel = document.getElementById('sccUrduLabel');
    var speakUrl = root.getAttribute('data-speak-url') || '';
    var speakAudio = null;
    var speaking = false;
    var lastResult = null;

    function freshCsrf() {
      csrf = (window.mlCsrf && window.mlCsrf.token ? window.mlCsrf.token() : null)
        || (document.querySelector('meta[name="csrf-token"]') || {}).content
        || root.getAttribute('data-csrf')
        || csrf;
      return csrf;
    }

    function isUr() {
      return document.documentElement.getAttribute('data-farmer-lang') === 'ur';
    }

    function money(n) {
      return 'Rs. ' + (Number(n) || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }

    function num(n) {
      return (Number(n) || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function escapeHtml(s) {
      return String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    function syncSelectLang() {
      var ur = isUr();
      document.querySelectorAll('#sccApp select option[data-en][data-ur]').forEach(function (opt) {
        opt.textContent = ur ? (opt.getAttribute('data-ur') || opt.getAttribute('data-en')) : (opt.getAttribute('data-en') || opt.textContent);
      });
      if (urduBtn) urduBtn.setAttribute('aria-pressed', ur ? 'true' : 'false');
      if (urduLabel) urduLabel.textContent = ur ? 'EN' : 'اردو';
    }

    function assistText() {
      if (isUr()) {
        return 'فصل کیلکولیٹر استعمال کریں۔ پہلے فصل چنیں۔ پھر زمین اور یونٹ لکھیں۔ کل خرچہ روپوں میں لکھیں۔ متوقع پیداوار اور بیچنے کا ریٹ لکھیں۔ ضائع فیصد رکھیں۔ حساب لگائیں دبائیں — منافع یا نقصان پاپ اپ میں دکھے گا۔';
      }
      return 'Use the crop calculator. First pick a crop. Then enter land and unit. Enter total cost in rupees. Enter expected harvest and selling rate. Set wastage percent. Tap Calculate — profit or loss opens in a popup.';
    }

    function stopAssist() {
      speaking = false;
      if (speakAudio) {
        try { speakAudio.pause(); } catch (e) {}
        speakAudio = null;
      }
      if (window.speechSynthesis) window.speechSynthesis.cancel();
      if (assistBtn) assistBtn.classList.remove('is-speaking');
    }

    function speakAssist() {
      if (speaking) {
        stopAssist();
        return;
      }
      var text = assistText();
      var lang = isUr() ? 'ur' : 'en';
      speaking = true;
      if (assistBtn) assistBtn.classList.add('is-speaking');

      if (speakUrl) {
        var url = speakUrl + '?lang=' + encodeURIComponent(lang) + '&text=' + encodeURIComponent(text);
        speakAudio = new Audio(url);
        speakAudio.addEventListener('ended', stopAssist);
        speakAudio.addEventListener('error', function () {
          // Fallback to browser voice
          tryBrowserSpeak(text, lang);
        });
        speakAudio.play().catch(function () {
          tryBrowserSpeak(text, lang);
        });
        return;
      }
      tryBrowserSpeak(text, lang);
    }

    function tryBrowserSpeak(text, lang) {
      if (!window.speechSynthesis) {
        stopAssist();
        return;
      }
      var u = new SpeechSynthesisUtterance(text);
      u.lang = lang === 'ur' ? 'ur-PK' : 'en-PK';
      u.onend = stopAssist;
      u.onerror = stopAssist;
      window.speechSynthesis.speak(u);
    }

    function toggleUrdu() {
      var next = isUr() ? 'en' : 'ur';
      if (window.farmerI18n && window.farmerI18n.apply) {
        if (next === 'en') {
          localStorage.setItem('farmer-lang', 'en');
          location.reload();
          return;
        }
        window.farmerI18n.apply('ur');
      } else {
        document.documentElement.setAttribute('data-farmer-lang', next);
      }
      syncSelectLang();
      stopAssist();
    }

    function syncBudget() {
      if (budget && otherCost) otherCost.value = budget.value || 0;
    }

    function toggleOther() {
      if (!cropOtherWrap) return;
      cropOtherWrap.hidden = crop.value !== 'other';
    }

    function matchPriceUnit() {
      if (!prodUnit || !priceUnit) return;
      var map = { kg: 'per_kg', maund: 'per_maund', ton: 'per_ton' };
      if (map[prodUnit.value]) priceUnit.value = map[prodUnit.value];
    }

    function showErrors(list) {
      if (!errorsEl) return;
      if (!list || !list.length) {
        errorsEl.hidden = true;
        errorsEl.innerHTML = '';
        return;
      }
      errorsEl.hidden = false;
      errorsEl.innerHTML = '<ul class="mb-0">' + list.map(function (e) {
        return '<li>' + escapeHtml(e) + '</li>';
      }).join('') + '</ul>';
    }

    function payloadFromForm() {
      var fd = new FormData(form);
      var obj = {};
      fd.forEach(function (v, k) { obj[k] = v; });
      [
        'land_amount', 'budget', 'seed_qty', 'seed_unit_cost', 'fertilizer', 'water',
        'labor', 'transport', 'other_cost', 'prod_qty', 'price', 'wastage',
      ].forEach(function (k) {
        if (obj[k] === '' || obj[k] == null) obj[k] = 0;
        obj[k] = Number(obj[k]);
      });
      return obj;
    }

    function openModal() {
      if (!modal) return;
      modal.hidden = false;
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      if (!modal) return;
      modal.hidden = true;
      document.body.style.overflow = '';
    }

    function showResult(r) {
      lastResult = r || null;
      var ur = isUr();
      var ok = !!r.is_profit;
      if (modalTitle) modalTitle.textContent = ur ? 'نتیجہ' : 'Result';
      if (resultHero) {
        resultHero.classList.toggle('is-profit', ok);
        resultHero.classList.toggle('is-loss', !ok);
      }
      if (resultLabel) {
        resultLabel.textContent = ok
          ? (ur ? 'اندازاً منافع' : 'Estimated profit')
          : (ur ? 'اندازاً نقصان' : 'Estimated loss');
      }
      if (resultAmt) resultAmt.textContent = money(Math.abs(Number(r.profit) || 0));
      if (resultList) {
        var rows = [
          [ur ? 'فصل' : 'Crop', (r.crop_icon || '') + ' ' + (r.crop || '')],
          [ur ? 'زمین' : 'Land', r.land || '—'],
          [ur ? 'کل خرچہ' : 'Total cost', money(r.total_cost)],
          [ur ? 'آمدنی' : 'Revenue', money(r.revenue)],
          [ur ? 'پیداوار' : 'Harvest', num(r.production) + ' ' + (r.production_unit || '')],
          [ur ? 'بیچنے لائق' : 'Sellable', num(r.sellable) + ' ' + (r.sellable_unit || '')],
          [ur ? 'ضائع' : 'Wastage', (r.wastage || 0) + '%'],
          [ur ? 'ریٹ' : 'Rate', money(r.price) + ' / ' + (r.price_unit || '')],
        ];
        resultList.innerHTML = rows.map(function (row) {
          return '<div><span>' + escapeHtml(row[0]) + '</span><strong>' + escapeHtml(String(row[1] || '—')) + '</strong></div>';
        }).join('');
      }
      openModal();
    }

    function print80() {
      if (!lastResult) return;
      var r = lastResult;
      var ur = isUr();
      var ok = !!r.is_profit;
      var label = ok ? (ur ? 'منافع' : 'Profit') : (ur ? 'نقصان' : 'Loss');
      var html = '<!DOCTYPE html><html><head><title>Crop calc</title><style>'
        + '@page{size:80mm auto;margin:2mm}'
        + 'body{font-family:monospace;font-size:12px;width:72mm;margin:0 auto;color:#000}'
        + 'h1{font-size:14px;margin:0 0 4px;text-align:center}'
        + 'p{margin:0 0 4px;text-align:center}'
        + '.line{display:flex;justify-content:space-between;gap:6px;border-bottom:1px dashed #999;padding:3px 0}'
        + '.tot{margin-top:8px;border-top:2px solid #000;padding-top:6px;font-weight:700}'
        + '</style></head><body>'
        + '<h1>MarketLink</h1>'
        + '<p>' + (ur ? 'فصل حساب' : 'Crop calculator') + '</p><hr>'
        + '<div class="line"><span>' + (ur ? 'فصل' : 'Crop') + '</span><strong>' + escapeHtml((r.crop_icon || '') + ' ' + (r.crop || '')) + '</strong></div>'
        + '<div class="line"><span>' + (ur ? 'زمین' : 'Land') + '</span><strong>' + escapeHtml(r.land || '—') + '</strong></div>'
        + '<div class="line"><span>' + (ur ? 'خرچہ' : 'Cost') + '</span><strong>' + money(r.total_cost) + '</strong></div>'
        + '<div class="line"><span>' + (ur ? 'آمدنی' : 'Revenue') + '</span><strong>' + money(r.revenue) + '</strong></div>'
        + '<div class="line"><span>' + (ur ? 'پیداوار' : 'Harvest') + '</span><strong>' + num(r.production) + ' ' + escapeHtml(r.production_unit || '') + '</strong></div>'
        + '<div class="line"><span>' + (ur ? 'ضائع' : 'Waste') + '</span><strong>' + (r.wastage || 0) + '%</strong></div>'
        + '<div class="tot line"><span>' + label + '</span><strong>' + money(Math.abs(Number(r.profit) || 0)) + '</strong></div>'
        + '<p style="margin-top:10px">' + (ur ? 'شکریہ' : 'Thank you') + '</p>'
        + '<script>window.onload=function(){window.print();}</' + 'script>'
        + '</body></html>';
      var w = window.open('', '_blank', 'width=320,height=600');
      if (!w) { alert(ur ? 'پرنٹ کے لیے پاپ اپ اجازت دیں' : 'Allow popups to print'); return; }
      w.document.open();
      w.document.write(html);
      w.document.close();
    }

    if (crop) {
      crop.addEventListener('change', toggleOther);
      toggleOther();
    }
    if (budget) {
      budget.addEventListener('input', syncBudget);
      syncBudget();
    }
    if (prodUnit) prodUnit.addEventListener('change', matchPriceUnit);
    if (assistBtn) assistBtn.addEventListener('click', speakAssist);
    if (urduBtn) urduBtn.addEventListener('click', toggleUrdu);
    document.addEventListener('farmer-lang-changed', function () {
      syncSelectLang();
      stopAssist();
    });
    syncSelectLang();

    if (resetBtn) {
      resetBtn.addEventListener('click', function () {
        setTimeout(function () {
          syncBudget();
          toggleOther();
          showErrors([]);
          closeModal();
        }, 0);
      });
    }

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalOk) modalOk.addEventListener('click', closeModal);
    if (printBtn) printBtn.addEventListener('click', print80);
    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
      });
    }
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal && !modal.hidden) closeModal();
    });

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      showErrors([]);
      syncBudget();

      if (!(Number(document.getElementById('sccLand').value) > 0)) {
        showErrors([isUr() ? 'زمین لکھیں' : 'Enter land amount']);
        return;
      }
      if (budget.value === '' || !(Number(budget.value) >= 0)) {
        showErrors([isUr() ? 'خرچہ لکھیں' : 'Enter total cost']);
        return;
      }
      if (!(Number(document.getElementById('sccProd').value) > 0)) {
        showErrors([isUr() ? 'پیداوار لکھیں' : 'Enter harvest']);
        return;
      }
      if (!(Number(document.getElementById('sccPrice').value) > 0)) {
        showErrors([isUr() ? 'ریٹ لکھیں' : 'Enter selling rate']);
        return;
      }
      if (crop.value === 'other' && !String(document.getElementById('sccCropOther').value || '').trim()) {
        showErrors([isUr() ? 'فصل کا نام لکھیں' : 'Write crop name']);
        return;
      }

      if (submitBtn) submitBtn.disabled = true;
      try {
        if (window.mlCsrf && window.mlCsrf.refresh) await window.mlCsrf.refresh();
        freshCsrf();
        var headers = {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
        };
        if (window.mlCsrf && window.mlCsrf.headers) {
          Object.assign(headers, window.mlCsrf.headers());
          headers['Content-Type'] = 'application/json';
          headers['X-CSRF-TOKEN'] = freshCsrf() || headers['X-CSRF-TOKEN'];
        }

        var res = await fetch(calcUrl, {
          method: 'POST',
          headers: headers,
          credentials: 'same-origin',
          body: JSON.stringify(payloadFromForm()),
        });

        if (res.status === 419) {
          showErrors([isUr() ? 'سیشن ختم — ریفریش کریں' : 'Session expired — refreshing…']);
          setTimeout(function () { window.location.reload(); }, 600);
          return;
        }

        var data = await res.json();
        if (!res.ok) {
          var errs = [];
          if (data && data.errors) {
            Object.values(data.errors).forEach(function (arr) {
              if (arr && arr[0]) errs.push(arr[0]);
            });
          }
          showErrors(errs.length ? errs : ['Form check karein.']);
          return;
        }
        if (!data.ok) {
          showErrors(data.errors || ['Hisab nahi ban saka.']);
          return;
        }
        showResult(data.result);
      } catch (err) {
        showErrors(['Server jawab nahi de raha. Refresh karke try karein.']);
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
