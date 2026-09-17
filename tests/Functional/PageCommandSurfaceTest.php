<?php

declare(strict_types=1);

namespace App\Paging\Tests\Functional;

use App\Paging\Kernel;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Verifies the standalone Paging kernel exposes executable RC and integration diagnostics.
 */
final class PageCommandSurfaceTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    /** @return iterable<string, array{0: string, 1: array<string, bool>}> */
    public static function commandProvider(): iterable
    {
        yield 'runtime probe' => ['page:debug:container', []];
        yield 'host integration' => ['page:host:integration-check', []];
        yield 'api contract' => ['page:api:contract', []];
        yield 'rc readiness' => ['page:rc:readiness', []];
        yield 'operations' => ['page:operations:check', []];
        yield 'final status json' => ['page:rc:final-status', ['--json' => true]];
        yield 'final status text' => ['page:rc:final-status', []];
        yield 'handoff json' => ['page:handoff:summary', ['--json' => true]];
        yield 'handoff text' => ['page:handoff:summary', []];
        yield 'canon json' => ['page:canon:guard', ['--json' => true]];
        yield 'canon text' => ['page:canon:guard', []];
        yield 'completion json' => ['page:completion:status', ['--json' => true]];
        yield 'completion text' => ['page:completion:status', []];
        yield 'release json' => ['page:release:stamp', ['--json' => true]];
        yield 'release text' => ['page:release:stamp', []];
        yield 'bridge json' => ['page:bridge:contract', ['--json' => true]];
        yield 'bridge text' => ['page:bridge:contract', []];
        yield 'usability' => ['page:user-usability:check', []];
        yield 'usability json' => ['page:user-usability:check', ['--json' => true]];
        yield 'interfacing' => ['page:interfacing:contract', []];
        yield 'security' => ['page:security:contract', []];
        yield 'workflow' => ['page:workflow:acceptance', []];
    }

    /** @param array<string, bool> $input */
    #[DataProvider('commandProvider')]
    public function testRegisteredCommandCompletesSuccessfully(string $name, array $input): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $command = $application->find($name);
        $tester = new CommandTester($command);
        $status = $tester->execute($input);

        self::assertSame(Command::SUCCESS, $status, $tester->getDisplay());
        self::assertNotSame('', trim($tester->getDisplay()));
    }
}
