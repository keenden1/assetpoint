{{-- Floating gear button + Settings offcanvas (dark mode & cursor trail) + trail canvas.
     Included by layouts/app so it is available on every page. --}}

{{-- Floating gear button toggles the settings offcanvas. --}}
<button type="button" class="btn btn-dark rounded-circle shadow d-flex align-items-center justify-content-center"
    style="position:fixed; right:1.25rem; bottom:1.25rem; width:52px; height:52px; z-index:1032;"
    data-bs-toggle="offcanvas" data-bs-target="#ctSettings" aria-controls="ctSettings" title="Settings">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
    </svg>
</button>

{{-- ===================== Settings (offcanvas) ===================== --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="ctSettings" aria-labelledby="ctSettingsLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="ctSettingsLabel">Settings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <h6 class="text-secondary text-uppercase small mb-2">Appearance</h6>
        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="ct-darkmode"
                data-theme-switch onchange="setTheme(this.checked ? 'dark' : 'light')">
            <label class="form-check-label" for="ct-darkmode">Dark mode</label>
        </div>

        <hr class="my-3">

        <h6 class="text-secondary text-uppercase small mb-2">Cursor Trail</h6>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="ct-enabled">
            <label class="form-check-label" for="ct-enabled">Enabled</label>
        </div>

        {{-- Shown only when the trail is enabled. --}}
        <div id="ct-controls">
        <div class="mb-3">
            <label for="ct-style" class="form-label small mb-1">Style</label>
            <select id="ct-style" class="form-select form-select-sm">
                <option value="fire">Fire (yellow → red)</option>
                <option value="solid">Solid color</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="ct-color" class="form-label small mb-1">Color <span class="text-secondary">(solid style)</span></label>
            <input type="color" id="ct-color" class="form-control form-control-color form-control-sm" value="#ff6a00">
        </div>

        <div class="mb-3">
            <label for="ct-size" class="form-label small mb-1">Size <span id="ct-size-val" class="text-secondary"></span></label>
            <input type="range" id="ct-size" class="form-range" min="2" max="24" step="1">
        </div>

        <div class="mb-3">
            <label for="ct-density" class="form-label small mb-1">Density <span id="ct-density-val" class="text-secondary"></span></label>
            <input type="range" id="ct-density" class="form-range" min="1" max="12" step="1">
        </div>

        <div class="mb-3">
            <label for="ct-trail" class="form-label small mb-1">Trail length <span id="ct-trail-val" class="text-secondary"></span></label>
            <input type="range" id="ct-trail" class="form-range" min="1" max="100" step="1">
        </div>

        <div class="mb-3">
            <label for="ct-rise" class="form-label small mb-1">Rise speed <span id="ct-rise-val" class="text-secondary"></span></label>
            <input type="range" id="ct-rise" class="form-range" min="0" max="40" step="1">
        </div>

        <div class="mb-3">
            <label for="ct-lag" class="form-label small mb-1">Follow delay <span id="ct-lag-val" class="text-secondary"></span></label>
            <input type="range" id="ct-lag" class="form-range" min="0" max="90" step="1">
        </div>

        <div class="d-flex gap-2 pt-2 border-top">
            <button type="button" id="ct-normal" class="btn btn-sm btn-dark">Normal cursor</button>
            <button type="button" id="ct-reset" class="btn btn-sm btn-outline-secondary">Reset to defaults</button>
        </div>
        </div>{{-- /#ct-controls --}}
    </div>
</div>

{{-- Trail canvas overlay (pointer-events:none; below the offcanvas so the panel sits on top). --}}
<canvas id="cursorTrail" aria-hidden="true"
    style="position:fixed; inset:0; width:100vw; height:100vh; pointer-events:none; z-index:1030;"></canvas>

<script>
    (function () {
        const STORAGE_KEY = 'cursorTrailConfig';
        const DEFAULTS = {
            enabled: true,
            style: 'fire',     // 'fire' | 'solid'
            color: '#ff6a00',  // used when style === 'solid'
            size: 8,           // base particle radius (px)
            density: 3,        // particles emitted per frame
            trail: 55,         // 1 (short) .. 100 (long) -> maps to decay
            rise: 16,          // 0..40 -> upward speed (val/10)
            lag: 0,            // 0..90 -> pointer smoothing (%)
        };

        // ---- config load / save -------------------------------------------------
        let cfg = Object.assign({}, DEFAULTS);
        try {
            const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
            cfg = Object.assign(cfg, saved);
        } catch (e) { /* ignore corrupt storage */ }

        function save() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cfg));
        }

        // ---- derived helpers ----------------------------------------------------
        const decayFromTrail = t => 0.05 - (t / 100) * 0.046;   // longer trail -> smaller decay
        const riseFromCfg = () => cfg.rise / 10;                 // 0..4
        const easeFromLag = () => 1 - (cfg.lag / 100);           // lag 0 -> instant, 90 -> laggy

        function hexToRgb(hex) {
            const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return m ? { r: parseInt(m[1], 16), g: parseInt(m[2], 16), b: parseInt(m[3], 16) }
                     : { r: 255, g: 106, b: 0 };
        }

        // ---- canvas + engine ----------------------------------------------------
        const canvas = document.getElementById('cursorTrail');
        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;
        let w, h;

        function resize() {
            w = window.innerWidth; h = window.innerHeight;
            canvas.width = w * dpr; canvas.height = h * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }
        resize();
        window.addEventListener('resize', resize);

        const particles = [];
        const target = { x: w / 2, y: h / 2 };
        const smooth = { x: w / 2, y: h / 2 };
        let pointerSeen = false;

        window.addEventListener('mousemove', function (e) {
            target.x = e.clientX; target.y = e.clientY; pointerSeen = true;
        });

        function emit(x, y) {
            const decay = decayFromTrail(cfg.trail);
            const rise = riseFromCfg();
            for (let i = 0; i < cfg.density; i++) {
                particles.push({
                    x: x + (Math.random() - 0.5) * 10,
                    y: y + (Math.random() - 0.5) * 10,
                    vx: (Math.random() - 0.5) * 0.7,
                    vy: -(rise * (0.6 + Math.random() * 0.8)),
                    life: 1,
                    decay: decay * (0.8 + Math.random() * 0.4),
                    size: cfg.size * (0.7 + Math.random() * 0.6),
                });
            }
        }

        function frame() {
            ctx.clearRect(0, 0, w, h);

            if (cfg.enabled) {
                const ease = easeFromLag();
                smooth.x += (target.x - smooth.x) * ease;
                smooth.y += (target.y - smooth.y) * ease;
                if (pointerSeen) emit(smooth.x, smooth.y);

                ctx.globalCompositeOperation = 'lighter';
                const rgb = hexToRgb(cfg.color);

                for (let i = particles.length - 1; i >= 0; i--) {
                    const p = particles[i];
                    p.x += p.vx; p.y += p.vy; p.vy -= 0.02; p.vx *= 0.99;
                    p.life -= p.decay;
                    if (p.life <= 0) { particles.splice(i, 1); continue; }

                    const r = p.size * p.life;
                    let c0, c1;
                    if (cfg.style === 'fire') {
                        const hue = 60 * p.life, light = 45 + 35 * p.life, a = 0.85 * p.life;
                        c0 = `hsla(${hue},100%,${light}%,${a})`;
                        c1 = `hsla(${hue},100%,${light}%,0)`;
                    } else {
                        const a = 0.85 * p.life;
                        c0 = `rgba(${rgb.r},${rgb.g},${rgb.b},${a})`;
                        c1 = `rgba(${rgb.r},${rgb.g},${rgb.b},0)`;
                    }
                    const grad = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, r);
                    grad.addColorStop(0, c0);
                    grad.addColorStop(1, c1);
                    ctx.fillStyle = grad;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, r, 0, Math.PI * 2);
                    ctx.fill();
                }
                ctx.globalCompositeOperation = 'source-over';
            } else {
                particles.length = 0;
            }

            requestAnimationFrame(frame);
        }
        frame();

        // ---- control panel wiring ----------------------------------------------
        const el = id => document.getElementById(id);
        const controls = {
            enabled: el('ct-enabled'), style: el('ct-style'), color: el('ct-color'),
            size: el('ct-size'), density: el('ct-density'), trail: el('ct-trail'),
            rise: el('ct-rise'), lag: el('ct-lag'),
        };

        function syncInputsFromCfg() {
            controls.enabled.checked = cfg.enabled;
            controls.style.value = cfg.style;
            controls.color.value = cfg.color;
            controls.size.value = cfg.size;
            controls.density.value = cfg.density;
            controls.trail.value = cfg.trail;
            controls.rise.value = cfg.rise;
            controls.lag.value = cfg.lag;
            el('ct-size-val').textContent = cfg.size + 'px';
            el('ct-density-val').textContent = cfg.density;
            el('ct-trail-val').textContent = cfg.trail + '%';
            el('ct-rise-val').textContent = (cfg.rise / 10).toFixed(1);
            el('ct-lag-val').textContent = cfg.lag + '%';
            el('ct-controls').classList.toggle('d-none', !cfg.enabled);
        }

        controls.enabled.addEventListener('change', e => {
            cfg.enabled = e.target.checked;
            el('ct-controls').classList.toggle('d-none', !cfg.enabled);
            save();
        });
        controls.style.addEventListener('change', e => { cfg.style = e.target.value; save(); });
        controls.color.addEventListener('input', e => { cfg.color = e.target.value; save(); });
        ['size', 'density', 'trail', 'rise', 'lag'].forEach(key => {
            controls[key].addEventListener('input', e => {
                cfg[key] = parseInt(e.target.value, 10);
                syncInputsFromCfg();
                save();
            });
        });
        el('ct-reset').addEventListener('click', () => {
            cfg = Object.assign({}, DEFAULTS);
            syncInputsFromCfg();
            save();
        });

        // Turn the trail off -> back to a plain, normal cursor.
        el('ct-normal').addEventListener('click', () => {
            cfg.enabled = false;
            particles.length = 0;
            syncInputsFromCfg();
            save();
        });

        syncInputsFromCfg();
    })();
</script>
