<!DOCTYPE html>
<html>
<head>
    <title>Subscribe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border-radius: 20px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,.4); }
        .btn-subscribe { background: #22c55e; border: none; font-weight: bold; }
        .btn-subscribe:hover { background: #16a34a; }
        .list-check .form-check { background: #f1f5f9; border-radius: 10px; padding: 10px 15px; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="container" style="max-width:460px">
    <div class="card p-4 mt-5">
        <h4 class="fw-bold mb-1 text-center">📬 Subscribe</h4>
        <p class="text-muted text-center small mb-3">Choose your mailing lists</p>

        @if($errors->any())
            <div class="alert alert-danger py-2">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('subscribe') }}">
            @csrf
            <input type="text" name="name" class="form-control mb-3" placeholder="Your Name" value="{{ old('name') }}" required>
            <input type="email" name="email" class="form-control mb-3" placeholder="Your Email" value="{{ old('email') }}" required>

            <select name="frequency" class="form-select mb-3" required>
                <option value="">Select Frequency</option>
                <option value="daily"   {{ old('frequency')=='daily'   ? 'selected' : '' }}>Daily</option>
                <option value="weekly"  {{ old('frequency','weekly')=='weekly'  ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ old('frequency')=='monthly' ? 'selected' : '' }}>Monthly</option>
            </select>

            <label class="form-label fw-semibold">Select Mailing Lists</label>
            <div class="list-check mb-3">
                @foreach(['newsletter' => '📰 Newsletter', 'offers' => '🎁 Offers & Deals', 'updates' => '🔔 Product Updates'] as $val => $label)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="lists[]" value="{{ $val }}" id="list_{{ $val }}"
                        {{ in_array($val, old('lists', ['newsletter'])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="list_{{ $val }}">{{ $label }}</label>
                </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-subscribe text-white w-100 py-2">Subscribe Now</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('admin.subscribers') }}" class="text-muted small">Admin Dashboard →</a>
        </div>
    </div>
</div>
</body>
</html>
