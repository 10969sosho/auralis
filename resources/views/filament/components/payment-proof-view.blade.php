<div class="p-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">Proof of Transfer</label>
    @if($getRecord() && $getRecord()->proof_of_transfer)
        @php
            $path = $getRecord()->proof_of_transfer;
            $ext = pathinfo($path, PATHINFO_EXTENSION);
        @endphp
        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
            <img src="{{ asset('storage/' . $path) }}" alt="Proof of Transfer" style="max-width:400px;border-radius:8px;border:1px solid #e5e7eb;padding:4px;">
        @else
            <a href="{{ asset('storage/' . $path) }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                &#128196; View Proof of Transfer ({{ strtoupper($ext) }})
            </a>
        @endif
    @else
        <p class="text-gray-400 text-sm italic">No proof of transfer uploaded.</p>
    @endif
</div>
