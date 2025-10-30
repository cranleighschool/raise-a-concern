<?php

namespace App\Domains\RaiseAConcern\Enums;

use App\Domains\RaiseAConcern\Actions\ScrapeWebsitePolicies;
use Illuminate\Support\Facades\Cache;

enum SafeguardingLink
{
    case SeniorSchool;
    case PrepSchool;

    private function getKey(): string
    {
        return match ($this) {
            self::SeniorSchool => 'cs',
            self::PrepSchool => 'cps',
        };
    }

    public function getUrl(): string
    {
        $domain = match ($this) {
            self::SeniorSchool => 'cranleigh.org',
            self::PrepSchool => 'cranprep.org',
        };

        // Allow Override using config/env files
        if (config("services.policies.{$this->getKey()}", 'unkonwn')) {
            return config("services.policies.{$this->getKey()}", 'unkonwn');
        }
Cache::forget('safeguarding_policy_link_'.$domain);
        // Use caching to avoid repeated requests for the same domain.
        return Cache::remember(
            'safeguarding_policy_link_'.$domain,
            now()->addWeek(),
            fn () => $this->fetchPolicyLink($domain)
        );
    }

    private function fetchPolicyLink(string $domain): string
    {
        return '';
        $scraper = new ScrapeWebsitePolicies($domain);

        // Search for the safeguarding policy link in the HTML content.
        $link = $scraper->findDownloadLink('safeguarding policy');

        if ($link === null) {
            throw new \RuntimeException("Safeguarding policy link not found for domain: {$domain}");
        }

        return $link;
    }
}
