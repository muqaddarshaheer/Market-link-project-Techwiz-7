(function () {
    // ============================================
    // PASTE YOUR YOUTUBE VIDEO LINK OR VIDEO ID HERE
    // Shorts, watch, youtu.be, and embed links all work.
    // ============================================
    const farmerYoutubeInput = 'https://youtube.com/shorts/XLdgRAx66g0?si=gArflvOI51kjCTHn';

    function marketLinkYouTubeId(input) {
        const value = String(input || '').trim();
        if (!value || value === 'YOUR_VIDEO_ID') return '';
        const match = value.match(/(?:shorts\/|embed\/|youtu\.be\/|watch\?v=)([A-Za-z0-9_-]{6,})/);
        return match ? match[1] : (/^[A-Za-z0-9_-]{6,}$/.test(value) ? value : '');
    }

    const marketLinkYouTubeVideoId = marketLinkYouTubeId(farmerYoutubeInput);

    // ============================================
    // EDIT 2026 HARVEST EVENTS HERE
    // Change day, crop, icon, type and message.
    // ============================================
    const marketLinkHarvestEvents = {
        1: [
            { day: 1, crop: 'Tomato', icon: '🍅', type: 'Arrival', message: 'Fresh tomatoes arrive from local growers.' },
            { day: 5, crop: 'Onion', icon: '🧅', type: 'Harvest', message: 'Onions are harvested for market stalls.' },
            { day: 12, crop: 'Spinach', icon: '🥬', type: 'Harvest', message: 'Spinach is cut for the week’s pickup.' },
            { day: 25, crop: 'Potato', icon: '🥔', type: 'Harvest', message: 'Potatoes come out of the ground.' },
            { day: 30, crop: 'Carrot', icon: '🥕', type: 'Pickup', message: 'Carrots are ready to collect at the stall.' }
        ],
        2: [
            { day: 3, crop: 'Tomato', icon: '🍅', type: 'Pickup', message: 'Tomato baskets are ready for pickup.' },
            { day: 9, crop: 'Cauliflower', icon: '🥦', type: 'Harvest', message: 'Cauliflower heads are harvested.' },
            { day: 14, crop: 'Carrot', icon: '🥕', type: 'Harvest', message: 'Carrots are pulled for market day.' },
            { day: 21, crop: 'Onion', icon: '🧅', type: 'Pickup', message: 'Onion bags are ready to collect.' },
            { day: 27, crop: 'Peas', icon: '🫛', type: 'Harvest', message: 'Peas are picked at peak sweetness.' }
        ],
        3: [
            { day: 2, crop: 'Potato', icon: '🥔', type: 'Pickup', message: 'Potatoes are bagged for stall pickup.' },
            { day: 8, crop: 'Spinach', icon: '🥬', type: 'Harvest', message: 'A fresh spinach cut is ready.' },
            { day: 15, crop: 'Strawberry', icon: '🍓', type: 'Arrival', message: 'The first strawberries arrive.' },
            { day: 23, crop: 'Cabbage', icon: '🥬', type: 'Pickup', message: 'Cabbage is ready to collect.' },
            { day: 29, crop: 'Tomato', icon: '🍅', type: 'Arrival', message: 'Another tomato arrival lands at the market.' }
        ],
        4: [
            { day: 4, crop: 'Mango', icon: '🥭', type: 'Season', message: 'Mango season begins with the first fruit.' },
            { day: 10, crop: 'Cucumber', icon: '🥒', type: 'Harvest', message: 'Cucumbers are harvested for the stall.' },
            { day: 18, crop: 'Tomato', icon: '🍅', type: 'Pickup', message: 'Tomato pickup windows open.' },
            { day: 24, crop: 'Okra', icon: '🌿', type: 'Arrival', message: 'Okra arrives from local rows.' },
            { day: 29, crop: 'Onion', icon: '🧅', type: 'Harvest', message: 'Late onions are harvested.' }
        ],
        5: [
            { day: 2, crop: 'Mango', icon: '🥭', type: 'Harvest', message: 'Mangoes are picked for market day.' },
            { day: 9, crop: 'Watermelon', icon: '🍉', type: 'Arrival', message: 'Watermelons arrive for the weekend.' },
            { day: 16, crop: 'Melon', icon: '🍈', type: 'Pickup', message: 'Melons are ready to collect.' },
            { day: 22, crop: 'Cucumber', icon: '🥒', type: 'Harvest', message: 'Cucumbers are cut for the stall.' },
            { day: 30, crop: 'Okra', icon: '🌿', type: 'Pickup', message: 'Okra is packed for pickup.' }
        ],
        6: [
            { day: 5, crop: 'Mango', icon: '🥭', type: 'Pickup', message: 'Mango boxes are ready at the stall.' },
            { day: 11, crop: 'Watermelon', icon: '🍉', type: 'Harvest', message: 'Watermelons are harvested.' },
            { day: 17, crop: 'Tomato', icon: '🍅', type: 'Arrival', message: 'Summer tomatoes arrive.' },
            { day: 25, crop: 'Onion', icon: '🧅', type: 'Pickup', message: 'Onions are ready to collect.' },
            { day: 29, crop: 'Bitter gourd', icon: '🥒', type: 'Harvest', message: 'Bitter gourd is harvested.' }
        ],
        7: [
            { day: 3, crop: 'Tomato', icon: '🍅', type: 'Harvest', message: 'Peak tomatoes are harvested.' },
            { day: 10, crop: 'Green chili', icon: '🌶️', type: 'Arrival', message: 'Green chilies arrive from the field.' },
            { day: 16, crop: 'Okra', icon: '🌿', type: 'Pickup', message: 'Okra is ready for pickup.' },
            { day: 23, crop: 'Pumpkin', icon: '🎃', type: 'Harvest', message: 'Pumpkins are cut from the vine.' },
            { day: 30, crop: 'Onion', icon: '🧅', type: 'Arrival', message: 'A new onion lot arrives.' }
        ],
        8: [
            { day: 4, crop: 'Onion', icon: '🧅', type: 'Harvest', message: 'Onions are lifted and cured.' },
            { day: 9, crop: 'Tomato', icon: '🍅', type: 'Pickup', message: 'Tomato pickup is open.' },
            { day: 15, crop: 'Green chili', icon: '🌶️', type: 'Harvest', message: 'Green chilies are picked.' },
            { day: 22, crop: 'Eggplant', icon: '🍆', type: 'Arrival', message: 'Eggplants arrive at the stall.' },
            { day: 28, crop: 'Pumpkin', icon: '🎃', type: 'Pickup', message: 'Pumpkins are ready to collect.' }
        ],
        9: [
            { day: 2, crop: 'Tomato', icon: '🍅', type: 'Arrival', message: 'Early autumn tomatoes arrive.' },
            { day: 8, crop: 'Potato', icon: '🥔', type: 'Harvest', message: 'Potatoes are harvested.' },
            { day: 14, crop: 'Onion', icon: '🧅', type: 'Pickup', message: 'Onions are ready for pickup.' },
            { day: 21, crop: 'Carrot', icon: '🥕', type: 'Arrival', message: 'Carrots arrive from local beds.' },
            { day: 27, crop: 'Spinach', icon: '🥬', type: 'Harvest', message: 'Spinach is cut for the market.' }
        ],
        10: [
            { day: 4, crop: 'Potato', icon: '🥔', type: 'Pickup', message: 'Potatoes are bagged for pickup.' },
            { day: 11, crop: 'Tomato', icon: '🍅', type: 'Harvest', message: 'Late tomatoes are harvested.' },
            { day: 17, crop: 'Cauliflower', icon: '🥦', type: 'Arrival', message: 'Cauliflower arrives for the stall.' },
            { day: 24, crop: 'Onion', icon: '🧅', type: 'Harvest', message: 'Onions are harvested.' },
            { day: 30, crop: 'Peas', icon: '🫛', type: 'Pickup', message: 'Peas are ready to collect.' }
        ],
        11: [
            { day: 3, crop: 'Carrot', icon: '🥕', type: 'Harvest', message: 'Carrots are harvested.' },
            { day: 9, crop: 'Spinach', icon: '🥬', type: 'Pickup', message: 'Spinach is ready for pickup.' },
            { day: 15, crop: 'Potato', icon: '🥔', type: 'Arrival', message: 'Potatoes arrive for market day.' },
            { day: 22, crop: 'Onion', icon: '🧅', type: 'Harvest', message: 'Onions are lifted.' },
            { day: 28, crop: 'Tomato', icon: '🍅', type: 'Pickup', message: 'Tomato pickup closes the month.' }
        ],
        12: [
            { day: 2, crop: 'Tomato', icon: '🍅', type: 'Harvest', message: 'The last tomato harvest of the year.' },
            { day: 8, crop: 'Onion', icon: '🧅', type: 'Pickup', message: 'Onions are ready to collect.' },
            { day: 14, crop: 'Potato', icon: '🥔', type: 'Harvest', message: 'Potatoes are harvested.' },
            { day: 20, crop: 'Carrot', icon: '🥕', type: 'Arrival', message: 'Winter carrots arrive.' },
            { day: 27, crop: 'Cauliflower', icon: '🥦', type: 'Pickup', message: 'Cauliflower is ready for pickup.' }
        ]
    };

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const monthNotes = [
        'Cool beds still give roots, greens, and the first tomato crates.',
        'Winter pickups continue while peas and cauliflower come in.',
        'Strawberries join the greens as the days lengthen.',
        'Mango season opens beside cucumbers, okra, and tomatoes.',
        'Warm-weather fruit and melons fill the stalls.',
        'Summer fruit and gourds move from field to pickup.',
        'Heat-loving crops — chili, okra, pumpkin — lead the month.',
        'Eggplant arrives while onions and tomatoes stay on the table.',
        'The harvest turns back toward roots, greens, and late tomatoes.',
        'Cauliflower and peas return beside potatoes and onions.',
        'Cool-season roots and greens take over the calendar.',
        'The year closes with stored roots and the last tomato crates.'
    ];

    const year = 2026;
    const monthsEl = document.getElementById('harvestMonths');
    const gridEl = document.getElementById('harvestGrid');
    const labelEl = document.getElementById('harvestMonthLabel');
    const messageEl = document.getElementById('harvestMessage');
    const countEl = document.getElementById('harvestCount');
    const chipsEl = document.getElementById('harvestChips');
    const selectedEl = document.getElementById('harvestSelected');

    function daysInMonth(monthIndex) {
        return new Date(year, monthIndex + 1, 0).getDate();
    }

    function renderSelected(events, monthIndex) {
        if (!selectedEl) return;
        if (!events || !events.length) {
            selectedEl.innerHTML = '<p class="small muted mb-0">Select a marked day to read the harvest note.</p>';
            return;
        }
        selectedEl.innerHTML = '<h3 class="h6 mb-2">Selected Harvest Event</h3>' + events.map(function (event) {
            const dateLabel = monthNames[monthIndex] + ' ' + event.day + ', ' + year;
            return '<article class="harvest-event">' +
                '<span class="harvest-event-icon" aria-hidden="true">' + event.icon + '</span>' +
                '<div><strong>' + event.crop + '</strong>' +
                '<div class="small">' + event.type + ' · <time datetime="' + year + '-' + String(monthIndex + 1).padStart(2, '0') + '-' + String(event.day).padStart(2, '0') + '">' + dateLabel + '</time></div>' +
                '<p class="mb-0 small">' + event.message + '</p></div></article>';
        }).join('');
    }

    function renderMonth(monthNumber) {
        if (!gridEl || !monthsEl) return;
        const monthIndex = monthNumber - 1;
        const events = marketLinkHarvestEvents[monthNumber] || [];
        const byDay = {};
        events.forEach(function (event) {
            byDay[event.day] = byDay[event.day] || [];
            byDay[event.day].push(event);
        });
        const firstWeekday = new Date(year, monthIndex, 1).getDay();
        const total = daysInMonth(monthIndex);
        monthsEl.querySelectorAll('[data-month]').forEach(function (button) {
            const on = Number(button.getAttribute('data-month')) === monthNumber;
            button.classList.toggle('is-active', on);
            button.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        if (labelEl) labelEl.textContent = monthNames[monthIndex] + ' ' + year;
        if (messageEl) messageEl.textContent = monthNotes[monthIndex];
        if (countEl) countEl.textContent = String(events.length);
        if (chipsEl) {
            const crops = [];
            events.forEach(function (event) {
                if (crops.indexOf(event.crop) === -1) crops.push(event.icon + ' ' + event.crop);
            });
            chipsEl.innerHTML = crops.map(function (crop) {
                return '<span class="harvest-chip">' + crop + '</span>';
            }).join('');
        }
        let html = '';
        for (let i = 0; i < firstWeekday; i++) html += '<span class="harvest-pad"></span>';
        for (let day = 1; day <= total; day++) {
            const dayEvents = byDay[day] || [];
            if (!dayEvents.length) {
                html += '<span class="harvest-day" role="gridcell"><span>' + day + '</span></span>';
                continue;
            }
            const icons = dayEvents.slice(0, 2).map(function (event) { return event.icon; }).join('');
            const more = dayEvents.length > 2 ? '<span class="harvest-more">+' + (dayEvents.length - 2) + '</span>' : '';
            const title = dayEvents.map(function (event) {
                return event.crop + ' · ' + event.type + ' · ' + monthNames[monthIndex] + ' ' + day;
            }).join('; ');
            html += '<button class="harvest-day is-event" type="button" role="gridcell" data-day="' + day + '" title="' + title + '" aria-label="' + title + '"><span>' + day + '</span><span class="harvest-icons" aria-hidden="true">' + icons + more + '</span></button>';
        }
        gridEl.innerHTML = html;
        gridEl.classList.remove('is-switching');
        void gridEl.offsetWidth;
        gridEl.classList.add('is-switching');
        renderSelected(null, monthIndex);
        gridEl.querySelectorAll('.is-event').forEach(function (button) {
            button.addEventListener('click', function () {
                const dayEvents = byDay[Number(button.getAttribute('data-day'))] || [];
                if (!dayEvents.length) return;
                gridEl.querySelectorAll('.is-event').forEach(function (item) { item.classList.remove('is-selected'); });
                button.classList.add('is-selected');
                renderSelected(dayEvents, monthIndex);
                openHarvestEventModal(dayEvents, 0, button, monthIndex);
            });
        });
    }

    const modalRoot = document.getElementById('harvestEventModalRoot');
    const productsUrl = (document.getElementById('harvest-calendar') || {}).dataset
        ? document.getElementById('harvest-calendar').dataset.productsUrl
        : '#';
    let harvestModalTrigger = null;
    let harvestModalEvents = [];
    let harvestModalIndex = 0;
    let harvestModalMonth = 0;

    function harvestDateLabel(event , monthIndex) {
        const date = new Date(year, monthIndex, event.day);
        return date.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    function harvestEsc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
        });
    }

    function harvestGrowers() {
        const node = document.getElementById('harvest-growers');
        if (!node) return [];
        try { return JSON.parse(node.textContent || '[]'); } catch (error) { return []; }
    }

    function harvestMatch(crop) {
        const word = String(crop || '').toLowerCase();
        const stem = word === 'tomato' ? 'tomato' : word === 'potato' ? 'potato' : word === 'peach' ? 'peach' : word === 'corn' ? 'corn' : word;
        return harvestGrowers().find(function (row) {
            return row.name.toLowerCase().indexOf(stem) !== -1;
        }) || null;
    }

    function harvestBenefit(crop) {
        const map = {
            Tomato: 'Sweet, ripe fruit for salads and cooking',
            Potato: 'Firm tubers for roasting and mash',
            Onion: 'Everyday kitchen staple with long shelf life',
            Carrot: 'Crunchy roots packed with natural sweetness',
            Spinach: 'Leafy greens for quick cooking',
            Corn: 'Sweet kernels for same-day pickup',
            Peach: 'Soft stone fruit at peak ripeness',
            Mango: 'Seasonal tropical fruit for market day',
        };
        return map[crop] || 'Seasonal produce reserved for stall pickup';
    }

    function paintHarvestModal() {
        if (!modalRoot || !harvestModalEvents.length) return;
        const first = harvestModalEvents[0];
        const heading = harvestDateLabel(first, harvestModalMonth);
        const results = harvestModalEvents.map(function (event) {
            const grower = harvestMatch(event.crop);
            const quality = grower ? grower.quality : 'Seasonal guide';
            const status = grower ? grower.status + ' · ' + grower.stock : 'Check markets closer to the date';
            const farmer = grower
                ? '<a href="' + harvestEsc(grower.url) + '">' + harvestEsc(grower.farmer) + '</a><div class="small muted">' + harvestEsc(grower.market) + (grower.phone ? ' · ' + harvestEsc(grower.phone) : '') + '</div>'
                : '<span class="muted">No approved stall lists this crop yet</span>';
            return '<article class="harvest-result is-in">' +
                '<div class="harvest-modal-icon" aria-hidden="true">' + event.icon + '</div>' +
                '<div><div class="d-flex flex-wrap gap-2 align-items-center mb-1">' +
                '<h3 class="h5 mb-0">' + harvestEsc(event.crop) + '</h3>' +
                '<span class="harvest-modal-badge">' + harvestEsc(event.type) + '</span></div>' +
                '<p class="mb-2">' + harvestEsc(event.message || '') + '</p>' +
                '<div class="harvest-facts">' +
                '<div><span>Benefit</span><strong>' + harvestEsc(harvestBenefit(event.crop)) + '</strong></div>' +
                '<div><span>Status</span><strong>' + harvestEsc(status) + '</strong></div>' +
                '<div><span>Quality</span><strong>' + harvestEsc(quality) + '</strong></div>' +
                '<div><span>Nearest stall</span><strong>' + farmer + '</strong></div>' +
                '</div></div></article>';
        }).join('');
        modalRoot.innerHTML =
            '<div class="harvest-modal is-open" role="dialog" aria-modal="true" aria-labelledby="harvest-event-modal-title">' +
            '<div class="harvest-modal-backdrop" data-harvest-close></div>' +
            '<div class="harvest-modal-card">' +
            '<button type="button" class="harvest-modal-close" data-harvest-close aria-label="Close harvest event details">×</button>' +
            '<p class="small muted mb-1">Harvest day details</p>' +
            '<h2 class="h4 mb-1" id="harvest-event-modal-title">' + heading + '</h2>' +
            '<p class="small mb-2">' + harvestModalEvents.length + ' crop' + (harvestModalEvents.length === 1 ? '' : 's') + ' on this day</p>' +
            results +
            '<div class="d-flex gap-2 flex-wrap mt-3"><a class="btn btn-ml" href="' + productsUrl + '">Browse produce</a><button type="button" class="btn btn-outline-ml" data-harvest-close>Close</button></div>' +
            '</div></div>';
        const dialog = modalRoot.querySelector('.harvest-modal');
        dialog.addEventListener('click', function (clickEvent) {
            if (clickEvent.target.closest('[data-harvest-close]')) closeHarvestEventModal();
        });
        const closeBtn = dialog.querySelector('.harvest-modal-close');
        if (closeBtn) closeBtn.focus();
    }

    function openHarvestEventModal(events, selectedEventIndex, triggerButton, monthIndex) {
        if (!modalRoot || !events || !events.length) return;
        harvestModalEvents = events;
        harvestModalIndex = selectedEventIndex || 0;
        harvestModalMonth = monthIndex;
        harvestModalTrigger = triggerButton || null;
        document.body.style.overflow = 'hidden';
        paintHarvestModal();
    }

    function closeHarvestEventModal() {
        if (!modalRoot) return;
        modalRoot.innerHTML = '';
        document.body.style.overflow = '';
        if (harvestModalTrigger && harvestModalTrigger.focus) harvestModalTrigger.focus();
    }

    document.addEventListener('keydown', function (keyEvent) {
        const dialog = modalRoot && modalRoot.querySelector('.harvest-modal');
        if (!dialog) return;
        if (keyEvent.key === 'Escape') {
            keyEvent.preventDefault();
            closeHarvestEventModal();
            return;
        }
        if (keyEvent.key !== 'Tab') return;
        const focusable = dialog.querySelectorAll('button, a[href]');
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (keyEvent.shiftKey && document.activeElement === first) {
            keyEvent.preventDefault();
            last.focus();
        } else if (!keyEvent.shiftKey && document.activeElement === last) {
            keyEvent.preventDefault();
            first.focus();
        }
    });

    if (monthsEl && gridEl) {
        monthNames.forEach(function (name, index) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'harvest-month';
            button.setAttribute('role', 'tab');
            button.setAttribute('data-month', String(index + 1));
            button.textContent = name;
            button.addEventListener('click', function () { renderMonth(index + 1); });
            monthsEl.appendChild(button);
        });
        const today = new Date();
        const startMonth = today.getFullYear() === 2026 ? today.getMonth() + 1 : 1;
        renderMonth(startMonth);
    }

    const videoHost = document.getElementById('storyVideo');
    const soundBtn = document.getElementById('storySound');
    const pauseBtn = document.getElementById('storyPause');
    const storyNote = document.getElementById('storyNote');
    const videoReady = marketLinkYouTubeVideoId && marketLinkYouTubeVideoId !== 'YOUR_VIDEO_ID';

    function videoSrc(id, muted) {
        return 'https://www.youtube-nocookie.com/embed/' + id +
            '?autoplay=1&mute=' + (muted ? '1' : '0') +
            '&loop=1&playlist=' + id +
            '&playsinline=1&rel=0&enablejsapi=1';
    }

    function postCommand(func) {
        const frame = videoHost && videoHost.querySelector('iframe');
        if (!frame || !frame.contentWindow) return;
        frame.contentWindow.postMessage(JSON.stringify({ event: 'command', func: func, args: [] }), '*');
    }

    if (videoHost) {
        if (!videoReady) {
            videoHost.innerHTML = '<div class="story-placeholder"><p class="mb-1"><strong>Farmer story video</strong></p><p class="mb-0 small">Paste a YouTube video ID in public/js/home.js to play this film. Playback stays muted until you choose sound.</p></div>';
            if (storyNote) storyNote.textContent = 'No video ID yet. The rest of the page still works.';
        } else {
            const frame = document.createElement('iframe');
            frame.src = videoSrc(marketLinkYouTubeVideoId, true);
            frame.title = 'MarketLink farmer story';
            frame.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
            frame.setAttribute('allowfullscreen', '');
            frame.loading = 'lazy';
            videoHost.appendChild(frame);
            if (storyNote) storyNote.textContent = 'The film starts muted. Use Watch with Sound if you want audio. Autoplay can still be blocked by the browser.';
        }
    }
    if (soundBtn) {
        soundBtn.addEventListener('click', function () {
            if (!videoReady) return;
            const frame = videoHost.querySelector('iframe');
            if (frame) frame.src = videoSrc(marketLinkYouTubeVideoId, false);
        });
    }
    if (pauseBtn) {
        pauseBtn.addEventListener('click', function () {
            if (!videoReady) return;
            postCommand(pauseBtn.getAttribute('data-paused') === '1' ? 'playVideo' : 'pauseVideo');
            const paused = pauseBtn.getAttribute('data-paused') === '1';
            pauseBtn.setAttribute('data-paused', paused ? '0' : '1');
            pauseBtn.textContent = paused ? 'Pause Background Video' : 'Play Background Video';
        });
    }

    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduce && 'IntersectionObserver' in window) {
        const nodes = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right, .reveal-scale, .stagger-children');
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.05, rootMargin: '80px 0px' });
        nodes.forEach(function (node) { observer.observe(node); });
    } else {
        document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right, .reveal-scale, .stagger-children').forEach(function (node) {
            node.classList.add('is-visible');
        });
    }
})();
