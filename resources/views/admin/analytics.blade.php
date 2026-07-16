<!DOCTYPE html>
<html>
<head>
    <title>Analytics Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f1f5f9; }
        .stat-card { border-radius: 16px; border: none; }
        .nav-admin a { color: #64748b; text-decoration: none; padding: 8px 16px; border-radius: 8px; }
        .nav-admin a:hover, .nav-admin a.active { background: #6366f1; color: #fff; }
        .rate-bar { height: 8px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container py-4">

    <div class="d-flex gap-2 nav-admin mb-4 flex-wrap">
        <a href="{{ route('admin.subscribers') }}">📊 Dashboard</a>
        <a href="{{ route('admin.analytics') }}" class="active">📈 Analytics</a>
        <a href="{{ route('admin.template-builder') }}">🎨 Template Builder</a>
        <a href="{{ url('/') }}" class="ms-auto">+ New Subscriber</a>
    </div>

    <h4 class="fw-bold mb-4">📈 Analytics & Tracking Dashboard</h4>

    {{-- Overall Stats --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['📨', 'Emails Sent',    $totalSent,      'secondary'],
            ['✅', 'Delivered',      $totalDelivered, 'success'],
            ['👁️', 'Opened',         $totalOpened,    'primary'],
            ['🖱️', 'Clicked',        $totalClicked,   'warning'],
        ] as [$icon, $label, $val, $color])
        <div class="col-md-3">
            <div class="card stat-card shadow-sm p-3 text-center">
                <div class="fs-2">{{ $icon }}</div>
                <div class="fs-2 fw-bold text-{{ $color }}">{{ $val }}</div>
                <div class="text-muted small">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Rate Cards --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Delivery Rate', $deliveryRate, 'success'],
            ['Open Rate',     $openRate,     'primary'],
            ['Click Rate',    $clickRate,    'warning'],
        ] as [$label, $rate, $color])
        <div class="col-md-4">
            <div class="card stat-card shadow-sm p-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold">{{ $label }}</span>
                    <span class="fw-bold text-{{ $color }}">{{ $rate }}%</span>
                </div>
                <div class="progress rate-bar">
                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $rate }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Per List Analytics --}}
    <div class="card shadow-sm p-4 mb-4">
        <h6 class="fw-bold mb-3">📋 Per Mailing List Breakdown</h6>
        <div class="row g-3">
            @foreach($listAnalytics as $la)
            <div class="col-md-4">
                <div class="card border p-3">
                    <div class="fw-bold text-capitalize mb-2">
                        {{ $la['list'] === 'newsletter' ? '📰' : ($la['list'] === 'offers' ? '🎁' : '🔔') }}
                        {{ ucfirst($la['list']) }}
                    </div>
                    <div class="small text-muted">Sent: <strong>{{ $la['sent'] }}</strong></div>
                    <div class="small text-muted">Opened: <strong>{{ $la['opened'] }}</strong> ({{ $la['open_rate'] }}%)</div>
                    <div class="small text-muted">Clicked: <strong>{{ $la['clicked'] }}</strong> ({{ $la['click_rate'] }}%)</div>
                    <div class="mt-2">
                        <div class="progress mb-1" style="height:6px">
                            <div class="progress-bar bg-primary" style="width:{{ $la['open_rate'] }}%"></div>
                        </div>
                        <div class="progress" style="height:6px">
                            <div class="progress-bar bg-warning" style="width:{{ $la['click_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Chart --}}
    <div class="card shadow-sm p-4 mb-4">
        <h6 class="fw-bold mb-3">📊 Delivery vs Open vs Click</h6>
        <canvas id="analyticsChart" height="80"></canvas>
    </div>

    {{-- Tracking Logs Table --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold">📋 Email Tracking Logs</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Subscriber</th><th>List</th><th>Subject</th>
                        <th>Delivered</th><th>Opened</th><th>Clicked</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->subscriber->name ?? '-' }}</td>
                        <td><span class="badge bg-info">{{ $log->mailing_list }}</span></td>
                        <td>{{ Str::limit($log->subject, 30) }}</td>
                        <td>{{ $log->delivered_at ? '✅ ' . $log->delivered_at->format('M d H:i') : '—' }}</td>
                        <td>{{ $log->opened_at    ? '👁️ ' . $log->opened_at->format('M d H:i')    : '—' }}</td>
                        <td>{{ $log->clicked_at   ? '🖱️ ' . $log->clicked_at->format('M d H:i')   : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No tracking data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $logs->links() }}</div>
</div>

<script>
new Chart(document.getElementById('analyticsChart'), {
    type: 'doughnut',
    data: {
        labels: ['Delivered', 'Opened', 'Clicked'],
        datasets: [{
            data: [{{ $totalDelivered }}, {{ $totalOpened }}, {{ $totalClicked }}],
            backgroundColor: ['#22c55e', '#6366f1', '#f59e0b'],
            borderWidth: 0,
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});
</script>
</body>
</html>
