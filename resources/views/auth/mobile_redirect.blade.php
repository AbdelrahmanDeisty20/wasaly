<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري فتح التطبيق...</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        h1 { font-size: 24px; margin-bottom: 10px; }
        p { font-size: 16px; opacity: 0.9; margin-bottom: 30px; }
        a {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
        }
        .hidden { display: none; }
    </style>
    <script>
        window.onload = function() {
            window.location.href = "{{ $appUrl }}";
            setTimeout(function() {
                document.getElementById('manual-link').classList.remove('hidden');
            }, 2000);
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>جاري فتح التطبيق...</h1>
        <p>يرجى الانتظار بينما نحولك إلى تطبيق وصّلي</p>
        <a href="{{ $appUrl }}" id="manual-link" class="hidden">اضغط هنا لفتح التطبيق</a>
    </div>
</body>
</html>
