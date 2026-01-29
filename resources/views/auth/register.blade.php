<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Little</title>

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

        /* RIGHT SIDE (REGISTER) with Africa background */
        .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            /* background image */
            background: url('/images/africa.png') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        /* semi-transparent overlay to make text readable */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(245, 246, 248, 0.85);
        }

        .register-card {
            position: relative;
            background: #ffffff;
            padding: 40px;
            width: 360px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            z-index: 1; /* keep on top of overlay */
        }

        .register-card h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        /* ERROR MESSAGE */
        .error {
            background: #ffecec;
            color: #b00000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

        /* INPUTS */
        .register-card input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }

        .register-card input:focus {
            outline: none;
            border-color: #f0b400;
        }

        /* BUTTON */
        .register-card button {
            width: 100%;
            padding: 12px;
            background: #f0b400;
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .register-card button:hover {
            background: #d9a200;
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

    <!-- RIGHT: REGISTER with background -->
    <div class="right">
        <!-- overlay for readability -->
        <div class="overlay"></div>

        <div class="register-card">
            <h2>Create Account</h2>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ url('/register') }}">
                @csrf

                <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

                <button type="submit">Register</button>
            </form>
        </div>
    </div>

</body>
</html>
