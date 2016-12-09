# singleton-command
Symfony singleton command. Command that can't be run in parallel before previous process stopped.
If you need to simply run symfony command only in one process and you don't want to use and manage lock files by yourself - this class is what you need :)

### Require
1. `symfony/console`
2. `jced-artem/lock-service` - uses this package to replace LockHandler because it can't work with several hosts. This is important here because command can be launched in several servers as cron with shared folder.

### Install
`composer require jced-artem/singleton-command`

### Example

```
class JobCommand extends SingletonCommand implements SingletonCommandInterface
{
    protected function configure()
    {
        $this
            ->setName('cron:job')
            ->addArgument('someArgument', InputArgument::REQUIRED)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function beforeLock(InputInterface $input, OutputInterface $output)
    {
        // You can create dynamic lock-names if you need. If don't - just remove this method.
        $this->setLockName($this->getName() . ':' . $input->getArgument('someArgument'));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function lockExecute(InputInterface $input, OutputInterface $output)
    {
        // command code here
    }
}
```
And you need to use this command as service, so, your `services.yml` should looks like this:
```
parameters:
    lock_path: '/path/to/shared/folder/locks'

services:
    lock_service:
        class: AppBundle\Service\LockService
        arguments: ['%lock_path%']
        shared: false # shouldn't be shared if you want to have commands with different lock paths.
    command.cron_job:
        class: AppBundle\Command\JobCommand
        arguments: ['@lock_service']
        tags:
            - { name: console.command }
```
