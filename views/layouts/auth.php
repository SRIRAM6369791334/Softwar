<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.O.S. - Login</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: rgba(22, 27, 34, 0.7);
            --text-color: #c9d1d9;
            --accent-color: #00f3ff; /* Cyan Neon */
            --error-color: #ff0055;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Consolas', 'Monaco', monospace; /* Technical font */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            background-image: 
                linear-gradient(rgba(0, 243, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 243, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .auth-container {
            width: 350px;
            padding: 2rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0, 243, 255, 0.1);
            position: relative;
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
        }

        h1 {
            text-align: center;
            font-size: 1.5rem;
            color: var(--accent-color);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
            text-shadow: 0 0 10px rgba(0, 243, 255, 0.5);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8b949e;
        }

        input {
            width: 100%;
            padding: 10px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            color: #fff;
            border-radius: 4px;
            box-sizing: border-box; /* Fix padding issue */
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: inherit;
        }

        input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 8px rgba(0, 243, 255, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background: rgba(0, 243, 255, 0.1);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }

        button:hover {
            background: var(--accent-color);
            color: #000;
            box-shadow: 0 0 15px var(--accent-color);
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--error-color);
            color: var(--error-color);
            background: rgba(255, 0, 85, 0.1);
            font-size: 0.9rem;
            text-align: center;
        }

        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #484f58;
        }
    </style>
</head>
<body>
    <?= $content ?>
</body>
</html>
