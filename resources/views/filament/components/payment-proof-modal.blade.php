<div class="p-2">
    @if($payment && $payment->proof_of_transfer)
        @php
            $path = $payment->proof_of_transfer;
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        @endphp
        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $path) }}" alt="Proof of Transfer" style="max-width:100%;max-height:500px;border-radius:8px;border:1px solid #e5e7eb;">
            </div>
        @else
            <div class="text-center py-8">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;color:#9ca3af;margin:0 auto 12px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Proof of Transfer
                </a>
                <p class="text-sm text-gray-400 mt-3">Format: {{ strtoupper($ext) }}</p>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;color:#9ca3af;margin:0 auto 12px;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <p class="text-gray-400 text-sm">No proof of transfer uploaded yet.</p>
        </div>
    @endif
</div>
