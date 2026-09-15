<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$project = 'paging_schema_parity';
$compose = [
    'docker',
    'compose',
    '-p',
    $project,
    '-f',
    $root.DIRECTORY_SEPARATOR.'compose.yaml',
    '-f',
    $root.DIRECTORY_SEPARATOR.'compose.override.yaml',
];

$run = static function (array $command, ?array $environment = null, bool $capture = false) use ($root): string {
    $process = proc_open(
        $command,
        [
            0 => ['file', 'php://stdin', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        $root,
        $environment,
    );
    if (!is_resource($process)) {
        throw new RuntimeException(sprintf('Unable to start command: %s', implode(' ', $command)));
    }

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if (!$capture && '' !== trim($stdout)) {
        fwrite(STDOUT, $stdout);
    }
    if ('' !== trim($stderr)) {
        fwrite(STDERR, $stderr);
    }
    if (0 !== $exitCode) {
        throw new RuntimeException(sprintf('Command failed with exit code %d: %s', $exitCode, implode(' ', $command)));
    }

    return trim($stdout);
};

$environment = getenv();
$environment = is_array($environment) ? $environment : [];
$environment['POSTGRES_DB'] = 'paging_parity';
$environment['POSTGRES_USER'] = 'paging';
$environment['POSTGRES_PASSWORD'] = 'paging-parity';

try {
    try {
        $run([...$compose, 'down', '-v', '--remove-orphans'], $environment, true);
    } catch (Throwable $cleanupError) {
        fwrite(STDERR, sprintf("Parity pre-clean warning: %s\n", $cleanupError->getMessage()));
    }

    $run([...$compose, 'up', '-d', 'database'], $environment);

    $portOutput = $run([...$compose, 'port', 'database', '5432'], $environment, true);
    if (1 !== preg_match('/:(\d+)$/', $portOutput, $matches)) {
        throw new RuntimeException(sprintf('Unable to resolve disposable PostgreSQL port from: %s', $portOutput));
    }

    $runtimeEnvironment = $environment;
    $runtimeEnvironment['APP_ENV'] = 'test';
    $runtimeEnvironment['APP_DEBUG'] = '0';
    $runtimeEnvironment['DATABASE_URL'] = sprintf('postgresql://paging:paging-parity@127.0.0.1:%s/paging_parity?serverVersion=16&charset=utf8', $matches[1]);

    $ready = false;
    for ($attempt = 0; $attempt < 30; ++$attempt) {
        try {
            $run([...$compose, 'exec', '-T', 'database', 'pg_isready', '-U', 'paging', '-d', 'paging_parity'], $environment, true);
            $ready = true;
            break;
        } catch (RuntimeException) {
            usleep(500_000);
        }
    }
    if (!$ready) {
        throw new RuntimeException('Disposable PostgreSQL did not become ready.');
    }

    $console = $root.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'console';
    $run([PHP_BINARY, $console, 'doctrine:schema:validate', '--env=test', '--skip-sync', '--no-interaction'], $runtimeEnvironment);
    $run([PHP_BINARY, $console, 'doctrine:migrations:migrate', '--env=test', '--no-interaction'], $runtimeEnvironment);

    $schemaDiff = $run([PHP_BINARY, $console, 'doctrine:schema:update', '--env=test', '--dump-sql', '--no-interaction'], $runtimeEnvironment, true);
    if ('' !== trim($schemaDiff)) {
        fwrite(STDERR, "Doctrine schema drift detected after applying migrations:\n".$schemaDiff."\n");
        throw new RuntimeException('Migration chain does not reproduce the current Doctrine metadata schema.');
    }

    fwrite(STDOUT, "Doctrine mapping and migration/schema parity passed on disposable PostgreSQL after migrating to the latest registered version.\n");
} finally {
    try {
        $run([...$compose, 'down', '-v', '--remove-orphans'], $environment);
    } catch (Throwable $cleanupError) {
        fwrite(STDERR, sprintf("Parity cleanup warning: %s\n", $cleanupError->getMessage()));
    }
}
