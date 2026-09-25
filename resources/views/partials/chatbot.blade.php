<div class="chat-widget" x-data="chatBot()" x-cloak>
    <div class="chat-panel mb-2" x-show="open" x-transition.opacity.duration.120ms>
        <div class="p-3 border-bottom d-flex justify-content-between align-items-start gap-2">
            <div>
                <strong>MarketLink AI</strong>
                <div class="small muted" x-text="lang === 'ur' ? 'صرف مارکیٹ، کسان، پیداوار، پک اپ' : 'Markets, farmers, produce, pickup & Rs only'"></div>
                <div class="chat-lang mt-2" role="group" aria-label="Chat language">
                    <button type="button" class="chat-lang-btn" :class="{ 'is-on': lang === 'en' }" @click="setLang('en')">EN</button>
                    <button type="button" class="chat-lang-btn" :class="{ 'is-on': lang === 'ur' }" @click="setLang('ur')">اردو</button>
                </div>
            </div>
            <button class="btn btn-sm" @click="open=false" aria-label="Close chat">&times;</button>
        </div>
        <div class="chat-log" x-ref="log" :dir="lang === 'ur' ? 'rtl' : 'ltr'">
            <div class="bubble" x-text="welcome"></div>
            <template x-for="(row, i) in history" :key="i">
                <div>
                    <div class="bubble me" x-text="row.q"></div>
                    <div class="bubble" x-text="row.a"></div>
                </div>
            </template>
            <div class="small muted" x-show="busy" x-text="lang === 'ur' ? 'سوچ رہا ہوں…' : 'Thinking…'"></div>
            <div class="d-flex flex-wrap gap-1">
                <template x-for="s in suggestions" :key="s">
                    <button class="btn btn-outline-ml btn-sm" type="button" @click="message=s; send()" x-text="s"></button>
                </template>
            </div>
        </div>
        <form class="p-2 d-flex gap-2" @submit.prevent="send()">
            <input class="form-control" x-model="message" :placeholder="lang === 'ur' ? 'MarketLink کے بارے میں پوچھیں…' : 'Ask about MarketLink…'" required :disabled="busy" maxlength="500" :dir="lang === 'ur' ? 'rtl' : 'ltr'">
            <button class="btn btn-ml" type="submit" :disabled="busy" x-text="lang === 'ur' ? 'بھیجیں' : 'Send'"></button>
        </form>
    </div>
    <button class="btn btn-ml rounded-circle chat-fab" style="width:56px;height:56px" @click="open=!open" aria-label="Open chat">
        <i class="bi bi-chat-dots"></i>
    </button>
</div>
<script>
    function chatBot() {
        const copy = {
            en: {
                welcome: 'Ask about MarketLink markets, stalls, produce, or pickup — I stay on that only.',
                suggestions: ['How does pickup work?', 'What costs Rs today?', 'Which farmers are open?', 'How do I create an account?'],
            },
            ur: {
                welcome: 'MarketLink کے بارے میں پوچھیں — مارکیٹ، کسان، پیداوار یا پک اپ۔ صرف یہی مدد ملتی ہے۔',
                suggestions: ['پک اپ کیسے ہوتا ہے؟', 'آج قیمتیں کیا ہیں؟', 'کون سے کسان کھلے ہیں؟', 'اکاؤنٹ کیسے بنائیں؟'],
            },
        };
        return {
            open: false, busy: false, message: '', history: [],
            lang: localStorage.getItem('ml-chat-lang') || 'en',
            get welcome() { return copy[this.lang].welcome; },
            get suggestions() { return copy[this.lang].suggestions; },
            setLang(next) {
                this.lang = next;
                localStorage.setItem('ml-chat-lang', next);
            },
            async send() {
                if (!this.message || this.busy) return;
                const q = this.message;
                this.message = '';
                this.busy = true;
                try {
                    const res = await fetch('{{ route('chatbot.ask') }}', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                        body: JSON.stringify({message: q, lang: this.lang})
                    });
                    const data = await res.json();
                    this.history.push({q, a: data.answer || (this.lang === 'ur' ? 'کچھ غلط ہو گیا۔ دوبارہ کوشش کریں۔' : 'Something went wrong. Please try again.')});
                    if (data.suggestions?.length) {
                        copy[this.lang].suggestions = data.suggestions;
                    }
                } catch (e) {
                    this.history.push({q, a: this.lang === 'ur' ? 'مددگار سے رابطہ نہیں ہو سکا۔' : 'Could not reach the helper. Try again.'});
                }
                this.busy = false;
                this.$nextTick(() => { if (this.$refs.log) this.$refs.log.scrollTop = this.$refs.log.scrollHeight; });
            }
        };
    }
</script>
