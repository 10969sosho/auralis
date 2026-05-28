@extends('layouts.app')
@section('title', 'Boarding Scanner')

@section('content')
<div class="scanner-page">
    <div class="scanner-layout">
        <div class="card scanner-card">
            <div class="scanner-header">
                <h2 class="text-lg font-semibold">Scan QR Code</h2>
                <div class="scanner-controls">
                    <button id="switchCameraBtn" class="btn btn-sm btn-outline" type="button" title="Switch Camera">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                    </button>
                    <button id="torchBtn" class="btn btn-sm btn-outline" type="button" title="Flashlight">
                        <svg id="torchIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6L6 18M6 6l12 12"/>
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="scanner-viewfinder">
                <div id="scanner-container">
                    <video id="scanner-video" autoplay playsinline></video>
                    <div class="scanner-overlay">
                        <div class="scanner-frame"></div>
                    </div>
                </div>
                <p id="scan-status" class="scan-status">Position QR code in front of camera</p>
            </div>
        </div>

        <div class="card manual-card">
            <h2 class="text-lg font-semibold">Manual Input</h2>
            <p class="text-sm text-gray-500 mt-1">Enter booking code if QR is damaged</p>
            <form id="manual-form" class="mt-4 space-y-4">
                @csrf
                <div class="form-group">
                    <label for="booking_code" class="form-label">Booking Code</label>
                    <input type="text" id="booking_code" name="booking_code" placeholder="e.g. BK-20260101-ABCDE" class="form-input" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Validate</button>
            </form>
        </div>
    </div>

    <div id="result-container" class="result-overlay" style="display:none">
        <div class="result-dialog" id="resultDialog">
            <div class="result-icon" id="resultIcon"></div>
            <h3 id="resultTitle" class="result-title"></h3>
            <p id="resultMessage" class="result-message"></p>
            <div id="resultDetails" class="result-details"></div>
            <button onclick="resetScan()" class="btn btn-primary btn-block" id="scanNextBtn">Scan Next</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
(function() {
    const video = document.getElementById('scanner-video');
    const resultContainer = document.getElementById('result-container');
    const resultDialog = document.getElementById('resultDialog');
    const resultIcon = document.getElementById('resultIcon');
    const resultTitle = document.getElementById('resultTitle');
    const resultMessage = document.getElementById('resultMessage');
    const resultDetails = document.getElementById('resultDetails');
    const scanStatus = document.getElementById('scan-status');
    const torchBtn = document.getElementById('torchBtn');
    const torchIcon = document.getElementById('torchIcon');
    const switchCameraBtn = document.getElementById('switchCameraBtn');
    const scanNextBtn = document.getElementById('scanNextBtn');

    let scanning = true;
    let scanTimer = null;
    let currentStream = null;
    let currentFacingMode = 'environment';
    let torchOn = false;
    let scanCooldown = false;

    async function startCamera(facingMode) {
        stopCamera();
        scanStatus.textContent = 'Starting camera...';
        try {
            const constraints = {
                video: {
                    facingMode: facingMode,
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                }
            };
            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = currentStream;

            torchBtn.style.display = facingMode === 'environment' ? 'inline-flex' : 'none';

            await video.play();
            scanStatus.textContent = 'Position QR code in front of camera';
            scanning = true;
            scanLoop();
        } catch (e) {
            scanStatus.textContent = 'Camera not available. Use manual input below.';
            torchBtn.style.display = 'none';
        }
    }

    function stopCamera() {
        scanning = false;
        if (scanTimer) {
            cancelAnimationFrame(scanTimer);
            scanTimer = null;
        }
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
    }

    let frameCount = 0;
    function scanLoop() {
        if (!scanning) return;
        scanTimer = requestAnimationFrame(scanLoop);

        frameCount++;
        if (frameCount % 3 !== 0) return;

        if (video.readyState !== video.HAVE_ENOUGH_DATA) return;
        if (video.videoWidth === 0 || video.videoHeight === 0) return;

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'dontInvert'
        });

        if (code && !scanCooldown) {
            scanning = false;
            scanCooldown = true;
            scanStatus.textContent = 'QR detected! Processing...';
            validateQR(code.data);
            return;
        }
    }

    async function toggleTorch() {
        if (!currentStream) return;
        const track = currentStream.getVideoTracks()[0];
        if (!track) return;

        try {
            await track.applyConstraints({
                advanced: [{ torch: !torchOn }]
            });
            torchOn = !torchOn;
            if (torchOn) {
                torchIcon.innerHTML = '<circle cx="12" cy="12" r="5"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M17.66 6.34l1.41-1.41"/>';
            } else {
                torchIcon.innerHTML = '<path d="M18 6L6 18M6 6l12 12"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>';
            }
        } catch (e) {
            // Torch not supported
        }
    }

    async function switchCamera() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        torchOn = false;
        torchIcon.innerHTML = '<path d="M18 6L6 18M6 6l12 12"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>';
        await startCamera(currentFacingMode);
    }

    async function validateQR(qrData) {
        try {
            const res = await fetch('{{ route("boarding.scan") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ qr_data: qrData })
            });
            const data = await res.json();
            showResult(data);
        } catch (e) {
            showResult({ status: 'invalid', message: 'Network error. Check connection.' });
        }
    }

    function showResult(data) {
        const isSuccess = data.status === 'valid';

        resultIcon.className = 'result-icon ' + (isSuccess ? 'result-icon-success' : 'result-icon-error');
        resultIcon.innerHTML = isSuccess
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

        resultTitle.textContent = isSuccess ? 'Boarding Success' : data.status.toUpperCase();
        resultTitle.className = 'result-title ' + (isSuccess ? 'text-success' : 'text-error');
        resultMessage.textContent = data.message;

        let detailsHtml = '';
        if (data.passenger) {
            detailsHtml += '<div class="detail-row"><span class="detail-label">Passenger</span><span class="detail-value">' + escapeHtml(data.passenger.name) + '</span></div>';
            detailsHtml += '<div class="detail-row"><span class="detail-label">Class</span><span class="detail-value capitalize">' + escapeHtml(data.passenger.ticket_class) + '</span></div>';
            detailsHtml += '<div class="detail-row"><span class="detail-label">Type</span><span class="detail-value capitalize">' + escapeHtml(data.passenger.passenger_type) + '</span></div>';
        }
        if (data.schedule) {
            detailsHtml += '<div class="detail-divider"></div>';
            detailsHtml += '<div class="detail-row"><span class="detail-label">Vessel</span><span class="detail-value">' + escapeHtml(data.schedule.vessel) + '</span></div>';
            detailsHtml += '<div class="detail-row"><span class="detail-label">Route</span><span class="detail-value">' + escapeHtml(data.schedule.route) + '</span></div>';
        }
        resultDetails.innerHTML = detailsHtml;

        resultContainer.style.display = 'flex';
        resultDialog.className = 'result-dialog ' + (isSuccess ? 'result-success' : 'result-error');

        if (isSuccess) {
            scanNextBtn.textContent = 'Scan Next Passenger';
        } else {
            scanNextBtn.textContent = 'Try Again';
        }

        stopCamera();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function resetScan() {
        scanCooldown = false;
        resultContainer.style.display = 'none';
        scanStatus.textContent = 'Position QR code in front of camera';
        startCamera(currentFacingMode);
    }

    torchBtn.addEventListener('click', toggleTorch);
    switchCameraBtn.addEventListener('click', switchCamera);

    document.getElementById('manual-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (scanCooldown) return;
        scanCooldown = true;

        const code = document.getElementById('booking_code').value.trim();
        if (!code) {
            scanCooldown = false;
            return;
        }

        scanStatus.textContent = 'Validating...';
        stopCamera();

        try {
            const res = await fetch('{{ route("boarding.manual-validate") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ booking_code: code })
            });
            const data = await res.json();
            showResult(data);
        } catch (e) {
            showResult({ status: 'invalid', message: 'Network error.' });
        }
    });

    startCamera(currentFacingMode);

    window.resetScan = resetScan;
})();
</script>
@endsection
