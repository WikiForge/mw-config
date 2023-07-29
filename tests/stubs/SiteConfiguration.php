<?php

/**
 * SiteConfiguration stub class for tests
 */
class SiteConfiguration {

	/** @var array */
	public $settings = [];

	/** @var array */
	public $suffixes = [];

	/** @var array */
	public $wikis = [];

	/**
	 * @param string $setting
	 * @param string $wiki
	 * @return mixed
	 */
	public function get( string $setting, string $wiki ) {
		if ( isset( $this->settings[$setting] ) ) {
			return $this->settings[$setting][$wiki] ??
				$this->settings[$setting]['default'] ?? null;
		}

		return $GLOBALS[$setting] ?? null;
	}
}
