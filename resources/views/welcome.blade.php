<x-app-layout>

<style>

/* ================= GLOBAL ================= */

html{
scroll-behavior:smooth;
}

body{
margin:0;
background:#020617;
font-family:system-ui,-apple-system,Segoe UI,Roboto;
color:white;
line-height:1.6;
}

.guest-container{
position:relative;
overflow:hidden;
}


/* ================= PARTICLES ================= */

#particles{
position:fixed;
width:100%;
height:100%;
pointer-events:none;
z-index:2;
}


/* ================= MOUSE GLOW ================= */

#mouseGlow{
position:fixed;
width:300px;
height:300px;
background:radial-gradient(circle,#3b82f6,transparent);
pointer-events:none;
border-radius:50%;
filter:blur(80px);
opacity:.25;
z-index:1;
}


/* ================= NAVBAR ================= */

.navbar{
position:fixed;
top:0;
width:100%;
z-index:999;
background:rgba(2,6,23,0.85);
backdrop-filter: blur(16px) saturate(180%);
border-bottom:1px solid rgba(59,130,246,0.2);
transition:all 0.3s ease;
}

.navbar.scrolled{
background:rgba(2,6,23,0.95);
box-shadow:0 4px 20px rgba(0,0,0,0.3);
}

.nav-inner{
display:flex;
justify-content:space-between;
align-items:center;
padding:15px 40px;
max-width:1400px;
margin:0 auto;
}

.logo{
font-size:26px;
font-weight:800;
background:linear-gradient(135deg,#3b82f6,#8b5cf6,#ec4899);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
letter-spacing:-0.5px;
}

.nav-links{
display:flex;
align-items:center;
}

.nav-links a{
position:relative;
margin-left:35px;
text-decoration:none;
color:#cbd5e1;
font-weight:500;
font-size:15px;
transition:color 0.3s ease;

/* BLACK OUTLINE */
text-shadow:
-1px -1px 0 #342f2f81,
1px -1px 0 #342f2f81,
-1px 1px 0 #342f2f81,
1px 1px 0 #342f2f81,
0 0 1px rgba(0,0,0,0.8);

}

.nav-links a::after{
content:"";
position:absolute;
bottom:-6px;
left:0;
width:0;
height:2px;
background:linear-gradient(90deg,white,yellow,orange,yellow,white);
transition:0.3s;
}

.nav-links a:hover{
color:orange;

/* KEEP OUTLINE */
text-shadow:
-1px -1px 0 #342f2f81,
1px -1px 0 #342f2f81,
-1px 1px 0 #342f2f81,
1px 1px 0 #342f2f81,
0 0 1px rgba(247, 245, 245, 0.3);
}

.nav-links a:hover::after{
width:100%;
}

.nav-links a.active{
color:white;
}

.nav-links a.active::after{
width:100%;
}




/* ================= HERO ================= */

.hero{
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  position: relative;
  overflow: hidden;
  background: none;
}

.hero{
  background-image:
    linear-gradient(
      to bottom,
      rgba(2,6,23,0.08) 0%,
      rgba(2,6,23,0.28) 45%,
      rgba(2,6,23,0.94) 100%
    ),
    url("{{ asset('images/hero1.jpg') }}");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}


/* REMOVE HERO IMAGE COMPLETELY */

.hero-bg{
display:none !important;
}

.hero-overlay{
display:none !important;
}

.hero-fade{
display:none !important;
}


/* ================= HERO GLASS ================= */

.hero-glass{

position: relative;
z-index: 3;

width: 75%;
max-width: 1200px;
margin: auto;

padding:70px 60px;

/* MATCH PAGE STYLE — NO BLUE TONE */
background: rgba(16, 44, 55, 0.22);

backdrop-filter: blur(12px) saturate(180%);
-webkit-backdrop-filter: blur(12px) saturate(180%);

border-radius:20px;

border: 1px solid rgba(255,255,255,0.08);

/* cleaner shadow */
box-shadow:
0 40px 80px rgba(105, 150, 194, 0.22),
inset 0 1px rgba(255, 255, 255, 0.53);

/* floating animation */
animation: floatGlass 6s ease-in-out infinite alternate;

}


/* FLOAT ANIMATION */

@keyframes floatGlass{

from{
transform: translateY(0px);
}

to{
transform: translateY(-15px);
}

}


.hero-glass h1{
font-size:62px;
font-weight:900;
background:linear-gradient(90deg,green 0%,orange 50%,#00FFFF 100%);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
margin-bottom:20px;
line-height:1.2;

text-shadow:
-1px -1px 0 aquamarine,
1px -1px 0 black,
0px 2px 0 black,
2px 2px 0 black,
0 0 10px rgba(17, 222, 233, 0.92);
}

/*.hero-glass h2{
font-size:40px;
font-weight:800;
background:linear-gradient(120deg,#3b82f6,#ec4899,#8b5cf6);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
margin-bottom:20px;
line-height:1.2;
}*/

.hero-glass .subtitle{
color:#e2e8f0;
margin-top:20px;
font-size:20px;
max-width:750px;
margin-left:auto;
margin-right:auto;
font-weight:400;

text-shadow:
-1px -1px 0 #00000081,
1px -1px 0 #000000a5,
-1px 1px 0 #00000085,
2px 1px 0 #000000be,
0 0 2px rgba(0, 0, 0, 0.78);
}

.hero-badge{
display:inline-block;
background:rgba(59,130,246,0.15);
border:1px solid rgba(59,130,246,0.3);
padding:8px 20px;
border-radius:20px;
font-size:14px;
color:#93c5fd;
margin-bottom:25px;
font-weight:600;
}


/* ================= BUTTONS ================= */

.btn{
display:inline-block;
padding:14px 32px;
border-radius:12px;
margin-top:30px;
font-weight:600;
text-decoration:none;
transition:all 0.3s ease;
font-size:16px;
}

.btn-primary {
  position: relative; /* Required for pseudo-element positioning */
  background: linear-gradient(110deg, orange, orange, white, white);
  color: black;
  box-shadow: 0 4px 15px rgba(214, 101, 2, 0.55);
  overflow: hidden; /* Contains the shine pseudo-element */
  transition: all 0.8s ease;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(110deg, 
    transparent 0%, 
    transparent 40%, 
    rgba(255, 255, 255, 0.6) 50%, 
    transparent 60%, 
    transparent 100%);
  transition: left 0.8s ease;
  pointer-events: none; /* Allows clicks through the overlay */
}

.btn-primary:hover {
  background: orange;
  transform: translateY(-3px);
  box-shadow: 0 6px 25px rgba(214, 101, 2, 0.88);
}

.btn-primary:hover::before {
  left: 100%; /* Sweeps the shine across the button */
}


.btn-demo {
  position: relative;
  background: linear-gradient(110deg, white, white, green, green);
  color: black;
  margin-left: 15px;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
  overflow: hidden;
  transition: all 0.8s ease;
}

.btn-demo::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(110deg, 
    transparent 0%, 
    transparent 40%, 
    rgba(255, 255, 255, 0.6) 50%, 
    transparent 60%, 
    transparent 100%);
  transition: left 0.8s ease;
  pointer-events: none;
}

.btn-demo:hover {
  background: green;
  transform: translateY(-3px);
  box-shadow: 0 6px 25px rgba(16, 185, 129, 0.62);
}

.btn-demo:hover::before {
  left: 100%;
}


/* ================= SECTIONS ================= */

.section{
padding:100px 20px;
text-align:center;
position:relative;
z-index:2;
}

.section-inner{
max-width:1200px;
margin:0 auto;
}

.section-title{
font-size:42px;
font-weight:800;
margin-bottom:20px;
background:linear-gradient(135deg,#3b82f6,#8b5cf6);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
}

.section-desc{
color:#94a3b8;
max-width:800px;
margin:auto;
font-size:18px;
line-height:1.7;
}


/* ================= FEATURES GRID ================= */

.grid{
display:grid;
gap:30px;
margin-top:60px;
}

.grid-3{
grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
}

.grid-2{
grid-template-columns:repeat(auto-fit,minmax(450px,1fr));
}


/* ================= FEATURE CARDS ================= */

.card{
background:rgba(255,255,255,.06);
padding:40px;
border-radius:20px;
border:1px solid rgba(255,255,255,.12);
transition:.4s;
position:relative;
overflow:hidden;
text-align:left;
}

.card:hover{
transform:translateY(-12px);
box-shadow:0 20px 50px rgba(59,130,246,.3);
border-color:rgba(59,130,246,0.3);
}

.card::before{
content:"";
position:absolute;
top:0;
left:-100%;
width:100%;
height:100%;
background:linear-gradient(90deg,
transparent,
rgba(59,130,246,.15),
transparent);
transition:.6s;
}

.card:hover::before{
left:100%;
}

.feature-icon{
width:60px;
height:60px;
margin-bottom:25px;
fill:#3b82f6;
}

.card h3{
font-size:22px;
font-weight:700;
margin-bottom:15px;
color:white;
}

.card p{
color:#94a3b8;
line-height:1.6;
font-size:15px;
}


/* ================= STATS SECTION ================= */

.stats-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:30px;
margin-top:50px;
}

.stat-card{
background:rgba(255,255,255,.05);
padding:30px;
border-radius:16px;
border:1px solid rgba(255,255,255,.1);
}

.stat-number{
font-size:48px;
font-weight:800;
background:linear-gradient(135deg,#3b82f6,#8b5cf6);
-webkit-background-clip:text;
-webkit-text-fill-color:transparent;
margin-bottom:10px;
}

.stat-label{
color:#94a3b8;
font-size:16px;
}


/* ================= BENEFITS SECTION ================= */

.benefit-item{
display:flex;
align-items:start;
gap:20px;
text-align:left;
padding:25px;
background:rgba(255,255,255,.04);
border-radius:16px;
border:1px solid rgba(255,255,255,.08);
transition:0.3s;
}

.benefit-item:hover{
background:rgba(255,255,255,.06);
border-color:rgba(59,130,246,0.2);
}

.benefit-icon{
width:48px;
height:48px;
min-width:48px;
background:linear-gradient(135deg,#3b82f6,#8b5cf6);
border-radius:12px;
display:flex;
align-items:center;
justify-content:center;
fill:white;
}

.benefit-icon svg{
width:28px;
height:28px;
}

.benefit-content h4{
font-size:18px;
font-weight:700;
margin-bottom:8px;
color:white;
}

.benefit-content p{
color:#94a3b8;
font-size:15px;
line-height:1.6;
}


/* ================= CONTACT SECTION ================= */

.contact-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:30px;
margin-top:50px;
}

.contact-card{
background:rgba(255,255,255,.05);
padding:35px;
border-radius:18px;
border:1px solid rgba(255,255,255,.12);
transition:0.3s;
}

.contact-card:hover{
background:rgba(255,255,255,.08);
border-color:rgba(59,130,246,0.3);
transform:translateY(-5px);
}

.contact-icon{
width:50px;
height:50px;
margin:0 auto 20px;
fill:#3b82f6;
}

.contact-card h4{
font-size:20px;
font-weight:700;
margin-bottom:12px;
color:white;
}

.contact-card p{
color:#94a3b8;
font-size:15px;
margin-bottom:15px;
}

.contact-link{
color:#3b82f6;
text-decoration:none;
font-weight:600;
transition:0.3s;
}

.contact-link:hover{
color:#60a5fa;
}


/* ================= FOOTER ================= */

.footer{
padding:72px 24px 28px;
margin-top:40px;
border-top:1px solid rgba(148,163,184,.16);
background:linear-gradient(180deg, rgba(15, 23, 42, 0.16) 0%, rgba(2, 6, 23, 0.7) 100%);
position:relative;
z-index:2;
}

.footer-inner{
max-width:1200px;
margin:0 auto;
padding:32px;
border:1px solid rgba(255,255,255,0.08);
border-radius:28px;
background:linear-gradient(145deg, rgba(15, 23, 42, 0.8), rgba(10, 16, 32, 0.94));
backdrop-filter:blur(14px);
-webkit-backdrop-filter:blur(14px);
box-shadow:0 24px 60px rgba(0,0,0,0.26);
}

.footer-grid{
display:grid;
grid-template-columns:minmax(0,1.2fr) repeat(3,minmax(0,0.8fr));
gap:32px;
margin-bottom:32px;
}

.footer-col h4{
font-weight:700;
margin-bottom:16px;
font-size:1.02rem;
color:white;
}

.footer-col p{
color:#b8c4d8;
line-height:1.75;
font-size:0.96rem;
max-width:34ch;
}

.footer-links{
list-style:none;
padding:0;
margin:0;
display:grid;
gap:12px;
}

.footer-links li{
margin-bottom:0;
}

.footer-links a{
color:#b8c4d8;
text-decoration:none;
transition:0.25s ease;
font-size:0.95rem;
display:inline-flex;
align-items:center;
gap:10px;
}

.footer-links a:hover{
color:#67e8f9;
transform:translateX(4px);
}

.footer-bottom{
display:flex;
align-items:center;
justify-content:space-between;
gap:18px;
padding-top:24px;
border-top:1px solid rgba(255,255,255,.08);
color:#94a3b8;
font-size:0.92rem;
flex-wrap:wrap;
background:transparent !important;
text-align:left;
}

.footer-bottom p{
margin:0;
display:flex;
flex-wrap:wrap;
align-items:center;
gap:10px 0;
color:#94a3b8;
}

.footer-bottom p a{
color:#cbd5e1 !important;
text-decoration:none !important;
margin:0 12px !important;
transition:color 0.25s ease;
}

.footer-bottom p a:hover{
color:#67e8f9 !important;
}

.footer-brand{
display:grid;
gap:14px;
}

.footer-brand-mark{
display:inline-flex;
align-items:center;
gap:10px;
font-size:1.4rem;
font-weight:800;
color:#fff;
letter-spacing:-0.02em;
}

.footer-brand-badge{
width:40px;
height:40px;
display:inline-flex;
align-items:center;
justify-content:center;
border-radius:14px;
background:linear-gradient(135deg, rgba(34,197,94,0.2), rgba(6,182,212,0.22));
border:1px solid rgba(103,232,249,0.22);
color:#a5f3fc;
box-shadow:inset 0 1px 0 rgba(255,255,255,0.12);
}

.footer-brand-copy{
font-size:0.95rem;
color:#b8c4d8;
max-width:40ch;
}

.footer-bottom-links{
display:flex;
flex-wrap:wrap;
align-items:center;
gap:10px 18px;
}

.footer-bottom-links a{
color:#cbd5e1;
text-decoration:none;
transition:color 0.25s ease;
}

.footer-bottom-links a:hover{
color:#67e8f9;
}

.footer-divider{
width:1px;
height:14px;
background:rgba(148,163,184,0.4);
}


/* ================= REVEAL ANIMATION ================= */

.reveal{
opacity:0;
transform:translateY(40px);
transition:1s;
}

.reveal.active{
opacity:1;
transform:translateY(0);
}


/* ================= RESPONSIVE ================= */

@media(max-width:768px){
.nav-inner{
padding:15px 20px;
}
.nav-links a{
margin-left:20px;
font-size:14px;
}
.hero{
align-items:flex-start;
padding-top:50px;
}
.hero-glass{
width:95%;
padding:50px 30px;
}
.hero-glass h1{
font-size:42px;
}
.hero-glass .subtitle{
font-size:16px;
}
.hero-glass .btn{
padding:8px 18px;
font-size:13px;
white-space:nowrap;
}
.section-title{
font-size:32px;
}
.btn-demo{
margin-left:8px;
margin-top:0;
}
.grid-2{
grid-template-columns:1fr;
}

.footer{
padding:56px 16px 24px;
}

.footer-inner{
padding:24px 18px;
border-radius:24px;
}

.footer-grid{
grid-template-columns:1fr;
gap:28px;
margin-bottom:24px;
}

.footer-col p{
max-width:none;
}

.footer-bottom{
flex-direction:column;
align-items:flex-start;
}

.footer-bottom p{
display:block;
line-height:1.8;
}

.footer-bottom p a{
display:inline-block;
margin:0 10px 0 0 !important;
}

.footer-bottom-links{
gap:10px 14px;
}

.footer-divider{
display:none;
}
}

</style>


<div class="guest-container">

<canvas id="particles"></canvas>
<div id="mouseGlow"></div>


<!-- ================= NAVBAR ================= 

<div class="navbar" id="navbar">
<div class="nav-inner">
<div class="logo">Academix</div>
<div class="nav-links">
<a href="#home" class="nav-link active">Home</a>
<a href="#features" class="nav-link">Features</a>
<a href="#about" class="nav-link">About</a>
<a href="#benefits" class="nav-link">Benefits</a>
<a href="#contact" class="nav-link">Contact</a>
<a href="#support" class="nav-link">Support</a>
</div>
</div>
</div>
-->

<!-- ================= HERO SECTION ================= -->

<section id="home" class="hero"
style="
  background-image:
    linear-gradient(
      to bottom,
      rgba(2,6,23,0.08) 0%,
      rgba(2,6,23,0.28) 45%,
      rgba(2,6,23,0.94) 100%
    ),
    url('{{ asset('images/hero11.png') }}');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
"
>
<div class="hero-glass">
<h1>Welcome to Academix</h1> 
<p class="subtitle">
A secure online examination platform with AI-assisted proctoring, instant MCQ evaluation, department-wise exam control and admin analytics built for modern academic workflows.
</p>

@auth
@if(auth()->user()->role === 'student')
<a href="{{ route('student.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
@else
<a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
@endif
@else
<a href="/register" class="btn btn-primary">Create Account</a>
<a href="{{ route('demo.index') }}" class="btn btn-demo">Try Demo Test</a>
@endauth
</div>
</section>


<!-- ================= FEATURES SECTION ================= -->

<section id="features" class="section reveal">
<div class="section-inner">
<h2 class="section-title">Powerful Platform Features</h2>
<p class="section-desc">
Comprehensive examination management for institutions that need secure delivery,
automated evaluation, proctoring controls and fast academic oversight from one platform.
</p>

<div class="grid grid-3">

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M12 1L3 5v6c0 5.25 3.75 10.5 9 12 5.25-1.5 9-6.75 9-12V5l-9-4z"/>
</svg>
<h3>Advanced Security</h3>
<p>Protect exam integrity with role-based access, one-attempt control, fullscreen and tab warnings,
  warning limits and department & semester based eligibility rules.</p>
</div>

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
</svg>
<h3>AI Proctoring Workflow</h3>
<p>Enable camera and microphone checks per exam, detect missing or multiple faces,
  flag sustained talking, capture screenshot evidence and review violations from the admin panel.</p>
</div>

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
</svg>
<h3>Instant MCQ Evaluation</h3>
<p>Automatically grade MCQ exams, publish results quickly and give students answer-level
  insight with AI-supported result explanations and performance guidance.</p>
</div>

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
</svg>
<h3>Real-Time AI Analytics</h3>
<p>Track exam performance through admin analytics, leaderboards, exam summaries,
  student-wise result views and AI-generated insights for faster academic review.</p>
</div>

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
</svg>
<h3>Smart Exam Builder</h3>
<p>Create exams with reusable questions, option mapping, schedule controls,
  duration limits and targeted assignment by department and semester.</p>
</div>

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
</svg>
<h3>Guided Exam Experience</h3>
<p>Give students a smooth exam flow with a pre-exam checkpoint, camera and microphone readiness test,
  countdown start, autosave behavior and a live demo test for practice.</p>
</div>



</div>
</div>
</section>


<!-- ================= STATISTICS SECTION =================

<section class="section reveal" style="background:rgba(59,130,246,0.05);padding:80px 20px;">
<div class="section-inner">
<h2 class="section-title">Trusted by Institutions Worldwide</h2>
<div class="stats-grid">
<div class="stat-card">
<div class="stat-number">99.9%</div>
<div class="stat-label">Platform Uptime</div>
</div>
<div class="stat-card">
<div class="stat-number">500K+</div>
<div class="stat-label">Exams Conducted</div>
</div>
<div class="stat-card">
<div class="stat-number">1000+</div>
<div class="stat-label">Active Institutions</div>
</div>
<div class="stat-card">
<div class="stat-number">24/7</div>
<div class="stat-label">Support Available</div>
</div>
</div>
</div>
</section>-->


<!-- ================= ABOUT SECTION ================= -->

<section id="about" class="section reveal">
<div class="section-inner">
<h2 class="section-title">About Academix</h2>
<p class="section-desc">
Academix is a full online examination management system built for institutions that want
better control over exam delivery, evaluation, proctoring, and performance review in one place.
</p>
<p class="section-desc" style="margin-top:25px;">
Admins can publish department-specific exams, configure per-exam camera and microphone requirements,
review proctoring evidence, manage students, and analyze outcomes while students attempt exams,
track scores and review their results from a dedicated dashboard.
</p>

<div class="grid grid-2" style="margin-top:60px;text-align:left;">
<div class="card">
<h3>Our Mission</h3>
<p>To simplify digital assessment through a secure, practical and insight-driven exam platform
that reduces manual work while improving fairness, visibility and student feedback.</p>
</div>
<div class="card">
<h3>Our Vision</h3>
<p>To help institutions run trustworthy online examinations with modern proctoring,
clear analytics and a smoother experience for both administrators and learners.</p>
</div>
</div>
</div>
</section>


<!-- ================= BENEFITS SECTION ================= -->

<section id="benefits" class="section reveal">
<div class="section-inner">
<h2 class="section-title">Why Choose Academix?</h2>
<p class="section-desc">
Designed around real exam operations, Academix combines secure delivery, fast evaluation
and admin visibility without forcing institutions into disconnected tools.
</p>

<div class="grid grid-2" style="margin-top:50px;">

<div class="benefit-item">
<div class="benefit-icon">
<svg viewBox="0 0 24 24">
<path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
</svg>
</div>
<div class="benefit-content">
<h4>Save Time & Resources</h4>
<p>Reduce manual checking and repetitive admin work with scheduled exams,
  automatic MCQ scoring, centralized result sheets and quick student filtering.</p>
</div>
</div>

<div class="benefit-item">
<div class="benefit-icon">
<svg viewBox="0 0 24 24">
<path d="M12 1L3 5v6c0 5.25 3.75 10.5 9 12 5.25-1.5 9-6.75 9-12V5l-9-4z"/>
</svg>
</div>
<div class="benefit-content">
<h4>Enhanced Security</h4>
<p>Restrict exams to eligible students, prevent repeat attempts, enforce timers
  and add per-exam camera or microphone checks when stronger monitoring is needed.</p>
</div>
</div>

<div class="benefit-item">
<div class="benefit-icon">
<svg viewBox="0 0 24 24">
<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
</svg>
</div>
<div class="benefit-content">
<h4>Instant Results</h4>
<p>Show scores, percentages, answer summaries and AI-assisted feedback soon after submission,
  so students can understand performance without delay.</p>
</div>
</div>

<div class="benefit-item">
<div class="benefit-icon">
<svg viewBox="0 0 24 24">
<path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
</svg>
</div>
<div class="benefit-content">
<h4>Connected Academic Workflow</h4>
<p>Keep student profiles, exams, results, analytics, proctoring evidence
  and AI result support connected inside a single academic platform.</p>
</div>
</div>

<div class="benefit-item">
<div class="benefit-icon">
<svg viewBox="0 0 24 24">
<path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
</svg>
</div>
<div class="benefit-content">
<h4>Scalable Solution</h4>
<p>Manage department-wise and semester-wise exams, student records,
  targeted publishing and admin review as your exam operations grow.</p>
</div>
</div>

<div class="benefit-item">
<div class="benefit-icon">
<svg viewBox="0 0 24 24">
<path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
</svg>
</div>
<div class="benefit-content">
<h4>Comprehensive Reporting</h4>
<p>Review answer sheets, performance trends, leaderboard snapshots,
  violations and exam-level summaries from the admin and student sides.</p>
</div>
</div>

</div>
</div>
</section>


<!-- ================= DEMO SECTION ================= -->

<section class="section reveal" style="background:rgba(139,92,246,0.05);padding:80px 20px;">
<div class="section-inner">
<h2 class="section-title">Experience Academix Today</h2>
<p class="section-desc">
Try the live demo exam flow to experience the timer, palette, answer flow
and overall student-side test experience before signing in.
</p>
<a href="{{ route('demo.index') }}" class="btn btn-demo">Start Free Demo Test</a>
</div>
</section>


<!-- ================= CONTACT SECTION ================= -->

<section id="contact" class="section reveal">
<div class="section-inner">
<h2 class="section-title">Get In Touch</h2>
<p class="section-desc">
Have questions about the platform, exam workflows or admin features?
Reach out through the contact options below.
</p>

<div class="contact-grid">

<div class="contact-card">
<svg class="contact-icon" viewBox="0 0 24 24">
<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
</svg>
<h4>Email Support</h4>
<p>Contact the Academix team for platform questions, setup help or project inquiries.</p>
<a href="mailto:academix.edutech@gmail.com" class="contact-link">academix.edutech@gmail.com</a>
</div>

<div class="contact-card">
<svg class="contact-icon" viewBox="0 0 24 24">
<path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57-.35-.11-.74-.03-1.02.24l-2.2 2.2c-2.83-1.44-5.15-3.75-6.59-6.59l2.2-2.21c.28-.26.36-.65.25-1C8.7 6.45 8.5 5.25 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1zM19 12h2c0-4.97-4.03-9-9-9v2c3.87 0 7 3.13 7 7zm-4 0h2c0-2.76-2.24-5-5-5v2c1.66 0 3 1.34 3 3z"/>
</svg>
<h4>Phone Support</h4>
<p>Speak directly with the team about usage flow, implementation or project support.</p>
<a href="tel:+1234567890" class="contact-link">+1 (234) 567-890</a>
</div>

<div class="contact-card">
<svg class="contact-icon" viewBox="0 0 24 24">
<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
</svg>
<h4>Office Location</h4>
<p>Project coordination and institutional communication details.</p>
<a href="#" class="contact-link">123 Education Lane, Tech City</a>
</div>

</div>
</div>
</section>


<!-- ================= SUPPORT SECTION ================= -->

<section id="support" class="section reveal">
<div class="section-inner">
<h2 class="section-title">Platform Guidance</h2>
<p class="section-desc">
Explore the parts of Academix that help admins and students work with exams,
results, proctoring and account management more confidently.
</p>

<div class="grid grid-3" style="margin-top:50px;">

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
</svg>
<h3>Knowledge Base</h3>
<p>Understand exam creation, publishing rules, result review, student management
and the exam flow from both admin and student sides.</p>
</div>

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-4 6V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10c.55 0 1-.45 1-1z"/>
</svg>
<h3>Admin Review Tools</h3>
<p>Use built-in dashboards to inspect results, analytics, answer sheets
and violation evidence without leaving the platform.</p>
</div>

<div class="card">
<svg class="feature-icon" viewBox="0 0 24 24">
<path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
</svg>
<h3>Video Tutorials</h3>
<p>Follow the student exam flow, demo experience and admin-side review process
to get familiar with the platform quickly.</p>
</div>

</div>

<div style="margin-top:40px;">
<a href="mailto:academix.edutech@gmail.com" class="btn btn-primary">Contact Support Team</a>
</div>
</div>
</section>


<!-- ================= FOOTER ================= -->

<footer class="footer">
<div class="footer-inner">
<div class="footer-grid">

<div class="footer-col">
<div class="footer-brand">
<div class="footer-brand-mark">
<span class="footer-brand-badge" aria-hidden="true">
<svg viewBox="0 0 24 24" width="20" height="20" fill="none">
<path d="M5 18h14M7 15V6.5a1.5 1.5 0 0 1 2.39-1.21L12 7.25l2.61-1.96A1.5 1.5 0 0 1 17 6.5V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M9 10h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
</svg>
</span>
<span>Academix</span>
</div>
<h4>About Academix</h4>
<p>Academix is an online exam management platform built for secure assessments, proctored exam delivery, result tracking and admin-side academic oversight.</p>
</div>
</div>

<div class="footer-col">
<h4>Platform</h4>
<ul class="footer-links">
<li><a href="#features">Features</a></li>
<li><a href="#about">About Us</a></li>
<li><a href="{{ route('demo.index') }}">Demo Test</a></li>
<li><a href="#benefits">Benefits</a></li>
</ul>
</div>

<div class="footer-col">
<h4>Resources</h4>
<ul class="footer-links">
<li><a href="#support">User Guide</a></li>
<li><a href="{{ route('demo.index') }}">Demo Test</a></li>
<li><a href="#support">Platform Help</a></li>
<li><a href="#support">FAQs</a></li>
</ul>
</div>

<div class="footer-col">
<h4>Support</h4>
<ul class="footer-links">
<li><a href="#contact">Contact Us</a></li>
<li><a href="mailto:academix.edutech@gmail.com">Email Support</a></li>
<li><a href="#support">Help</a></li>
<li><a href="#about">Project Info</a></li>
</ul>
</div>


</div>

<div class="footer-bottom">
<p>&copy; {{ date('Y') }} Academix. All rights reserved. | 
<a href="#" style="color:#64748b;text-decoration:none;margin:0 10px;">Privacy Policy</a> | 
<a href="#" style="color:#64748b;text-decoration:none;margin:0 10px;">Terms of Service</a> | 
<a href="#" style="color:#64748b;text-decoration:none;margin:0 10px;">Cookie Policy</a>
</p>
</div>
</div>
</footer>

</div>


<script>

/* ================= PARTICLES ANIMATION ================= */

const canvas = document.getElementById("particles");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];

for(let i = 0; i < 80; i++){
particles.push({
x: Math.random() * canvas.width,
y: Math.random() * canvas.height,
r: Math.random() * 2 + 0.5,
dx: (Math.random() - 0.5) * 0.5,
dy: (Math.random() - 0.5) * 0.5
});
}

function animate(){
ctx.clearRect(0, 0, canvas.width, canvas.height);

particles.forEach(p => {
p.x += p.dx;
p.y += p.dy;

if(p.x < 0 || p.x > canvas.width) p.dx = -p.dx;
if(p.y < 0 || p.y > canvas.height) p.dy = -p.dy;

ctx.beginPath();
ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
ctx.fillStyle = "rgba(255,255,255,0.6)";
ctx.fill();
});

requestAnimationFrame(animate);
}

animate();

window.addEventListener('resize', () => {
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;
});


/* ================= MOUSE GLOW EFFECT ================= */

const mouseGlow = document.getElementById('mouseGlow');

document.addEventListener("mousemove", e => {
mouseGlow.style.left = e.clientX - 150 + "px";
mouseGlow.style.top = e.clientY - 150 + "px";
});


/* ================= NAVBAR SCROLL EFFECT ================= */

const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
if(window.scrollY > 50){
navbar.classList.add('scrolled');
} else {
navbar.classList.remove('scrolled');
}
});


/* ================= SMOOTH SCROLL & ACTIVE NAV LINKS ================= */

const navLinks = document.querySelectorAll('.nav-link');

navLinks.forEach(link => {
link.addEventListener('click', function(e){
navLinks.forEach(l => l.classList.remove('active'));
this.classList.add('active');
});
});

// Update active link on scroll
window.addEventListener('scroll', () => {
let current = '';
const sections = document.querySelectorAll('section[id]');

sections.forEach(section => {
const sectionTop = section.offsetTop;
const sectionHeight = section.clientHeight;
if(scrollY >= (sectionTop - 200)){
current = section.getAttribute('id');
}
});

navLinks.forEach(link => {
link.classList.remove('active');
if(link.getAttribute('href') === '#' + current){
link.classList.add('active');
}
});
});


/* ================= REVEAL ON SCROLL ANIMATION ================= */

const observer = new IntersectionObserver(entries => {
entries.forEach(entry => {
if(entry.isIntersecting){
entry.target.classList.add("active");
}
});
}, {
threshold: 0.1
});

document.querySelectorAll(".reveal").forEach(el => {
observer.observe(el);
});

</script>

</x-app-layout>
