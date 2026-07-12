<?php /** @var array $config */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($config['brand']) ?> — Login</title>
    <style>
        *{box-sizing:border-box} body{font-family:system-ui,Segoe UI,Roboto,sans-serif;background:#0f1420;color:#e7ebf3;margin:0;height:100vh;display:flex;align-items:center;justify-content:center}
        .card{background:#171d2b;border:1px solid #232c3f;border-radius:14px;padding:32px;width:340px}
        h1{font-size:18px;margin:0 0 4px} p{color:#8a94a6;font-size:13px;margin:0 0 20px}
        input{width:100%;padding:12px 14px;border-radius:9px;border:1px solid #2b3448;background:#0f1420;color:#e7ebf3;font-size:14px}
        button{width:100%;margin-top:14px;padding:12px;border:none;border-radius:9px;background:#2f6fed;color:#fff;font-weight:600;cursor:pointer}
        .err{background:#3a1d24;border:1px solid #6b2b38;color:#ff9aa8;padding:10px;border-radius:8px;font-size:13px;margin-bottom:14px}
    </style>
</head>
<body>
    <form class="card" method="POST" action="/admin/login">
        <h1><?= htmlspecialchars($config['brand']) ?></h1>
        <p>Enter your admin token to continue.</p>
        <?php if (!empty($_SESSION['login_error'])): ?>
            <div class="err"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>
        <input type="password" name="token" placeholder="Admin token" autofocus>
        <button type="submit">Sign in</button>
    </form>
</body>
</html>
