{{-- Apply the saved Bootstrap theme as early as possible to avoid a flash of the wrong theme. --}}
<script>
    (function () {
        try {
            document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('theme') || 'light');
        } catch (e) { /* storage blocked */ }
    })();
</script>
