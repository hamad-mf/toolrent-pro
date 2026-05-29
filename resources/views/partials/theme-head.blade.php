{{-- Anti-FOUC: apply stored theme before paint. MUST be the first script in <head>. --}}
<script>
    (function () {
        try {
            var stored = localStorage.getItem('tr-theme') || 'auto';
            var systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = stored === 'auto' ? (systemDark ? 'dark' : 'light') : stored;
            document.documentElement.setAttribute('data-bs-theme', theme);
        } catch (e) {
            document.documentElement.setAttribute('data-bs-theme', 'light');
        }
    })();
</script>

{{-- Tenant accent injection. Overrides the default --tr-primary in both light and dark modes. --}}
@php
    $accent = session('tenant_primary_color', '#4f46e5');
    $parsed = sscanf($accent, "#%02x%02x%02x");
    [$r, $g, $b] = (is_array($parsed) && count($parsed) === 3 && $parsed[0] !== null)
        ? $parsed
        : [79, 70, 229];
    $secondary = ($tenant->secondary_color ?? '#6c757d');
    $secondaryParsed = sscanf($secondary, "#%02x%02x%02x");
    [$sr, $sg, $sb] = (is_array($secondaryParsed) && count($secondaryParsed) === 3 && $secondaryParsed[0] !== null)
        ? $secondaryParsed
        : [108, 117, 125];
@endphp
<style>
    :root,
    [data-bs-theme="light"],
    [data-bs-theme="dark"] {
        --tr-primary: {{ $accent }};
        --tr-primary-rgb: {{ $r }}, {{ $g }}, {{ $b }};
        --bs-primary: {{ $accent }};
        --bs-primary-rgb: {{ $r }}, {{ $g }}, {{ $b }};
        --bs-secondary: {{ $secondary }};
        --bs-secondary-rgb: {{ $sr }}, {{ $sg }}, {{ $sb }};
        --bs-link-color: {{ $accent }};
    }
</style>
