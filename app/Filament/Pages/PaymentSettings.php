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

    public $payment_qr_image = [];
    public ?string $toyibpay_secret_key = null;
    public ?string $toyibpay_category_code = null;

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
        $this->toyibpay_secret_key = Setting::getValue('toyibpay_secret_key', config('toyibpay.secret_key'));
        $this->toyibpay_category_code = Setting::getValue('toyibpay_category_code', config('toyibpay.category_code'));
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('ToyibPay Gateway')
                    ->description('Konfigurasi payment gateway ToyibPay. Secret key didapat dari dashboard ToyibPay.')
                    ->schema([
                        \Filament\Schemas\Components\TextInput::make('toyibpay_secret_key')
                            ->label('ToyibPay Secret Key')
                            ->password()
                            ->revealable()
                            ->helperText('Masukkan User Secret Key dari akun ToyibPay Anda.'),
                        \Filament\Schemas\Components\TextInput::make('toyibpay_category_code')
                            ->label('ToyibPay Category Code')
                            ->helperText('Category code dibuat via API ToyibPay (Create Category).'),
                    ]),
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
                            ->helperText('QR code ini akan muncul di halaman pembayaran buyer dengan masa berlaku mengikuti batas waktu booking.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save ToyibPay settings
        if (isset($data['toyibpay_secret_key'])) {
            Setting::setValue('toyibpay_secret_key', $data['toyibpay_secret_key']);
        }
        if (isset($data['toyibpay_category_code'])) {
            Setting::setValue('toyibpay_category_code', $data['toyibpay_category_code']);
        }

        $qrImage = $data['payment_qr_image'] ?? null;
        if ($qrImage !== null && $qrImage !== '' && !(is_array($qrImage) && empty($qrImage))) {
            $path = is_array($qrImage) ? ($qrImage[0] ?? null) : $qrImage;
            if ($path) {
                Setting::setValue('payment_qr_image', $path);
                // Keep as array for FileUpload compatibility
                $this->payment_qr_image = [$path];
            }
        }

        Notification::make()
            ->title('Payment settings updated successfully')
            ->success()
            ->send();
    }
}
