<div class="chatbot-widget" x-data="chatbot()" x-cloak>
    <div class="chatbot-panel" x-show="open" x-transition>
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-success text-white">
            <strong><i class="bi bi-chat-dots"></i> MarketLink Help</strong>
            <button class="btn btn-sm btn-light" @click="open=false">&times;</button>
        </div>
        <div class="chatbot-messages" x-ref="messages">
            <div class="chat-bubble bot">Hi! Ask me about pickup, orders, farmers, or payment.</div>
            <template x-for="(m, i) in messages" :key="i">
                <div class="chat-bubble" :class="m.role">
                    <span x-text="m.text"></span>
                </div>
            </template>
            <div class="mt-2" x-show="suggestions.length">
                <template x-for="s in suggestions" :key="s">
                    <button type="button" class="btn btn-sm btn-outline-success me-1 mb-1" @click="ask(s)" x-text="s"></button>
                </template>
            </div>
        </div>
        <form class="p-2 border-top d-flex gap-1" @submit.prevent="ask(input); input=''">
            <input class="form-control form-control-sm" x-model="input" placeholder="Type a question...">
            <button class="btn btn-sm btn-success" type="submit">Send</button>
        </form>
    </div>
    <button class="btn btn-success rounded-circle shadow" style="width:56px;height:56px" @click="toggle()" title="Help chatbot">
        <i class="bi bi-robot fs-5"></i>
    </button>
</div>
