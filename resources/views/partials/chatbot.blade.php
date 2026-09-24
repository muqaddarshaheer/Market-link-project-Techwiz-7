<div class="chat-widget" x-data="chatBot()" x-cloak>
    <div class="chat-panel mb-2" x-show="open">
        <div class="p-3 border-bottom d-flex justify-content-between">
            <strong>Market helper</strong>
            <button class="btn btn-sm" @click="open=false" aria-label="Close chat">&times;</button>
        </div>
        <div class="chat-log" x-ref="log">
            <div class="bubble">Ask about market hours, pickup, or how pre-orders work.</div>
            <template x-for="(row, i) in history" :key="i">
                <div>
                    <div class="bubble me" x-text="row.q"></div>
                    <div class="bubble" x-text="row.a"></div>
                </div>
            </template>
            <div class="d-flex flex-wrap gap-1">
                <template x-for="s in suggestions" :key="s">
                    <button class="btn btn-outline-ml btn-sm" type="button" @click="message=s; send()" x-text="s"></button>
                </template>
            </div>
        </div>
        <form class="p-2 d-flex gap-2" @submit.prevent="send()">
            <input class="form-control" x-model="message" placeholder="Type a question" required>
            <button class="btn btn-ml" type="submit">Send</button>
        </form>
    </div>
    <button class="btn btn-ml rounded-circle" style="width:56px;height:56px" @click="open=!open" aria-label="Open chat">
        <i class="bi bi-chat-dots"></i>
    </button>
</div>
<script>
    function chatBot() {
        return {
            open: false, message: '', history: [], suggestions: ['What are market hours?', 'How do I pre-order?', 'When is pickup?'],
            async send() {
                if (!this.message) return;
                const q = this.message;
                this.message = '';
                const res = await fetch('{{ route('chatbot.ask') }}', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: JSON.stringify({message: q})
                });
                const data = await res.json();
                this.history.push({q, a: data.answer});
                this.suggestions = data.suggestions || this.suggestions;
            }
        };
    }
</script>
