<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Crypt;

class MailSettings
{
    /** @return array{host: string, port: int, username: string, password: string, scheme: string, from_address: string, from_name: string} */
    public function get(): array
    {
        return [
            'host' => Setting::get('smtp_host', (string) config('mail.mailers.smtp.host')) ?? '',
            'port' => (int) (Setting::get('smtp_port', (string) config('mail.mailers.smtp.port')) ?? 587),
            'username' => Setting::get('smtp_username', (string) config('mail.mailers.smtp.username')) ?? '',
            'password' => $this->decryptPassword(Setting::get('smtp_password')),
            'scheme' => Setting::get('smtp_scheme', (string) config('mail.mailers.smtp.scheme')) ?? '',
            'from_address' => Setting::get('smtp_from_address', (string) config('mail.from.address')) ?? '',
            'from_name' => Setting::get('smtp_from_name', (string) config('mail.from.name')) ?? '',
        ];
    }

    /** @param array{host: string, port: int, username: string, password?: string, scheme: string, from_address: string, from_name: string} $settings */
    public function save(array $settings): void
    {
        Setting::set('smtp_host', $settings['host']);
        Setting::set('smtp_port', (string) $settings['port']);
        Setting::set('smtp_username', $settings['username']);
        Setting::set('smtp_scheme', $settings['scheme']);
        Setting::set('smtp_from_address', $settings['from_address']);
        Setting::set('smtp_from_name', $settings['from_name']);

        if (filled($settings['password'] ?? null)) {
            Setting::set('smtp_password', Crypt::encryptString($settings['password']));
        }

        $this->apply();
    }

    public function apply(): void
    {
        $settings = $this->get();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $settings['host'],
            'mail.mailers.smtp.port' => $settings['port'],
            'mail.mailers.smtp.username' => $settings['username'] !== '' ? $settings['username'] : null,
            'mail.mailers.smtp.password' => $settings['password'] !== '' ? $settings['password'] : null,
            'mail.mailers.smtp.scheme' => $settings['scheme'] === 'smtps' ? 'smtps' : null,
            'mail.from.address' => $settings['from_address'],
            'mail.from.name' => $settings['from_name'],
        ]);

        app(MailManager::class)->purge('smtp');
    }

    private function decryptPassword(?string $password): string
    {
        if (blank($password)) {
            return '';
        }

        try {
            return Crypt::decryptString($password);
        } catch (DecryptException) {
            return '';
        }
    }
}
