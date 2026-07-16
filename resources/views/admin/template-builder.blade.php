<!DOCTYPE html>
<html>
<head>
    <title>Template Builder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        .nav-admin a { color: #64748b; text-decoration: none; padding: 8px 16px; border-radius: 8px; }
        .nav-admin a:hover, .nav-admin a.active { background: #f97316; color: #fff; }
        #preview-frame { border: none; width: 100%; height: 500px; border-radius: 12px; background: #fff; }
        .builder-panel { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
        .template-thumb { cursor: pointer; border: 3px solid transparent; border-radius: 10px; padding: 10px; transition: .2s; }
        .template-thumb:hover, .template-thumb.selected { border-color: #f97316; }
    </style>
</head>
<body>
<div class="container-fluid py-4 px-4">

    <div class="d-flex gap-2 nav-admin mb-4 flex-wrap">
        <a href="{{ route('admin.subscribers') }}">📊 Dashboard</a>
        <a href="{{ route('admin.analytics') }}">📈 Analytics</a>
        <a href="{{ route('admin.template-builder') }}" class="active">🎨 Template Builder</a>
        <a href="{{ url('/') }}" class="ms-auto">+ New Subscriber</a>
    </div>

    <h4 class="fw-bold mb-4">🎨 Live Template Builder</h4>

    <div class="row g-4">
        {{-- Builder Panel --}}
        <div class="col-md-5">
            <div class="builder-panel">
                <form method="POST" action="{{ route('admin.template-builder.preview') }}" id="builderForm">
                    @csrf

                    <label class="form-label fw-semibold">Email Subject</label>
                    <input type="text" name="subject" class="form-control mb-3" placeholder="Your Newsletter Subject"
                        value="{{ $preview['subject'] ?? '' }}" oninput="livePreview()">

                    <label class="form-label fw-semibold">Greeting</label>
                    <input type="text" name="greeting" class="form-control mb-3" placeholder="Hello Subscriber!"
                        value="{{ $preview['greeting'] ?? '' }}" oninput="livePreview()">

                    <label class="form-label fw-semibold">Email Body</label>
                    <textarea name="body" class="form-control mb-3" rows="5" placeholder="Write your email content here..."
                        oninput="livePreview()">{{ $preview['body'] ?? '' }}</textarea>

                    <label class="form-label fw-semibold">CTA Button Text</label>
                    <input type="text" name="cta" class="form-control mb-3" placeholder="Visit Now"
                        value="{{ $preview['cta'] ?? '' }}" oninput="livePreview()">

                    <label class="form-label fw-semibold">CTA URL</label>
                    <input type="url" name="cta_url" class="form-control mb-3" placeholder="https://example.com"
                        value="{{ $preview['cta_url'] ?? '' }}" oninput="livePreview()">

                    <label class="form-label fw-semibold">Choose Template</label>
                    <div class="row g-2 mb-3">
                        @foreach($templates as $key => $tmpl)
                        <div class="col-6">
                            <div class="template-thumb {{ ($preview['template'] ?? 'default') === $key ? 'selected' : '' }}"
                                onclick="selectTemplate('{{ $key }}')"
                                style="background: {{ $tmpl['bg'] }}; border-color: {{ ($preview['template'] ?? 'default') === $key ? $tmpl['accent'] : 'transparent' }}">
                                <div style="height:8px; background:{{ $tmpl['accent'] }}; border-radius:4px; margin-bottom:6px"></div>
                                <div style="font-size:12px; color:#64748b">{{ $tmpl['name'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="template" id="templateInput" value="{{ $preview['template'] ?? 'default' }}">

                    <button type="submit" class="btn w-100 text-white fw-bold" style="background:#f97316">
                        🔄 Generate Preview
                    </button>
                </form>
            </div>
        </div>

        {{-- Live Preview --}}
        <div class="col-md-7">
            <div class="builder-panel p-0 overflow-hidden">
                <div class="p-3 border-bottom d-flex align-items-center gap-2">
                    <span class="fw-bold">📧 Live Preview</span>
                    @if($preview)
                        <span class="badge bg-success ms-auto">Preview Ready</span>
                    @endif
                </div>

                @if($preview)
                <iframe id="preview-frame" srcdoc="{{ htmlspecialchars(view('admin.partials.email-preview', ['preview' => $preview])->render()) }}"></iframe>
                @else
                <div id="live-preview-area" style="padding:30px; min-height:500px; background:#fff; border-radius:0 0 12px 12px">
                    <div style="max-width:500px; margin:0 auto; font-family:Arial,sans-serif; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden">
                        <div id="prev-header" style="background:#22c55e; padding:24px; text-align:center">
                            <div id="prev-subject" style="color:#fff; font-size:18px; font-weight:bold">Your Newsletter Subject</div>
                        </div>
                        <div style="padding:24px">
                            <div id="prev-greeting" style="font-size:16px; font-weight:bold; margin-bottom:12px; color:#1e293b">Hello Subscriber!</div>
                            <div id="prev-body" style="color:#475569; line-height:1.7; margin-bottom:20px">Write your email content here...</div>
                            <div style="text-align:center">
                                <a id="prev-cta" href="#" style="display:inline-block; background:#22c55e; color:#fff; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:bold">Visit Now</a>
                            </div>
                        </div>
                        <div style="background:#f8fafc; padding:16px; text-align:center; font-size:12px; color:#94a3b8">
                            TrendyKart Newsletter &bull; <a href="#" style="color:#94a3b8">Unsubscribe</a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
const templates = @json($templates);

function selectTemplate(key) {
    document.getElementById('templateInput').value = key;
    document.querySelectorAll('.template-thumb').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    updatePreviewColors(key);
}

function updatePreviewColors(key) {
    const t = templates[key];
    if (!t) return;
    const header = document.getElementById('prev-header');
    const cta    = document.getElementById('prev-cta');
    if (header) header.style.background = t.accent;
    if (cta)    { cta.style.background = t.accent; }
    const area = document.getElementById('live-preview-area');
    if (area) area.style.background = t.bg;
}

function livePreview() {
    const subject  = document.querySelector('[name=subject]')?.value  || 'Your Newsletter Subject';
    const greeting = document.querySelector('[name=greeting]')?.value || 'Hello Subscriber!';
    const body     = document.querySelector('[name=body]')?.value     || 'Write your email content here...';
    const cta      = document.querySelector('[name=cta]')?.value      || 'Visit Now';

    const el = (id) => document.getElementById(id);
    if (el('prev-subject'))  el('prev-subject').textContent  = subject;
    if (el('prev-greeting')) el('prev-greeting').textContent = greeting;
    if (el('prev-body'))     el('prev-body').textContent     = body;
    if (el('prev-cta'))      el('prev-cta').textContent      = cta;
}
</script>
</body>
</html>
