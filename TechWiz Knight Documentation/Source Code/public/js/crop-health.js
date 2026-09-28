(function () {
  'use strict';

  function boot() {
    var root = document.getElementById('chaApp');
    if (!root) return;

    var messageUrl = root.getAttribute('data-message-url');
    var answerUrl = root.getAttribute('data-answer-url');
    var photoUrl = root.getAttribute('data-photo-url');
    var resetUrl = root.getAttribute('data-reset-url');
    var speakUrl = root.getAttribute('data-speak-url');
    var csrf = root.getAttribute('data-csrf') || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    var statusEl = document.getElementById('chaStatus');
    var micBtn = document.getElementById('chaMic');
    var micCaption = document.getElementById('chaMicCaption');
    var heardBox = document.getElementById('chaHeard');
    var heardText = document.getElementById('chaHeardText');
    var detectBox = document.getElementById('chaDetect');
    var detectText = document.getElementById('chaDetectText');
    var textInput = document.getElementById('chaText');
    var sendBtn = document.getElementById('chaSend');
    var askBox = document.getElementById('chaAsk');
    var askPrompt = document.getElementById('chaAskPrompt');
    var optionsEl = document.getElementById('chaOptions');
    var resultBox = document.getElementById('chaResult');
    var speakBtn = document.getElementById('chaSpeakBtn');
    var resetBtn = document.getElementById('chaReset');
    var photoInput = document.getElementById('chaPhoto');
    var audio = document.getElementById('chaAudio');
    var stickyBoard = document.getElementById('chaStickyBoard');
    var stickyList = document.getElementById('chaStickyList');
    var STICKY_KEY = 'gla-sticky-notes';
    var lastResultPayload = null;

    var lang = localStorage.getItem('cha-lang') || 'roman';
    if (lang === 'ur') lang = 'roman';
    var lastSpeak = '';
    var busy = false;
    var recognition = null;
    var listening = false;
    var speakToken = 0;

    var i18n = {
      ur: {
        title: 'Crop Health Assistant',
        lead: 'اپنی فصل کا مسئلہ بتائیں',
        micHint: 'Bol kar batayein',
        holdSpeak: '🎤 Bol Kar Batayein',
        listening: '🎤 Listening… bolte rahiye',
        thinking: '⏳ Processing… samajh raha hoon',
        speaking: '🔊 Answer bol raha hoon…',
        ready: '🔊 Answer Ready — Sunayein dabayein',
        youSaid: 'Aap ne kaha',
        detected: 'Samajh aaya',
        typePh: 'Ya type karein… (Roman Urdu / English)',
        send: 'Bhejein',
        orType: 'Ya masla likhein',
        photo: 'Photo lein',
        speak: '🔊 Sunayein',
        reset: 'Naya masla',
        safe: 'Ye possible wajahain hain — confirmed bimari nahi.',
        checkTitle: 'Ye check karein',
        causesTitle: 'Mumkin wajahain',
        protectTitle: 'Baqi fasal ka khayal',
        nutritionTitle: 'Plant nutrition',
        seasonTitle: 'Mausami rehnumai',
        weatherTitle: 'Mausam',
        spreadTitle: 'Phailne ka risk',
        noSpeech: 'Awaz samajh nahi aayi. Dobara bolain ya likhein.',
        micFail: 'Mic nahi chala. Roman Urdu mein likh kar bhejein.',
        netFail: 'Server jawab nahi de raha. Page refresh karke try karein.'
      },
      roman: {
        title: 'Crop Health Assistant',
        lead: 'Apni fasal ka masla batayein',
        micHint: 'Bol kar batayein',
        holdSpeak: '🎤 Bol Kar Batayein',
        listening: '🎤 Listening… bolte rahiye',
        thinking: '⏳ Processing… samajh raha hoon',
        speaking: '🔊 Answer bol raha hoon…',
        ready: '🔊 Answer Ready — Sunayein dabayein',
        youSaid: 'Aap ne kaha',
        detected: 'Samajh aaya',
        typePh: 'Ya type karein… (Roman Urdu / English)',
        send: 'Bhejein',
        orType: 'Ya masla likhein',
        photo: 'Photo lein',
        speak: '🔊 Sunayein',
        reset: 'Naya masla',
        safe: 'Ye possible wajahain hain — confirmed bimari nahi.',
        checkTitle: 'Ye check karein',
        causesTitle: 'Mumkin wajahain',
        protectTitle: 'Baqi fasal ka khayal',
        nutritionTitle: 'Plant nutrition',
        seasonTitle: 'Mausami rehnumai',
        weatherTitle: 'Mausam',
        spreadTitle: 'Phailne ka risk',
        noSpeech: 'Awaz samajh nahi aayi. Dobara bolain ya likhein.',
        micFail: 'Mic nahi chala. Roman Urdu mein likh kar bhejein.',
        netFail: 'Server jawab nahi de raha. Page refresh karke try karein.'
      },
      en: {
        title: 'Crop Health Assistant',
        lead: 'Tell us about your crop problem',
        micHint: 'Tap the microphone to speak',
        holdSpeak: '🎤 Tap mic and speak',
        listening: '🎤 Listening…',
        thinking: '⏳ Processing…',
        speaking: '🔊 Speaking answer…',
        ready: '🔊 Answer Ready',
        youSaid: 'You said',
        detected: 'Understood',
        typePh: 'Or type here… (Roman Urdu / English)',
        send: 'Send',
        orType: 'Or type the problem',
        photo: 'Take photo',
        speak: '🔊 Listen',
        reset: 'New problem',
        safe: 'Possible causes only — not a confirmed diagnosis.',
        checkTitle: 'Check these',
        causesTitle: 'Possible causes',
        protectTitle: 'Protect the rest',
        nutritionTitle: 'Plant nutrition',
        seasonTitle: 'Season guide',
        weatherTitle: 'Weather',
        spreadTitle: 'Possible spread risk',
        noSpeech: 'Could not catch speech. Try again or type.',
        micFail: 'Mic unavailable. Type in Roman Urdu / English.',
        netFail: 'Server did not respond. Refresh and try again.'
      }
    };

    function t() { return i18n[lang] || i18n.roman; }

    function applyLang() {
      root.setAttribute('data-lang', lang);
      root.setAttribute('dir', 'ltr');
      root.querySelectorAll('.cha-lang-btn').forEach(function (b) {
        b.classList.toggle('is-on', b.getAttribute('data-lang') === lang);
      });
    }

    function setStatus(msg) {
      if (statusEl) statusEl.textContent = msg || t().micHint;
    }

    function setListening(on) {
      listening = on;
      micBtn.classList.toggle('is-listening', on);
      micBtn.classList.toggle('is-busy', false);
      setStatus(on ? t().listening : t().micHint);
      if (micCaption) micCaption.textContent = on ? t().listening : t().holdSpeak;
    }

    function setBusy(on) {
      busy = on;
      micBtn.classList.toggle('is-busy', on);
      micBtn.disabled = on;
      sendBtn.disabled = on;
      if (on) setStatus(t().thinking);
    }

    async function postJson(url, body) {
      var res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify(body)
      });
      var data = null;
      try { data = await res.json(); } catch (e) {}
      if (!res.ok) {
        var msg = (data && (data.message || data.error)) || t().netFail;
        if (data && data.errors) {
          var first = Object.values(data.errors)[0];
          if (first && first[0]) msg = first[0];
        }
        throw new Error(msg);
      }
      return data;
    }

    function showHeard(text) {
      if (!text) { heardBox.hidden = true; return; }
      heardBox.hidden = false;
      heardText.textContent = text;
    }

    function showDetected(detected) {
      if (!detectBox) return;
      if (!detected || !detected.name) {
        detectBox.hidden = true;
        return;
      }
      detectBox.hidden = false;
      var bits = [(detected.icon || '🌱'), detected.name];
      if (detected.color) bits.push(detected.color);
      if (detected.part) bits.push(String(detected.part).replace(/_/g, ' '));
      detectText.textContent = bits.join(' · ');
    }

    function stopSpeak() {
      speakToken += 1;
      try {
        if (window.speechSynthesis) window.speechSynthesis.cancel();
      } catch (e) {}
      if (audio) {
        try {
          audio.pause();
          audio.removeAttribute('src');
        } catch (e2) {}
      }
      if (speakBtn) speakBtn.classList.remove('is-playing');
    }

    function speakBrowser(text, token) {
      return new Promise(function (resolve) {
        if (!window.speechSynthesis || !text) {
          resolve(false);
          return;
        }
        try {
          window.speechSynthesis.cancel();
          var u = new SpeechSynthesisUtterance(text);
          u.lang = lang === 'en' ? 'en-US' : 'hi-IN';
          u.rate = 0.92;
          var voices = window.speechSynthesis.getVoices() || [];
          var prefer = lang === 'en'
            ? ['en-US', 'en-GB', 'en']
            : ['hi-IN', 'hi', 'ur-PK', 'ur', 'en-IN'];
          for (var p = 0; p < prefer.length; p++) {
            var hit = voices.find(function (v) {
              return (v.lang || '').toLowerCase().indexOf(prefer[p].toLowerCase()) === 0;
            });
            if (hit) { u.voice = hit; break; }
          }
          u.onend = function () { resolve(speakToken === token); };
          u.onerror = function () { resolve(false); };
          window.speechSynthesis.speak(u);
        } catch (e) {
          resolve(false);
        }
      });
    }

    async function speakText(text, auto) {
      text = String(text || '').trim();
      if (!text) return;
      lastSpeak = text;
      if (speakBtn) speakBtn.hidden = false;
      stopSpeak();
      var token = speakToken;
      setStatus(t().speaking);
      if (speakBtn) speakBtn.classList.add('is-playing');

      var played = false;
      if (speakUrl && audio) {
        try {
          var voiceLang = lang === 'en' ? 'en' : 'ur';
          var url = speakUrl + '?lang=' + encodeURIComponent(voiceLang) + '&text=' + encodeURIComponent(text.slice(0, 180));
          var res = await fetch(url, { headers: { Accept: 'audio/mpeg' }, credentials: 'same-origin' });
          if (res.ok && speakToken === token) {
            var blob = await res.blob();
            if (blob && blob.size > 800) {
              var obj = URL.createObjectURL(blob);
              played = await new Promise(function (resolve) {
                var done = function (ok) {
                  URL.revokeObjectURL(obj);
                  resolve(!!ok);
                };
                audio.onended = function () { done(true); };
                audio.onerror = function () { done(false); };
                audio.src = obj;
                var p = audio.play();
                if (p && p.catch) p.catch(function () { done(false); });
              });
            }
          }
        } catch (e) {
          played = false;
        }
      }

      if (!played && speakToken === token) {
        await speakBrowser(text, token);
      }

      if (speakToken === token) {
        if (speakBtn) speakBtn.classList.remove('is-playing');
        setStatus(t().ready);
      }
    }

    function renderAsk(question) {
      askBox.hidden = false;
      resultBox.hidden = true;
      if (speakBtn) speakBtn.hidden = false;
      askPrompt.textContent = question.prompt || '';
      optionsEl.innerHTML = '';
      (question.options || []).forEach(function (opt) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cha-option';
        btn.innerHTML = '<span class="cha-option-icon">' + (opt.icon || '') + '</span><span>' + (opt.label || '') + '</span>';
        btn.addEventListener('click', function () {
          submitAnswer(question.id, opt.id);
        });
        optionsEl.appendChild(btn);
      });
    }

    function renderResult(result) {
      askBox.hidden = true;
      resultBox.hidden = false;
      if (speakBtn) speakBtn.hidden = false;
      lastResultPayload = result;

      var html = '';
      html += '<div class="cha-result-head"><span class="cha-result-icon">' + (result.icon || '🌿') + '</span>';
      html += '<div><p class="cha-advisor-tag">🌿 Green Leaf Advisor</p>';
      html += '<h2>' + escapeHtml(result.crop_name || 'Plant') + '</h2>';
      html += '<p class="cha-problem">⚠️ ' + escapeHtml(result.problem || '') + '</p></div></div>';

      if (result.spread_warning) {
        html += '<div class="cha-alert"><strong>🦠 Phailne ka risk</strong><p>' + escapeHtml(result.spread_warning) + '</p></div>';
      }

      html += '<div class="cha-block"><h3>🔎 Possible causes</h3><ol class="cha-causes">';
      (result.causes || []).forEach(function (c) {
        html += '<li>' + escapeHtml(c) + '</li>';
      });
      html += '</ol></div>';

      if (result.checklist && result.checklist.length) {
        html += '<div class="cha-block"><h3>👀 Abhi kya check karein?</h3><ul class="cha-checks">';
        result.checklist.forEach(function (item) {
          html += '<li><label><input type="checkbox"> <span>' + escapeHtml(item.label) + '</span></label></li>';
        });
        html += '</ul></div>';
      }

      if (result.prevention && result.prevention.length) {
        html += '<div class="cha-block"><h3>🛠️ Kya karein? (step by step)</h3><ul>';
        result.prevention.forEach(function (p) {
          html += '<li>' + escapeHtml(p) + '</li>';
        });
        html += '</ul></div>';
      }

      if (result.avoid && result.avoid.length) {
        html += '<div class="cha-block"><h3>🚫 Kya na karein?</h3><ul>';
        result.avoid.forEach(function (p) {
          html += '<li>' + escapeHtml(p) + '</li>';
        });
        html += '</ul></div>';
      }

      if (result.routine) {
        html += '<div class="cha-block cha-routine"><h3>📅 Daily / weekly routine</h3><ul>';
        if (result.routine.morning) html += '<li><strong>Morning:</strong> ' + escapeHtml(result.routine.morning) + '</li>';
        if (result.routine.evening) html += '<li><strong>Evening:</strong> ' + escapeHtml(result.routine.evening) + '</li>';
        if (result.routine.every_few_days) html += '<li><strong>Every 3–4 days:</strong> ' + escapeHtml(result.routine.every_few_days) + '</li>';
        if (result.routine.weekly) html += '<li><strong>Weekly:</strong> ' + escapeHtml(result.routine.weekly) + '</li>';
        html += '</ul></div>';
      }

      if (result.nutrition) {
        html += '<div class="cha-block"><h3>🧪 Plant nutrition</h3><p>' + escapeHtml(result.nutrition) + '</p></div>';
      }
      if (result.season) {
        html += '<div class="cha-block"><h3>📅 ' + escapeHtml(t().seasonTitle) + '</h3><p>' + escapeHtml(result.season) + '</p></div>';
      }
      if (result.weather) {
        html += '<div class="cha-block"><h3>🌦️ ' + escapeHtml(t().weatherTitle) + '</h3><p>' + escapeHtml(result.weather) + '</p></div>';
      }
      if (result.photo_note) {
        html += '<div class="cha-block cha-photo-note"><p>' + escapeHtml(result.photo_note) + '</p></div>';
      }
      if (result.disclaimer) {
        html += '<p class="cha-disclaimer">' + escapeHtml(result.disclaimer) + '</p>';
      }

      if (result.sticky_offer) {
        html += '<div class="cha-sticky-offer">';
        html += '<p>' + escapeHtml(result.sticky_offer) + '</p>';
        html += '<button type="button" class="cha-sticky-save" id="chaStickySave">📌 Sticky note banao</button>';
        html += '</div>';
      }

      resultBox.innerHTML = html;

      var saveBtn = document.getElementById('chaStickySave');
      if (saveBtn) {
        saveBtn.addEventListener('click', function () {
          submitText(lang === 'en' ? 'yes save sticky note' : 'haan save sticky note');
        });
      }
    }

    function loadStickies() {
      try {
        return JSON.parse(localStorage.getItem(STICKY_KEY) || '[]') || [];
      } catch (e) {
        return [];
      }
    }

    function saveStickies(list) {
      try {
        localStorage.setItem(STICKY_KEY, JSON.stringify(list.slice(0, 12)));
      } catch (e) {}
    }

    function renderStickyBoard() {
      if (!stickyBoard || !stickyList) return;
      var list = loadStickies();
      if (!list.length) {
        stickyBoard.hidden = true;
        stickyList.innerHTML = '';
        return;
      }
      stickyBoard.hidden = false;
      stickyList.innerHTML = '';
      list.forEach(function (note, idx) {
        var card = document.createElement('div');
        card.className = 'cha-sticky-card';
        card.innerHTML = '<pre class="cha-sticky-pre">' + escapeHtml(note.text || '') + '</pre>'
          + '<div class="cha-sticky-actions">'
          + '<button type="button" class="cha-tool" data-copy="' + idx + '">Copy</button>'
          + '<button type="button" class="cha-tool cha-tool-muted" data-del="' + idx + '">Delete</button>'
          + '</div>';
        stickyList.appendChild(card);
      });
      stickyList.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var i = Number(btn.getAttribute('data-copy'));
          var n = loadStickies()[i];
          if (n && n.text && navigator.clipboard) {
            navigator.clipboard.writeText(n.text).then(function () {
              setStatus(lang === 'en' ? 'Sticky note copied.' : 'Sticky note copy ho gaya.');
            }).catch(function () {});
          }
        });
      });
      stickyList.querySelectorAll('[data-del]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var i = Number(btn.getAttribute('data-del'));
          var list2 = loadStickies();
          list2.splice(i, 1);
          saveStickies(list2);
          renderStickyBoard();
        });
      });
    }

    function showSticky(sticky) {
      askBox.hidden = true;
      resultBox.hidden = false;
      var html = '<div class="cha-sticky-live"><pre class="cha-sticky-pre">' + escapeHtml(sticky.text || '') + '</pre>';
      html += '<button type="button" class="cha-sticky-save" id="chaStickyCopyNow">📋 Copy sticky note</button></div>';
      resultBox.innerHTML = html;
      var list = loadStickies();
      list.unshift({
        title: sticky.title || 'Care',
        text: sticky.text || '',
        at: Date.now()
      });
      saveStickies(list);
      renderStickyBoard();
      var copyBtn = document.getElementById('chaStickyCopyNow');
      if (copyBtn) {
        copyBtn.addEventListener('click', function () {
          if (sticky.text && navigator.clipboard) {
            navigator.clipboard.writeText(sticky.text).then(function () {
              setStatus(lang === 'en' ? 'Copied!' : 'Copy ho gaya!');
            }).catch(function () {});
          }
        });
      }
    }

    function escapeHtml(s) {
      return String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    async function handlePayload(data) {
      if (!data) return;
      if (data.heard) showHeard(data.heard);
      showDetected(data.detected || null);

      var speak = data.speak || data.assistant_text || '';
      if (data.status === 'sticky' && data.sticky) {
        showSticky(data.sticky);
        lastSpeak = speak || 'Sticky note ready.';
      } else if (data.status === 'ask' && data.question) {
        renderAsk(data.question);
        lastSpeak = speak || data.question.prompt || '';
      } else if (data.status === 'result' && data.result) {
        renderResult(data.result);
        lastSpeak = speak || data.result.speak || '';
      }

      if (lastSpeak) {
        var auto = lastSpeak.length > 220 ? lastSpeak.slice(0, 200) : lastSpeak;
        await speakText(auto, true);
      } else {
        setStatus(t().ready);
      }
    }

    function detectLangFromText(text) {
      if (/[\u0600-\u06FF]/.test(text)) return 'roman'; // keep roman UI; answers still Roman-friendly
      return lang;
    }

    async function submitText(text) {
      text = String(text || '').trim();
      if (!text || busy) return;
      stopSpeak();
      setBusy(true);
      showHeard(text);
      try {
        var sendLang = detectLangFromText(text);
        var data = await postJson(messageUrl, { text: text, lang: sendLang });
        setBusy(false);
        await handlePayload(data);
      } catch (e) {
        setBusy(false);
        setStatus((e && e.message) ? e.message : t().netFail);
      } finally {
        setListening(false);
      }
    }

    async function submitAnswer(questionId, optionId) {
      if (busy) return;
      stopSpeak();
      setBusy(true);
      try {
        var data = await postJson(answerUrl, {
          question_id: questionId,
          option_id: optionId,
          lang: lang
        });
        setBusy(false);
        await handlePayload(data);
      } catch (e) {
        setBusy(false);
        setStatus((e && e.message) ? e.message : t().netFail);
      }
    }

    function sttLang() {
      // Farmers mostly speak Roman Urdu; ur-PK often fails on desktop Chrome.
      if (lang === 'en') return 'en-US';
      return 'en-IN';
    }

    function initSpeech() {
      var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
      if (!SR) return null;
      var rec = new SR();
      rec.continuous = false;
      rec.interimResults = true;
      rec.maxAlternatives = 3;
      rec.lang = sttLang();
      var finalBits = [];
      var submitTimer = null;

      rec.onstart = function () {
        finalBits = [];
        if (submitTimer) { clearTimeout(submitTimer); submitTimer = null; }
        setListening(true);
      };
      rec.onerror = function (ev) {
        setListening(false);
        var err = (ev && ev.error) || '';
        if (err === 'not-allowed') {
          setStatus(lang === 'en' ? 'Mic permission blocked. Allow mic or type.' : 'Mic ki ijazat band hai. Ijazat dein ya likhein.');
        } else if (err !== 'aborted' && err !== 'no-speech') {
          setStatus(t().noSpeech);
        } else if (err === 'no-speech') {
          setStatus(t().noSpeech);
        }
      };
      rec.onend = function () {
        setListening(false);
        var said = finalBits.join(' ').trim();
        if (said && !busy) {
          submitText(said);
        }
      };
      rec.onresult = function (ev) {
        try {
          for (var i = ev.resultIndex; i < ev.results.length; i++) {
            var res = ev.results[i];
            var piece = (res[0] && res[0].transcript) ? res[0].transcript : '';
            if (!piece) continue;
            if (res.isFinal) {
              finalBits.push(piece);
              showHeard(finalBits.join(' '));
            } else {
              showHeard((finalBits.join(' ') + ' ' + piece).trim());
              setStatus(t().listening);
            }
          }
        } catch (e) {}
      };
      return rec;
    }

    function toggleMic() {
      if (busy) return;
      stopSpeak();
      if (!recognition) recognition = initSpeech();
      if (!recognition) {
        setStatus(t().micFail);
        textInput.focus();
        return;
      }
      if (listening) {
        try { recognition.stop(); } catch (e) {}
        setListening(false);
        return;
      }
      try {
        recognition.lang = sttLang();
        recognition.start();
      } catch (e) {
        recognition = null;
        recognition = initSpeech();
        try {
          if (recognition) {
            recognition.lang = sttLang();
            recognition.start();
          } else {
            setStatus(t().micFail);
          }
        } catch (e2) {
          setStatus(t().micFail);
        }
      }
    }

    micBtn.addEventListener('click', toggleMic);
    sendBtn.addEventListener('click', function () {
      submitText(textInput.value);
      textInput.value = '';
    });
    textInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        submitText(textInput.value);
        textInput.value = '';
      }
    });

    if (speakBtn) {
      speakBtn.addEventListener('click', function () {
        if (lastSpeak) speakText(lastSpeak, false);
      });
      speakBtn.hidden = false;
    }

    resetBtn.addEventListener('click', async function () {
      stopSpeak();
      try { await postJson(resetUrl, {}); } catch (e) {}
      askBox.hidden = true;
      resultBox.hidden = true;
      heardBox.hidden = true;
      if (detectBox) detectBox.hidden = true;
      lastSpeak = '';
      optionsEl.innerHTML = '';
      resultBox.innerHTML = '';
      setStatus(t().micHint);
    });

    if (photoInput) {
      photoInput.addEventListener('change', async function () {
        if (!photoInput.files || !photoInput.files[0]) return;
        var fd = new FormData();
        fd.append('photo', photoInput.files[0]);
        fd.append('lang', lang);
        try {
          setBusy(true);
          var res = await fetch(photoUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: fd
          });
          var data = await res.json();
          setStatus(data.note || t().ready);
        } catch (e) {
          setStatus(lang === 'en' ? 'Photo upload failed.' : 'تصویر نہیں گئی۔');
        } finally {
          setBusy(false);
          photoInput.value = '';
        }
      });
    }

    root.querySelectorAll('.cha-example').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var ex = btn.getAttribute('data-example') || '';
        if (ex) submitText(ex);
      });
    });

    root.querySelectorAll('.cha-lang-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        lang = btn.getAttribute('data-lang') || 'roman';
        localStorage.setItem('cha-lang', lang);
        recognition = null;
        applyLang();
      });
    });

    if (window.speechSynthesis) {
      try { window.speechSynthesis.getVoices(); } catch (e) {}
      window.speechSynthesis.onvoiceschanged = function () {
        try { window.speechSynthesis.getVoices(); } catch (e2) {}
      };
    }

    applyLang();
    renderStickyBoard();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
