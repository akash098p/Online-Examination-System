<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Academix | Login & Register</title>
<link rel="icon" type="png" href="{{ asset('App-logo.png') }}">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');
*{margin:0;padding:0;box-sizing:border-box;font-family:'Montserrat',sans-serif;}
body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:
      linear-gradient(rgba(2,6,23,.35), rgba(2,6,23,.45)),
      url("/images/hero3.jpg") no-repeat center/cover;
}

.form-container form{
    overflow-y: auto;
    scrollbar-width: thin;
}

.form-container form::-webkit-scrollbar{
    width: 6px;
}
.form-container form::-webkit-scrollbar-thumb{
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
}

.container{
    position:relative;
    width:768px;
    max-width:100%;
    max-height: 90vh;
    min-height:480px;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(22px);
    -webkit-backdrop-filter:blur(22px);
    border-radius:30px;
    border:1px solid rgba(255,255,255,.18);
    box-shadow:0 25px 60px rgba(0,0,0,.45);
    overflow:hidden;
}

/* LOGO */
.auth-logo{margin-bottom:18px;position:relative;overflow:hidden;display:inline-block;}
.auth-logo::before{
    content:"";
    position:absolute;
    top:-120%;
    left:-120%;
    width:240%;
    height:240%;
    background: linear-gradient(60deg, transparent 30%, rgba(255,255,255,0.8) 50%, transparent 70%);
    transform: translateX(-120%) rotate(60deg);
    opacity: 0;
    pointer-events:none;
    animation: shineSweep 2.8s ease-in-out infinite;
}
.auth-logo img{width:75px;cursor:pointer;transition:.3s ease, transform .3s ease;}
.auth-logo img:hover{transform:scale(1.08);}

.container button:not(.hidden){position:relative;overflow:hidden;background:orangered;color:white;font-size:12px;padding:10px 45px;border:none;border-radius:8px;font-weight:600;margin-top:10px;cursor:pointer;transition:transform .2s ease, background .2s ease;}
.container button:not(.hidden)::before{
    content:"";
    position:absolute;
    top:-120%;
    left:-120%;
    width:240%;
    height:240%;
    background: linear-gradient(60deg, transparent 30%, rgba(255,255,255,0.75) 50%, transparent 70%);
    transform: translateX(-120%) rotate(60deg);
    opacity: 0;
    pointer-events:none;
    animation: shineSweep 2.8s ease-in-out infinite;
}
.container button:not(.hidden):hover{transform:translateY(-1px);}

@keyframes shineSweep {
    0% {transform: translateX(-120%) rotate(60deg);opacity:0;}
    45% {transform: translateX(-20%) rotate(60deg);opacity:0.75;}
    55% {transform: translateX(20%) rotate(60deg);opacity:0.75;}
    100% {transform: translateX(120%) rotate(60deg);opacity:0;}
}

/* VISIBILITY */
.container.active .sign-in{transform:translateX(100%);opacity:0;visibility:hidden;pointer-events:none;}
.container:not(.active) .sign-up{opacity:0;visibility:hidden;pointer-events:none;}
.container.active .sign-up{transform:translateX(100%);opacity:1;visibility:visible;pointer-events:auto;z-index:5;}
.container:not(.active) .sign-in{opacity:1;visibility:visible;pointer-events:auto;}

h1{color:white;}
p,span,a{color:#e5e7eb;}

.forgot-link{
    color:#facc15;
    transition:color .35s ease, opacity .35s ease;
}

.forgot-link:hover, 
.forgot-link:focus-visible{
    color:#ef4444;
    opacity:.92;
}

.container input{
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.25);
    margin:8px 0;
    padding:10px 15px;
    font-size:13px;
    border-radius:8px;
    width:100%;
    color:white;
}

.container input::placeholder{color:#e5e7eb;}

.container select{
    background:rgba(255,255,255,.18);
    border:1px solid rgba(255,255,255,.25);
    margin:8px 0;
    padding:10px 15px;
    font-size:13px;
    border-radius:8px;
    width:100%;
    color:white;
}

.container select option{
    background:#2b2f3a;   /* dark dropdown background */
    color:white;          /* option text color */
}


.container button{
    background:orangered;
    color:white;
    font-size:12px;
    padding:10px 45px;
    border:none;
    border-radius:8px;
    font-weight:600;
    margin-top:10px;
    cursor:pointer;
    box-shadow:0 0 15px rgba(0,0,0,.5);
}

.container button.hidden{
    background:#facc15;
    color:black;
    border:1px solid #ffffff;
    box-shadow:none;
}

.form-container{
    position:absolute;
    top:0;
    height:100%;
    width:50%;
    transition:.6s ease-in-out;
}

/* 🔧 ONLY CHANGE HERE (justify-content REMOVED) */
.container form{
    display:flex;
    align-items:center;
    flex-direction:column;
    padding:0 40px;
    height:100%;
    overflow-y:auto;
    padding-top:25px;
    padding-bottom:25px;
}

.sign-in{left:0;z-index:2;}
.sign-up{left:0;z-index:1;}

.toggle-container{
    position:absolute;
    top:0;
    left:50%;
    width:50%;
    height:100%;
    overflow:hidden;
    transition:.6s ease-in-out;
    border-radius:20px;
    z-index:1000;
}

.container.active .toggle-container{transform:translateX(-100%);}

.toggle{
    background:linear-gradient(135deg,#ff6a00,#ff2e00);
    height:100%;
    color:white;
    position:relative;
    left:-100%;
    width:200%;
    transition:.6s ease-in-out;
}

.container.active .toggle{transform:translateX(50%);}

.toggle-panel{
    position:absolute;
    width:50%;
    height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    padding:0 30px;
    text-align:center;
}

.toggle-left{transform:translateX(-200%);}
.container.active .toggle-left{transform:translateX(0);}
.toggle-right{right:0;}
</style>
</head>

<body>

<div class="container" id="container">

<!-- SIGN UP -->
<div class="form-container sign-up">
<form method="POST" action="{{ route('register') }}">
@csrf

@if ($errors->any() && old('_form') == 'register')
<div style="background:#ffeded;color:#b91c1c;padding:10px;border-radius:8px;width:100%;margin-bottom:10px;font-size:13px;">
@foreach ($errors->all() as $error)
<div>• {{ $error }}</div>
@endforeach
</div>
@endif

<input type="hidden" name="_form" value="register">

<h1>Create Account</h1><br>

<input type="text" name="name" placeholder="Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="college_name" placeholder="College Name" required>
<select name="department" required>
    <option value="">Select Department</option>
    @foreach(config('academix.departments', []) as $department)
        <option value="{{ $department }}" {{ old('department') === $department ? 'selected' : '' }}>{{ $department }}</option>
    @endforeach
</select>
<select name="sex" required>
    <option value="">Select Sex</option>
    <option value="male" {{ old('sex') === 'male' ? 'selected' : '' }}>Male</option>
    <option value="female" {{ old('sex') === 'female' ? 'selected' : '' }}>Female</option>
</select>
<input type="text" name="registration_no" placeholder="Registration Number" required>

<!-- ✅ SEMESTER FIXED -->
<select name="semester" required>
    <option value="">Select Semester</option>
    @foreach(config('academix.semesters', []) as $semester)
        <option value="{{ $semester }}" {{ old('semester') === $semester ? 'selected' : '' }}>{{ $semester }}</option>
    @endforeach
</select>

<input type="text" name="phone" placeholder="Contact Number" required>

<input type="password" name="password" placeholder="Password" required>
<input type="password" name="password_confirmation" placeholder="Confirm Password" required>
<button type="submit">Sign Up</button>
</form>
</div>

<!-- SIGN IN -->
<div class="form-container sign-in">
<form method="POST" action="{{ route('login') }}">
@csrf

@if ($errors->any() && old('_form') == 'login')
<div style="background:#ffeded;color:#b91c1c;padding:10px;border-radius:8px;width:100%;margin-bottom:10px;font-size:13px;">
@foreach ($errors->all() as $error)
<div>• {{ $error }}</div>
@endforeach
</div>
@endif

<input type="hidden" name="_form" value="login">

<br><h1>Sign In</h1><br><br>
<span>Login with Email and Password</span><br>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<a href="{{ route('password.request') }}" class="forgot-link">Forgot password ?</a><br><br>
<button type="submit">Sign In</button>
</form>
</div>

<!-- TOGGLE -->
<div class="toggle-container">
<div class="toggle">

<div class="toggle-panel toggle-left">
<a href="/" class="auth-logo">
<img src="{{ asset('logo.png' ) }}" style="width:160px; height:160px;">
</a>
<h1>Welcome Back !</h1> <br><br>
<p>Sign in with your Email and Password</p><br>
<button type="button" class="hidden" id="login">Sign In</button>
</div>

<div class="toggle-panel toggle-right">
<a href="/" class="auth-logo">
<img src="{{ asset('logo.png') }}" style="width:160px; height:160px;">
</a>
<h1>Hello, Students !</h1>
<p>Join<br>Academix,<br>Online Examination System</p>
<button type="button" class="hidden" id="register">Sign Up</button>
</div>

</div>
</div>

</div>

<script>
const container = document.getElementById('container');

document.getElementById("register").onclick = () => {
    container.classList.add("active");
    setTimeout(() => {
        window.location.href = "{{ route('register') }}";
    }, 450);
};

document.getElementById("login").onclick = () => {
    container.classList.remove("active");
    setTimeout(() => {
        window.location.href = "{{ route('login') }}";
    }, 450);
};

@if (request()->routeIs('register'))
    container.classList.add("active");
@else
    container.classList.remove("active");
@endif

@if ($errors->any() && old('_form') == 'register')
    container.classList.add("active");
@endif
</script>

</body>
</html>
