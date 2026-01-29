<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LITTLE POS - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #008bbb, #00bcd4);
            color: white;
            text-align: center;
            position: relative;
        }

        h1 {
            font-size: 8rem;
            font-weight: 900;
            letter-spacing: 0.3em;
            text-shadow: 3px 3px 15px rgba(0,0,0,0.3);
            margin-bottom: 50px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .logout-btn button {
            background: #ff4d4f;
            border: none;
            padding: 10px 16px;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
            transition: background 0.3s;
        }

        .logout-btn button:hover {
            background: #e04346;
        }

        .next-btn {
            background: #ffffff;
            color: #008bbb;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.2rem;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            transition: background 0.3s, color 0.3s;
        }

        .next-btn:hover {
            background: #008bbb;
            color: #ffffff;
        }

         .next-btn:hover {
            background: #008bbb;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 4rem;
                letter-spacing: 0.2em;
            }
            .logout-btn button {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            .next-btn {
                font-size: 1rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

    {{-- Logout Button --}}
    @auth
    <div class="logout-btn">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
    @endauth

    {{-- Big Text --}}
    <h1>LITTLE POS</h1>

    {{-- Next Page Button --}}
    <a href="{{ route('users.index') }}" class="next-btn">Go to Users</a>
    <a href="{{ route('products.index') }}" class="next-btn">Go to Products</a>

</body>
</html>
