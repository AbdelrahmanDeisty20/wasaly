<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>جاري فتح التطبيق...</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap');

:root {
  --blue-darkest: #020B18;
  --blue-deep:    #061D3A;
  --blue-mid:     #0A3A6E;
  --blue-core:    #1565C0;
  --blue-bright:  #1E88E5;
  --blue-light:   #42A5F5;
  --blue-pale:    #90CAF9;
  --blue-glow:    rgba(30, 136, 229, 0.45);
  --white:        #EEF4FF;
  --muted:        rgba(238, 244, 255, 0.55);
}

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

body {
  font-family: 'Cairo', sans-serif;
  background: var(--blue-darkest);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  position: relative;
}

/* ── layered background ── */
.bg-mesh {
  position: fixed; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 20% 10%,  rgba(21,101,192,0.55) 0%, transparent 60%),
    radial-gradient(ellipse 60% 80% at 85% 80%,  rgba(6,29,58,0.9)     0%, transparent 65%),
    radial-gradient(ellipse 50% 50% at 55% 45%,  rgba(10,58,110,0.4)   0%, transparent 70%),
    linear-gradient(160deg, #020B18 0%, #061D3A 50%, #020B18 100%);
}

.bg-grid {
  position: fixed; inset: 0; z-index: 0;
  background-image:
    linear-gradient(rgba(30,136,229,0.07) 1px, transparent 1px),
    linear-gradient(90deg, rgba(30,136,229,0.07) 1px, transparent 1px);
  background-size: 44px 44px;
  mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
}

/* floating orbs */
.orb {
  position: fixed; border-radius: 50%; filter: blur(80px); z-index: 0; opacity: 0.55;
}
.orb-1 {
  width: 420px; height: 420px;
  background: radial-gradient(circle, #1565C0, transparent 70%);
  top: -100px; left: -80px;
  animation: drift1 12s ease-in-out infinite alternate;
}
.orb-2 {
  width: 320px; height: 320px;
  background: radial-gradient(circle, #1E88E5, transparent 70%);
  bottom: -80px; right: -60px;
  animation: drift2 15s ease-in-out infinite alternate;
}
.orb-3 {
  width: 200px; height: 200px;
  background: radial-gradient(circle, #42A5F5, transparent 70%);
  top: 50%; left: 55%;
  opacity: 0.25;
  animation: drift3 9s ease-in-out infinite alternate;
}
@keyframes drift1 { to { transform: translate(40px, 50px) scale(1.1); } }
@keyframes drift2 { to { transform: translate(-30px, -40px) scale(1.08); } }
@keyframes drift3 { to { transform: translate(-20px, 20px) scale(0.9); } }

/* ── card ── */
.card {
  position: relative; z-index: 10;
  width: min(400px, 92vw);
  background: linear-gradient(145deg, rgba(10,58,110,0.45), rgba(6,20,46,0.7));
  border: 1px solid rgba(66,165,245,0.2);
  border-radius: 28px;
  padding: 48px 36px 40px;
  text-align: center;
  box-shadow:
    0 0 0 1px rgba(30,136,229,0.08),
    0 8px 32px rgba(2,11,24,0.6),
    0 0 80px rgba(21,101,192,0.15);
  backdrop-filter: blur(18px);
  animation: cardIn 0.7s cubic-bezier(0.22,1,0.36,1) both;
}
@keyframes cardIn {
  from { opacity:0; transform: translateY(32px) scale(0.96); }
  to   { opacity:1; transform: translateY(0)    scale(1); }
}

/* top badge */
.badge {
  display: inline-flex; align-items: center; gap: 7px;
  background: rgba(30,136,229,0.12);
  border: 1px solid rgba(66,165,245,0.25);
  border-radius: 50px;
  padding: 6px 16px;
  font-size: 12px;
  color: var(--blue-pale);
  letter-spacing: 0.05em;
  margin-bottom: 32px;
  animation: fadeUp 0.6s 0.15s both;
}
.badge-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: var(--blue-light);
  box-shadow: 0 0 8px var(--blue-light);
  animation: pulse-dot 1.8s ease-in-out infinite;
}
@keyframes pulse-dot {
  0%,100% { opacity:1; transform:scale(1); }
  50%      { opacity:0.5; transform:scale(0.7); }
}

/* ── ring spinner ── */
.spinner-wrap {
  position: relative;
  width: 88px; height: 88px;
  margin: 0 auto 32px;
  animation: fadeUp 0.6s 0.25s both;
}
.ring {
  position: absolute; inset: 0;
  border-radius: 50%;
  border: 3px solid transparent;
}
.ring-1 {
  border-top-color: var(--blue-bright);
  border-right-color: rgba(30,136,229,0.3);
  animation: spin 1.1s linear infinite;
}
.ring-2 {
  inset: 10px;
  border-bottom-color: var(--blue-light);
  border-left-color: rgba(66,165,245,0.25);
  animation: spin 0.75s linear infinite reverse;
}
.ring-3 {
  inset: 22px;
  border-top-color: var(--blue-pale);
  border-right-color: rgba(144,202,249,0.2);
  animation: spin 1.5s linear infinite;
}
.ring-core {
  position: absolute;
  inset: 33px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(30,136,229,0.5), rgba(21,101,192,0.2));
  box-shadow: 0 0 18px rgba(30,136,229,0.6);
  animation: glow-core 2s ease-in-out infinite alternate;
}
@keyframes spin        { to { transform: rotate(360deg); } }
@keyframes glow-core   {
  from { box-shadow: 0 0 12px rgba(30,136,229,0.5); }
  to   { box-shadow: 0 0 28px rgba(66,165,245,0.85); }
}

/* ── text ── */
h1 {
  font-size: 26px; font-weight: 700;
  color: var(--white);
  line-height: 1.3;
  margin-bottom: 12px;
  animation: fadeUp 0.6s 0.35s both;
}
.subtitle {
  font-size: 15px; font-weight: 400;
  color: var(--muted);
  line-height: 1.7;
  margin-bottom: 36px;
  animation: fadeUp 0.6s 0.45s both;
}

/* ── progress bar ── */
.progress-wrap {
  width: 100%;
  height: 4px;
  background: rgba(30,136,229,0.15);
  border-radius: 99px;
  overflow: hidden;
  margin-bottom: 36px;
  animation: fadeUp 0.6s 0.5s both;
}
.progress-bar {
  height: 100%;
  border-radius: 99px;
  background: linear-gradient(90deg, var(--blue-mid), var(--blue-bright), var(--blue-pale));
  background-size: 200% 100%;
  animation: fill 2.5s cubic-bezier(0.4,0,0.2,1) forwards, shimmer 1.5s linear infinite;
}
@keyframes fill    { from { width:0%; } to { width:100%; } }
@keyframes shimmer { to   { background-position: -200% 0; } }

/* ── manual button ── */
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 10px;
  background: linear-gradient(135deg, var(--blue-core), var(--blue-bright));
  color: var(--white);
  padding: 15px 32px;
  border-radius: 50px;
  text-decoration: none;
  font-family: 'Cairo', sans-serif;
  font-size: 15px; font-weight: 700;
  letter-spacing: 0.02em;
  border: 1px solid rgba(66,165,245,0.35);
  box-shadow: 0 4px 24px rgba(21,101,192,0.45), 0 0 0 0 var(--blue-glow);
  transition: transform 0.2s, box-shadow 0.2s;
  animation: fadeUp 0.6s 0.6s both;
  cursor: pointer;
}
.btn:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 8px 36px rgba(30,136,229,0.6), 0 0 0 6px rgba(30,136,229,0.12);
}
.btn-icon { font-size: 18px; }
.btn.hidden { display: none; }

/* ── bottom hint ── */
.hint {
  margin-top: 20px;
  font-size: 12px;
  color: rgba(144,202,249,0.4);
  animation: fadeUp 0.6s 0.65s both;
}

@keyframes fadeUp {
  from { opacity:0; transform: translateY(14px); }
  to   { opacity:1; transform: translateY(0); }
}
</style>
</head>
<body>

<div class="bg-mesh"></div>
<div class="bg-grid"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="card">

  <div class="badge">
    <span class="badge-dot"></span>
    تطبيق وصّلي
  </div>

  <div class="spinner-wrap">
    <div class="ring ring-1"></div>
    <div class="ring ring-2"></div>
    <div class="ring ring-3"></div>
    <div class="ring-core"></div>
  </div>

  <h1>جاري فتح التطبيق…</h1>
  <p class="subtitle">يرجى الانتظار بينما نحوّلك<br>إلى تطبيق وصّلي</p>

  <div class="progress-wrap">
    <div class="progress-bar"></div>
  </div>

  <a href="{{ $appUrl }}" id="manual-link" class="btn hidden">
    <span class="btn-icon">↗</span>
    اضغط هنا لفتح التطبيق
  </a>

  <p class="hint">سيتم التحويل تلقائياً خلال ثوانٍ</p>

</div>

<script>
  window.onload = function() {
    window.location.href = "{{ $appUrl }}";
    setTimeout(function() {
      document.getElementById('manual-link').classList.remove('hidden');
    }, 2500);
  };
</script>
</body>
</html>