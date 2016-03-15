<?php

namespace hypeJunction\Videolist;

use GuzzleHttp\Client;
use hypeJunction\Parser;
use Videolist_PlatformInterface;

/**
 * Implements HTTP parser as videolist platform
 */
class HttpParser implements Videolist_PlatformInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getData($parsed) {
		$url = elgg_extract('url', $parsed);
		if (!$url) {
			return false;
		}
		$parser = new Parser($this->getHttpClient());
		$data = $parser->parse($url);

		if (!$data) {
			return false;
		}
		$type = elgg_extract('type', $data);
		if (empty($data['html'])) {
			if (!empty($data['metatags']['twitter:player'])) {
				$data['html'] = elgg_format_element('iframe', array(
					'src' => $data['metatags']['twitter:player'],
					'frameborder' => 0,
					'width' => $data['metatags']['twitter:player:width'] ? : 640,
					'height' => $data['metatags']['twitter:player:height'] ? : 480,
				));
			}
		} else {
			return false;
		}

		return array(
			'title' => elgg_extract('title', $data, ''),
			'description' => elgg_extract('description', $data, ''),
			'thumbnail' => elgg_extract('thumbnail_url', $data, ''),
			'embed_html' => elgg_extract('html', $data, ''),
			'oembed_subtype' => elgg_extract('type', $data, ''),
			'provider_name' => elgg_extract('provider_name', $data, ''),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType() {
		return 'oembed';
	}

	/**
	 * {@inheritdoc}
	 */
	public function parseUrl($url) {
		$host = parse_url($url, PHP_URL_HOST);
		if (!$host) {
			return false;
		}
		$domains = explode(PHP_EOL, elgg_get_plugin_setting('domains', 'videolist', ''));
		foreach ($domains as $domain) {
			// see if domain was set with protocol in settings
			$domain_host = parse_url(trim($domain), PHP_URL_HOST);
			if (!$domain_host) {
				$domain_host = trim($domain);
			}
			if ($this->stripW3($domain_host) == $this->stripW3($host)) {
				return array(
					'url' => $url,
				);
			}
		}
		return false;
	}

	/**
	 * Construct new guzzle client
	 * @return Client
	 */
	protected function getHttpClient() {
		$http_config = [
			'headers' => [
				'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
			],
			'allow_redirects' => [
				'max' => 3,
				'strict' => true,
				'referer' => true,
				'protocols' => ['http', 'https']
			],
			'timeout' => 5,
			'connect_timeout' => 5,
			'verify' => false,
		];

		// Same hook that is triggered in hypeScraper for convenience
		$http_config = elgg_trigger_plugin_hook('http:config', 'framework:scraper', null, $http_config);

		return new Client($http_config);
	}

	/**
	 * Strip www. from host names
	 *
	 * @param string $host Host name
	 * @return string
	 */
	protected function stripW3($host = '') {
		return preg_replace('/^www\./i', '', $host);
	}

}
