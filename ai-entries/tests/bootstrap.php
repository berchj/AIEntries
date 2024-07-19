<?php

// First we need to load the composer autoloader, so we can use WP Mock
require_once dirname(__DIR__).'/vendor/autoload.php';

// Bootstrap WP_Mock to initialize built-in features
WP_Mock::bootstrap();

// Optional step
// If your project does not use autoloading via Composer, include your files now
//require_once dirname(__DIR__).'/ai-entries.php';