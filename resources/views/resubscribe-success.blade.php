<!DOCTYPE html>
<html>
<head>
    <title>Re-subscribed!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;}</style>
</head>
<body>
<div class="card p-5 text-center shadow" style="border-radius:20px;max-width:400px">
    <div class="fs-1 mb-2">🎉</div>
    <h4 class="fw-bold text-success">Welcome Back, {{ $subscriber->name }}!</h4>
    <p class="text-muted">You have successfully re-subscribed to all mailing lists.</p>
    <a href="{{ url('/') }}" class="btn btn-success mt-2">Go Home</a>
</div>
</body>
</html>
