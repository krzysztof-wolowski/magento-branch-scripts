<?php

/**
 * Parse shell-style config assignments from config.
 *
 * @param string $configPath
 *
 * @return array<string, string>
 */
function magentoBootstrapScriptsLoadConfig(string $configPath): array
{
    if (!is_file($configPath)) {
        return [];
    }

    $config = [];
    $configLines = file($configPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($configLines === false) {
        return [];
    }

    foreach ($configLines as $line) {
        $trimmedLine = trim($line);

        if ($trimmedLine === '' || str_starts_with($trimmedLine, '#')) {
            continue;
        }

        if (!preg_match('/^([A-Z_]+)=(.*)$/', $trimmedLine, $matches)) {
            continue;
        }

        $config[$matches[1]] = trim($matches[2], " \t\n\r\0\x0B\"'");
    }

    return $config;
}

/**
 * Register a PSR-4 style helper namespace mapping.
 *
 * @param string $namespace
 * @param string $baseDir
 *
 * @return void
 */
function magentoBootstrapScriptsRegisterHelperNamespace(string $namespace, string $baseDir): void
{
    $namespacePrefix = rtrim($namespace, '\\') . '\\';
    $helperBaseDir = rtrim($baseDir, '/') . '/';

    spl_autoload_register(static function (string $class) use (
        $helperBaseDir,
        $namespacePrefix
    ): void {
        if (strncmp($namespacePrefix, $class, strlen($namespacePrefix)) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($namespacePrefix));
        $filePath = $helperBaseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($filePath)) {
            require_once $filePath;
        }
    });
}

$config = magentoBootstrapScriptsLoadConfig(__DIR__ . '/config');
$commonHelperNamespace = $config['COMMON_HELPER_NAMESPACE'] ?? $config['HELPER_NAMESPACE'] ?? 'CommonTools';
$commonHelperSrcDir = $config['COMMON_HELPER_SRC_DIR'] ?? $config['HELPER_SRC_DIR'] ?? 'src/CommonTools';
$projectHelperNamespace = $config['PROJECT_HELPER_NAMESPACE'] ?? 'ProjectTools';
$projectHelperSrcDir = $config['PROJECT_HELPER_SRC_DIR'] ?? 'src/ProjectTools';
$packageRoot = __DIR__;

magentoBootstrapScriptsRegisterHelperNamespace(
    $commonHelperNamespace,
    $packageRoot . '/' . trim($commonHelperSrcDir, '/')
);
magentoBootstrapScriptsRegisterHelperNamespace(
    $projectHelperNamespace,
    $packageRoot . '/' . trim($projectHelperSrcDir, '/')
);
