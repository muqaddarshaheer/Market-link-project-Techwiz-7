(function () {
  'use strict';

  function boot() {
    var root = document.getElementById('sccApp');
    if (!root) return;

    var calcUrl = root.getAttribute('data-calc-url');
    var csrf = root.getAttribute('data-csrf') || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var insights = {};
    try {
      insights = JSON.parse(root.getAttribute('data-insights') || '{}') || {};
    } catch (e) {
      insights = {};
    }

    var form = document.getElementById('sccForm');
    var crop = document.getElementById('sccCrop');
    var cropOtherWrap = document.getElementById('sccCropOtherWrap');
    var waste = document.getElementById('sccWaste');
    var wasteLabel = document.getElementById('sccWasteLabel');
    var errorsEl = document.getElementById('sccErrors');
    var modal = document.getElementById('sccModal');
    var modalBody = document.getElementById('sccModalBody');
    var modalClose = document.getElementById('sccModalClose');
    var exportBtn = document.getElementById('sccExportBtn');
    var printBtn = document.getElementById('sccPrintBtn');
    var waBtn = document.getElementById('sccWaBtn');
    var waPhone = document.getElementById('sccWaPhone');
    var insightPanel = document.getElementById('sccInsights');
    var insightTitle = document.getElementById('sccInsightTitle');
    var insightSeason = document.getElementById('sccInsightSeason');
    var insightTips = document.getElementById('sccInsightTips');
    var insightWatch = document.getElementById('sccInsightWatch');
    var lastResult = null;
    var lastSummaryText = '';

    function money(n) {
      var v = Number(n) || 0;
      return 'Rs. ' + v.toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }

    function num(n) {
      var v = Number(n) || 0;
      return v.toLocaleString('en-PK', { maximumFractionDigits: 2 });
    }

    function isUr() {
      return document.documentElement.getAttribute('data-farmer-lang') === 'ur';
    }

    function escapeHtml(s) {
      return String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    function cropLabel() {
      var opt = crop.options[crop.selectedIndex];
      return opt ? String(opt.text || '').trim() : crop.value;
    }

    function renderInsights() {
      var key = crop.value || 'other';
      var data = insights[key] || insights.other || null;
      var ur = isUr();

      if (insightPanel) insightPanel.classList.add('is-updating');

      if (!data) {
        if (insightTitle) insightTitle.textContent = cropLabel() || '—';
        if (insightSeason) insightSeason.textContent = ur
          ? 'اس فصل کے لیے عمومی حساب لگائیں'
          : 'Use the form for a general estimate';
        if (insightTips) insightTips.innerHTML = '';
        if (insightWatch) insightWatch.textContent = '—';
      } else {
        if (insightTitle) insightTitle.textContent = cropLabel();
        if (insightSeason) {
          insightSeason.textContent = ur
            ? (data.season_ur || data.season_en || '')
            : (data.season_en || data.season_ur || '');
        }
        var tips = ur ? (data.tips_ur || data.tips_en || []) : (data.tips_en || data.tips_ur || []);
        if (insightTips) {
          insightTips.innerHTML = (tips || []).map(function (t) {
            return '<li>' + escapeHtml(t) + '</li>';
          }).join('');
        }
        if (insightWatch) {
          insightWatch.textContent = ur
            ? (data.watch_ur || data.watch_en || '—')
            : (data.watch_en || data.watch_ur || '—');
        }
      }

      window.setTimeout(function () {
        if (insightPanel) insightPanel.classList.remove('is-updating');
      }, 160);
    }

    function toggleOther() {
      cropOtherWrap.hidden = crop.value !== 'other';
      renderInsights();
    }
    crop.addEventListener('change', toggleOther);
    toggleOther();

    document.addEventListener('farmer-lang-changed', renderInsights);

    waste.addEventListener('input', function () {
      wasteLabel.textContent = waste.value + '%';
    });

    function showErrors(list) {
      if (!list || !list.length) {
        errorsEl.hidden = true;
        errorsEl.innerHTML = '';
        return;
      }
      errorsEl.hidden = false;
      errorsEl.innerHTML = '<ul class="mb-0">' + list.map(function (e) {
        return '<li>' + escapeHtml(e) + '</li>';
      }).join('') + '</ul>';
      errorsEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function payloadFromForm() {
      var fd = new FormData(form);
      var obj = {};
      fd.forEach(function (v, k) { obj[k] = v; });
      ['land_amount', 'budget', 'seed_qty', 'seed_unit_cost', 'fertilizer', 'water', 'labor', 'transport', 'other_cost', 'prod_qty', 'price', 'wastage'].forEach(function (k) {
        if (obj[k] === '' || obj[k] == null) {
          obj[k] = k === 'wastage' ? 0 : (['seed_qty', 'seed_unit_cost', 'fertilizer', 'water', 'labor', 'transport', 'other_cost'].indexOf(k) >= 0 ? 0 : obj[k]);
        }
        if (obj[k] !== '' && obj[k] != null) obj[k] = Number(obj[k]);
      });
      return obj;
    }

    function buildSummaryText(r) {
      var lines = [];
      lines.push('MarketLink — Crop Calculator Summary');
      lines.push('================================');
      lines.push('Fasal / Crop: ' + (r.crop || ''));
      lines.push('Zameen / Land: ' + (r.land || ''));
      lines.push('Kul kharcha / Total cost: ' + money(r.total_cost));
      lines.push('Paidawar / Production: ' + num(r.production) + ' ' + (r.production_unit || ''));
      lines.push('Wastage: ' + r.wastage + '%');
      lines.push('Bechne layak / Sellable: ' + num(r.sellable) + ' ' + (r.sellable_unit || ''));
      lines.push('Rate: ' + money(r.price) + ' ' + (r.price_unit || ''));
      lines.push('Expected revenue: ' + money(r.revenue));
      if (r.is_profit) {
        lines.push('Estimated PROFIT: ' + money(r.profit));
      } else {
        lines.push('Estimated LOSS: ' + money(r.profit));
      }
      lines.push('Status: ' + (r.status_label || ''));
      lines.push('');
      lines.push('Note: Yeh sirf estimate hai — guarantee nahi.');
      lines.push('MarketLink Farmer Panel');
      return lines.join('\n');
    }

    function row(label, value) {
      return '<div><span>' + escapeHtml(label) + '</span><span>' + escapeHtml(String(value)) + '</span></div>';
    }

    function openModal(r) {
      lastResult = r;
      lastSummaryText = buildSummaryText(r);
      var ur = isUr();
      var html = '';
      var profitClass = r.is_profit ? 'is-profit' : 'is-loss';
      html += '<div class="scc-summary-big ' + profitClass + '">';
      html += '<span>' + (r.is_profit
        ? (ur ? 'اندازاً منافع' : 'Estimated Profit')
        : (ur ? 'اندازاً نقصان' : 'Estimated Loss')) + '</span>';
      html += '<span class="amt">' + money(r.profit) + '</span>';
      html += '</div>';

      html += '<div class="scc-modal-kv">';
      html += row(ur ? 'فصل' : 'Crop', (r.crop_icon || '') + ' ' + (r.crop || ''));
      html += row(ur ? 'زمین' : 'Land', r.land);
      html += row(ur ? 'کل خرچہ' : 'Total cost', money(r.total_cost));
      html += row(ur ? 'متوقع آمدنی' : 'Expected revenue', money(r.revenue));
      html += row(ur ? 'پیداوار' : 'Production', num(r.production) + ' ' + (r.production_unit || ''));
      html += row(ur ? 'ضائع' : 'Wastage', r.wastage + '%');
      html += '</div>';
      html += '<p class="scc-note mb-0">' + escapeHtml(r.status_label || '') + '</p>';

      modalBody.innerHTML = html;
      modal.hidden = false;
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      modal.hidden = true;
      document.body.style.overflow = '';
    }

    function exportTxt() {
      if (!lastSummaryText) return;
      var blob = new Blob([lastSummaryText], { type: 'text/plain;charset=utf-8' });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = 'marketlink-crop-summary.txt';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }

    function printSummary() {
      if (!lastSummaryText) return;
      var w = window.open('', '_blank', 'width=640,height=720');
      if (!w) {
        alert('Popup blocked. Browser allow karein.');
        return;
      }
      w.document.write('<pre style="font-family:Segoe UI,sans-serif;padding:24px;white-space:pre-wrap;font-size:15px;line-height:1.5">' +
        escapeHtml(lastSummaryText) + '</pre>');
      w.document.close();
      w.focus();
      w.print();
    }

    function normalizePkPhone(raw) {
      var d = String(raw || '').replace(/\D/g, '');
      if (d.indexOf('92') === 0 && d.length >= 12) return d;
      if (d.indexOf('0') === 0 && d.length >= 11) return '92' + d.slice(1);
      if (d.length === 10) return '92' + d;
      return d;
    }

    function sendWhatsApp() {
      if (!lastSummaryText) return;
      var phone = normalizePkPhone(waPhone.value);
      if (!phone || phone.length < 11) {
        alert(isUr() ? 'درست واٹس ایپ نمبر لکھیں (جیسے 03001234567)' : 'Sahi WhatsApp number likhein (e.g. 03001234567)');
        waPhone.focus();
        return;
      }
      var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(lastSummaryText);
      window.open(url, '_blank');
    }

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      showErrors([]);
      var submitBtn = document.getElementById('sccSubmit');
      if (submitBtn) submitBtn.disabled = true;
      try {
        var res = await fetch(calcUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest'
          },
          credentials: 'same-origin',
          body: JSON.stringify(payloadFromForm())
        });
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
        openModal(data.result);
      } catch (err) {
        showErrors(['Server jawab nahi de raha. Refresh karke try karein.']);
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
      });
    }
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal && !modal.hidden) closeModal();
    });
    if (exportBtn) exportBtn.addEventListener('click', exportTxt);
    if (printBtn) printBtn.addEventListener('click', printSummary);
    if (waBtn) waBtn.addEventListener('click', sendWhatsApp);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
