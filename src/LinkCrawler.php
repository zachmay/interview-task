<?php

namespace Sample;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Symfony\Component\DomCrawler\Crawler;

class LinkCrawler
{
    protected $maxDepth;
    protected $client;
    protected $crawler;
    protected $urlCache;

    public function __construct($maxDepth = 3, Client $client = null, Crawler $crawler = null)
    {
        $this->maxDepth = $maxDepth;

        $this->client  = $client === null  ? new Client()  : $client;
        $this->crawler = $crawler === null ? new Crawler() : $crawler;
    }

    public function run($url)
    {
        $this->urlCache = [];
        return $this->_run($url, "Root", 0);
    }

    protected function _run($url, $title, $currentDepth)
    {
        $out = [
            'url'         => $url,
            'title'       => $title,
            'descendants' => []
        ];

        if ( $currentDepth > $this->maxDepth )
        {
            $out['error'] = sprintf('Depth %d beyond max. depth %d!', $currentDepth, $this->maxDepth);
            return $out;
        }

        if ( array_key_exists($url, $this->urlCache) )
        {
            $out['error'] = sprintf('Already visited at depth %d!', $this->urlCache[$url]);
            return $out;
        }

        if ( strpos(strtoupper($url), 'HTTP') !== 0 )
        {
            $out['error'] = 'Skipping non-absolute URL.';
            return $out;
        }

        $this->urlCache[$url] = $currentDepth;

        try
        {
            $response = $this->client->get($url);
            $body = (string) $response->getBody();

            $this->crawler->addContent($body);

            $links = $this->crawler->filterXPath('//a')
                                   ->extract(['_text', 'href']);
            $this->crawler->clear();

            $descendants = [];

            foreach ( $links as $link )
            {
                $title = $link[0];
                $url   = $link[1];

                $descendants[] = $this->_run($url, $title, $currentDepth + 1);
            }

            $out['descendants'] = $descendants;
        }
        catch ( \Exception $e )
        {
            $out['error'] = "Exception: {$e->getMessage()}";
            return $out;
        }

        return $out;
    }
}
