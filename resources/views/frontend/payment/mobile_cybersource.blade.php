<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .loading-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        #cybersource_form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="spinner"></div>
        <h2>Processing Payment</h2>
        <p>Please wait while we redirect you to the payment gateway...</p>
        
        <form id="cybersource_form" action="{{ paymentSetting('cybersource.api').'/pay' }}" method="post">
            @foreach($formData as $key => $value)
                <input type="hidden" id="{{ $key }}" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                document.getElementById('cybersource_form').submit();
            }, 500);
        };
    </script>
</body>
</html>

