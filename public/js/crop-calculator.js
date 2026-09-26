(function () {
  'use strict';

  function boot() {
    var root = document.getElementById('sccApp');
    if (!root) return;

    var calcUrl = root.getAttribute('data-calc-url');
    var csrf = root.getAttribute('data-csrf') || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    function freshCsrf() {
      csrf = (window.mlCsrf && window.mlCsrf.token ? window.mlCsrf.token() : null)
        || (document.querySelector('meta[name="csrf-token"]') || {}).content
        || root.getAttribute('data-csrf')
        || csrf;
      return csrf;
    }
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
    var againBtn = document.getElementById('sccAgainBtn');
    var waBtn = document.getElementById('sccWaBtn');
    var waPhone = document.getElementById('sccWaPhone');
    var insightPanel = document.getElementById('sccInsights');
    var insightTitle = document.getElementById('sccInsightTitle');
    var insightSeason = document.getElementById('sccInsightSeason');
    var insightTips = document.getElementById('sccInsightTips');
    var insightWatch = document.getElementById('sccInsightWatch');
    var backBtn = document.getElementById('sccBack');
    var nextBtn = document.getElementById('sccNext');
    var submitBtn = document.getElementById('sccSubmit');
    var progressBar = document.getElementById('sccProgressBar');
    var budget = document.getElementById('sccBudget');
    var otherCost = document.getElementById('sccOtherCost');
    var liveAmt = document.getElementById('sccLiveAmt');
    var liveSub = document.getElementById('sccLiveSub');
    var liveBox = document.getElementById('sccLive');
    var pillCrop = document.getElementById('sccPillCrop');
    var pillLand = document.getElementById('sccPillLand');
    var pillCost = document.getElementById('sccPillCost');
    var cropSearch = document.getElementById('sccCropSearch');
    var cropEmpty = document.getElementById('sccCropEmpty');
    var resetBtn = document.getElementById('sccReset');
    var step = 1;
    var lastResult = null;
    var lastSummaryText = '';
    var lastCropTap = { key: '', at: 0 };

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

    function syncBudget() {
      if (budget && otherCost) otherCost.value = budget.value || 0;
      updateLive();
    }

    function selectedCropChip() {
      return document.querySelector('.scc-crop-chip.is-selected');
    }

    function selectedUnitLabel(groupSel) {
      var chip = document.querySelector(groupSel + ' .scc-unit-chip.is-selected');
      return chip ? chip.textContent.trim() : '';
    }

    function markQuickOn(container, attr, value) {
      if (!container) return;
      container.querySelectorAll('[' + attr + ']').forEach(function (btn) {
        btn.classList.toggle('is-on', String(btn.getAttribute(attr)) === String(value));
      });
    }

    function billNo() {
      var d = new Date();
      var pad = function (n) { return String(n).padStart(2, '0'); };
      return 'ML-' + d.getFullYear().toString().slice(-2) + pad(d.getMonth() + 1) + pad(d.getDate()) + '-' + pad(d.getHours()) + pad(d.getMinutes());
    }

    function billDate() {
      try {
        return new Date().toLocaleString(isUr() ? 'ur-PK' : 'en-PK', {
          day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });
      } catch (e) {
        return new Date().toLocaleString();
      }
    }

    function updateLive() {
      var chip = selectedCropChip();
      var emoji = chip ? ((chip.querySelector('.scc-crop-emoji') || {}).textContent || '🌱') : '🌱';
      var cropName = chip
        ? ((chip.querySelector(isUr() ? '.scc-crop-ur' : '.scc-crop-en') || {}).textContent || '')
        : '';
      var land = Number((document.getElementById('sccLand') || {}).value || 0);
      var landUnit = selectedUnitLabel('#sccLandUnits') || '';
      var cost = Number((budget || {}).value || 0);
      var prod = Number((document.getElementById('sccProd') || {}).value || 0);
      var price = Number((document.getElementById('sccPrice') || {}).value || 0);
      var wastePct = Number((waste || {}).value || 0);
      var prodUnit = (document.getElementById('sccProdUnit') || {}).value || 'kg';
      var priceUnit = (document.getElementById('sccPriceUnit') || {}).value || 'per_kg';

      if (pillCrop) pillCrop.textContent = emoji + (cropName ? ' ' + cropName : '');
      if (pillLand) pillLand.textContent = land > 0 ? (num(land) + ' ' + (landUnit || '')) : '—';
      if (pillCost) pillCost.textContent = cost >= 0 && budget && budget.value !== '' ? money(cost) : '—';

      // Live estimate when harvest unit matches price unit (kg/maund/ton)
      var matched =
        (prodUnit === 'kg' && priceUnit === 'per_kg') ||
        (prodUnit === 'maund' && priceUnit === 'per_maund') ||
        (prodUnit === 'ton' && priceUnit === 'per_ton');
      var canSimple = matched && prod > 0 && price > 0 && cost >= 0 && budget && budget.value !== '';
      if (!canSimple) {
        if (liveAmt) liveAmt.textContent = '—';
        if (liveSub) {
          liveSub.textContent = isUr()
            ? 'قدم بھریں — منافع یہاں دکھے گا'
            : 'Fill steps — profit shows here';
        }
        if (liveBox) liveBox.classList.remove('is-profit', 'is-loss');
        return;
      }

      var sellable = prod * (1 - Math.min(40, Math.max(0, wastePct)) / 100);
      var revenue = sellable * price;
      var profit = revenue - cost;
      var ok = profit >= 0;

      if (liveAmt) {
        liveAmt.textContent = (ok ? (isUr() ? 'منافع ' : 'Profit ') : (isUr() ? 'نقصان ' : 'Loss ')) + money(Math.abs(profit));
      }
      if (liveSub) {
        liveSub.textContent = isUr()
          ? ('آمدنی ' + money(revenue) + ' · خرچہ ' + money(cost))
          : ('Revenue ' + money(revenue) + ' · Cost ' + money(cost));
      }
      if (liveBox) {
        liveBox.classList.toggle('is-profit', ok);
        liveBox.classList.toggle('is-loss', !ok);
      }
    }

    function setStep(n, fromGoto) {
      var target = Math.max(1, Math.min(4, n));
      // Jumping ahead only allowed if previous steps valid
      if (fromGoto && target > step) {
        for (var i = step; i < target; i++) {
          var errs = validateStep(i);
          if (errs.length) {
            showErrors(errs);
            return;
          }
        }
      }
      step = target;
      root.setAttribute('data-step', String(step));

      document.querySelectorAll('[data-step-panel]').forEach(function (panel) {
        var on = Number(panel.getAttribute('data-step-panel')) === step;
        panel.hidden = !on;
        panel.classList.toggle('is-active', on);
      });

      document.querySelectorAll('[data-step-dot]').forEach(function (dot) {
        var d = Number(dot.getAttribute('data-step-dot'));
        dot.classList.toggle('is-on', d === step);
        dot.classList.toggle('is-done', d < step);
      });

      if (progressBar) progressBar.style.setProperty('--scc-pct', step * 25 + '%');
      if (backBtn) backBtn.hidden = step === 1;
      if (nextBtn) nextBtn.hidden = step === 4;
      if (submitBtn) submitBtn.hidden = step !== 4;
      showErrors([]);
      updateLive();
    }

    function validateStep(n) {
      var ur = isUr();
      if (n === 1) {
        if (!crop.value) return [ur ? 'فصل چنیں' : 'Pick a crop'];
        if (crop.value === 'other' && !String(document.getElementById('sccCropOther').value || '').trim()) {
          return [ur ? 'فصل کا نام لکھیں' : 'Write crop name'];
        }
        return [];
      }
      if (n === 2) {
        if (!(Number(document.getElementById('sccLand').value) > 0)) {
          return [ur ? 'زمین کی مقدار لکھیں' : 'Enter land amount'];
        }
        return [];
      }
      if (n === 3) {
        if (budget.value === '' || !(Number(budget.value) >= 0)) {
          return [ur ? 'کل خرچہ لکھیں' : 'Enter total cost'];
        }
        return [];
      }
      if (n === 4) {
        var errs = [];
        if (!(Number(document.getElementById('sccProd').value) > 0)) {
          errs.push(ur ? 'متوقع پیداوار لکھیں' : 'Enter expected harvest');
        }
        if (!(Number(document.getElementById('sccPrice').value) > 0)) {
          errs.push(ur ? 'بیچنے کا ریٹ لکھیں' : 'Enter selling rate');
        }
        return errs;
      }
      return [];
    }

    function selectChipGroup(containerSels, attr, selectId, value) {
      var select = document.getElementById(selectId);
      if (select) select.value = value;
      var sels = Array.isArray(containerSels) ? containerSels : [containerSels];
      sels.forEach(function (sel) {
        document.querySelectorAll(sel + ' [' + attr + ']').forEach(function (btn) {
          var on = btn.getAttribute(attr) === value;
          btn.classList.toggle('is-selected', on);
          if (btn.hasAttribute('aria-selected')) btn.setAttribute('aria-selected', on ? 'true' : 'false');
        });
      });
      updateLive();
    }

    function renderInsights() {
      var key = crop.value || 'other';
      var data = insights[key] || insights.other || null;
      var ur = isUr();
      var chip = selectedCropChip();
      var label = chip
        ? ((chip.querySelector('.scc-crop-emoji') || {}).textContent || '') + ' ' +
          ((chip.querySelector(ur ? '.scc-crop-ur' : '.scc-crop-en') || {}).textContent || '')
        : key;

      if (insightPanel) insightPanel.classList.add('is-updating');
      if (!data) {
        if (insightTitle) insightTitle.textContent = label || '—';
        if (insightSeason) insightSeason.textContent = ur ? 'عمومی حساب لگائیں' : 'Use the form for a general estimate';
        if (insightTips) insightTips.innerHTML = '';
        if (insightWatch) insightWatch.textContent = '—';
      } else {
        if (insightTitle) insightTitle.textContent = label;
        if (insightSeason) {
          insightSeason.textContent = ur ? (data.season_ur || data.season_en || '') : (data.season_en || data.season_ur || '');
        }
        var tips = ur ? (data.tips_ur || data.tips_en || []) : (data.tips_en || data.tips_ur || []);
        if (insightTips) {
          insightTips.innerHTML = (tips || []).map(function (t) {
            return '<li>' + escapeHtml(t) + '</li>';
          }).join('');
        }
        if (insightWatch) {
          insightWatch.textContent = ur ? (data.watch_ur || data.watch_en || '—') : (data.watch_en || data.watch_ur || '—');
        }
      }
      window.setTimeout(function () {
        if (insightPanel) insightPanel.classList.remove('is-updating');
      }, 140);
      updateLive();
    }

    function toggleOther() {
      cropOtherWrap.hidden = crop.value !== 'other';
      renderInsights();
    }

    // Crop search
    if (cropSearch) {
      cropSearch.addEventListener('input', function () {
        var q = String(cropSearch.value || '').toLowerCase().trim();
        var shown = 0;
        document.querySelectorAll('#sccCropGrid .scc-crop-chip').forEach(function (chip) {
          var en = chip.getAttribute('data-en') || '';
          var ur = chip.getAttribute('data-ur') || '';
          var hit = !q || en.indexOf(q) !== -1 || ur.indexOf(q) !== -1 || (chip.getAttribute('data-crop') || '').indexOf(q) !== -1;
          chip.hidden = !hit;
          if (hit) shown++;
        });
        if (cropEmpty) cropEmpty.hidden = shown > 0;
      });
    }

    // Crop chips + double-tap next
    document.getElementById('sccCropGrid')?.addEventListener('click', function (e) {
      var chip = e.target.closest('[data-crop]');
      if (!chip) return;
      var key = chip.getAttribute('data-crop');
      selectChipGroup('#sccCropGrid', 'data-crop', 'sccCrop', key);
      toggleOther();
      var now = Date.now();
      if (lastCropTap.key === key && now - lastCropTap.at < 450) {
        setStep(2);
      }
      lastCropTap = { key: key, at: now };
    });

    function bindUnitRow(sel, attr, selectId) {
      document.querySelector(sel)?.addEventListener('click', function (e) {
        var btn = e.target.closest('[' + attr + ']');
        if (!btn) return;
        var val = btn.getAttribute(attr);
        selectChipGroup(['#sccLandUnits', '#sccProdUnits', '#sccPriceUnits'], attr, selectId, val);
        // Auto-match price unit when harvest unit changes (easier for farmers)
        if (attr === 'data-prod-unit') {
          var map = { kg: 'per_kg', maund: 'per_maund', ton: 'per_ton' };
          if (map[val]) {
            selectChipGroup('#sccPriceUnits', 'data-price-unit', 'sccPriceUnit', map[val]);
          }
        }
      });
    }
    bindUnitRow('#sccLandUnits', 'data-land-unit', 'sccLandUnit');
    bindUnitRow('#sccProdUnits', 'data-prod-unit', 'sccProdUnit');
    bindUnitRow('#sccPriceUnits', 'data-price-unit', 'sccPriceUnit');

    // Steppers
    document.querySelectorAll('[data-nudge]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-nudge');
        var delta = Number(btn.getAttribute('data-delta') || 0);
        var el = document.getElementById(id);
        if (!el) return;
        var cur = Number(el.value || 0);
        var next = Math.round((cur + delta) * 100) / 100;
        if (id === 'sccLand' || id === 'sccProd' || id === 'sccPrice') next = Math.max(0.01, next);
        if (id === 'sccBudget') next = Math.max(0, next);
        el.value = next;
        if (id === 'sccBudget') syncBudget();
        else updateLive();
      });
    });

    // Quick set numbers
    document.querySelectorAll('[data-set]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var el = document.getElementById(btn.getAttribute('data-set'));
        if (!el) return;
        el.value = btn.getAttribute('data-val');
        markQuickOn(btn.parentElement, 'data-val', btn.getAttribute('data-val'));
        updateLive();
      });
    });

    document.getElementById('sccQuickMoney')?.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-money]');
      if (!btn || !budget) return;
      budget.value = btn.getAttribute('data-money');
      markQuickOn(btn.parentElement, 'data-money', btn.getAttribute('data-money'));
      syncBudget();
    });

    document.querySelectorAll('[data-waste]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        waste.value = btn.getAttribute('data-waste');
        wasteLabel.textContent = waste.value + '%';
        markQuickOn(btn.parentElement, 'data-waste', waste.value);
        updateLive();
      });
    });

    if (budget) {
      budget.addEventListener('input', syncBudget);
      syncBudget();
    }
    ['sccLand', 'sccProd', 'sccPrice'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.addEventListener('input', updateLive);
    });

    crop.addEventListener('change', toggleOther);
    toggleOther();
    document.addEventListener('farmer-lang-changed', function () {
      renderInsights();
      updateLive();
    });

    waste.addEventListener('input', function () {
      wasteLabel.textContent = waste.value + '%';
      markQuickOn(document.querySelector('.scc-waste-presets'), 'data-waste', waste.value);
      updateLive();
    });

    // Progress dots jump
    document.querySelectorAll('[data-goto]').forEach(function (dot) {
      dot.addEventListener('click', function () {
        setStep(Number(dot.getAttribute('data-goto')), true);
      });
    });

    if (backBtn) backBtn.addEventListener('click', function () { setStep(step - 1); });
    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        var errs = validateStep(step);
        if (errs.length) {
          showErrors(errs);
          return;
        }
        setStep(step + 1);
      });
    }

    if (resetBtn) {
      resetBtn.addEventListener('click', function () {
        form.reset();
        selectChipGroup('#sccCropGrid', 'data-crop', 'sccCrop', crop.options[0] ? crop.options[0].value : 'potato');
        selectChipGroup('#sccLandUnits', 'data-land-unit', 'sccLandUnit', 'kanal');
        selectChipGroup('#sccProdUnits', 'data-prod-unit', 'sccProdUnit', 'kg');
        selectChipGroup('#sccPriceUnits', 'data-price-unit', 'sccPriceUnit', 'per_kg');
        document.getElementById('sccLand').value = 2;
        budget.value = 50000;
        document.getElementById('sccProd').value = 5000;
        document.getElementById('sccPrice').value = 40;
        waste.value = 10;
        wasteLabel.textContent = '10%';
        markQuickOn(document.getElementById('sccQuickLand'), 'data-val', '2');
        markQuickOn(document.getElementById('sccQuickMoney'), 'data-money', '50000');
        markQuickOn(document.getElementById('sccQuickProd'), 'data-val', '5000');
        markQuickOn(document.querySelector('.scc-waste-presets'), 'data-waste', '10');
        if (cropSearch) {
          cropSearch.value = '';
          document.querySelectorAll('#sccCropGrid .scc-crop-chip').forEach(function (c) { c.hidden = false; });
          if (cropEmpty) cropEmpty.hidden = true;
        }
        syncBudget();
        toggleOther();
        setStep(1);
      });
    }

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
      return [
        'MarketLink — Profit Bill',
        '========================',
        'Crop: ' + (r.crop || ''),
        'Land: ' + (r.land || ''),
        'Cost: ' + money(r.total_cost),
        'Revenue: ' + money(r.revenue),
        (r.is_profit ? 'PROFIT: ' : 'LOSS: ') + money(r.profit),
        'Wastage: ' + r.wastage + '%',
        'Sellable: ' + num(r.sellable) + ' ' + (r.sellable_unit || ''),
        '',
        'Estimate only — not a guarantee.',
        'MarketLink Farmer Panel',
      ].join('\n');
    }

    function openModal(r) {
      lastResult = r;
      lastSummaryText = buildSummaryText(r);
      var ur = isUr();
      var ok = !!r.is_profit;
      var cost = Number(r.total_cost) || 0;
      var rev = Number(r.revenue) || 0;
      var maxBar = Math.max(cost, rev, 1);
      var costPct = Math.round((cost / maxBar) * 100);
      var revPct = Math.round((rev / maxBar) * 100);
      var margin = cost > 0 ? Math.round(((rev - cost) / cost) * 100) : 0;
      var no = billNo();
      var when = billDate();

      var html = '';
      html += '<article class="scc-bill-sheet ' + (ok ? 'is-profit' : 'is-loss') + '">';
      html += '<header class="scc-bill-brand">';
      html += '<div class="scc-bill-brand-mark"><span>ML</span></div>';
      html += '<div class="scc-bill-brand-text">';
      html += '<p class="scc-bill-kicker">' + (ur ? 'مارکیٹ لنک' : 'MarketLink') + '</p>';
      html += '<h2 id="sccModalTitle">' + (ur ? 'منافع بل' : 'Profit Bill') + '</h2>';
      html += '</div>';
      html += '<div class="scc-bill-meta">';
      html += '<span>' + escapeHtml(no) + '</span>';
      html += '<span>' + escapeHtml(when) + '</span>';
      html += '</div></header>';

      html += '<div class="scc-bill-hero">';
      html += '<p class="scc-bill-hero-label">' + (ok ? (ur ? 'اندازاً منافع' : 'Estimated profit') : (ur ? 'اندازاً نقصان' : 'Estimated loss')) + '</p>';
      html += '<p class="scc-bill-hero-amt">' + money(Math.abs(Number(r.profit) || 0)) + '</p>';
      if (cost > 0) {
        html += '<p class="scc-bill-hero-sub">' + (ur ? 'مارجن ' : 'Margin ') + margin + '%</p>';
      }
      html += '</div>';

      html += '<div class="scc-bars">';
      html += '<div class="scc-bar-row"><span>' + (ur ? 'خرچہ' : 'Cost') + '</span><div class="scc-bar"><i style="width:' + costPct + '%"></i></div><strong>' + money(cost) + '</strong></div>';
      html += '<div class="scc-bar-row"><span>' + (ur ? 'آمدنی' : 'Revenue') + '</span><div class="scc-bar scc-bar-rev"><i style="width:' + revPct + '%"></i></div><strong>' + money(rev) + '</strong></div>';
      html += '</div>';

      html += '<ul class="scc-bill-lines">';
      [
        [ur ? 'فصل' : 'Crop', (r.crop_icon || '') + ' ' + (r.crop || '')],
        [ur ? 'زمین' : 'Land', r.land],
        [ur ? 'کل خرچہ' : 'Total cost', money(r.total_cost)],
        [ur ? 'متوقع آمدنی' : 'Expected revenue', money(r.revenue)],
        [ur ? 'پیداوار' : 'Harvest', num(r.production) + ' ' + (r.production_unit || '')],
        [ur ? 'بیچنے لائق' : 'Sellable', num(r.sellable) + ' ' + (r.sellable_unit || '')],
        [ur ? 'ضائع' : 'Wastage', r.wastage + '%'],
        [ur ? 'ریٹ' : 'Rate', money(r.price) + ' / ' + (r.price_unit || '')],
      ].forEach(function (row) {
        html += '<li><span>' + escapeHtml(row[0]) + '</span><strong>' + escapeHtml(String(row[1] || '—')) + '</strong></li>';
      });
      html += '</ul>';

      html += '<div class="scc-bill-total ' + (ok ? 'is-profit' : 'is-loss') + '">';
      html += '<span>' + (ok ? (ur ? 'نتیجہ — منافع' : 'Result — Profit') : (ur ? 'نتیجہ — نقصان' : 'Result — Loss')) + '</span>';
      html += '<strong>' + money(Math.abs(Number(r.profit) || 0)) + '</strong>';
      html += '</div>';

      html += '<p class="scc-bill-note">' + escapeHtml(ur
        ? 'صرف اندازہ — مارکیٹ اور موسم بدل سکتے ہیں۔ یقینی منافع نہیں۔'
        : 'Estimate only — market & weather can change. Not a guarantee.') + '</p>';
      html += '<p class="scc-bill-foot">MarketLink Farmer · Crop Calculator</p>';
      html += '</article>';

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
      var sheet = modalBody ? modalBody.querySelector('.scc-bill-sheet') : null;
      if (!sheet && !lastSummaryText) return;
      var w = window.open('', '_blank', 'width=320,height=720');
      if (!w) {
        alert('Popup blocked.');
        return;
      }
      // 80mm thermal receipt (printable ~72–76mm)
      var css = [
        '@page{size:80mm auto;margin:0}',
        'html,body{margin:0;padding:0;background:#fff}',
        'body{width:80mm;max-width:80mm;margin:0 auto;padding:2mm 3mm 4mm;font-family:"Courier New",Consolas,monospace;color:#000;font-size:11px;line-height:1.35;-webkit-print-color-adjust:exact;print-color-adjust:exact}',
        '.scc-bill-sheet{width:100%;max-width:74mm;margin:0 auto;background:#fff;border:0;border-radius:0;padding:0;position:relative;box-shadow:none}',
        '.scc-bill-brand{display:flex;align-items:center;gap:6px;margin-bottom:6px;padding-bottom:6px;border-bottom:1px dashed #000}',
        '.scc-bill-brand-mark{width:28px;height:28px;border-radius:4px;background:#000;color:#fff;display:grid;place-items:center;font-weight:800;font-size:10px;flex-shrink:0}',
        '.scc-bill-kicker{margin:0;font-size:9px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}',
        '.scc-bill-brand-text h2{margin:0;font-size:13px;font-weight:800;font-family:inherit}',
        '.scc-bill-meta{margin-left:auto;text-align:right;font-size:9px;display:grid;gap:1px;line-height:1.2}',
        '.scc-bill-hero{text-align:center;padding:8px 4px;margin:6px 0;border:2px solid #000;background:#fff;color:#000}',
        '.scc-bill-sheet.is-loss .scc-bill-hero{border-style:double}',
        '.scc-bill-hero-label{margin:0;font-size:10px;font-weight:700;text-transform:uppercase}',
        '.scc-bill-hero-amt{margin:4px 0 2px;font-size:18px;font-weight:900}',
        '.scc-bill-hero-sub{margin:0;font-size:10px}',
        '.scc-bars{display:none!important}',
        '.scc-bill-lines{list-style:none;margin:0;padding:0}',
        '.scc-bill-lines li{display:flex;justify-content:space-between;gap:6px;padding:3px 0;border-bottom:1px dotted #999;font-size:11px}',
        '.scc-bill-lines li span{font-weight:700;font-size:9px;text-transform:uppercase;max-width:42%}',
        '.scc-bill-lines li strong{font-weight:800;text-align:right;word-break:break-word}',
        '.scc-bill-total{display:flex;justify-content:space-between;align-items:center;gap:6px;margin-top:6px;padding:6px 4px;border:2px solid #000;font-weight:800;font-size:11px}',
        '.scc-bill-total strong{font-size:14px}',
        '.scc-bill-note{margin:8px 0 0;font-size:9px;text-align:center;line-height:1.3}',
        '.scc-bill-foot{margin:4px 0 0;font-size:9px;font-weight:800;text-align:center;text-transform:uppercase}',
        '.scc-print-cut{text-align:center;margin-top:8px;font-size:9px}',
        '@media print{body{width:80mm;padding:1mm 2mm 3mm}.scc-print-cut{display:none}}',
      ].join('');
      w.document.write('<!doctype html><html><head><meta charset="utf-8"><title>MarketLink 80mm Bill</title><style>' + css + '</style></head><body>');
      if (sheet) {
        w.document.write(sheet.outerHTML);
      } else {
        w.document.write('<pre style="white-space:pre-wrap;font-size:11px;margin:0">' + escapeHtml(lastSummaryText) + '</pre>');
      }
      w.document.write('<p class="scc-print-cut">— 80mm receipt —</p>');
      w.document.write('</body></html>');
      w.document.close();
      w.focus();
      setTimeout(function () { w.print(); }, 280);
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
        alert(isUr() ? 'درست واٹس ایپ نمبر لکھیں' : 'Enter a valid WhatsApp number');
        waPhone.focus();
        return;
      }
      window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(lastSummaryText), '_blank');
    }

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      var stepErrs = validateStep(4);
      if (stepErrs.length) {
        showErrors(stepErrs);
        return;
      }
      showErrors([]);
      if (submitBtn) submitBtn.disabled = true;
      try {
        if (window.mlCsrf && window.mlCsrf.refresh) {
          await window.mlCsrf.refresh();
        }
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
          showErrors([isUr() ? 'سیشن ختم — صفحہ ریفریش کریں' : 'Session expired — refreshing…']);
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
    if (againBtn) {
      againBtn.addEventListener('click', function () {
        closeModal();
        setStep(1);
      });
    }

    setStep(1);
    updateLive();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
