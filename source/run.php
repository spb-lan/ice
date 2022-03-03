<?php
/**
 * @file Application run script
 *
 * Run ice application
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @version 0.2
 * @since 0.0
 */

use Ice\App;

require_once __DIR__ . '/bootstrap.php';

try {
    App::run();

    exit(0);
} catch (Exception | Throwable $e) {
    fwrite(STDERR, "\033[0;31m" . $e->getMessage() . "\033[0m\n");
    exit(1);
}
