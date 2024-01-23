<?php

declare(strict_types=1);

namespace Project\Command\Sandbox;

use Override;
use Project\Command\AbstractCommand;
use WebServCo\Command\Contract\CommandInterface;
use WebServCo\Command\Contract\OutputInterface;
use WebServCo\Stopwatch\Contract\LapTimerInterface;

use function sprintf;

final class TestCommand extends AbstractCommand implements CommandInterface
{
    private const PROCESSING_LAP_NAME = 'sandbox-test';

    public function __construct(private LapTimerInterface $lapTimer, private OutputInterface $output)
    {
    }

    #[Override]
    public function run(): bool
    {
        $this->lapTimer->lap(sprintf('%s: start', __FUNCTION__));

        // Log.
        $this->output->write(__METHOD__);

        $this->lapTimer->lap(self::PROCESSING_LAP_NAME);

        return true;
    }
}
