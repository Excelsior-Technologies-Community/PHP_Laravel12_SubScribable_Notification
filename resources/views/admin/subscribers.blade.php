<!DOCTYPE html>
<html>

<head>

    <title>
        Subscriber Dashboard
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <style>
        body {

            background: #f1f5f9;

        }

        .dashboard-card {

            border: none;

            border-radius: 20px;

        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div
            class="card dashboard-card shadow-sm p-4 mb-4">

            <h2>

                Subscriber Dashboard

            </h2>


            <p class="mb-0">

                Total Subscribers:

                <strong>

                    {{ $totalSubscribers }}

                </strong>

            </p>

        </div>



        <form
            class="mb-4">

            <div class="row">

                <div class="col-md-9">

                    <input
                        type="text"

                        name="search"

                        class="form-control"

                        placeholder="Search by name or email"

                        value="{{request('search')}}">

                </div>


                <div class="col-md-3">

                    <button
                        class="btn btn-success w-100">

                        Search

                    </button>

                </div>

            </div>

        </form>



        <div class="card shadow-sm">

            <div class="card-body">

                <table
                    class="table table-hover">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Name</th>

                            <th>Email</th>

                            <th>Frequency</th>

                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                        $subscribers
                        as $subscriber
                        )

                        <tr>

                            <td>

                                {{$subscriber->id}}

                            </td>


                            <td>

                                {{$subscriber->name}}

                            </td>


                            <td>

                                {{$subscriber->email}}

                            </td>


                            <td>

                                {{ucfirst($subscriber->frequency)}}

                            </td>


                            <td>

                                @if(
                                $subscriber
                                ->unsubscribed_at
                                )

                                <span
                                    class="badge bg-danger">

                                    Unsubscribed

                                </span>

                                @else

                                <span
                                    class="badge bg-success">

                                    Active

                                </span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5">

                                No subscribers found

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>


                <div
                    class="mt-3">

                    {{ $subscribers->links() }}

                </div>


            </div>

        </div>

    </div>

</body>

</html>