<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @keyframes slideInRight {
            from {
                transform: translateX(60px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            background: #f5f6f8;
        }

        /* LEFT SIDE (LOGO) */
        .left {
            flex: 1;
            background: #555555;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .left img {
            max-width: 300px;
            opacity: 0.9;
        }

        /* RIGHT SIDE (POS card) with Africa background */
        .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('/images/africa.png') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        /* overlay for readability */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(245, 246, 248, 0.85);
        }

        .pos-card {
            position: relative;
            background: #ffffff;
            padding: 40px;
            width: 380px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            z-index: 1;
            text-align: center;
        }

        .pos-card h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .pos-card p {
            margin-bottom: 20px;
            font-size: 16px;
        }

        .pos-card a, .pos-card button {
            display: block;
            margin: 12px 0;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .pos-card a.btn-primary {
            background: #008bbb;
            color: white;
        }

        .pos-card a.btn-primary:hover {
            background: #006f94;
        }

        .pos-card a.btn-secondary,
        .pos-card button.btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
        }

        .pos-card a.btn-secondary:hover,
        .pos-card button.btn-secondary:hover {
            background: #565e64;
        }

        .pos-card form button {
            width: 100%;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .left {
                height: 35vh;
            }

            .right {
                height: 65vh;
            }
        }
    </style>
</head>
<body>

    <!-- LEFT: LOGO -->
    <div class="left">
        <img src="/images/little.png" alt="Little Logo">
    </div>

    <!-- RIGHT: POS CARD with Africa background -->
    <div class="right">
        <!-- overlay for readability -->
        <div class="overlay"></div>

        <div class="pos-card">
            <h2><strong>Little POS System</strong></h2>

            @auth
                <p>Welcome back, <strong>{{ auth()->user()->name }}</strong></p>

                <a href="{{ url('/home') }}" class="btn-primary">
                    Go to Home
                </a>    <!-- reference to dashboard -->

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <p><em>Please login to continue</em></p>

                <a href="{{ url('/login') }}" class="btn-primary">
                    Login
                </a>

                <a href="{{ url('/register') }}" class="btn-secondary">
                    Register
                </a>
            @endguest
        </div>
    </div>

</body>
</html>
