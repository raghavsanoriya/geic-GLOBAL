<?php

namespace App\Support;

use App\Models\CrmIntegrationSetting;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Throwable;

class CrmProviderTest
{
    public function verify(CrmIntegrationSetting $setting): bool
    {
        if (! filled($setting->secret)) {
            return false;
        }
        try {
            if ($setting->provider === 'onesignal') {
                $id = $setting->configuration['app_id'];
                $response = Http::acceptJson()->withHeaders(['Authorization' => 'Key '.$setting->secret])->connectTimeout(3)->timeout(6)
                    ->withoutRedirecting()->get('https://api.onesignal.com/apps/'.$id, ['view' => 'config']);

                return $response->successful() && $response->json('id') === $id;
            }
            if ($setting->provider === 'email') {
                $transport = $this->mailer($setting)->getSymfonyTransport();
                try {
                    $transport->start();

                    return true;
                } finally {
                    $transport->stop();
                }
            }
        } catch (Throwable) {
            return false;
        }

        return false;
    }

    public function mailer(CrmIntegrationSetting $setting): Mailer
    {
        $config = $setting->configuration;
        $addresses = $this->resolveAddresses($config['host']);
        if ($addresses === [] || collect($addresses)->contains(fn (string $ip) => str_starts_with(mb_strtolower($ip), '::ffff:') || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))) {
            throw new RuntimeException('The mail host must resolve only to public addresses.');
        }
        $mailer = Mail::build(['transport' => 'smtp', 'scheme' => $config['scheme'], 'host' => $config['host'], 'port' => $config['port'],
            'username' => $config['username'], 'password' => $setting->secret, 'timeout' => 6, 'require_tls' => true, 'verify_peer' => true]);
        $stream = $mailer->getSymfonyTransport()->getStream();
        if (! $stream instanceof SocketStream) {
            throw new RuntimeException('Unsupported mail transport.');
        }
        $stream->setHost($addresses[0]);
        $stream->setStreamOptions(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => $config['host'], 'SNI_enabled' => true, 'SNI_server_name' => $config['host']]]);

        return $mailer;
    }

    protected function resolveAddresses(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }
        $records = dns_get_record($host, DNS_A | DNS_AAAA);

        return array_values(array_unique(array_filter(array_map(fn (array $record) => $record['ip'] ?? $record['ipv6'] ?? null, $records ?: []))));
    }
}
