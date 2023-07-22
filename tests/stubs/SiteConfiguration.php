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

	public function get( string $setting, string $wiki ) {
		return $this->settings[$setting][$wiki];
	}
}
