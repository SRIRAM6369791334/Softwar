<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link rel="stylesheet" href="/css/hud.css">
    <style>
        body {
            background: #0d1117;
            color: #c9d1d9;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #161b22;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #30363d;
            width: 100%;
            max-width: 320px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h1 { margin-bottom: 2rem; color: #2f81f7; }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background: #0d1117;
            border: 1px solid #30363d;
            color: white;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #238636;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        .error {
            color: #da3633;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h1>Staff Portal</h1>
    
    <?php if(isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form action="/employee/login" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login to Portal</button>
    </form>
    
    <div style="margin-top: 20px; font-size: 0.8rem; color: #8b949e;">
        <a href="/login" style="color: #58a6ff; text-decoration: none;">Admin Login</a>
    </div>
</div>

</body>
</html>
