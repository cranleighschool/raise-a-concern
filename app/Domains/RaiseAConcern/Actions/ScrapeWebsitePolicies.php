<?php

namespace App\Domains\RaiseAConcern\Actions;

use Illuminate\Support\Facades\Http;

/**
 * @deprecated
 */
class ScrapeWebsitePolicies
{
    public function __construct(public string $site)
    {
        // Prepend a scheme if one isn't present to help parse_url work reliably.
        $normalizedSite = $site;
        if (! preg_match('~^https?://~i', $site)) {
            $normalizedSite = 'https://'.$site;
        }

        // Parse the URL and get the host component.
        $host = parse_url($normalizedSite, PHP_URL_HOST);

        // If parse_url fails to find a host, the input is invalid.
        if (empty($host)) {
            throw new \InvalidArgumentException("Invalid site format provided: '{$site}'");
        }

        // Remove 'www.' from the beginning of the host if it exists.
        $cleanedHost = preg_replace('/^www\./i', '', $host);

        $this->site = $cleanedHost;
    }

    public function getHtml(): string
    {
        $fullUrl = 'https://www.'.$this->site.'/policies/';

        // Use file_get_contents to fetch the HTML content from the provided URL.
        // This is a simple way to retrieve the content of a web page.
        // Note: In production, consider using cURL or Guzzle for better error handling and performance.
        return Http::get($fullUrl)->body();
    }

    /**
     * Finds a download link in a given HTML string based on a search term.
     *
     * @param  string  $searchTerm  The term to search for in the link's text.
     * @return string|null The URL of the first matching download link, or null if not found.
     */
    public function findDownloadLink(string $searchTerm): ?string
    {
        $html = $this->getHtml();

        // Create a new DOMDocument instance and load the HTML.
        // The '@' suppresses warnings from malformed HTML.
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);

        // Create a new DOMXPath instance to query the document.
        $xpath = new \DOMXPath($dom);

        // The XPath query to find all `<a>` tags that are descendants of a `div`
        // with the class 'search-filter-results'.
        $query = "//div[contains(@class, 'search-filter-results')]//a";

        // Execute the XPath query.
        $links = $xpath->query($query);

        // Check if the query returned any results.
        if ($links === false) {
            // Handle query execution error, though it's unlikely here.
            return null;
        }

        // Iterate over the found links.
        foreach ($links as $link) {
            // Check if the link's text content contains the search term (case-insensitive).
            if (stripos($link->nodeValue, $searchTerm) !== false) {
                // If a match is found, return the 'href' attribute.
                return $link->getAttribute('href');
            }
        }

        // If no matching link is found after checking all of them, return null.
        return null;
    }
}
