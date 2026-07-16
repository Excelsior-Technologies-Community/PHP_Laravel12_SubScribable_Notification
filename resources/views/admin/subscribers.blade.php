<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f1f5f9; }
        .stat-card { border-radius: 16px; border: none; }
        .nav-admin a { color: #64748b; text-decoration: none; padding: 8px 16px; border-radius: 8px; }
        .nav-admin a:hover, .nav-admin a.active { background: #22c55e; color: #fff; }
        .badge-list { font-size: 12px; }
    </style>
</head>
<body>
<div class="container py-4">

    {{-- Nav --}}
    <div class="d-flex gap-2 nav-admin mb-4 flex-wrap">
        <a href="{{ route('admin.subscribers') }}" class="active">📊 Dashboard</a>
        <a href="{{ route('admin.analytics') }}">📈 Analytics</a>
        <a href="{{ route('admin.template-builder') }}">🎨 Template Builder</a>
        <a href="{{ url('/') }}" class="ms-auto">+ New Subscriber</a>
    </div>

    <h4 class="fw-bold mb-4">Subscriber Dashboard</h4>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center">
                <div class="fs-1 fw-bold text-success">{{ $total }}</div>
                <div class="text-muted small">Total Subscribers</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center">
                <div class="fs-1 fw-bold text-primary">{{ $active }}</div>
                <div class="text-muted small">Active</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center">
                <div class="fs-1 fw-bold text-danger">{{ $unsubscribed }}</div>
                <div class="text-muted small">Unsubscribed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center">
                <div class="fs-1 fw-bold text-warning">{{ $total > 0 ? round(($active/$total)*100) : 0 }}%</div>
                <div class="text-muted small">Retention Rate</div>
            </div>
        </div>
    </div>

    {{-- Mailing List Counts --}}
    <div class="row g-3 mb-4">
        @foreach(['newsletter' => ['📰','info'], 'offers' => ['🎁','warning'], 'updates' => ['🔔','secondary']] as $list => [$icon, $color])
        <div class="col-md-4">
            <div class="card stat-card shadow-sm p-3 d-flex flex-row align-items-center gap-3">
                <div class="fs-2">{{ $icon }}</div>
                <div>
                    <div class="fw-bold fs-4 text-{{ $color }}">{{ $listCounts[$list] }}</div>
                    <div class="text-muted small">{{ ucfirst($list) }} subscribers</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Chart --}}
    <div class="card shadow-sm p-4 mb-4">
        <h6 class="fw-bold mb-3">📅 New Subscribers (Last 7 Days)</h6>
        <canvas id="dailyChart" height="80"></canvas>
    </div>

    {{-- Search + Table --}}
    <form class="mb-3">
        <div class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100">Search</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Name</th><th>Email</th><th>Frequency</th><th>Lists</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->email }}</td>
                        <td>{{ ucfirst($s->frequency) }}</td>
                        <td>
                            @foreach($s->subscribed_lists ?? [] as $list)
                                <span class="badge bg-info badge-list">{{ $list }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($s->unsubscribed_at)
                                <span class="badge bg-danger">Unsubscribed</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            @if($s->unsubscribed_at)
                                <a href="{{ route('resubscribe', $s) }}" class="btn btn-sm btn-outline-success">Re-subscribe</a>
                            @else
                                <a href="{{ route('unsubscribe', $s) }}" class="btn btn-sm btn-outline-danger">Unsubscribe</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No subscribers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $subscribers->links() }}</div>
</div>

<script>
new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($dailyStats->pluck('date')) !!},
        datasets: [{
            label: 'New Subscribers',
            data: {!! json_encode($dailyStats->pluck('count')) !!},
            backgroundColor: '#22c55e',
            borderRadius: 6,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
</body>
</html>
