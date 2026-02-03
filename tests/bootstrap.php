<?php

use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// #region agent log
try {
    $projectRoot = dirname(__DIR__);
    $logFile = $projectRoot.'/.cursor/debug.log';

    $logs = [];

    // Hypothesis H1: symfony/browser-kit is missing from vendor in CI
    $logs[] = [
        'sessionId' => 'debug-session',
        'runId' => 'pre-fix',
        'hypothesisId' => 'H1',
        'location' => 'tests/bootstrap.php:'.__LINE__,
        'message' => 'Checking BrowserKit vendor directory presence',
        'data' => [
            'phpVersion' => PHP_VERSION,
            'browserKitVendorDirExists' => is_dir($projectRoot.'/vendor/symfony/browser-kit'),
        ],
        'timestamp' => (int) (microtime(true) * 1000),
    ];

    // Hypothesis H2: PHPUnit runs without dev dependencies (e.g. --no-dev)
    $logs[] = [
        'sessionId' => 'debug-session',
        'runId' => 'pre-fix',
        'hypothesisId' => 'H2',
        'location' => 'tests/bootstrap.php:'.__LINE__,
        'message' => 'Checking if Composer dev autoload is present',
        'data' => [
            'devAutoloadFileExists' => is_file($projectRoot.'/vendor/autoload.php'),
        ],
        'timestamp' => (int) (microtime(true) * 1000),
    ];

    // Hypothesis H3: Symfony test tools (WebTestCase) are unavailable/misconfigured
    $logs[] = [
        'sessionId' => 'debug-session',
        'runId' => 'pre-fix',
        'hypothesisId' => 'H3',
        'location' => 'tests/bootstrap.php:'.__LINE__,
        'message' => 'Checking WebTestCase class availability',
        'data' => [
            'webTestCaseClassExists' => class_exists(Symfony\Bundle\FrameworkBundle\Test\WebTestCase::class, false),
        ],
        'timestamp' => (int) (microtime(true) * 1000),
    ];

    foreach ($logs as $entry) {
        @file_put_contents($logFile, json_encode($entry).PHP_EOL, FILE_APPEND);
    }
} catch (Throwable $e) {
    // Intentionally ignore logging failures in tests bootstrap
}
// #endregion
