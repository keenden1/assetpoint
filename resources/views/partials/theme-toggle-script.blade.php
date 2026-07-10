{{-- Theme apply/toggle logic. Drives icon buttons ([data-theme-icon]) and switches ([data-theme-switch]). --}}
<script>
    (function () {
        function apply(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            try { localStorage.setItem('theme', theme); } catch (e) { /* storage blocked */ }

            document.querySelectorAll('[data-theme-icon]').forEach(function (btn) {
                var light = btn.querySelector('.theme-icon-light');
                var dark = btn.querySelector('.theme-icon-dark');
                if (light) light.classList.toggle('d-none', theme === 'dark');
                if (dark) dark.classList.toggle('d-none', theme !== 'dark');
            });

            document.querySelectorAll('[data-theme-switch]').forEach(function (sw) {
                sw.checked = theme === 'dark';
            });
        }

        window.setTheme = function (theme) { apply(theme === 'dark' ? 'dark' : 'light'); };

        window.toggleTheme = function () {
            var current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
            apply(current === 'dark' ? 'light' : 'dark');
        };

        document.addEventListener('DOMContentLoaded', function () {
            apply(document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light');
        });
    })();
</script>
