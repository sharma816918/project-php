
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Online Quiz Management System</title>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: 'Poppins', sans-serif;

            background-color: #071a35;
            background-image:
                linear-gradient(
                    rgba(5, 18, 42, 0.55),
                    rgba(5, 18, 42, 0.55)
                ),
                url('image/background.jpg');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            width: 100%;
            max-width: 850px;
            padding: 55px 30px;
            text-align: center;
            color: white;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: clamp(26px, 5vw, 42px);
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .highlight {
            color: #8dc5ff;
        }

        .description {
            font-size: 17px;
            margin-bottom: 35px;
            line-height: 1.8;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .buttons a {
            display: inline-block;
            min-width: 165px;
            padding: 14px 28px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            background: #2456d8;
            transition: 0.3s;
        }

        .buttons a:last-child {
            background: #4488f5;
        }

        .buttons a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .tagline {
            margin-top: 28px;
            font-size: 13px;
            color: #e0eaff;
        }

        @media (max-width: 500px) {
            .container {
                padding: 35px 20px;
            }

            .buttons {
                flex-direction: column;
            }

            .buttons a {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>
            Online Quiz
            <span class="highlight">Management System</span>
        </h1>

        <p class="description">
            Test your knowledge, challenge yourself, and learn something new!
        </p>

        <div class="buttons">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>

        <p class="tagline">Learn • Practice • Improve</p>

    </div>
</body>
</html>
