@extends('layouts.farmer')
@section('title', 'Farmer dashboard')
@section('content')
@if($farmer->approval_status !== 'approved')
    <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
        <i class="bi bi-hourglass-split fs-5"></i>
        <div>
            <strong><span data-i18n="dash.status">Stall status:</span> {{ $farmer->approval_status }}</strong>
            <div class="small mb-0" data-i18n="dash.statusNote">Listings stay hidden until an admin approves your stall. You can still finish your profile and pickup slots.</div>
        </div>
    </div>
@endif

<div class="panel-head mb-4">
    <div>
        <p class="panel-kicker mb-1" data-i18n="dash.kicker">Stall</p>
        <h1 class="section-title mb-1">{{ $farmer->stall_name }}</h1>
        <p class="muted mb-0" data-i18n="dash.lead">Manage stock, accept pickups, and track stall revenue in Rs.</p>
    </div>
    <div class="panel-actions">
        <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.products.index') }}"><i class="bi bi-basket"></i> <span data-i18n="dash.products">Products</span></a>
        <a class="btn btn-ml btn-sm" href="{{ route('farmer.orders.index') }}"><i class="bi bi-receipt"></i> <span data-i18n="dash.orders">Orders</span></a>
    </div>
</div>

{{-- At-a-glance stats --}}
<div class="dash-stats row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a class="stat-tile tone-orange d-block text-decoration-none" href="{{ route('farmer.orders.index') }}">
            <span class="stat-tile-label"><i class="bi bi-receipt" aria-hidden="true"></i> <span data-i18n="dash.ordersStat">Orders</span></span>
            <strong>{{ $stats['orders'] }}</strong>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a class="stat-tile tone-amber d-block text-decoration-none" href="{{ route('farmer.orders.index', ['status' => 'placed']) }}">
            <span class="stat-tile-label"><i class="bi bi-hourglass-split" aria-hidden="true"></i> <span data-i18n="dash.pending">Pending</span></span>
            <strong>{{ $stats['pending'] }}</strong>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-tile tone-forest">
            <span class="stat-tile-label"><i class="bi bi-cash-coin" aria-hidden="true"></i> <span data-i18n="dash.revenue">Revenue</span></span>
            <strong>{{ money($stats['revenue']) }}</strong>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <a class="stat-tile tone-lime d-block text-decoration-none" href="{{ route('farmer.products.index') }}">
            <span class="stat-tile-label"><i class="bi bi-basket" aria-hidden="true"></i> <span data-i18n="dash.productsStat">Products</span></span>
            <strong>{{ $stats['products'] }}</strong>
        </a>
    </div>
</div>

@if($weather)
    @php
        $weatherSpeech = app(\App\Services\WeatherSpeechService::class);
        $wxSpeakEn = $weatherSpeech->forToday($weather, 'en');
        $wxSpeakUr = $weatherSpeech->forToday($weather, 'ur');
        $forecastDays = $weather['days'] ?? [];
        $wxSky = $weather['sky'] ?? 'sky-partly';
        $wxTemp = $weather['temp'] ?? $weather['temp_max'] ?? null;
        $tagLabels = [
            'protect' => ['en' => 'Protect', 'ur' => 'بچاؤ'],
            'pause_water' => ['en' => 'Pause water', 'ur' => 'پانی روکیں'],
            'watch' => ['en' => 'Watch', 'ur' => 'نگرانی'],
            'light_water' => ['en' => 'Light water', 'ur' => 'ہلکا پانی'],
            'heat' => ['en' => 'Heat care', 'ur' => 'گرمی'],
            'water_cool' => ['en' => 'Cool water', 'ur' => 'ٹھنڈا پانی'],
            'wind' => ['en' => 'Wind', 'ur' => 'ہوا'],
            'support' => ['en' => 'Support', 'ur' => 'سہارا'],
            'care' => ['en' => 'Field care', 'ur' => 'دیکھ بھال'],
            'normal' => ['en' => 'Normal day', 'ur' => 'معمول'],
        ];
    @endphp

    {{-- Compact weather opener --}}
    <button type="button" class="wx-strip weather-risk-{{ $weather['risk'] }} mb-4" id="wxOpenBtn" aria-haspopup="dialog">
        <span class="wx-strip-icon" aria-hidden="true"><i class="bi {{ $weather['icon'] }}"></i></span>
        <span class="wx-strip-body min-w-0">
            <span class="wx-strip-top">
                <span class="wx-en">Weather · {{ $weather['place'] }}</span>
                <span class="wx-ur" dir="rtl" hidden>موسم · {{ $weather['place'] }}</span>
            </span>
            <span class="wx-strip-main">
                <strong class="wx-en">{{ $weather['summary'] }}@if($wxTemp !== null) · {{ $wxTemp }}°C @endif</strong>
                <strong class="wx-ur" dir="rtl" hidden>{{ $weather['summary_ur'] ?? $weather['summary'] }}@if($wxTemp !== null) · {{ $wxTemp }}°C @endif</strong>
            </span>
        </span>
        @if($wxTemp !== null)
            <span class="wx-strip-temp">{{ $wxTemp }}°</span>
        @endif
        <span class="wx-strip-cta">
            <span class="wx-en">Open</span>
            <span class="wx-ur" dir="rtl" hidden>کھولیں</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
        </span>
    </button>

    <div class="modal fade weather-panel-modal" id="weatherPanelModal" tabindex="-1" aria-labelledby="weatherPanelTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content weather-panel-sheet">
                <div class="modal-header weather-panel-head">
                    <div>
                        <p class="weather-day-kicker mb-0">
                            <span class="wx-en" id="weatherPanelTitle">Stall weather</span>
                            <span class="wx-ur" dir="rtl" hidden>اسٹال موسم</span>
                        </p>
                        <h2 class="modal-title h5 mb-0">
                            <span class="wx-en">{{ $weather['place'] }}</span>
                            <span class="wx-ur" dir="rtl" hidden>{{ $weather['place'] }}</span>
                        </h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="wx-live weather-card weather-risk-{{ $weather['risk'] }} {{ $wxSky }} border-0 rounded-0 shadow-none mb-0" id="weatherCard" data-lang="en"
                         data-speak-en="{{ e($wxSpeakEn) }}" data-speak-ur="{{ e($wxSpeakUr) }}"
                         data-place="{{ e($weather['place']) }}"
                         data-as-of="{{ e($weather['as_of'] ?? now()->toIso8601String()) }}">

                        <div class="wx-hero">
                            <div class="wx-hero-main">
                                <div class="weather-icon" id="wxHeroIcon" aria-hidden="true"><i class="bi {{ $weather['icon'] }}"></i></div>
                                <div class="wx-hero-copy min-w-0">
                                    <div class="wx-hero-top">
                                        <p class="weather-kicker mb-0">
                                            <span class="wx-en" id="wxHeroKicker">Live · {{ $weather['place'] }}</span>
                                            <span class="wx-ur" dir="rtl" hidden id="wxHeroKickerUr">لائیو · {{ $weather['place'] }}</span>
                                        </p>
                                        <div class="weather-actions">
                                            <button type="button" class="btn btn-ml btn-sm weather-voice-btn" id="weatherVoiceBtn" aria-label="Listen to weather">
                                                <i class="bi bi-volume-up" aria-hidden="true"></i>
                                                <span id="weatherVoiceLabel" data-i18n="dash.listen">Listen</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-ml btn-sm weather-translate-btn" id="weatherTranslateBtn" aria-pressed="false">
                                                <i class="bi bi-translate" aria-hidden="true"></i>
                                                <span id="weatherTranslateLabel">اردو</span>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="wx-date mb-0">
                                        <span class="wx-en" id="wxHeroDate">{{ $weather['date_full'] ?? now()->format('l, j F Y') }}</span>
                                        <span class="wx-ur" dir="rtl" hidden id="wxHeroDateUr">{{ $weather['date_full_ur'] ?? '' }}</span>
                                    </p>
                                    <div class="wx-condition">
                                        <h2 class="weather-title mb-0">
                                            <span class="wx-en" id="wxHeroSummary">{{ $weather['summary'] }}</span>
                                            <span class="wx-ur" dir="rtl" hidden id="wxHeroSummaryUr">{{ $weather['summary_ur'] ?? $weather['summary'] }}</span>
                                        </h2>
                                        @if($wxTemp !== null)
                                            <span class="wx-temp" id="wxHeroTemp">{{ $wxTemp }}°</span>
                                        @else
                                            <span class="wx-temp" id="wxHeroTemp" hidden></span>
                                        @endif
                                    </div>
                                    <div class="wx-meta" id="wxHeroMetaWrap">
                                        <span class="wx-chip wx-en" id="wxHeroMeta">
                                            @if($weather['temp_min'] !== null && $weather['temp_max'] !== null)
                                                H {{ $weather['temp_max'] }}° · L {{ $weather['temp_min'] }}°
                                            @endif
                                            @if($weather['rain_chance'] !== null) · Rain {{ $weather['rain_chance'] }}% @endif
                                            @if($weather['wind'] !== null) · Wind {{ $weather['wind'] }} km/h @endif
                                        </span>
                                        <span class="wx-chip wx-ur" dir="rtl" hidden id="wxHeroMetaUr">
                                            @if($weather['temp_min'] !== null && $weather['temp_max'] !== null)
                                                زیادہ {{ $weather['temp_max'] }}° · کم {{ $weather['temp_min'] }}°
                                            @endif
                                            @if($weather['rain_chance'] !== null) · بارش {{ $weather['rain_chance'] }}% @endif
                                            @if($weather['wind'] !== null) · ہوا {{ $weather['wind'] }} km/h @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wx-skill" id="wxSkillBox">
                            <div class="wx-skill-head">
                                <i class="bi bi-lightbulb" aria-hidden="true"></i>
                                <strong class="wx-en" id="wxSkillHeadEn">Farmer skill</strong>
                                <strong class="wx-ur" dir="rtl" hidden id="wxSkillHeadUr">کسان مہارت</strong>
                            </div>
                            <p class="wx-skill-text mb-0">
                                <span class="wx-en" id="wxSkillEn">{{ $weather['skill_en'] ?? $weather['crop_note'] }}</span>
                                <span class="wx-ur" dir="rtl" hidden id="wxSkillUr">{{ $weather['skill_ur'] ?? ($weather['crop_note_ur'] ?? $weather['crop_note']) }}</span>
                            </p>
                            <div class="wx-skill-tags" id="wxSkillTags">
                                @foreach(($weather['skill_tags'] ?? []) as $tag)
                                    @php $tl = $tagLabels[$tag] ?? null; @endphp
                                    <span class="wx-tag">
                                        <span class="wx-en">{{ $tl['en'] ?? str_replace('_', ' ', $tag) }}</span>
                                        <span class="wx-ur" dir="rtl" hidden>{{ $tl['ur'] ?? str_replace('_', ' ', $tag) }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        @if(count($forecastDays) > 0)
                            <div class="weather-week" aria-label="7-day forecast">
                                <div class="weather-week-head">
                                    <strong class="wx-en">7-day forecast</strong>
                                    <strong class="wx-ur" dir="rtl" hidden>۷ دن کا موسم</strong>
                                    <span class="wx-week-hint wx-en">Tap a day</span>
                                    <span class="wx-week-hint wx-ur" dir="rtl" hidden>دن دبائیں</span>
                                </div>
                                <div class="weather-week-grid" id="wxDayGrid">
                                    @foreach($forecastDays as $day)
                                        @php
                                            $daySpeakEn = $weatherSpeech->forDay($day, 'en');
                                            $daySpeakUr = $weatherSpeech->forDay($day, 'ur');
                                        @endphp
                                        <button type="button"
                                            class="weather-day weather-day-{{ $day['risk'] }}{{ $loop->first ? ' is-today is-active' : '' }}"
                                            data-date="{{ e($day['date'] ?? '') }}"
                                            data-day-en="{{ e($day['label']) }}"
                                            data-day-ur="{{ e($day['label_ur']) }}"
                                            data-date-full-en="{{ e($day['date_full'] ?? $day['label']) }}"
                                            data-date-full-ur="{{ e($day['date_full_ur'] ?? $day['label_ur']) }}"
                                            data-date-short-en="{{ e($day['date_short'] ?? $day['day_num']) }}"
                                            data-date-short-ur="{{ e($day['date_short_ur'] ?? $day['day_num']) }}"
                                            data-summary-en="{{ e($day['summary']) }}"
                                            data-summary-ur="{{ e($day['summary_ur']) }}"
                                            data-tmax="{{ $day['temp_max'] }}"
                                            data-tmin="{{ $day['temp_min'] }}"
                                            data-rain="{{ $day['rain_chance'] }}"
                                            data-wind="{{ $day['wind'] }}"
                                            data-risk="{{ e($day['risk']) }}"
                                            data-tip-en="{{ e($day['tip'] ?? '') }}"
                                            data-tip-ur="{{ e($day['tip_ur'] ?? '') }}"
                                            data-skill-en="{{ e($day['skill_en'] ?? '') }}"
                                            data-skill-ur="{{ e($day['skill_ur'] ?? '') }}"
                                            data-skill-tags="{{ e(implode(',', $day['skill_tags'] ?? [])) }}"
                                            data-sky="{{ e($day['sky'] ?? 'sky-partly') }}"
                                            data-icon="{{ e($day['icon']) }}"
                                            data-speak-en="{{ e($daySpeakEn) }}"
                                            data-speak-ur="{{ e($daySpeakUr) }}">
                                            <span class="weather-day-label wx-en">{{ $day['label'] }}</span>
                                            <span class="weather-day-label wx-ur" dir="rtl" hidden>{{ $day['label_ur'] }}</span>
                                            <span class="weather-day-num">{{ $day['day_num'] }}</span>
                                            <i class="bi {{ $day['icon'] }} weather-day-icon" aria-hidden="true"></i>
                                            <span class="weather-day-temp">
                                                @if($day['temp_max'] !== null){{ $day['temp_max'] }}°@endif
                                                <small>@if($day['temp_min'] !== null){{ $day['temp_min'] }}°@endif</small>
                                            </span>
                                            <span class="weather-day-rain">{{ $day['rain_chance'] }}%</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <audio id="weatherAudio" preload="none" hidden></audio>
    <script>
    (function () {
        var card = document.getElementById('weatherCard');
        var strip = document.getElementById('wxOpenBtn');
        var panelEl = document.getElementById('weatherPanelModal');
        var btn = document.getElementById('weatherTranslateBtn');
        var label = document.getElementById('weatherTranslateLabel');
        var voiceBtn = document.getElementById('weatherVoiceBtn');
        var voiceLabel = document.getElementById('weatherVoiceLabel');
        var audio = document.getElementById('weatherAudio');
        var audioQueue = [];
        var playing = false;
        if (!card || !btn || !strip) return;

        var panelModal = null;
        function getPanel() {
            if (panelModal) return panelModal;
            if (panelEl && window.bootstrap && bootstrap.Modal) {
                panelModal = bootstrap.Modal.getOrCreateInstance(panelEl);
            }
            return panelModal;
        }
        function openPanel() {
            var m = getPanel();
            if (m) { m.show(); return; }
            var tries = 0;
            var timer = setInterval(function () {
                tries += 1;
                m = getPanel();
                if (m) { clearInterval(timer); m.show(); }
                else if (tries > 40) clearInterval(timer);
            }, 50);
        }

        function isUrdu() {
            return card.getAttribute('data-lang') === 'ur';
        }

        function applyLangIn(root, ur) {
            (root || document).querySelectorAll('.wx-en').forEach(function (el) { el.hidden = ur; });
            (root || document).querySelectorAll('.wx-ur').forEach(function (el) { el.hidden = !ur; });
        }

        function apply(ur) {
            card.setAttribute('data-lang', ur ? 'ur' : 'en');
            applyLangIn(card, ur);
            applyLangIn(strip, ur);
            if (panelEl) applyLangIn(panelEl, ur);
            label.textContent = ur ? 'English' : 'اردو';
            btn.setAttribute('aria-pressed', ur ? 'true' : 'false');
            if (voiceLabel) voiceLabel.textContent = ur ? 'سنیں' : 'Listen';
            localStorage.setItem('ml-weather-ur', ur ? '1' : '0');
        }

        function setSpeaking(on) {
            playing = on;
            if (voiceBtn) voiceBtn.classList.toggle('is-speaking', on);
        }

        function stopSpeech() {
            if (window.speechSynthesis) window.speechSynthesis.cancel();
            audioQueue = [];
            if (audio) {
                audio.pause();
                audio.removeAttribute('src');
                audio.load();
            }
            setSpeaking(false);
        }

        function chunkText(text, maxLen) {
            var parts = [];
            var clean = String(text || '').replace(/\s+/g, ' ').trim();
            if (!clean) return parts;
            var pieces = clean.split(/[۔.!?؟]+\s*/);
            var buf = '';
            pieces.forEach(function (piece) {
                piece = piece.trim();
                if (!piece) return;
                if ((buf + ' ' + piece).trim().length <= maxLen) {
                    buf = (buf + ' ' + piece).trim();
                } else {
                    if (buf) parts.push(buf);
                    if (piece.length <= maxLen) buf = piece;
                    else {
                        for (var i = 0; i < piece.length; i += maxLen) parts.push(piece.slice(i, i + maxLen));
                        buf = '';
                    }
                }
            });
            if (buf) parts.push(buf);
            return parts;
        }

        function pickVoice(preferUrdu) {
            var voices = window.speechSynthesis.getVoices() || [];
            if (!voices.length) return null;
            if (preferUrdu) {
                return voices.find(function (v) { return /ur|pakistan/i.test(v.lang + ' ' + v.name); })
                    || voices.find(function (v) { return /hi|hindi/i.test(v.lang + ' ' + v.name); })
                    || null;
            }
            return voices.find(function (v) { return /^en/i.test(v.lang); }) || voices[0];
        }

        function speakEnglish(text) {
            if (!window.speechSynthesis || !text) return;
            var u = new SpeechSynthesisUtterance(text);
            var voice = pickVoice(false);
            if (voice) u.voice = voice;
            u.lang = (voice && voice.lang) || 'en-US';
            u.rate = 0.95;
            u.onend = function () { setSpeaking(false); };
            u.onerror = function () { setSpeaking(false); };
            setSpeaking(true);
            window.speechSynthesis.speak(u);
        }

        function playNextAudio() {
            if (!audioQueue.length) { setSpeaking(false); return; }
            var url = audioQueue.shift();
            audio.src = url;
            setSpeaking(true);
            audio.onended = playNextAudio;
            audio.onerror = function () { setSpeaking(false); audioQueue = []; };
            audio.play().catch(function () { setSpeaking(false); audioQueue = []; });
        }

        function speakUrduGoogle(text) {
            var speakUrl = @json(route('farmer.weather.speak'));
            audioQueue = chunkText(text, 140).map(function (part) {
                return speakUrl + '?lang=ur&text=' + encodeURIComponent(part);
            });
            if (!audioQueue.length) return;
            playNextAudio();
        }

        function speakText(text, ur) {
            if (!text) return;
            if (ur) speakUrduGoogle(text);
            else speakEnglish(text);
        }

        function speakWeather() {
            if (playing) { stopSpeech(); return; }
            stopSpeech();
            var ur = isUrdu();
            speakText(ur ? card.getAttribute('data-speak-ur') : card.getAttribute('data-speak-en'), ur);
        }

        function selectDay(dayBtn) {
            if (!dayBtn) return;
            card.querySelectorAll('.weather-day').forEach(function (b) {
                b.classList.toggle('is-active', b === dayBtn);
            });

            var place = card.getAttribute('data-place') || '';
            var summaryEn = dayBtn.getAttribute('data-summary-en') || '';
            var summaryUr = dayBtn.getAttribute('data-summary-ur') || summaryEn;
            var tmax = dayBtn.getAttribute('data-tmax');
            var tmin = dayBtn.getAttribute('data-tmin');
            var rain = dayBtn.getAttribute('data-rain');
            var wind = dayBtn.getAttribute('data-wind');
            var risk = dayBtn.getAttribute('data-risk') || 'ok';
            var sky = dayBtn.getAttribute('data-sky') || 'sky-partly';
            var icon = dayBtn.getAttribute('data-icon') || 'bi-cloud-sun';
            var tipEn = dayBtn.getAttribute('data-tip-en') || '';
            var tipUr = dayBtn.getAttribute('data-tip-ur') || tipEn;
            var skillEn = dayBtn.getAttribute('data-skill-en') || tipEn;
            var skillUr = dayBtn.getAttribute('data-skill-ur') || tipUr;
            var tags = (dayBtn.getAttribute('data-skill-tags') || '').split(',').filter(Boolean);
            var dateFullEn = dayBtn.getAttribute('data-date-full-en') || '';
            var dateFullUr = dayBtn.getAttribute('data-date-full-ur') || '';
            var isToday = dayBtn.classList.contains('is-today');
            var tagMap = {
                protect: { en: 'Protect', ur: 'بچاؤ' },
                pause_water: { en: 'Pause water', ur: 'پانی روکیں' },
                watch: { en: 'Watch', ur: 'نگرانی' },
                light_water: { en: 'Light water', ur: 'ہلکا پانی' },
                heat: { en: 'Heat care', ur: 'گرمی' },
                water_cool: { en: 'Cool water', ur: 'ٹھنڈا پانی' },
                wind: { en: 'Wind', ur: 'ہوا' },
                support: { en: 'Support', ur: 'سہارا' },
                care: { en: 'Field care', ur: 'دیکھ بھال' },
                normal: { en: 'Normal day', ur: 'معمول' }
            };

            card.className = 'wx-live weather-card weather-risk-' + risk + ' ' + sky + ' border-0 rounded-0 shadow-none mb-0';
            var iconEl = document.getElementById('wxHeroIcon');
            if (iconEl) iconEl.innerHTML = '<i class="bi ' + icon + '"></i>';

            var kick = document.getElementById('wxHeroKicker');
            var kickUr = document.getElementById('wxHeroKickerUr');
            if (kick) kick.textContent = (isToday ? 'Live' : 'Forecast') + (place ? ' · ' + place : '');
            if (kickUr) kickUr.textContent = (isToday ? 'لائیو' : 'پیش گوئی') + (place ? ' · ' + place : '');

            var dEn = document.getElementById('wxHeroDate');
            var dUr = document.getElementById('wxHeroDateUr');
            if (dEn) dEn.textContent = dateFullEn;
            if (dUr) dUr.textContent = dateFullUr;

            var sEn = document.getElementById('wxHeroSummary');
            var sUr = document.getElementById('wxHeroSummaryUr');
            if (sEn) sEn.textContent = summaryEn;
            if (sUr) sUr.textContent = summaryUr;

            var tempEl = document.getElementById('wxHeroTemp');
            if (tempEl) {
                if (tmax) { tempEl.textContent = tmax + '°'; tempEl.hidden = false; }
                else { tempEl.textContent = ''; tempEl.hidden = true; }
            }

            var mEn = document.getElementById('wxHeroMeta');
            var mUr = document.getElementById('wxHeroMetaUr');
            if (mEn) {
                mEn.textContent = [
                    (tmax && tmin) ? ('H ' + tmax + '° · L ' + tmin + '°') : '',
                    rain !== '' && rain !== null ? ('Rain ' + rain + '%') : '',
                    wind !== '' && wind !== null ? ('Wind ' + wind + ' km/h') : ''
                ].filter(Boolean).join(' · ');
            }
            if (mUr) {
                mUr.textContent = [
                    (tmax && tmin) ? ('زیادہ ' + tmax + '° · کم ' + tmin + '°') : '',
                    rain !== '' && rain !== null ? ('بارش ' + rain + '%') : '',
                    wind !== '' && wind !== null ? ('ہوا ' + wind + ' km/h') : ''
                ].filter(Boolean).join(' · ');
            }

            var skEn = document.getElementById('wxSkillEn');
            var skUr = document.getElementById('wxSkillUr');
            if (skEn) skEn.textContent = skillEn;
            if (skUr) skUr.textContent = skillUr;
            var tagsEl = document.getElementById('wxSkillTags');
            if (tagsEl) {
                tagsEl.innerHTML = tags.map(function (t) {
                    var map = tagMap[t] || { en: String(t).replace(/_/g, ' '), ur: String(t).replace(/_/g, ' ') };
                    return '<span class="wx-tag"><span class="wx-en">' + map.en + '</span><span class="wx-ur" dir="rtl" hidden>' + map.ur + '</span></span>';
                }).join('');
            }

            card.setAttribute('data-speak-en', dayBtn.getAttribute('data-speak-en') || '');
            card.setAttribute('data-speak-ur', dayBtn.getAttribute('data-speak-ur') || '');
            applyLangIn(card, isUrdu());
        }

        strip.addEventListener('click', openPanel);

        var todayBtn = card.querySelector('.weather-day.is-today') || card.querySelector('.weather-day');
        if (todayBtn) selectDay(todayBtn);

        card.querySelectorAll('.weather-day').forEach(function (dayBtn) {
            dayBtn.addEventListener('click', function () { selectDay(dayBtn); });
        });

        btn.addEventListener('click', function () {
            stopSpeech();
            apply(!isUrdu());
        });
        if (voiceBtn) voiceBtn.addEventListener('click', speakWeather);
        if (panelEl) {
            panelEl.addEventListener('hidden.bs.modal', function () { stopSpeech(); });
        }
        if (window.speechSynthesis) {
            window.speechSynthesis.getVoices();
            window.speechSynthesis.onvoiceschanged = function () { window.speechSynthesis.getVoices(); };
        }
        var saved = localStorage.getItem('ml-weather-ur');
        apply(saved === null ? true : saved === '1');
    })();
    </script>
@endif

{{-- Meri zameen + farm helpers + lists --}}
@php
    $dashLands = $lands ?? collect();
    $help = $farmHelp ?? ['tip_en' => '', 'tip_ur' => '', 'todos' => [], 'quickRestock' => collect()];
@endphp

<div class="dash-shell" id="farmHelp" data-farmer="{{ $farmer->id }}">
    {{-- Today tip --}}
    <div class="farm-help-tip card-ml panel-card p-3 p-md-4 mb-4">
        <div class="d-flex align-items-start gap-3">
            <span class="farm-help-icon" aria-hidden="true"><i class="bi bi-lightbulb"></i></span>
            <div class="flex-grow-1 min-w-0">
                <p class="panel-kicker mb-1" data-en="Today’s farm tip" data-ur="آج کی کھیتی ٹپ">Today’s farm tip</p>
                <h2 class="h5 mb-1">{{ $help['tip_en'] }}</h2>
                <p class="muted small mb-0" dir="rtl">{{ $help['tip_ur'] }}</p>
            </div>
            <div class="d-none d-md-flex flex-column gap-2 flex-shrink-0 farm-help-actions">
                <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.crop-calculator') }}"><i class="bi bi-calculator"></i> Calculator</a>
                <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.lands.index') }}"><i class="bi bi-geo-alt"></i> My Land</a>
            </div>
        </div>
    </div>

    {{-- Do today + restock --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="dash-section-head">
                <h2 class="h5 mb-0" data-en="Do today" data-ur="آج کریں">Do today</h2>
                <span class="small muted" data-en="Tap Done when finished" data-ur="ہو جائے تو Done دبائیں">Tap Done when finished</span>
            </div>
            @forelse($help['todos'] as $todo)
                <div class="farm-todo card-ml panel-card p-3 mb-2 d-flex align-items-center gap-3" data-todo="{{ $todo['id'] }}">
                    <span class="farm-todo-icon"><i class="bi {{ $todo['icon'] }}"></i></span>
                    <div class="flex-grow-1 min-w-0">
                        <a class="fw-bold text-decoration-none text-reset d-block text-truncate" href="{{ $todo['href'] }}">{{ $todo['title'] }}</a>
                        <div class="small muted text-truncate" dir="rtl">{{ $todo['title_ur'] }}</div>
                    </div>
                    <button type="button" class="btn btn-ml btn-sm farm-todo-btn" data-todo-done>Done</button>
                </div>
            @empty
                <div class="dash-empty card-ml panel-card text-center py-4">
                    <i class="bi bi-check2-circle"></i>
                    <p class="mb-0 muted" data-en="No urgent tasks — you’re clear." data-ur="کوئی فوری کام نہیں — سب ٹھیک۔">No urgent tasks — you’re clear.</p>
                </div>
            @endforelse
        </div>
        <div class="col-lg-5">
            <div class="dash-section-head">
                <h2 class="h5 mb-0" data-en="Quick restock" data-ur="فوری اسٹاک">Quick restock</h2>
            </div>
            @forelse($help['quickRestock'] as $product)
                <form method="POST" action="{{ route('farmer.products.update', $product) }}" class="farm-restock card-ml panel-card p-3 mb-2" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                    <input type="hidden" name="market_id" value="{{ $product->market_id }}">
                    <input type="hidden" name="price" value="{{ $product->price }}">
                    <input type="hidden" name="unit" value="{{ $product->unit }}">
                    <input type="hidden" name="quality" value="{{ $product->quality ?? 'fresh' }}">
                    <input type="hidden" name="is_available" value="1">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                        <strong class="text-truncate">{{ $product->name }}</strong>
                        <span class="small muted">{{ $product->stock_quantity }} left</span>
                    </div>
                    <div class="d-flex gap-2">
                        <input class="form-control form-control-sm" name="stock_quantity" type="number" min="0" value="{{ max(10, (int) $product->stock_quantity + 10) }}" required>
                        <button class="btn btn-ml btn-sm" type="submit">Save</button>
                    </div>
                </form>
            @empty
                <div class="dash-empty card-ml panel-card text-center py-4">
                    <i class="bi bi-box-seam"></i>
                    <p class="mb-0 muted" data-en="Stock looks fine." data-ur="اسٹاک ٹھیک ہے۔">Stock looks fine.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Quick links --}}
    <div class="dash-section-head mb-2">
        <h2 class="h5 mb-0" data-en="Shortcuts" data-ur="شارٹ کٹس">Shortcuts</h2>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset h-100" href="{{ route('farmer.slots.index') }}">
                <span class="panel-quick-icon tone-amber"><i class="bi bi-clock"></i></span>
                <span><strong data-en="Pickup slots" data-ur="پک اپ سلاٹ">Pickup slots</strong><div class="small muted" data-en="Hours & cutoff" data-ur="وقت اور بندش">Hours &amp; cutoff</div></span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset h-100" href="{{ route('farmer.sales') }}">
                <span class="panel-quick-icon tone-teal"><i class="bi bi-graph-up"></i></span>
                <span><strong data-en="Sales" data-ur="فروخت">Sales</strong><div class="small muted" data-en="Revenue by day" data-ur="دن بہ دن آمدنی">Revenue by day</div></span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset h-100" href="{{ route('farmer.expenses.index') }}">
                <span class="panel-quick-icon tone-orange"><i class="bi bi-wallet2"></i></span>
                <span><strong data-en="Expenses" data-ur="خرچہ">Expenses</strong><div class="small muted" data-en="Farm costs" data-ur="فارم لاگت">Farm costs</div></span>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset h-100" href="{{ route('farmer.profile') }}">
                <span class="panel-quick-icon tone-green"><i class="bi bi-shop"></i></span>
                <span><strong data-en="Stall profile" data-ur="اسٹال پروفائل">Stall profile</strong><div class="small muted" data-en="Photo & markets" data-ur="تصویر اور مارکیٹ">Photo &amp; markets</div></span>
            </a>
        </div>
    </div>

    {{-- My land --}}
    <div class="dash-section-head mb-2">
        <div>
            <h2 class="h5 mb-0" data-i18n="dash.lands">My land</h2>
            <p class="small muted mb-0" data-i18n="dash.landsLead">What’s planted where</p>
        </div>
        <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.lands.index') }}"><i class="bi bi-geo-alt"></i> <span data-i18n="dash.landsAll">Manage land</span></a>
    </div>
    @if($dashLands->isEmpty())
        <div class="card-ml panel-card p-3 mb-4 d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <p class="mb-0 muted" data-i18n="dash.landsEmpty">Add plots to track crops per field.</p>
            <a class="btn btn-ml btn-sm" href="{{ route('farmer.lands.index') }}">+ Add land</a>
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach($dashLands as $land)
                @php
                    $dh = $land->daysToHarvest();
                    $harvestNote = null;
                    if ($dh !== null && $land->crop_name) {
                        $harvestNote = $dh > 0 ? '~'.$dh.'d harvest' : ($dh === 0 ? 'harvest today' : 'overdue');
                    }
                @endphp
                <div class="col-md-6 col-xl-4">
                    <a href="{{ route('farmer.lands.index') }}" class="dash-land card-ml panel-card p-3 d-block text-decoration-none text-reset h-100">
                        <div class="dash-land-top">
                            <strong class="dash-land-name text-truncate">{{ $land->name }}</strong>
                            <span class="dash-land-area">{{ $land->areaLabel() }}</span>
                        </div>
                        <div class="dash-land-crop">{{ $land->crop_name ?: 'Empty' }}</div>
                        <div class="dash-land-meta">
                            <span class="dash-land-stage stage-{{ $land->stage }}">{{ $land->stageLabel() }}</span>
                            @if($harvestNote)
                                <span class="dash-land-harvest">{{ $harvestNote }}</span>
                            @endif
                        </div>
                        <p class="dash-land-tip mb-0 text-truncate" title="{{ $land->careTip() }}"><i class="bi bi-lightbulb" aria-hidden="true"></i> {{ $land->careTip() }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Orders + stock --}}
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="dash-panel card-ml panel-card p-3 p-md-4 h-100">
                <div class="dash-section-head mb-3">
                    <div>
                        <h2 class="h5 mb-0" data-i18n="dash.recent">Recent orders</h2>
                        <p class="small muted mb-0" data-en="Latest pickups" data-ur="تازہ پک اپس">Latest pickups</p>
                    </div>
                    <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.orders.index') }}" data-en="View all" data-ur="سب دیکھیں">View all</a>
                </div>
                @forelse($recent as $order)
                    <a class="dash-order d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.orders.index') }}">
                        <span class="dash-order-icon" aria-hidden="true"><i class="bi bi-bag-check"></i></span>
                        <span class="flex-grow-1 min-w-0">
                            <strong class="d-block text-truncate">{{ $order->order_number }}</strong>
                            <span class="small muted text-truncate d-block">{{ $order->buyerName() }} · {{ money($order->total_amount) }}</span>
                        </span>
                        @include('partials.order-status-badge', ['status' => $order->status])
                    </a>
                @empty
                    <div class="dash-empty text-center py-4">
                        <i class="bi bi-receipt d-block mb-2"></i>
                        <p class="mb-0 muted" data-en="No pickup orders yet." data-ur="ابھی کوئی پک اپ آرڈر نہیں۔">No pickup orders yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dash-panel card-ml panel-card p-3 p-md-4 mb-3">
                <div class="dash-section-head mb-3">
                    <div>
                        <h2 class="h5 mb-0" data-i18n="dash.lowStock">Low stock</h2>
                        <p class="small muted mb-0" data-en="Restock soon" data-ur="جلد بھریں">Restock soon</p>
                    </div>
                    <a class="btn btn-outline-ml btn-sm" href="{{ route('farmer.products.index') }}">Products</a>
                </div>
                @forelse($lowStock as $product)
                    <a class="dash-stock d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.products.index') }}">
                        <span class="dash-stock-qty" data-no-i18n>{{ $product->stock_quantity }}</span>
                        <span class="flex-grow-1 min-w-0">
                            <strong class="d-block text-truncate">{{ $product->name }}</strong>
                            <span class="small muted" data-en="left in stock" data-ur="اسٹاک میں باقی">left in stock</span>
                        </span>
                        <i class="bi bi-chevron-right muted" aria-hidden="true"></i>
                    </a>
                @empty
                    <div class="dash-empty text-center py-3">
                        <i class="bi bi-check2-circle d-block mb-2"></i>
                        <p class="mb-0 muted" data-en="Stock looks healthy." data-ur="اسٹاک ٹھیک لگ رہا ہے۔">Stock looks healthy.</p>
                    </div>
                @endforelse
            </div>

            @if(($best ?? collect())->isNotEmpty())
                <div class="dash-panel card-ml panel-card p-3 p-md-4">
                    <h2 class="h5 mb-3" data-en="Top sellers" data-ur="سب سے زیادہ فروخت">Top sellers</h2>
                    @foreach($best as $product)
                        <div class="dash-seller d-flex justify-content-between align-items-center gap-2">
                            <span class="text-truncate">{{ $product->name }}</span>
                            <strong class="text-nowrap">{{ (int) ($product->sold ?? 0) }} sold</strong>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
  var root = document.getElementById('farmHelp');
  if (!root) return;
  var key = 'farm-todos:' + (root.getAttribute('data-farmer') || '0') + ':' + new Date().toISOString().slice(0, 10);
  var map = {};
  try { map = JSON.parse(localStorage.getItem(key) || '{}') || {}; } catch (e) {}
  root.querySelectorAll('[data-todo]').forEach(function (row) {
    var id = row.getAttribute('data-todo');
    var btn = row.querySelector('[data-todo-done]');
    if (!btn) return;
    function paint(on) {
      row.classList.toggle('is-done', on);
      btn.textContent = on ? 'Undo' : 'Done';
    }
    paint(!!map[id]);
    btn.addEventListener('click', function () {
      map[id] = !map[id];
      localStorage.setItem(key, JSON.stringify(map));
      paint(!!map[id]);
    });
  });
})();
</script>
@endsection
