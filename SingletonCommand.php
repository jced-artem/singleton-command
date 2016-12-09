<?php

namespace Jced;

use Jced\LockService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SingletonCommand
 */
abstract class SingletonCommand extends Command
{
    /** @var string */
    private $lockName = null;

    /** @var LockService */
    private $lockService;

    /**
     * SingletonCommand constructor.
     * @param LockService $lockService
     * @throws \Exception
     */
    public function __construct(LockService $lockService)
    {
        $this->lockService = $lockService;
        parent::__construct();
    }

    /**
     * @param $name
     */
    protected function setLockName($name)
    {
        $this->lockName = $name;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function beforeLock(InputInterface $input, OutputInterface $output)
    {
        return ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract public function lockExecute(InputInterface $input, OutputInterface $output);

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     * @throws \Exception
     */
    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->beforeLock($input, $output);
        if (empty($this->lockName)) {
            $this->lockName = $this->getName();
        }
        if (!$this->lockService->lock($this->lockName)) {
            throw new \Exception('Already launched command [ ' . $this->lockName .  ' ]');
        }
        register_shutdown_function([$this->lockService, 'release'], $this->lockName);
        return $this->lockExecute($input, $output);
    }

    /**
     * @return mixed
     */
    protected function release()
    {
        return $this->lockService->release($this->lockName);
    }
}
