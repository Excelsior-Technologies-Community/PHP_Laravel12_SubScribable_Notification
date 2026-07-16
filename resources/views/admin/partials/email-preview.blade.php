<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:20px;background:{{ $preview['style']['bg'] }};font-family:Arial,sans-serif">
<div style="max-width:500px;margin:0 auto;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden">
    <div style="background:{{ $preview['style']['accent'] }};padding:24px;text-align:center">
        <div style="color:#fff;font-size:18px;font-weight:bold">{{ $preview['subject'] }}</div>
    </div>
    <div style="padding:24px;background:#fff">
        <div style="font-size:16px;font-weight:bold;margin-bottom:12px;color:#1e293b">{{ $preview['greeting'] }}</div>
        <div style="color:#475569;line-height:1.7;margin-bottom:20px">{{ $preview['body'] }}</div>
        <div style="text-align:center">
            <a href="{{ $preview['cta_url'] }}" style="display:inline-block;background:{{ $preview['style']['accent'] }};color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold">
                {{ $preview['cta'] }}
            </a>
        </div>
    </div>
    <div style="background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8">
        TrendyKart Newsletter &bull; <a href="#" style="color:#94a3b8">Unsubscribe</a>
    </div>
</div>
</body>
</html>
