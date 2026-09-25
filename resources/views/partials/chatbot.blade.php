<div class="chat-widget" x-data="chatBot()" x-cloak>
    <div class="chat-panel mb-2" x-show="open" x-transition.opacity.duration.120ms>
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <strong>MarketLink AI</strong>
                <div class="small muted">Ask anything — produce, pickup, cooking, or general help</div>
            </div>
            <button class="btn btn-sm" @click="open=false" aria-label="Close chat">&times;</button>
        </div>
        <div class="chat-log" x-ref="log">
            <div class="bubble">Hi — ask about markets, Rs prices, farmers, pickup, or anything else.</div>
            <template x-for="(row, i) in history" :key="i">
                <div>
                    <div class="bubble me" x-text="row.q"></div>
                    <div class="bubble" x-text="row.a"></div>
                </div>
            </template>
            <div class="small muted" x-show="busy" x-text="'Thinking…'"></div>
            <div class="d-flex flex-wrap gap-1">
                <template x-for="s in suggestions" :key="s">
                    <button class="btn btn-outline-ml btn-sm" type="button" @click="message=s; send()" x-text="s"></button>
                </template>
            </div>
        </div>
        <form class="p-2 d-flex gap-2" @submit.prevent="send()">
            <input class="form-control" x-model="message" placeholder="Ask anything…" required :disabled="busy" maxlength="800">
            <button class="btn btn-ml" type="submit" :disabled="busy">Send</button>
        </form>
    </div>
    <button class="btn btn-ml rounded-circle chat-fab" style="width:56px;height:56px" @click="open=!open" aria-label="Open chat">
        <i class="bi bi-chat-dots"></i>
    </button>
</div>
<script>
    function chatBot() {
        return {
            open: false, busy: false, message: '', history: [],
            suggestions: ['How does pickup work?', 'What costs Rs today?', 'Which farmers are open?', 'Cooking tip for tomatoes'],
            async send() {
                if (!this.message || this.busy) return;
                const q = this.message;
                this.message = '';
                this.busy = true;
                try {
                    const res = await fetch('{{ route('chatbot.ask') }}', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                        body: JSON.stringify({message: q})
                    });
                    const data = await res.json();
                    this.history.push({q, a: data.answer || 'Something went wrong. Please try again.'});
                    this.suggestions = data.suggestions || this.suggestions;
                } catch (e) {
                    this.history.push({q, a: 'Could not reach the helper. Try again.'});
                }
                this.busy = false;
                this.$nextTick(() => { if (this.$refs.log) this.$refs.log.scrollTop = this.$refs.log.scrollHeight; });
            }
        };
    }
</script>
