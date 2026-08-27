<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
    <title>Admin sign in | Trans Globe Indore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --navy:#0c244b; --red:#e5242e; --muted:#71809b; } *{box-sizing:border-box} body{min-width:320px;min-height:100dvh;margin:0;display:grid;place-items:center;padding:24px;background:radial-gradient(circle at 100% 0,#eaf0ff 0,transparent 38%),#f5f7fb;color:var(--navy);font:15px/1.5 'Plus Jakarta Sans',system-ui,sans-serif}.card{width:min(100%,440px);padding:40px;border:1px solid #dfe6f0;border-radius:24px;background:#fff;box-shadow:0 24px 60px rgba(16,37,75,.12)}.mark{display:grid;width:45px;height:45px;place-items:center;border-radius:14px;background:var(--red);color:#fff;box-shadow:0 10px 20px rgba(229,36,46,.22)}.mark svg{width:23px;height:23px}.eyebrow{margin:20px 0 5px;color:var(--red);font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}h1{margin:0;font-size:29px;line-height:1.08;letter-spacing:-.05em}p{margin:10px 0 26px;color:var(--muted);font-size:13px}label{display:block;margin:15px 0 6px;font-size:12px;font-weight:800}input{width:100%;min-height:46px;padding:0 13px;border:1px solid #cfd9e8;border-radius:11px;outline:none;font:inherit}input:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(229,36,46,.12)}.remember{display:flex;align-items:center;gap:8px;margin:15px 0 19px;color:#61708b;font-size:12px;font-weight:700}.remember input{width:16px;min-height:16px;accent-color:var(--red)}button{width:100%;min-height:48px;border:0;border-radius:11px;background:var(--navy);color:#fff;font:inherit;font-size:13px;font-weight:800;cursor:pointer}button:hover{background:#173866}.error{margin:0 0 15px;padding:11px 12px;border-radius:10px;background:#fff0f1;color:#b81520;font-size:12px;font-weight:700}.back{display:block;margin-top:22px;color:var(--muted);font-size:12px;text-align:center}.back:hover{color:var(--navy)}
    </style>
</head>
<body>
    <section class="card" aria-labelledby="login-heading">
        <span class="mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span>
        <div class="eyebrow">Protected workspace</div><h1 id="login-heading">Admin sign in</h1><p>Review and manage counselling enquiries securely.</p>
        @if ($errors->any())<div class="error" role="alert">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('admin.login.store') }}">@csrf
            <label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required autofocus>
            <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="remember"><input type="checkbox" name="remember" value="1"> Keep me signed in on this device</label>
            <button type="submit">Sign in to dashboard</button>
        </form>
        <a class="back" href="{{ url('/') }}">← Return to public website</a>
    </section>
</body>
</html>
