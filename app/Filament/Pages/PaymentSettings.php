<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public $payment_qr_image = [];

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
        $path = Setting::getValue('payment_qr_image');
        $this->payment_qr_image = $path ? [$path] : [];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('QR Code Pembayaran (Manual Transfer)')
                    ->description('Upload QR code yang akan ditampilkan ke buyer saat pembayaran manual transfer. Format: JPG/PNG. Maks 2MB.')
                    ->schema([
                        FileUpload::make('payment_qr_image')
                            ->label('QR Code Image')
                            ->image()
                            ->maxSize(2048)
                            ->directory('settings/qr')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->previewable(true)
                            ->imagePreviewHeight(200)
                            ->helperText('QR code ini akan muncul di halaman pembayaran buyer.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $qrImage = $data['payment_qr_image'] ?? null;
        if ($qrImage !== null && $qrImage !== '' && ! (is_array($qrImage) && empty($qrImage))) {
            $path = is_array($qrImage) ? ($qrImage[0] ?? null) : $qrImage;
            if ($path) {
                Setting::setValue('payment_qr_image', $path);
                $this->payment_qr_image = [$path];
            }
        } else {
            // Cleared upload — actually remove the QR so admins can delete it from the UI
            Setting::setValue('payment_qr_image', null);
            $this->payment_qr_image = [];
        }

        Notification::make()
            ->title('Payment QR settings updated successfully')
            ->success()
            ->send();
    }
}
