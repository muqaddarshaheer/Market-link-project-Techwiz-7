@php
    $chatFarmer = $chatFarmer ?? false;
    $chatSpeakUrl = $chatSpeakUrl ?? route('chatbot.speak');
    $chatAskUrl = route('chatbot.ask');
@endphp
<div class="chat-widget{{ $chatFarmer ? ' chat-widget-farmer' : '' }}"
     id="mlChatWidget"
     data-farmer="{{ $chatFarmer ? '1' : '0' }}"
     data-speak-url="{{ $chatSpeakUrl }}"
     data-ask-url="{{ $chatAskUrl }}"
     data-csrf="{{ csrf_token() }}">
    <div class="chat-panel" id="mlChatPanel" hidden>
        <div class="chat-panel-head">
            <div>
                <strong id="mlChatTitle">MarketLink AI</strong>
                <div class="small muted" id="mlChatSub">Markets, farmers, produce & pickup</div>
                <div class="chat-lang mt-2" role="group" aria-label="Chat language">
                    <button type="button" class="chat-lang-btn is-on" id="mlChatLangEn" data-lang="en">EN</button>
                    <button type="button" class="chat-lang-btn" id="mlChatLangUr" data-lang="ur">اردو</button>
                </div>
            </div>
            <button class="btn btn-sm chat-close" type="button" id="mlChatClose" aria-label="Close">&times;</button>
        </div>

        <div class="chat-log" id="mlChatLog">
            <div class="bubble" id="mlChatWelcome"></div>
            <div id="mlChatHistory"></div>
            <div class="small muted" id="mlChatStatus" hidden></div>
            <div class="d-flex flex-wrap gap-1 mt-1" id="mlChatSuggestions"></div>
        </div>

        <form class="chat-compose" id="mlChatForm">
            <input class="form-control" id="mlChatInput" maxlength="500" required autocomplete="off">
            <button class="btn btn-ml" type="submit" id="mlChatSend">Send</button>
        </form>
    </div>

    <button type="button" class="btn btn-ml chat-fab" id="mlChatFab" aria-label="Open chat">
        <i class="bi bi-chat-dots" id="mlChatFabIcon"></i>
    </button>

    <audio id="mlChatAudio" preload="none" hidden></audio>
</div>
<script>
(function () {
    var root = document.getElementById('mlChatWidget');
    if (!root) return;

    var farmer = root.getAttribute('data-farmer') === '1';
    var speakUrl = root.getAttribute('data-speak-url');
    var askUrl = root.getAttribute('data-ask-url');
    var csrf = root.getAttribute('data-csrf') || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    var panel = document.getElementById('mlChatPanel');
    var fab = document.getElementById('mlChatFab');
    var fabIcon = document.getElementById('mlChatFabIcon');
    var closeBtn = document.getElementById('mlChatClose');
    var form = document.getElementById('mlChatForm');
    var input = document.getElementById('mlChatInput');
    var sendBtn = document.getElementById('mlChatSend');
    var log = document.getElementById('mlChatLog');
    var historyEl = document.getElementById('mlChatHistory');
    var welcomeEl = document.getElementById('mlChatWelcome');
    var statusEl = document.getElementById('mlChatStatus');
    var suggestionsEl = document.getElementById('mlChatSuggestions');
    var titleEl = document.getElementById('mlChatTitle');
    var subEl = document.getElementById('mlChatSub');
    var audio = document.getElementById('mlChatAudio');
    var langEn = document.getElementById('mlChatLangEn');
    var langUr = document.getElementById('mlChatLangUr');

    var copy = {
        en: {
            title: farmer ? 'Farmer helper' : 'MarketLink AI',
            sub: farmer ? 'Ask about weather, orders, products, or pickup' : 'Markets, farmers, produce & pickup',
            welcome: farmer
                ? 'Ask about weather, orders, products, or pickup. Tap Listen to hear answers.'
                : 'Ask about MarketLink markets, stalls, produce, or pickup.',
            suggestions: farmer
                ? ['How is weather?', 'Pending orders?', 'How to add product?', 'Pickup slots?']
                : ['How does pickup work?', 'What costs Rs today?', 'Which farmers are open?', 'How do I create an account?'],
            placeholder: farmer ? 'Type your question…' : 'Ask about MarketLink…',
            send: 'Send',
            listen: 'Listen',
            thinking: 'Thinking…',
            speaking: 'Speaking answer…',
            error: 'Could not reach the helper. Try again.',
        },
        ur: {
            title: farmer ? 'کسان مددگار' : 'MarketLink AI',
            sub: farmer ? 'موسم، آرڈر، پروڈکٹ یا پک اپ پوچھیں' : 'مارکیٹ، کسان، پیداوار، پک اپ',
            welcome: farmer
                ? 'موسم، آرڈر، پروڈکٹ یا پک اپ پوچھیں۔ جواب سننے کے لیے سنیں دبائیں۔'
                : 'MarketLink کے بارے میں پوچھیں — مارکیٹ، کسان، پیداوار یا پک اپ۔',
            suggestions: farmer
                ? ['موسم کیسا ہے؟', 'آرڈر کتنے ہیں؟', 'پروڈکٹ کیسے ڈالیں؟', 'پک اپ سلاٹ؟']
                : ['پک اپ کیسے ہوتا ہے؟', 'آج قیمتیں کیا ہیں؟', 'کون سے کسان کھلے ہیں؟', 'اکاؤنٹ کیسے بنائیں؟'],
            placeholder: farmer ? 'سوال لکھیں…' : 'سوال لکھیں…',
            send: 'بھیجیں',
            listen: 'سنیں',
            thinking: 'سوچ رہا ہوں…',
            speaking: 'جواب بول رہا ہوں…',
            error: 'مددگار سے رابطہ نہیں ہو سکا۔',
        }
    };

    var open = false;
    var busy = false;
    var speakToken = 0;
    var lang = localStorage.getItem('ml-chat-lang') || (farmer ? 'ur' : 'en');
    if (lang !== 'ur' && lang !== 'en') lang = farmer ? 'ur' : 'en';

    function t() { return copy[lang] || copy.en; }

    function setOpen(next) {
        open = !!next;
        if (open) {
            panel.hidden = false;
            panel.classList.add('is-open');
        } else {
            panel.hidden = true;
            panel.classList.remove('is-open');
            stopTts();
        }
        fabIcon.className = 'bi ' + (open ? 'bi-x-lg' : (farmer ? 'bi-headset' : 'bi-chat-dots'));
        fab.setAttribute('aria-label', open ? 'Close chat' : 'Open chat');
    }

    function setStatus(msg) {
        if (!msg) {
            statusEl.hidden = true;
            statusEl.textContent = '';
            return;
        }
        statusEl.hidden = false;
        statusEl.textContent = msg;
    }

    function applyLang() {
        var c = t();
        titleEl.textContent = c.title;
        subEl.textContent = c.sub;
        welcomeEl.textContent = c.welcome;
        input.placeholder = c.placeholder;
        sendBtn.textContent = c.send;
        log.setAttribute('dir', lang === 'ur' ? 'rtl' : 'ltr');
        input.setAttribute('dir', lang === 'ur' ? 'rtl' : 'ltr');
        langEn.classList.toggle('is-on', lang === 'en');
        langUr.classList.toggle('is-on', lang === 'ur');
        renderSuggestions(c.suggestions);
        localStorage.setItem('ml-chat-lang', lang);
    }

    function renderSuggestions(list) {
        suggestionsEl.innerHTML = '';
        (list || []).forEach(function (s) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-ml btn-sm';
            btn.textContent = s;
            btn.addEventListener('click', function () {
                input.value = s;
                sendMessage();
            });
            suggestionsEl.appendChild(btn);
        });
    }

    function addTurn(q, a) {
        var wrap = document.createElement('div');
        wrap.className = 'chat-turn';

        var qEl = document.createElement('div');
        qEl.className = 'bubble me';
        qEl.textContent = q;

        var aEl = document.createElement('div');
        aEl.className = 'bubble';
        aEl.textContent = a;

        var replay = document.createElement('button');
        replay.type = 'button';
        replay.className = 'chat-replay';
        replay.innerHTML = '<i class="bi bi-volume-up"></i> <span></span>';
        replay.querySelector('span').textContent = t().listen;
        replay.addEventListener('click', function () { speakAnswer(a); });

        wrap.appendChild(qEl);
        wrap.appendChild(aEl);
        wrap.appendChild(replay);
        historyEl.appendChild(wrap);
        log.scrollTop = log.scrollHeight;
    }

    function stopTts() {
        speakToken += 1;
        if (audio) {
            try {
                audio.onended = null;
                audio.onerror = null;
                audio.pause();
                audio.removeAttribute('src');
            } catch (e) {}
        }
    }

    function chunkText(text, maxLen) {
        var clean = String(text || '').replace(/[*#_>`]/g, '').replace(/\s+/g, ' ').trim();
        if (!clean) return [];
        clean = clean.replace(/https?:\/\/\S+/g, '').replace(/[•·]/g, '.');
        var pieces = clean.split(/[۔.!?؟]+\s*/);
        var parts = [];
        var buf = '';
        pieces.forEach(function (piece) {
            piece = (piece || '').trim();
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
        return parts.length ? parts : [clean.slice(0, maxLen)];
    }

    function speakAnswer(text) {
        stopTts();
        if (!text || !speakUrl || !audio) return;
        var chunks = chunkText(text, 100);
        if (!chunks.length) return;
        var token = speakToken;
        var voiceLang = lang === 'ur' ? 'ur' : 'en';
        setStatus(t().speaking);

        (async function () {
            for (var i = 0; i < chunks.length; i++) {
                if (speakToken !== token) return;
                try {
                    var res = await fetch(speakUrl + '?lang=' + voiceLang + '&text=' + encodeURIComponent(chunks[i]), {
                        headers: { 'Accept': 'audio/mpeg' }
                    });
                    if (!res.ok) continue;
                    var blob = await res.blob();
                    if (!blob || blob.size < 200) continue;
                    var url = URL.createObjectURL(blob);
                    await new Promise(function (resolve) {
                        var done = function () {
                            URL.revokeObjectURL(url);
                            resolve();
                        };
                        audio.onended = done;
                        audio.onerror = done;
                        audio.src = url;
                        var p = audio.play();
                        if (p && p.catch) p.catch(done);
                    });
                } catch (e) {}
            }
            if (speakToken === token) setStatus('');
        })();
    }

    function sendMessage() {
        var q = (input.value || '').trim();
        if (!q || busy) return;
        input.value = '';
        busy = true;
        sendBtn.disabled = true;
        stopTts();
        setStatus(t().thinking);

        fetch(askUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: q, lang: lang }),
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                var answer = (data && data.answer) ? data.answer : t().error;
                addTurn(q, answer);
                speakAnswer(answer);
            })
            .catch(function () {
                addTurn(q, t().error);
            })
            .finally(function () {
                busy = false;
                sendBtn.disabled = false;
                setStatus('');
            });
    }

    fab.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        setOpen(!open);
    });

    closeBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        setOpen(false);
    });

    langEn.addEventListener('click', function () {
        lang = 'en';
        applyLang();
        stopTts();
    });
    langUr.addEventListener('click', function () {
        lang = 'ur';
        applyLang();
        stopTts();
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        sendMessage();
    });

    // Esc closes
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && open) setOpen(false);
    });

    applyLang();
    setOpen(false);
})();
</script>
