<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Cache;

use OsumiFramework\OFW\Tools\OTools;

/**
 * OCache - Cache item instance
 */
class OCache {
	private ?string $key = null;
	private bool $loaded = false;
	private ?string $route = null;
	private bool $is_hit = false;
	private mixed $value;
	private int $expires_after = 0;

	/**
	 * Create a new cache instance with a given key.
	 */
	function __construct(string $key) {
		global $core;
		$this->key = $key;
		$this->route = $core->config->getDir('ofw_cache').$key.'.cache.json';
		$this->expires_after = 60 * 60 * 24 * 7; // Default expiration time: one week
	}

	/**
	 * Get the cache item key name.
	 *
	 * @return string Item key name
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 * Get if cache file exists.
	 *
	 * @return bool True if cache file exists, false otherwise
	 */
	public function isHit(): bool {
		if ($this->loaded) {
			return $this->is_hit;
		}
		return file_exists($this->route);
	}

	/**
	 * Get cached value
	 *
	 * @return mixed Cached value
	 */
	public function get(): mixed {
		if ($this->loaded) {
			return $this->value;
		}

		if ($this->isHit()) {
			$this->is_hit = true;
			$content = file_get_contents($this->route);
			$content_parsed = json_decode($content, true);
			if ($content_parsed===null) {
				return null;
			}
			if (time() > $content_parsed['expiresAt']) {
				return null;
			}
			$this->loaded = true;
			$this->value = OTools::base64urlDecode($content_parsed['value']);
		}

		return $this->value;
	}

	/**
	 * Override manually the default expiration time
	 *
	 * @param int $expires_after Expiration time in seconds
	 *
	 * @return void
	 */
	public function setExpiresAfter(int $expires_after): void {
		$this->expires_after = $expires_after;
	}

	/**
	 * Set in memory the value that will be cached
	 *
	 * @return void
	 */
	public function set(mixed $value): void {
		$this->value = $value;
	}

	/**
	 * Saves the stored value into a cache file
	 *
	 * @return bool True if the cache file was saved, false otherwise
	 */
	public function save(): bool {
		$content = [
			'expiresAt' => (time() + $this->expires_after),
			'value' => OTools::base64urlEncode($this->value)
		];
		$status = (file_put_contents($this->route, json_encode($content))!==false);
		if ($status) {
			$this->is_hit = true;
			$this->loaded = true;
		}
		return $status;
	}

	/**
	 * Reset values so that the next time is used it will load the cache again fron scratch
	 *
	 * @return void
	 */
	public function reload(): void {
		$this->loaded = false;
		$this->is_hit = false;
		$this->value  = null;
	}
}