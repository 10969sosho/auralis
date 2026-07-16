<x-filament-panels::page>
    <div class="payment-settings-page">

        {{-- Form Card --}}
        <div class="payment-settings-card">
            <div class="payment-settings-card-header">
                <div class="payment-settings-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div>
                    <h2 class="payment-settings-card-title">QR Code Configuration</h2>
                    <p class="payment-settings-card-desc">Configure QR payment display for buyers</p>
                </div>
            </div>
            <div class="payment-settings-card-body">
                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="payment-settings-actions">
                        <x-filament::button type="submit" color="primary" class="payment-settings-btn">
                            {{ __('Save Settings') }}
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Preview Card --}}
        <div class="payment-settings-card">
            <div class="payment-settings-card-header">
                <div class="payment-settings-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                </div>
                <div>
                    <h2 class="payment-settings-card-title">Buyer Preview</h2>
                    <p class="payment-settings-card-desc">This QR code will appear on the buyer's payment page.</p>
                </div>
            </div>
            <div class="payment-settings-card-body payment-settings-preview-body">
                @php
                    $qrPath = is_array($this->payment_qr_image) ? ($this->payment_qr_image[0] ?? null) : $this->payment_qr_image;
                @endphp
                <div class="payment-settings-preview">
                    @if($qrPath)
                        <div class="payment-settings-qr-wrap">
                            <img src="{{ asset('storage/' . $qrPath) }}" alt="QR Preview" class="payment-settings-qr-img">
                        </div>
                    @else
                        <div class="payment-settings-qr-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:40px;height:40px;color:#9ca3af;margin-bottom:10px;"><rect x="2" y="2" width="8" height="8"/><rect x="14" y="2" width="8" height="8"/><rect x="14" y="14" width="8" height="8"/><line x1="6" y1="18" x2="6" y2="22"/><line x1="18" y1="6" x2="22" y2="6"/></svg>
                            <p class="payment-settings-qr-empty-text">No QR code uploaded</p>
                            <p class="payment-settings-qr-empty-hint">Upload a QR payment code above to display to buyers</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <style>
        .payment-settings-page {
            max-width: 720px;
            margin: 0 auto;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .payment-settings-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .payment-settings-card-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 20px 24px 0;
        }

        .payment-settings-card-icon {
            width: 40px;
            height: 40px;
            background: #eff6ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #2563eb;
        }
        .payment-settings-card-icon svg {
            width: 22px;
            height: 22px;
        }

        .payment-settings-card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 4px;
        }

        .payment-settings-card-desc {
            font-size: 0.85rem;
            color: #6b7280;
            margin: 0;
        }

        .payment-settings-card-body {
            padding: 20px 24px 24px;
        }

        .payment-settings-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }

        .payment-settings-btn {
            padding: 8px 28px;
            font-weight: 600;
        }

        .payment-settings-preview-body {
            display: flex;
            justify-content: center;
            padding: 32px 24px;
        }

        .payment-settings-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .payment-settings-qr-wrap {
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: border-color 0.2s;
        }
        .payment-settings-qr-wrap:hover {
            border-color: #93c5fd;
        }

        .payment-settings-qr-img {
            width: 200px;
            height: 200px;
            object-fit: contain;
            display: block;
        }

        .payment-settings-qr-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 40px;
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 14px;
            text-align: center;
        }

        .payment-settings-qr-empty-text {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin: 0 0 4px;
        }

        .payment-settings-qr-empty-hint {
            font-size: 0.85rem;
            color: #9ca3af;
            margin: 0;
            max-width: 280px;
        }
    </style>
</x-filament-panels::page>
