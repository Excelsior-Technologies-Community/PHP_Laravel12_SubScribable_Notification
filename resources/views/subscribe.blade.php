<!DOCTYPE html>
<html>

<head>
    <title>Subscribe</title>

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f1f5f9;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 400px;

            background: #fff;

            padding: 35px;

            border-radius: 20px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #1e293b;
        }

        input,
        select {

            width: 100%;
            padding: 14px;

            margin-bottom: 15px;

            border: 1px solid #cbd5e1;

            border-radius: 10px;

            outline: none;

            background: #f8fafc;
        }

        input:focus,
        select:focus {

            border-color: #22c55e;

        }

        button {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 10px;

            background: #22c55e;

            color: white;

            cursor: pointer;

            font-weight: bold;

            transition: .3s;
        }

        button:hover {

            background: #16a34a;

        }

        .error {

            color: red;
            margin-bottom: 10px;
            font-size: 14px;

        }
    </style>
</head>

<body>

    <div class="card">

        <h2>

            Subscribe Newsletter

        </h2>

        @if($errors->any())

        <div class="error">

            @foreach($errors->all() as $error)

            <div>

                {{ $error }}

            </div>

            @endforeach

        </div>

        @endif


        <form method="POST"
            action="{{route('subscribe')}}">

            @csrf

            <input
                type="text"
                name="name"
                placeholder="Enter Name"
                value="{{old('name')}}"
                required>


            <input
                type="email"
                name="email"
                placeholder="Enter Email"
                value="{{old('email')}}"
                required>



            <select
                name="frequency"
                required>

                <option value="">

                    Select Notification Frequency

                </option>


                <option
                    value="daily">

                    Daily

                </option>


                <option
                    value="weekly"
                    selected>

                    Weekly

                </option>


                <option
                    value="monthly">

                    Monthly

                </option>

            </select>


            <button>

                Subscribe Now

            </button>

        </form>

    </div>

</body>

</html>