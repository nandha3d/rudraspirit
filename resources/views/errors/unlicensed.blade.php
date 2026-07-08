<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('License required') }}</title>
    <style>
        body { margin:0; font:16px/1.6 system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
               background:#0f1216; color:#e7ecf2; display:flex; min-height:100vh;
               align-items:center; justify-content:center; }
        .box { max-width:520px; text-align:center; padding:40px 32px; background:#181d24;
               border:1px solid #28303a; border-radius:14px; }
        h1 { color:#c8a24a; margin:0 0 12px; }
        code { background:#0b0e12; padding:2px 8px; border-radius:6px; color:#c8a24a; }
        p { color:#93a1b3; }
    </style>
</head>
<body>
    <div class="box">
        <h1>{{ translate('License required') }}</h1>
        <p>{{ translate('This deployment is not licensed, so the admin panel is locked. The storefront is unaffected.') }}</p>
        <p>{{ translate('Status') }}: <code>{{ $status }}</code></p>
        <p>{{ translate('Set a valid LICENSE_KEY (and license server details) in your environment, then reload.') }}</p>
    </div>
</body>
</html>
