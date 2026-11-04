<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            animation: checkmark 0.5s ease-out 0.3s both;
            stroke-dasharray: 100;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        @keyframes checkmark {
            from { stroke-dashoffset: 100; }
            to { stroke-dashoffset: 0; }
        }
        h1 {
            color: #10b981;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .order-id {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 20px;
        }
        .label {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg viewBox="0 0 52 52">
                <path d="M14 27l8 8 16-16"/>
            </svg>
        </div>
        <h1>Payment Successful!</h1>
        <p>Your order has been confirmed</p>
        <div class="order-id">
            <div class="label">Order ID</div>
            <div>{{ $orderId }}</div>
        </div>
    </div>
</body>
</html>

