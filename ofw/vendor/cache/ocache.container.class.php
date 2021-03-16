<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Cache;

/**
 * OCacheContainer - Container for all json cache files.
 */
class OCacheContainer {
	private ?string $cache_folder = null;
	private array $list = [];

	/**
	 * On startup, the cache container reads the cache folder and loads the cache keys, not the objects itself.
	 */
	function __construct() {
		global $core;
		$this->cache_folder = $core->config->getDir('ofw_cache');
		$this->loadItems();
	}

	/**
	 * Reads the cache folder and loads the cache items found there into the list.
	 *
	 * @return void
	 */
	public function loadItems(): void {
		$this->list = [];
		if ($model = opendir($this->cache_folder)) {
			while (false !== ($entry = readdir($model))) {
				if ($entry != '.' && $entry != '..') {
					$name = str_ireplace('.cache.json', '', $entry);
					$this->list[$name] = new OCache($name);
				}
			}
			closedir($model);
		}
	}

	/**
	 * Get the OCache item with the given key. If the cache item doesn't exist, return an empty new OCache.
	 *
	 * @param string $key Key of the cache item to be retrieved
	 *
	 * @return OCache Cache instance.
	 */
	public function getItem(string $key): OCache {
		if ($this->hasItem($key)) {
			return $this->list[$key];
		}
		return new OCache($key);
	}

	/**
	 * Get the cache item list.
	 *
	 * @return array Cache item list
	 */
	public function getItems(): array {
		return $this->list;
	}

	/**
	 * Get if an item exists on the cache item list.
	 */
	public function hasItem(string $key): bool {
		return array_key_exists($key, $this->list);
	}

	/**
	 * Deletes all cached files.
	 *
	 * @return bool True if all cache files where successfully deleted, false otherwise.
	 */
	public function clear(): bool {
		$ret = true;
		foreach ($this->list as $key => $item) {
			if (!$this->deleteItem($key)) {
				$ret = false;
			}
		}
		return $ret;
	}

	/**
	 * Deletes a cache item, both from the list and the cache file.
	 *
	 * @return bool True if the cache item was successfully deleted, false otherwise.
	 */
	public function deleteItem(string $key): bool {
		if (!$this->hasItem($key)) {
			return false;
		}
		$route = $this->cache_folder.$key.'.cache.json';
		if (!file_exists($route)) {
			return false;
		}
		unset($this->list[$key]);
		if (!unlink($route)) {
			return false;
		}
		return true;
	}

	/**
	 * Deletes a list of cache items.
	 *
	 * @param array $keys List of cache item keys.
	 *
	 * @return bool True if the cache items were successfully deleted, false otherwise.
	 */
	public function deleteItems(array $keys): bool {
		$ret = true;
		foreach ($keys as $item) {
			if (!$this->deleteItem($item)) {
				$ret = false;
			}
		}
		return $ret;
	}

	/**
	 * Save the cache item and store it on the cache container.
	 *
	 * @param OCache $item Cache item to be saved.
	 *
	 * @return bool True if the item was successfully saved, false otherwise.
	 */
	public function save(OCache $item): bool {
		if ($item->save()) {
			if (!$this->hasItem($item->getKey())) {
				$this->list[$item->getKey()] = $item;
			}
			return true;
		}
		return false;
	}
}