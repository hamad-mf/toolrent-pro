{{-- Light / Dark / Auto theme toggle. Requires Alpine.js (loaded in layout). --}}
<div x-data="themeToggle()" x-init="init()" class="d-inline-flex">
    <button type="button"
            class="btn btn-sm border-0 d-inline-flex align-items-center justify-content-center"
            style="width:38px;height:38px;border-radius:50%;background:var(--tr-surface-2);color:var(--tr-text-muted);"
            @click="cycle()"
            :title="'Theme: ' + mode"
            :aria-label="'Switch theme, current mode ' + mode">
        <i class="bi" :class="icon" style="font-size:1.05rem;"></i>
    </button>
</div>

<script>
    function themeToggle() {
        return {
            mode: localStorage.getItem('tr-theme') || 'auto',
            get icon() {
                return this.mode === 'dark'  ? 'bi-moon-stars-fill'
                     : this.mode === 'light' ? 'bi-sun-fill'
                     : 'bi-circle-half';
            },
            apply() {
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = this.mode === 'auto' ? (systemDark ? 'dark' : 'light') : this.mode;
                document.documentElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('tr-theme', this.mode);
                document.documentElement.dispatchEvent(new CustomEvent('tr-theme-changed'));
            },
            cycle() {
                this.mode = this.mode === 'light' ? 'dark' : this.mode === 'dark' ? 'auto' : 'light';
                this.apply();
            },
            init() {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    if (this.mode === 'auto') this.apply();
                });
            },
        };
    }
</script>
