<?php

namespace WikiForge\Config\Tests;

use WikiForgeFunctions;

require_once __DIR__ . '/../initialise/WikiForgeFunctions.php';

class WikiForgeFunctionsWrapper extends WikiForgeFunctions {
	private const CACHE_DIRECTORY = '/path/to/custom/cache/directory';
}
