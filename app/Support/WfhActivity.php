<?php

namespace App\Support;

use Illuminate\Support\Str;

class WfhActivity
{
    public static function browserName(?string $userAgent): string
    {
        $userAgent = (string) $userAgent;

        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Microsoft Edge',
            str_contains($userAgent, 'OPR/'), str_contains($userAgent, 'Opera/') => 'Opera',
            str_contains($userAgent, 'Chrome/'), str_contains($userAgent, 'CriOS/') => 'Google Chrome',
            str_contains($userAgent, 'Firefox/'), str_contains($userAgent, 'FxiOS/') => 'Mozilla Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Web browser',
        };
    }

    public static function pageLabel(?string $title, ?string $url): string
    {
        $path = trim((string) parse_url((string) $url, PHP_URL_PATH), '/');
        $knownPages = [
            'home' => 'HRIS Home',
            'daily-time-record' => 'Daily Time Record',
            'filing-and-approval' => 'Filing and Approval',
            'downloadable-forms' => 'Downloadable Forms',
            'employee-management/wfh-monitoring' => 'WFH Monitoring',
        ];

        foreach ($knownPages as $route => $label) {
            if ($path === $route || str_contains($path, $route)) {
                return "Opened {$label}";
            }
        }

        $cleanTitle = trim((string) preg_replace('/\s*[-|]\s*JJWC HRIS.*$/i', '', (string) $title));

        if ($cleanTitle !== '' && ! str_contains(strtolower($cleanTitle), 'jjwc hris')) {
            return 'Opened '.Str::limit($cleanTitle, 80, '');
        }

        if ($path !== '') {
            return 'Opened '.Str::headline(basename($path));
        }

        return 'Opened JJWC HRIS';
    }

    public static function pageKey(?string $url): string
    {
        $parts = parse_url((string) $url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = rtrim((string) ($parts['path'] ?? '/'), '/') ?: '/';

        return $host.$path;
    }
}
