/**
 * Advanced MarketLink harvest calendar — seasons, crops, vitamins, day detail.
 */
(function () {
  'use strict';

  var root = document.getElementById('harvest-calendar');
  if (!root || root.getAttribute('data-engine') !== 'advanced') return;
  var H = window.MLHarvest;
  if (!H) return;

  var year = 2026;
  var now = new Date();
  var currentMonth = now.getFullYear() === year ? now.getMonth() + 1 : Math.min(12, Math.max(1, now.getMonth() + 1));
  var selectedDay = null;
  var selectedCropId = null;
  var viewMode = 'grid';
  var productsUrl = root.dataset.productsUrl || '/products';
  var favKey = 'ml-harvest-favs-v2';

  var els = {
    months: document.getElementById('harvestMonths'),
    grid: document.getElementById('harvestGrid'),
    list: document.getElementById('harvestList'),
    weekdays: document.getElementById('harvestWeekdays'),
    label: document.getElementById('harvestMonthLabel'),
    message: document.getElementById('harvestMessage'),
    count: document.getElementById('harvestCount'),
    status: document.getElementById('harvestStatus'),
    seasonFill: document.getElementById('harvestSeasonFill'),
    seasonTabs: document.getElementById('harvestSeasonTabs'),
    seasonNote: document.getElementById('harvestSeasonNote'),
    cropRail: document.getElementById('harvestCropRail'),
    detail: document.getElementById('harvestDetail'),
    search: document.getElementById('harvestSearch'),
    kindFilter: document.getElementById('harvestKindFilter'),
    chips: document.getElementById('harvestChips'),
    upcoming: document.getElementById('harvestUpcoming'),
    modalRoot: document.getElementById('harvestEventModalRoot'),
  };

  function loadFavs() {
    try { return JSON.parse(localStorage.getItem(favKey) || '[]'); } catch (e) { return []; }
  }
  function saveFavs(list) {
    localStorage.setItem(favKey, JSON.stringify(list.slice(0, 24)));
  }
  function isFav(id) { return loadFavs().indexOf(id) !== -1; }
  function toggleFav(id) {
    var list = loadFavs();
    var i = list.indexOf(id);
    if (i === -1) list.push(id); else list.splice(i, 1);
    saveFavs(list);
  }

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function daysInMonth(m) {
    return new Date(year, m, 0).getDate();
  }

  function filteredCrops(month) {
    var q = ((els.search && els.search.value) || '').trim().toLowerCase();
    var kind = (els.kindFilter && els.kindFilter.value) || '';
    return H.cropsInMonth(month).filter(function (crop) {
      if (kind === 'fav' && !isFav(crop.id)) return false;
      if (kind && kind !== 'fav' && crop.kind !== kind) return false;
      if (!q) return true;
      var hay = (crop.name + ' ' + crop.info + ' ' + crop.kind).toLowerCase();
      return hay.indexOf(q) !== -1;
    });
  }

  function renderSeasonTabs() {
    if (!els.seasonTabs) return;
    var season = H.seasonForMonth(currentMonth);
    els.seasonTabs.innerHTML = Object.values(H.SEASONS).map(function (s) {
      var on = season && season.id === s.id;
      return '<button type="button" class="hc-season' + (on ? ' is-on' : '') + '" data-season="' + s.id + '">' +
        '<strong>' + esc(s.name) + '</strong><span>' + esc(s.starts) + '</span></button>';
    }).join('');
    els.seasonTabs.querySelectorAll('[data-season]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var s = H.SEASONS[btn.getAttribute('data-season')];
        if (s && s.months.length) renderMonth(s.months[0]);
      });
    });
    if (els.seasonNote && season) els.seasonNote.textContent = season.note;
  }

  function renderMonthTabs() {
    if (!els.months) return;
    els.months.innerHTML = H.MONTH_SHORT.map(function (name, i) {
      var m = i + 1;
      return '<button type="button" class="harvest-month' + (m === currentMonth ? ' is-active' : '') + '" data-month="' + m + '" role="tab" aria-selected="' + (m === currentMonth) + '">' + name + '</button>';
    }).join('');
    els.months.querySelectorAll('[data-month]').forEach(function (btn) {
      btn.addEventListener('click', function () { renderMonth(parseInt(btn.getAttribute('data-month'), 10)); });
    });
  }

  function renderSeasonFill() {
    if (!els.seasonFill) return;
    var season = H.seasonForMonth(currentMonth);
    if (!season) { els.seasonFill.style.width = '0%'; return; }
    var idx = season.months.indexOf(currentMonth);
    var pct = ((idx + 1) / season.months.length) * 100;
    els.seasonFill.style.width = pct + '%';
    els.seasonFill.dataset.season = season.id;
  }

  function renderCropRail(crops) {
    if (!els.cropRail) return;
    if (!crops.length) {
      els.cropRail.innerHTML = '<p class="muted small mb-0">No crops match this filter.</p>';
      return;
    }
    els.cropRail.innerHTML = crops.map(function (crop) {
      var on = selectedCropId === crop.id;
      return '<button type="button" class="hc-crop-pill is-' + crop.kind + (on ? ' is-on' : '') + (isFav(crop.id) ? ' is-fav' : '') + '" data-crop="' + crop.id + '">' +
        '<span aria-hidden="true">' + H.glyphFor(crop) + '</span>' +
        '<span>' + esc(crop.name) + '</span></button>';
    }).join('');
    els.cropRail.querySelectorAll('[data-crop]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectedCropId = btn.getAttribute('data-crop');
        var crop = H.CROPS.find(function (c) { return c.id === selectedCropId; });
        showDetail(crop);
        renderCropRail(crops);
      });
    });
  }

  function showDetail(crop) {
    if (!els.detail) return;
    if (!crop) {
      els.detail.innerHTML = '<p class="muted mb-0">Select a crop or a calendar day to see vitamins, benefits, and season notes.</p>';
      return;
    }
    var months = crop.months.map(function (m) { return H.MONTH_SHORT[m - 1]; }).join(' · ');
    var vitamins = (crop.vitamins || []).map(function (v) {
      return '<li><strong>' + esc(v.name) + '</strong><span>' + esc(v.helps) + '</span></li>';
    }).join('');
    var benefits = (crop.benefits || []).map(function (b) {
      return '<li>' + esc(b) + '</li>';
    }).join('');
    var q = encodeURIComponent(H.searchTerm(crop));
    els.detail.innerHTML =
      '<div class="hc-detail-head">' +
        '<span class="hc-detail-glyph" aria-hidden="true">' + H.glyphFor(crop) + '</span>' +
        '<div>' +
          '<p class="hc-detail-kind">' + esc(crop.kind) + ' · starts ' + esc(crop.starts) + '</p>' +
          '<h3 class="hc-detail-title">' + esc(crop.name) + '</h3>' +
          '<p class="hc-detail-months">' + esc(months) + '</p>' +
        '</div>' +
        '<button type="button" class="btn btn-outline-ml btn-sm hc-fav-btn' + (isFav(crop.id) ? ' is-on' : '') + '" data-fav="' + crop.id + '" aria-label="Save crop">' + (isFav(crop.id) ? '★ Saved' : '☆ Save') + '</button>' +
      '</div>' +
      '<p class="hc-detail-info">' + esc(crop.info) + '</p>' +
      '<div class="hc-detail-cols">' +
        '<div><h4>Vitamins</h4><ul class="hc-vitamins">' + vitamins + '</ul></div>' +
        '<div><h4>At the stall</h4><ul class="hc-benefits">' + benefits + '</ul></div>' +
      '</div>' +
      '<div class="hc-detail-actions">' +
        '<a class="btn btn-ml btn-sm" href="' + productsUrl + '?q=' + q + '">Browse matching produce</a>' +
      '</div>';
    var favBtn = els.detail.querySelector('[data-fav]');
    if (favBtn) {
      favBtn.addEventListener('click', function () {
        toggleFav(crop.id);
        showDetail(crop);
        renderMonth(currentMonth);
      });
    }
  }

  function openDayModal(day, crop) {
    if (!els.modalRoot || !crop) return;
    var season = H.seasonForMonth(currentMonth);
    els.modalRoot.innerHTML =
      '<div class="harvest-modal is-open" role="dialog" aria-modal="true">' +
        '<div class="harvest-modal-backdrop" data-close></div>' +
        '<div class="harvest-modal-card hc-modal-card">' +
          '<button type="button" class="harvest-modal-close" data-close aria-label="Close">×</button>' +
          '<div class="hc-modal-top"><span aria-hidden="true">' + H.glyphFor(crop) + '</span>' +
            '<div><p class="small muted mb-0">' + esc(H.monthName(currentMonth)) + ' ' + day + ', ' + year + (season ? ' · ' + season.name : '') + '</p>' +
            '<h2 class="h4 mb-0">' + esc(crop.name) + '</h2></div></div>' +
          '<p class="mt-2">' + esc(crop.info) + '</p>' +
          '<div class="harvest-facts">' +
            (crop.vitamins || []).slice(0, 3).map(function (v) {
              return '<div><span>' + esc(v.name) + '</span><strong>' + esc(v.helps) + '</strong></div>';
            }).join('') +
          '</div>' +
          '<div class="d-flex gap-2 flex-wrap mt-3">' +
            '<a class="btn btn-ml" href="' + productsUrl + '?q=' + encodeURIComponent(H.searchTerm(crop)) + '">Browse produce</a>' +
            '<button type="button" class="btn btn-outline-ml" data-close>Close</button>' +
          '</div>' +
        '</div></div>';
    els.modalRoot.querySelectorAll('[data-close]').forEach(function (el) {
      el.addEventListener('click', function () { els.modalRoot.innerHTML = ''; });
    });
  }

  function renderGrid(crops) {
    if (!els.grid) return;
    var first = new Date(year, currentMonth - 1, 1).getDay();
    var total = daysInMonth(currentMonth);
    var html = '';
    for (var i = 0; i < first; i++) html += '<span class="harvest-pad"></span>';
    for (var day = 1; day <= total; day++) {
      var crop = H.cropOnDay(currentMonth, day);
      var inFilter = crop && crops.some(function (c) { return c.id === crop.id; });
      var isToday = now.getFullYear() === year && (now.getMonth() + 1) === currentMonth && now.getDate() === day;
      var selected = selectedDay === day;
      if (!crop || !inFilter) {
        html += '<span class="harvest-day' + (isToday ? ' is-today' : '') + '" role="gridcell"><span>' + day + '</span></span>';
        continue;
      }
      html += '<button type="button" class="harvest-day is-event is-' + crop.kind + (isToday ? ' is-today' : '') + (selected ? ' is-selected' : '') + '" data-day="' + day + '" data-crop="' + crop.id + '" title="' + esc(crop.name) + '" aria-label="' + day + ' · ' + esc(crop.name) + '">' +
        '<span>' + day + '</span><span class="harvest-icons" aria-hidden="true">' + H.glyphFor(crop) + '</span></button>';
    }
    els.grid.innerHTML = html;
    els.grid.hidden = viewMode !== 'grid';
    els.grid.querySelectorAll('[data-day]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectedDay = parseInt(btn.getAttribute('data-day'), 10);
        selectedCropId = btn.getAttribute('data-crop');
        var crop = H.CROPS.find(function (c) { return c.id === selectedCropId; });
        showDetail(crop);
        openDayModal(selectedDay, crop);
        renderMonth(currentMonth, true);
      });
    });
  }

  function renderList(crops) {
    if (!els.list) return;
    var total = daysInMonth(currentMonth);
    var rows = [];
    for (var day = 1; day <= total; day++) {
      var crop = H.cropOnDay(currentMonth, day);
      if (!crop) continue;
      if (!crops.some(function (c) { return c.id === crop.id; })) continue;
      rows.push({ day: day, crop: crop });
    }
    // unique crops in month order of first appearance
    var seen = {};
    var unique = [];
    rows.forEach(function (row) {
      if (seen[row.crop.id]) return;
      seen[row.crop.id] = true;
      unique.push(row);
    });
    els.list.innerHTML = unique.map(function (row) {
      return '<button type="button" class="harvest-list-row" data-crop="' + row.crop.id + '" data-day="' + row.day + '">' +
        '<span class="harvest-list-day">' + row.day + '</span>' +
        '<span class="harvest-event-icon" aria-hidden="true">' + H.glyphFor(row.crop) + '</span>' +
        '<span><strong>' + esc(row.crop.name) + '</strong><br><span class="small muted">' + esc(row.crop.kind) + ' · from ' + esc(row.crop.starts) + '</span></span></button>';
    }).join('') || '<p class="muted small mb-0">No crops in this view.</p>';
    els.list.hidden = viewMode !== 'list';
    els.list.querySelectorAll('[data-crop]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectedCropId = btn.getAttribute('data-crop');
        selectedDay = parseInt(btn.getAttribute('data-day'), 10);
        var crop = H.CROPS.find(function (c) { return c.id === selectedCropId; });
        showDetail(crop);
        openDayModal(selectedDay, crop);
      });
    });
  }

  function renderChips(crops) {
    if (!els.chips) return;
    els.chips.innerHTML = crops.slice(0, 10).map(function (crop) {
      return '<button type="button" class="harvest-chip' + (isFav(crop.id) ? ' is-fav' : '') + '" data-crop="' + crop.id + '">' +
        H.glyphFor(crop) + ' ' + esc(crop.name) + '</button>';
    }).join('');
    els.chips.querySelectorAll('[data-crop]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectedCropId = btn.getAttribute('data-crop');
        var crop = H.CROPS.find(function (c) { return c.id === selectedCropId; });
        showDetail(crop);
      });
    });
  }

  function renderUpcoming() {
    if (!els.upcoming) return;
    var items = [];
    var m = currentMonth;
    var d = selectedDay || (now.getFullYear() === year && now.getMonth() + 1 === m ? now.getDate() : 1);
    for (var step = 0; step < 60 && items.length < 5; step++) {
      d += 1;
      if (d > daysInMonth(m)) { d = 1; m = m >= 12 ? 1 : m + 1; }
      var crop = H.cropOnDay(m, d);
      if (!crop) continue;
      if (items.some(function (x) { return x.crop.id === crop.id; })) continue;
      items.push({ month: m, day: d, crop: crop });
    }
    els.upcoming.innerHTML = items.map(function (row) {
      return '<button type="button" class="harvest-upcoming-row" data-month="' + row.month + '" data-day="' + row.day + '" data-crop="' + row.crop.id + '">' +
        '<span class="harvest-upcoming-day">' + H.MONTH_SHORT[row.month - 1] + '<br>' + row.day + '</span>' +
        '<span>' + H.glyphFor(row.crop) + ' ' + esc(row.crop.name) + '</span></button>';
    }).join('') || '<p class="muted small mb-0">Quiet stretch ahead.</p>';
    els.upcoming.querySelectorAll('[data-month]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectedDay = parseInt(btn.getAttribute('data-day'), 10);
        selectedCropId = btn.getAttribute('data-crop');
        renderMonth(parseInt(btn.getAttribute('data-month'), 10));
        var crop = H.CROPS.find(function (c) { return c.id === selectedCropId; });
        showDetail(crop);
      });
    });
  }

  function renderMonth(month, keepDetail) {
    currentMonth = month;
    var crops = filteredCrops(currentMonth);
    var season = H.seasonForMonth(currentMonth);
    if (els.label) els.label.textContent = H.monthName(currentMonth) + ' ' + year;
    if (els.count) els.count.textContent = String(crops.length);
    if (els.message) {
      els.message.textContent = season
        ? season.name + ' board · ' + crops.length + ' crop' + (crops.length === 1 ? '' : 's') + ' in season'
        : 'Season guide for stall pickup — no delivery.';
    }
    if (els.status) {
      els.status.textContent = selectedDay
        ? H.monthName(currentMonth) + ' ' + selectedDay + ' selected'
        : 'Tap a day to open crop vitamins & benefits';
    }
    if (els.weekdays) els.weekdays.hidden = viewMode !== 'grid';
    renderSeasonTabs();
    renderMonthTabs();
    renderSeasonFill();
    renderCropRail(crops);
    renderGrid(crops);
    renderList(crops);
    renderChips(crops);
    renderUpcoming();
    if (!keepDetail) {
      var featured = selectedCropId
        ? H.CROPS.find(function (c) { return c.id === selectedCropId; })
        : (crops[0] || null);
      if (featured && crops.some(function (c) { return c.id === featured.id; })) showDetail(featured);
      else showDetail(crops[0] || null);
    }
  }

  function jumpNextInSeason() {
    var total = daysInMonth(currentMonth);
    var start = (selectedDay || 0) + 1;
    for (var d = start; d <= total; d++) {
      var crop = H.cropOnDay(currentMonth, d);
      if (crop) {
        selectedDay = d;
        selectedCropId = crop.id;
        showDetail(crop);
        openDayModal(d, crop);
        renderMonth(currentMonth, true);
        return;
      }
    }
    renderMonth(currentMonth >= 12 ? 1 : currentMonth + 1);
  }

  function bindChrome() {
    var prev = document.getElementById('harvestPrev');
    var next = document.getElementById('harvestNext');
    var today = document.getElementById('harvestToday');
    var nextEvent = document.getElementById('harvestNextEvent');
    var gridBtn = document.getElementById('harvestViewGrid');
    var listBtn = document.getElementById('harvestViewList');
    if (prev) prev.addEventListener('click', function () { selectedDay = null; renderMonth(currentMonth <= 1 ? 12 : currentMonth - 1); });
    if (next) next.addEventListener('click', function () { selectedDay = null; renderMonth(currentMonth >= 12 ? 1 : currentMonth + 1); });
    if (today) today.addEventListener('click', function () {
      selectedDay = now.getFullYear() === year ? now.getDate() : 1;
      renderMonth(now.getFullYear() === year ? now.getMonth() + 1 : currentMonth);
      var crop = H.cropOnDay(currentMonth, selectedDay);
      if (crop) { selectedCropId = crop.id; showDetail(crop); }
    });
    if (nextEvent) nextEvent.addEventListener('click', jumpNextInSeason);
    if (els.search) els.search.addEventListener('input', function () { renderMonth(currentMonth); });
    if (els.kindFilter) els.kindFilter.addEventListener('change', function () { renderMonth(currentMonth); });
    if (gridBtn && listBtn) {
      gridBtn.addEventListener('click', function () {
        viewMode = 'grid';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        renderMonth(currentMonth, true);
      });
      listBtn.addEventListener('click', function () {
        viewMode = 'list';
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        renderMonth(currentMonth, true);
      });
    }
  }

  bindChrome();
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && els.modalRoot && els.modalRoot.innerHTML) {
      els.modalRoot.innerHTML = '';
    }
  });
  renderMonth(currentMonth);
})();
