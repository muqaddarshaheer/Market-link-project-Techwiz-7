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

@if($weather)
    @php
        $weatherSpeech = app(\App\Services\WeatherSpeechService::class);
        $wxSpeakEn = $weatherSpeech->forToday($weather, 'en');
        $wxSpeakUr = $weatherSpeech->forToday($weather, 'ur');
        $forecastDays = $weather['days'] ?? [];
    @endphp
    <div class="weather-card weather-risk-{{ $weather['risk'] }} mb-4" id="weatherCard" data-lang="en"
         data-speak-en="{{ e($wxSpeakEn) }}" data-speak-ur="{{ e($wxSpeakUr) }}">
        <div class="weather-card-top">
            <div class="weather-card-main">
                <div class="weather-icon" aria-hidden="true"><i class="bi {{ $weather['icon'] }}"></i></div>
                <div>
                    <p class="weather-kicker mb-1">
                        <span class="wx-en">Today’s weather · {{ $weather['place'] }}</span>
                        <span class="wx-ur" dir="rtl" hidden>آج کا موسم · {{ $weather['place'] }}</span>
                    </p>
                    <h2 class="weather-title mb-1">
                        <span class="wx-en">{{ $weather['summary'] }}@if($weather['temp'] !== null) · {{ $weather['temp'] }}°C @endif</span>
                        <span class="wx-ur" dir="rtl" hidden>{{ $weather['summary_ur'] ?? $weather['summary'] }}@if($weather['temp'] !== null) · {{ $weather['temp'] }}°C @endif</span>
                    </h2>
                    <p class="weather-meta mb-0">
                        <span class="wx-en">
                            @if($weather['temp_min'] !== null && $weather['temp_max'] !== null)
                                High {{ $weather['temp_max'] }}° / Low {{ $weather['temp_min'] }}°
                            @endif
                            @if($weather['rain_chance'] !== null) · Rain {{ $weather['rain_chance'] }}% @endif
                            @if($weather['wind'] !== null) · Wind {{ $weather['wind'] }} km/h @endif
                        </span>
                        <span class="wx-ur" dir="rtl" hidden>
                            @if($weather['temp_min'] !== null && $weather['temp_max'] !== null)
                                زیادہ {{ $weather['temp_max'] }}° / کم {{ $weather['temp_min'] }}°
                            @endif
                            @if($weather['rain_chance'] !== null) · بارش {{ $weather['rain_chance'] }}% @endif
                            @if($weather['wind'] !== null) · ہوا {{ $weather['wind'] }} کلومیٹر/گھنٹہ @endif
                        </span>
                    </p>
                </div>
            </div>
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
        <div class="weather-advice" id="weatherAdvice">
            <strong class="wx-en">Crop note</strong>
            <strong class="wx-ur" dir="rtl" hidden>فصل کا مشورہ</strong>
            <p class="mb-0 wx-en">{{ $weather['crop_note'] }}</p>
            <p class="mb-0 wx-ur" dir="rtl" hidden>{{ $weather['crop_note_ur'] ?? $weather['crop_note'] }}</p>
        </div>

        @if(count($forecastDays) > 0)
            <div class="weather-week" aria-label="7-day forecast">
                <div class="weather-week-head">
                    <strong class="wx-en">Next 7 days · tap a day</strong>
                    <strong class="wx-ur" dir="rtl" hidden>اگلے ۷ دن · دن دبائیں</strong>
                </div>
                <div class="weather-week-grid">
                    @foreach($forecastDays as $day)
                        @php
                            $daySpeakEn = $weatherSpeech->forDay($day, 'en');
                            $daySpeakUr = $weatherSpeech->forDay($day, 'ur');
                        @endphp
                        <button type="button"
                            class="weather-day weather-day-{{ $day['risk'] }}{{ $loop->first ? ' is-today' : '' }}"
                            data-day-en="{{ e($day['label']) }}"
                            data-day-ur="{{ e($day['label_ur']) }}"
                            data-summary-en="{{ e($day['summary']) }}"
                            data-summary-ur="{{ e($day['summary_ur']) }}"
                            data-tmax="{{ $day['temp_max'] }}"
                            data-tmin="{{ $day['temp_min'] }}"
                            data-rain="{{ $day['rain_chance'] }}"
                            data-wind="{{ $day['wind'] }}"
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
                            <span class="weather-day-rain">
                                <span class="wx-en">{{ $day['rain_chance'] }}% rain</span>
                                <span class="wx-ur" dir="rtl" hidden>بارش {{ $day['rain_chance'] }}%</span>
                            </span>
                            <span class="weather-day-hint wx-en">Open</span>
                            <span class="weather-day-hint wx-ur" dir="rtl" hidden>کھولیں</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade weather-modal" id="weatherDayModal" tabindex="-1" aria-labelledby="weatherDayModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5 mb-0" id="weatherDayModalTitle">Day weather</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="weather-icon mx-auto mb-2" id="weatherDayModalIcon" aria-hidden="true"><i class="bi bi-cloud-sun"></i></div>
                    <p class="weather-title mb-1" id="weatherDayModalSummary"></p>
                    <div class="weather-modal-stats">
                        <div class="weather-modal-stat">
                            <strong id="weatherDayModalHigh">—</strong>
                            <span class="wx-en">High</span><span class="wx-ur" dir="rtl" hidden>زیادہ</span>
                        </div>
                        <div class="weather-modal-stat">
                            <strong id="weatherDayModalLow">—</strong>
                            <span class="wx-en">Low</span><span class="wx-ur" dir="rtl" hidden>کم</span>
                        </div>
                        <div class="weather-modal-stat">
                            <strong id="weatherDayModalRain">—</strong>
                            <span class="wx-en">Rain</span><span class="wx-ur" dir="rtl" hidden>بارش</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-ml weather-modal-listen" id="weatherDayListenBtn">
                        <i class="bi bi-volume-up" aria-hidden="true"></i>
                        <span id="weatherDayListenLabel">Listen</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <audio id="weatherAudio" preload="none" hidden></audio>
    <script>
    (function () {
        var card = document.getElementById('weatherCard');
        var btn = document.getElementById('weatherTranslateBtn');
        var label = document.getElementById('weatherTranslateLabel');
        var voiceBtn = document.getElementById('weatherVoiceBtn');
        var voiceLabel = document.getElementById('weatherVoiceLabel');
        var audio = document.getElementById('weatherAudio');
        var dayModalEl = document.getElementById('weatherDayModal');
        var dayListenBtn = document.getElementById('weatherDayListenBtn');
        var dayListenLabel = document.getElementById('weatherDayListenLabel');
        var activeDaySpeak = { en: '', ur: '' };
        var audioQueue = [];
        var playing = false;
        if (!card || !btn) return;

        var dayModal = null;
        if (dayModalEl && window.bootstrap) {
            dayModal = bootstrap.Modal.getOrCreateInstance(dayModalEl);
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
            if (dayModalEl) applyLangIn(dayModalEl, ur);
            label.textContent = ur ? 'English' : 'اردو';
            btn.setAttribute('aria-pressed', ur ? 'true' : 'false');
            if (voiceLabel) voiceLabel.textContent = ur ? 'سنیں' : 'Listen';
            if (dayListenLabel) dayListenLabel.textContent = ur ? 'سنیں' : 'Listen';
            localStorage.setItem('ml-weather-ur', ur ? '1' : '0');
        }

        function setSpeaking(on) {
            playing = on;
            if (voiceBtn) voiceBtn.classList.toggle('is-speaking', on);
            if (dayListenBtn) dayListenBtn.classList.toggle('is-speaking', on);
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
                    if (piece.length <= maxLen) {
                        buf = piece;
                    } else {
                        for (var i = 0; i < piece.length; i += maxLen) {
                            parts.push(piece.slice(i, i + maxLen));
                        }
                        buf = '';
                    }
                }
            });
            if (buf) parts.push(buf);
            return parts;
        }

        function pickVoice(preferUrdu) {
            var voices = window.speechSynthesis.getVoices() || [];
            var prefer = preferUrdu
                ? ['ur-PK', 'ur-IN', 'ur', 'hi-IN', 'hi']
                : ['en-PK', 'en-GB', 'en-US', 'en'];
            for (var i = 0; i < prefer.length; i++) {
                var hit = voices.find(function (v) {
                    return v.lang && v.lang.toLowerCase().indexOf(prefer[i].toLowerCase()) === 0;
                });
                if (hit) return hit;
            }
            return null;
        }

        function speakEnglish(text) {
            if (!window.speechSynthesis) {
                alert('Voice is not available in this browser.');
                return;
            }
            var utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'en-US';
            utter.rate = 0.95;
            var voice = pickVoice(false);
            if (voice) utter.voice = voice;
            utter.onstart = function () { setSpeaking(true); };
            utter.onend = function () { setSpeaking(false); };
            utter.onerror = function () { setSpeaking(false); };
            window.speechSynthesis.speak(utter);
        }

        function playNextAudio() {
            if (!audioQueue.length) {
                setSpeaking(false);
                return;
            }
            var next = audioQueue.shift();
            audio.onended = playNextAudio;
            audio.onerror = function () {
                setSpeaking(false);
                alert('Urdu voice load nahi ho saki. Internet check karein.');
            };
            audio.src = next;
            setSpeaking(true);
            var playPromise = audio.play();
            if (playPromise && playPromise.catch) {
                playPromise.catch(function () {
                    setSpeaking(false);
                    alert('Urdu voice play nahi ho saki. Browser allow karein.');
                });
            }
        }

        function speakUrduGoogle(text) {
            var speakUrl = @json(route('farmer.weather.speak'));
            audioQueue = chunkText(text, 110).map(function (part) {
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
            if (playing) {
                stopSpeech();
                return;
            }
            stopSpeech();
            var ur = isUrdu();
            var text = ur ? card.getAttribute('data-speak-ur') : card.getAttribute('data-speak-en');
            speakText(text, ur);
        }

        function openDayPopup(dayBtn) {
            activeDaySpeak.en = dayBtn.getAttribute('data-speak-en') || '';
            activeDaySpeak.ur = dayBtn.getAttribute('data-speak-ur') || '';
            var ur = isUrdu();
            var title = ur ? dayBtn.getAttribute('data-day-ur') : dayBtn.getAttribute('data-day-en');
            var summary = ur ? dayBtn.getAttribute('data-summary-ur') : dayBtn.getAttribute('data-summary-en');
            var tmax = dayBtn.getAttribute('data-tmax');
            var tmin = dayBtn.getAttribute('data-tmin');
            var rain = dayBtn.getAttribute('data-rain');
            var icon = dayBtn.getAttribute('data-icon') || 'bi-cloud-sun';

            document.getElementById('weatherDayModalTitle').textContent = title || (ur ? 'موسم' : 'Weather');
            document.getElementById('weatherDayModalSummary').textContent = summary || '';
            document.getElementById('weatherDayModalHigh').textContent = tmax ? (tmax + '°') : '—';
            document.getElementById('weatherDayModalLow').textContent = tmin ? (tmin + '°') : '—';
            document.getElementById('weatherDayModalRain').textContent = rain !== null ? (rain + '%') : '—';
            document.getElementById('weatherDayModalIcon').innerHTML = '<i class="bi ' + icon + '"></i>';
            applyLangIn(dayModalEl, ur);
            if (dayListenLabel) dayListenLabel.textContent = ur ? 'سنیں' : 'Listen';

            if (dayModal) {
                dayModal.show();
            } else if (dayModalEl && window.bootstrap) {
                dayModal = bootstrap.Modal.getOrCreateInstance(dayModalEl);
                dayModal.show();
            }
        }

        card.querySelectorAll('.weather-day').forEach(function (dayBtn) {
            dayBtn.addEventListener('click', function () {
                openDayPopup(dayBtn);
            });
        });

        if (dayListenBtn) {
            dayListenBtn.addEventListener('click', function () {
                if (playing) {
                    stopSpeech();
                    return;
                }
                stopSpeech();
                var ur = isUrdu();
                speakText(ur ? activeDaySpeak.ur : activeDaySpeak.en, ur);
            });
        }

        btn.addEventListener('click', function () {
            stopSpeech();
            apply(!isUrdu());
        });
        if (voiceBtn) voiceBtn.addEventListener('click', speakWeather);
        if (window.speechSynthesis) {
            window.speechSynthesis.getVoices();
            window.speechSynthesis.onvoiceschanged = function () {
                window.speechSynthesis.getVoices();
            };
        }
        // Farmers who do not read: default Urdu + restore preference.
        var saved = localStorage.getItem('ml-weather-ur');
        apply(saved === null ? true : saved === '1');
    })();
    </script>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><a class="stat-tile tone-orange d-block text-decoration-none" href="{{ route('farmer.orders.index') }}"><span data-i18n="dash.ordersStat">Orders</span><strong>{{ $stats['orders'] }}</strong></a></div>
    <div class="col-6 col-md-3"><a class="stat-tile tone-amber d-block text-decoration-none" href="{{ route('farmer.orders.index', ['status' => 'placed']) }}"><span data-i18n="dash.pending">Pending</span><strong>{{ $stats['pending'] }}</strong></a></div>
    <div class="col-6 col-md-3"><div class="stat-tile tone-forest"><span data-i18n="dash.revenue">Revenue</span><strong>{{ money($stats['revenue']) }}</strong></div></div>
    <div class="col-6 col-md-3"><a class="stat-tile tone-lime d-block text-decoration-none" href="{{ route('farmer.products.index') }}"><span data-i18n="dash.productsStat">Products</span><strong>{{ $stats['products'] }}</strong></a></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.slots.index') }}"><span class="panel-quick-icon tone-amber"><i class="bi bi-clock"></i></span><span><strong data-en="Pickup slots" data-ur="پک اپ سلاٹ">Pickup slots</strong><div class="small muted" data-en="Windows and cutoff hours" data-ur="وقت اور بند ہونے کے گھنٹے">Windows and cutoff hours</div></span></a></div>
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.insights') }}"><span class="panel-quick-icon tone-teal"><i class="bi bi-bar-chart"></i></span><span><strong data-en="Insights" data-ur="رپورٹ">Insights</strong><div class="small muted" data-en="Sales by day" data-ur="دن بہ دن فروخت">Sales by day</div></span></a></div>
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.profile') }}"><span class="panel-quick-icon tone-green"><i class="bi bi-shop"></i></span><span><strong data-en="Stall profile" data-ur="اسٹال پروفائل">Stall profile</strong><div class="small muted" data-en="Photo, phone, markets" data-ur="تصویر، فون، مارکیٹس">Photo, phone, markets</div></span></a></div>
    <div class="col-md-4"><a class="panel-quick card-ml panel-card p-3 d-flex align-items-center gap-3 text-decoration-none text-reset" href="{{ route('farmer.account') }}"><span class="panel-quick-icon tone-blue"><i class="bi bi-person"></i></span><span><strong data-en="Account" data-ur="اکاؤنٹ">Account</strong><div class="small muted" data-en="Avatar & password" data-ur="تصویر اور پاس ورڈ">Avatar &amp; password</div></span></a></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h5 mb-0" data-i18n="dash.recent">Recent orders</h2>
            <a class="small fw-bold" href="{{ route('farmer.orders.index') }}" data-en="Manage orders" data-ur="آرڈرز سنبھالیں">Manage orders</a>
        </div>
        @forelse($recent as $order)
            <a class="panel-list-item card-ml panel-card p-3 mb-2 d-flex justify-content-between align-items-center gap-2 text-decoration-none text-reset" href="{{ route('farmer.orders.index') }}">
                <span>
                    <strong>{{ $order->order_number }}</strong>
                    <div class="small muted">{{ $order->buyerName() }} · {{ money($order->total_amount) }}</div>
                </span>
                @include('partials.order-status-badge', ['status'=>$order->status])
            </a>
        @empty
            <div class="empty-state card-ml panel-card"><i class="bi bi-receipt"></i><p data-en="No pickup orders yet." data-ur="ابھی کوئی پک اپ آرڈر نہیں۔">No pickup orders yet.</p></div>
        @endforelse
    </div>
    <div class="col-lg-5">
        <h2 class="h5 mb-2" data-i18n="dash.lowStock">Low stock</h2>
        @forelse($lowStock as $product)
            <a class="stat-tile tone-rose p-2 mb-2 d-block text-decoration-none" href="{{ route('farmer.products.index') }}">
                <strong>{{ $product->name }}</strong>
                <span><span data-no-i18n>{{ $product->stock_quantity }}</span> <span data-en="left" data-ur="باقی">left</span></span>
            </a>
        @empty
            <p class="muted" data-en="Stock looks healthy." data-ur="اسٹاک ٹھیک لگ رہا ہے۔">Stock looks healthy.</p>
        @endforelse

        @if(($best ?? collect())->isNotEmpty())
            <h2 class="h5 mt-4 mb-2">Top sellers</h2>
            @foreach($best as $product)
                <div class="panel-list-item card-ml panel-card p-2 px-3 mb-2 d-flex justify-content-between">
                    <span>{{ $product->name }}</span>
                    <strong>{{ (int) ($product->sold ?? 0) }} sold</strong>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
