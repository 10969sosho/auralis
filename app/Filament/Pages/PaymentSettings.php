<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public array | string | null $payment_qr_image = null;

    public function getView(): string
    {
        return 'filament.pages.payment-settings';
    }

    public function getTitle(): string
    {
        return 'Payment QR Code Settings';
    }

    public static function getNavigationLabel(): string
    {
        return 'Payment QR';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-qr-code';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public function mount(): void
    {
        $this->payment_qr_image = Setting::getValue('payment_qr_image');
        $this->form->fill([
            'payment_qr_image' => $this->payment_qr_image,
        ]);
    }

    public function updatedPaymentQrImage(): void
    {
        // Normalize array from FileUpload to string for preview
        if (is_array($this->payment_qr_image)) {
            $this->payment_qr_image = $this->payment_qr_image[0] ?? null;
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('QR Code Pembayaran')
                    ->description('Upload QR code yang akan ditampilkan ke buyer saat pembayaran. Format: JPG/PNG. Maks 2MB.')
                    ->schema([
                        FileUpload::make('payment_qr_image')
                            ->label('QR Code Image')
                            ->image()
                            ->maxSize(2048)
                            ->directory('settings/qr')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->previewable(true)
                            ->imagePreviewHeight(200)
                            ->helperText('QR code ini akan muncul di halaman pembayaran buyer dengan masa berlaku mengikuti batas waktu booking.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Only update QR image if a new file was uploaded (not null/empty)
        $qrImage = $data['payment_qr_image'] ?? null;
        if ($qrImage !== null && $qrImage !== '' && !(is_array($qrImage) && empty($qrImage))) {
            $path = is_array($qrImage) ? ($qrImage[0] ?? null) : $qrImage;
            if ($path) {
                Setting::setValue('payment_qr_image', $path);
                $this->payment_qr_image = $path;
            }
        }

        Notification::make()
            ->title('Payment settings updated successfully')
            ->success()
            ->send();
    }
}
