<!DOCTYPE html>
<html>

<head>
    <title>Subscribe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: #f1f5f9; /* light background */
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            color: #1e293b;
        }

        .card {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            width: 350px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #0f172a;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #cbd5f5;
            border-radius: 8px;
            outline: none;
            background: #f8fafc;
            color: #1e293b;
        }

        input::placeholder {
            color: #64748b;
        }

        input:focus {
            border-color: #22c55e;
            background: #ffffff;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #22c55e;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #16a34a;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Subscribe</h2>

        <form method="POST" action="{{ route('subscribe') }}">
            @csrf

            <input type="text" name="name" placeholder="Enter your name" required>
            <input type="email" name="email" placeholder="Enter your email" required>

            <button type="submit">Subscribe Now</button>
        </form>
    </div>

</body>
</html>