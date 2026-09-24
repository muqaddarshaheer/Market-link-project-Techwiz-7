/* MarketLink front-end behaviours */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    if (toggle) {
        toggle.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('ml-theme', next);
        });
    }

    // Poll notifications every 30s for a real-time feel
    if (document.getElementById('notifBell')) {
        setInterval(async () => {
            try {
                const res = await fetch('/notifications/poll', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const data = await res.json();
                let badge = document.getElementById('notifCount');
                if (data.count > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.id = 'notifCount';
                        badge.className = 'badge rounded-pill bg-danger notif-badge';
                        document.getElementById('notifBell').appendChild(badge);
                    }
                    badge.textContent = data.count;
                } else if (badge) {
                    badge.remove();
                }
            } catch (e) { /* ignore */ }
        }, 30000);
    }
});

function searchAutocomplete() {
    return {
        q: '',
        results: [],
        async fetchResults() {
            if (this.q.length < 2) { this.results = []; return; }
            const res = await fetch(`/products/autocomplete?q=${encodeURIComponent(this.q)}`);
            this.results = await res.json();
        }
    };
}

function chatbot() {
    return {
        open: false,
        input: '',
        messages: [],
        suggestions: [],
        async toggle() {
            this.open = !this.open;
            if (this.open && !this.suggestions.length) {
                try {
                    const res = await fetch('/chatbot/suggestions');
                    const data = await res.json();
                    this.suggestions = data.suggestions || [];
                } catch (e) {}
            }
        },
        async ask(text) {
            if (!text || !text.trim()) return;
            this.messages.push({ role: 'user', text });
            this.suggestions = [];
            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const res = await fetch('/chatbot/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: text })
                });
                const data = await res.json();
                this.messages.push({ role: 'bot', text: data.answer });
            } catch (e) {
                this.messages.push({ role: 'bot', text: 'Something went wrong. Please try again.' });
            }
            this.$nextTick(() => {
                const el = this.$refs.messages;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    };
}

function initMap(elId, markers, zoom = 12) {
    const el = document.getElementById(elId);
    if (!el || typeof L === 'undefined') return null;
    const map = L.map(elId);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const bounds = [];
    markers.forEach(m => {
        if (m.lat == null || m.lng == null) return;
        const marker = L.marker([m.lat, m.lng]).addTo(map);
        if (m.popup) marker.bindPopup(m.popup);
        bounds.push([m.lat, m.lng]);
    });

    if (bounds.length === 1) {
        map.setView(bounds[0], zoom);
    } else if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [40, 40] });
    } else {
        map.setView([40.7128, -74.006], 11);
    }
    return map;
}
