<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;}</style>
</head>
<body>
<div class="card p-5 text-center shadow" style="border-radius:20px;max-width:400px">
    <div class="fs-1 mb-2">😢</div>
    <h4 class="fw-bold text-danger">You Unsubscribed</h4>
    <p class="text-muted">We're sad to see you go, {{ $subscriber->name ?? '' }}.</p>
    @isset($subscriber)
    <a href="{{ route('resubscribe', $subscriber) }}" class="btn btn-outline-success mt-2">Re-subscribe</a>
    @endisset
    <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-2 ms-2">Go Home</a>
</div>
</body>
</html>
