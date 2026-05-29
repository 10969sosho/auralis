@extends('layouts.app')
@section('title', 'Boarding Scanner')

@section('content')
<div class="scanner-page">
    <div class="scanner-header">
        <h1 class="scanner-title">Boarding QR Scanner</h1>
        <p class="scanner-sub">Scan passenger ticket QR code for boarding validation</p>
    </div>

    <div class="scanner-layout">
        <div class="scanner-camera-section">
            <div class="scanner-camera-wrapper" id="cameraWrapper">
                <video id="scannerVideo" playsinline autoplay></video>
                <div id="scannerOverlay" class="scanner-overlay">
                    <div class="scanner-frame"></div>
                </div>
                <div class="scanner-placeholder" id="cameraPlaceholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="scanner-placeholder-icon"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <p class="scanner-placeholder-text">Press <strong>Start Camera</strong> to begin scanning</p>
                </div>
            </div>
            <div class="scanner-controls">
                <button id="startScanBtn" class="scanner-btn scanner-btn-primary" onclick="startScanner()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Start Camera
                </button>
                <button id="stopScanBtn" class="scanner-btn scanner-btn-danger" onclick="stopScanner()" style="display:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><rect x="6" y="6" width="12" height="12"/></svg>
                    Stop Camera
                </button>
                <span id="scannerStatus" class="scanner-status"></span>
            </div>
        </div>

        <div class="scanner-result-section">
            <div class="scanner-result-card" id="scanResult" style="display:none;">
                <div class="scanner-result-icon-wrap" id="scanResultIconWrap">
                    <span id="scanResultIcon" class="scanner-result-icon"></span>
                </div>
                <h3 id="scanResultTitle" class="scanner-result-title"></h3>
                <div id="scanResultBody" class="scanner-result-body"></div>
                <button id="scanAgainBtn" class="scanner-btn scanner-btn-outline" style="display:none;margin-top:14px;width:100%;" onclick="resetAndScan()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="23,4 23,10 17,10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Scan Another Ticket
                </button>
            </div>

            <div class="scanner-manual-section">
                <h3 class="scanner-manual-title">Manual Validation</h3>
                <form id="manualForm" onsubmit="manualValidate(event)" class="scanner-manual-form">
                    @csrf
                    <input type="text" id="manualCode" placeholder="Enter ticket number or booking code..." class="scanner-manual-input">
                    <button type="submit" class="scanner-btn scanner-btn-primary" style="width:100%;justify-content:center;">Validate</button>
                </form>
                <div id="manualResult" class="mt-3" style="display:none;"></div>
            </div>

            @if(request('schedule_id'))
                @php $schedule = \App\Models\Schedule::find(request('schedule_id')); @endphp
                @if($schedule)
                    <a href="{{ route('boarding.manifest', $schedule) }}" class="scanner-manifest-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        View Manifest: {{ $schedule->vessel->name }} — {{ $schedule->departure_time->format('d M Y') }}
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.scanner-page {
    padding: 24px 0;
}

/* Header */
.scanner-header { margin-bottom: 24px; }
.scanner-title { font-size: 24px; font-weight: 700; }
.scanner-sub { color: #6b7280; margin-top: 4px; }

/* Layout */
.scanner-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
.scanner-camera-section { display: flex; flex-direction: column; gap: 12px; }

/* Camera Wrapper */
.scanner-camera-wrapper {
    position: relative;
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
    border-radius: 16px;
    overflow: hidden;
    background: #000;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.scanner-camera-wrapper video { width: 100%; height: auto; display: block; }
.scanner-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; }
.scanner-frame {
    width: 180px; height: 180px;
    border: 3px solid rgba(34,197,94,0.8);
    border-radius: 16px;
    box-shadow: 0 0 0 4px rgba(34,197,94,0.15), 0 0 30px rgba(34,197,94,0.1);
}

/* Camera placeholder */
.scanner-placeholder {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: rgba(255,255,255,0.6);
    gap: 12px;
}
.scanner-placeholder-icon { width: 56px; height: 56px; opacity: 0.5; }
.scanner-placeholder-text { font-size: 0.9rem; text-align: center; padding: 0 20px; }

/* Buttons */
.scanner-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.15s;
}
.scanner-btn-primary { background: #2563EB; color: #fff; }
.scanner-btn-primary:hover { background: #1D4ED8; }
.scanner-btn-danger {
    background: #DC2626;
    color: #fff;
}
.scanner-btn-danger:hover { background: #B91C1C; }
.scanner-btn-outline {
    background: #fff;
    color: #374151;
    border: 1.5px solid #d1d5db;
}
.scanner-btn-outline:hover { background: #f9fafb; border-color: #9ca3af; }

/* Controls */
.scanner-controls { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 4px; justify-content: center; }
.scanner-status { font-size: 0.82rem; color: #6b7280; }

/* Result Section */
.scanner-result-section { display: flex; flex-direction: column; gap: 14px; }
.scanner-result-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    padding: 28px 24px;
    text-align: center;
    border: 1px solid #e5e7eb;
}
.scanner-result-icon-wrap { margin-bottom: 8px; }
.scanner-result-icon { font-size: 44px; line-height: 1; }
.scanner-result-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 12px; }
.scanner-result-body { font-size: 0.9rem; text-align: left; }
.scanner-result-body table { width: 100%; }
.scanner-result-body td { padding: 8px 4px; font-size: 0.85rem; border-bottom: 1px solid #f3f4f6; }
.scanner-result-body td:first-child { color: #6b7280; font-weight: 500; width: 35%; white-space: nowrap; }
.scanner-result-body td:last-child { font-weight: 600; }

/* Manual */
.scanner-manual-section {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
}
.scanner-manual-title { font-size: 0.95rem; font-weight: 700; margin-bottom: 12px; }
.scanner-manual-form { display: flex; flex-direction: column; gap: 10px; }
.scanner-manual-input {
    padding: 10px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.85rem;
    outline: none;
    width: 100%;
    transition: border-color 0.15s;
}
.scanner-manual-input:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }

.scanner-manifest-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 18px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    color: #2563EB;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s;
}
.scanner-manifest-link:hover { background: #EFF6FF; border-color: #93C5FD; }

@media (max-width: 768px) {
    .scanner-layout { grid-template-columns: 1fr; }
    .scanner-camera-wrapper { max-width: 100%; }
    .scanner-frame { width: 140px; height: 140px; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let videoStream = null;
let isScanning = false;

async function startScanner() {
    try {
        const video = document.getElementById('scannerVideo');
        videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 480 } } });
        video.srcObject = videoStream;
        video.play();

        document.getElementById('cameraPlaceholder').style.display = 'none';
        document.getElementById('startScanBtn').style.display = 'none';
        document.getElementById('stopScanBtn').style.display = 'inline-flex';
        document.getElementById('scannerStatus').textContent = 'Scanning...';
        document.getElementById('scannerStatus').style.color = '#2563EB';
        isScanning = true;

        requestAnimationFrame(scanLoop);
    } catch (err) {
        document.getElementById('scannerStatus').textContent = 'Camera access denied or unavailable.';
        document.getElementById('scannerStatus').style.color = '#DC2626';
    }
}

function stopScanner() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
    const video = document.getElementById('scannerVideo');
    video.srcObject = null;
    document.getElementById('cameraPlaceholder').style.display = 'flex';
    document.getElementById('startScanBtn').style.display = 'inline-flex';
    document.getElementById('stopScanBtn').style.display = 'none';
    document.getElementById('scannerStatus').textContent = 'Camera stopped.';
    document.getElementById('scannerStatus').style.color = '#6b7280';
    isScanning = false;
}

function scanLoop() {
    if (!videoStream || !isScanning) return;

    const video = document.getElementById('scannerVideo');
    if (video.readyState !== video.HAVE_ENOUGH_DATA) {
        requestAnimationFrame(scanLoop);
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, canvas.width, canvas.height);

    if (code) {
        processScan(code.data);
        return;
    }

    requestAnimationFrame(scanLoop);
}

function manualValidate(e) {
    e.preventDefault();
    const code = document.getElementById('manualCode').value.trim();
    if (!code) return;
    processScan(code);
}

function processScan(qrData) {
    isScanning = false;
    stopScanner();
    document.getElementById('scannerStatus').textContent = 'Processing...';
    document.getElementById('scannerStatus').style.color = '#6b7280';

    document.getElementById('scanResult').style.display = 'none';

    fetch('{{ route("boarding.scan") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ qr_data: qrData }),
    })
    .then(r => r.json())
    .then(data => {
        const result = document.getElementById('scanResult');
        const icon = document.getElementById('scanResultIcon');
        const title = document.getElementById('scanResultTitle');
        const body = document.getElementById('scanResultBody');
        const againBtn = document.getElementById('scanAgainBtn');

        result.style.display = 'block';

        if (data.success) {
            icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" style="width:48px;height:48px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
            title.textContent = '✓ Boarding Approved';
            title.style.color = '#059669';
            body.innerHTML = '<table>' +
                '<tr><td>Passenger</td><td><strong>' + data.passenger_name + '</strong></td></tr>' +
                '<tr><td>Ticket</td><td>' + data.ticket_number + '</td></tr>' +
                '<tr><td>Class</td><td>' + data.ticket_class + '</td></tr>' +
                '<tr><td>Type</td><td>' + data.passenger_type + '</td></tr>' +
                '<tr><td>Route</td><td>' + (data.route || '—') + '</td></tr>' +
                '<tr><td>Departure</td><td>' + (data.departure || '—') + '</td></tr>' +
                '<tr><td>Status</td><td><span style="color:#059669;font-weight:700;">BOARDED</span></td></tr>' +
                '</table>';
            againBtn.style.display = 'block';
        } else {
            icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" style="width:48px;height:48px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            title.textContent = '✕ ' + (data.message || 'Invalid Ticket');
            title.style.color = '#DC2626';
            body.innerHTML = '<p style="color:#DC2626;text-align:center;padding:8px 0;font-size:0.95rem;">' + (data.message || 'Ticket not recognized.') + '</p>';
            againBtn.style.display = 'block';
        }

        againBtn.textContent = 'Scan Another Ticket';
    })
    .catch(err => {
        document.getElementById('scanResult').style.display = 'block';
        document.getElementById('scanResultIcon').innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" style="width:48px;height:48px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
        document.getElementById('scanResultTitle').textContent = 'Connection Error';
        document.getElementById('scanResultTitle').style.color = '#D97706';
        document.getElementById('scanResultBody').innerHTML = '<p style="color:#D97706;text-align:center;">Check your connection and try again.</p>';
        document.getElementById('scanAgainBtn').style.display = 'block';
    });
}

function resetAndScan() {
    document.getElementById('scanResult').style.display = 'none';
    document.getElementById('scanAgainBtn').style.display = 'none';
    document.getElementById('manualCode').value = '';
    document.getElementById('manualResult').style.display = 'none';
    startScanner();
}

document.getElementById('scanResult').style.display = 'none';
</script>
@endsection
