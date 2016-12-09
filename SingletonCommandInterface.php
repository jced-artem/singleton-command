<?php

namespace Jced;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface CronCommandSingletonInterface
 */
interface SingletonCommandInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function lockExecute(InputInterface $input, OutputInterface $output);
}
