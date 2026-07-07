@extends('layouts.app')
@section('title', 'Scanner QR Deportasi')

@section('content')
<div class="scanner-page">
    <div class="scanner-header">
        <h1 class="scanner-title">Scanner QR Deportasi</h1>
        <p class="scanner-sub">Imbas kod QR tiket deportasi untuk validasi boarding. Tiket deportasi adalah tiket terbuka tanpa tarikh luput.</p>
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
                    <p class="scanner-placeholder-text">Tekan <strong>Mulakan Kamera</strong> untuk imbas</p>
                </div>
            </div>
            <div class="scanner-controls">
                <button id="startScanBtn" class="scanner-btn scanner-btn-primary" onclick="startScanner()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Mulakan Kamera
                </button>
                <button id="stopScanBtn" class="scanner-btn scanner-btn-danger" onclick="stopScanner()" style="display:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><rect x="6" y="6" width="12" height="12"/></svg>
                    Hentikan Kamera
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
                    Imbas Tiket Lain
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.scanner-page { padding: 24px 0; }
.scanner-header { margin-bottom: 24px; }
.scanner-title { font-size: 24px; font-weight: 700; }
.scanner-sub { color: #6b7280; margin-top: 4px; }
.scanner-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
.scanner-camera-section { display: flex; flex-direction: column; gap: 12px; }
.scanner-camera-wrapper {
    position: relative; width: 100%; max-width: 420px; margin: 0 auto;
    border-radius: 16px; overflow: hidden; background: #000;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.scanner-camera-wrapper video { width: 100%; height: auto; display: block; }
.scanner-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; }
.scanner-frame {
    width: 180px; height: 180px;
    border: 3px solid rgba(234,88,12,0.8);
    border-radius: 16px;
    box-shadow: 0 0 0 4px rgba(234,88,12,0.15), 0 0 30px rgba(234,88,12,0.1);
}
.scanner-placeholder {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: rgba(255,255,255,0.6); gap: 12px;
}
.scanner-placeholder-icon { width: 56px; height: 56px; opacity: 0.5; }
.scanner-placeholder-text { font-size: 0.9rem; text-align: center; padding: 0 20px; }
.scanner-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 18px; border-radius: 10px; font-size: 0.85rem;
    font-weight: 600; border: none; cursor: pointer; transition: all 0.15s;
}
.scanner-btn-primary { background: #EA580C; color: #fff; }
.scanner-btn-primary:hover { background: #C2410C; }
.scanner-btn-danger { background: #DC2626; color: #fff; }
.scanner-btn-danger:hover { background: #B91C1C; }
.scanner-btn-outline {
    background: #fff; color: #374151;
    border: 1.5px solid #d1d5db;
}
.scanner-btn-outline:hover { background: #f9fafb; border-color: #9ca3af; }
.scanner-controls { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 4px; justify-content: center; }
.scanner-status { font-size: 0.82rem; color: #6b7280; }
.scanner-result-section { display: flex; flex-direction: column; gap: 14px; }
.scanner-result-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    padding: 28px 24px; text-align: center;
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
        document.getElementById('scannerStatus').textContent = 'Mengimbas...';
        document.getElementById('scannerStatus').style.color = '#EA580C';
        isScanning = true;
        requestAnimationFrame(scanLoop);
    } catch (err) {
        document.getElementById('scannerStatus').textContent = 'Capaian kamera dinafikan.';
        document.getElementById('scannerStatus').style.color = '#DC2626';
    }
}

function stopScanner() {
    if (videoStream) { videoStream.getTracks().forEach(track => track.stop()); videoStream = null; }
    const video = document.getElementById('scannerVideo');
    video.srcObject = null;
    document.getElementById('cameraPlaceholder').style.display = 'flex';
    document.getElementById('startScanBtn').style.display = 'inline-flex';
    document.getElementById('stopScanBtn').style.display = 'none';
    document.getElementById('scannerStatus').textContent = 'Kamera dihentikan.';
    document.getElementById('scannerStatus').style.color = '#6b7280';
    isScanning = false;
}

function scanLoop() {
    if (!videoStream || !isScanning) return;
    const video = document.getElementById('scannerVideo');
    if (video.readyState !== video.HAVE_ENOUGH_DATA) { requestAnimationFrame(scanLoop); return; }
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, canvas.width, canvas.height);
    if (code) { processScan(code.data); return; }
    requestAnimationFrame(scanLoop);
}

function processScan(qrData) {
    isScanning = false;
    stopScanner();
    document.getElementById('scannerStatus').textContent = 'Memproses...';
    document.getElementById('scannerStatus').style.color = '#6b7280';
    document.getElementById('scanResult').style.display = 'none';

    fetch('{{ route("deportation.scan") }}', {
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
            title.textContent = 'Boarding Deportasi Diluluskan';
            title.style.color = '#059669';
            body.innerHTML = '<table>' +
                '<tr><td>Penumpang</td><td><strong>' + data.passenger_name + '</strong></td></tr>' +
                '<tr><td>Tiket</td><td>' + data.ticket_number + '</td></tr>' +
                '<tr><td>Kelas</td><td>' + data.ticket_class + '</td></tr>' +
                '<tr><td>Jenis</td><td>' + data.passenger_type + '</td></tr>' +
                '<tr><td>Titik</td><td>' + (data.shelter_point || '—') + '</td></tr>' +
                '<tr><td>Laluan</td><td>' + (data.route || '—') + '</td></tr>' +
                '<tr><td>Kapal</td><td>' + (data.vessel || '—') + '</td></tr>' +
                '<tr><td>Status</td><td><span style="color:#059669;font-weight:700;">BOARDED</span></td></tr>' +
                '</table>';
            againBtn.style.display = 'block';
        } else {
            icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" style="width:48px;height:48px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            title.textContent = (data.message || 'Tiket Tidak Sah');
            title.style.color = '#DC2626';
            body.innerHTML = '<p style="color:#DC2626;text-align:center;padding:8px 0;font-size:0.95rem;">' + (data.message || 'Tiket deportasi tidak sah.') + '</p>';
            if (data.boarded_at) {
                body.innerHTML += '<p style="color:#64748b;text-align:center;font-size:0.85rem;">Telah digunakan pada: ' + data.boarded_at + '</p>';
            }
            againBtn.style.display = 'block';
        }
        againBtn.textContent = 'Imbas Tiket Lain';
    })
    .catch(err => {
        document.getElementById('scanResult').style.display = 'block';
        document.getElementById('scanResultIcon').innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" style="width:48px;height:48px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
        document.getElementById('scanResultTitle').textContent = 'Ralat';
        document.getElementById('scanResultTitle').style.color = '#D97706';
        document.getElementById('scanResultBody').innerHTML = '<p style="color:#D97706;text-align:center;">Sila cuba lagi.</p>';
        document.getElementById('scanAgainBtn').style.display = 'block';
    });
}

function resetAndScan() {
    document.getElementById('scanResult').style.display = 'none';
    document.getElementById('scanAgainBtn').style.display = 'none';
    startScanner();
}

document.getElementById('scanResult').style.display = 'none';
</script>
@endsection
