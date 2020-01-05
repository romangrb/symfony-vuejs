<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 13.12.2019
 * Time: 19:33
 */
declare(strict_types=1);

namespace App\Command;

use App\Message\TestMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SentEmailAsyncCommand extends Command
{
    /**
     * MessageBusInterface
     *
     * @var $bus
     */
    protected $bus;

    /**
     * Command name
     */
    protected static $defaultName = 'email:send-async';

    /**
     * Class initiation
     *
     * @param MessageBusInterface $bus
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setDescription('Command to sent email async.')
            ->setHelp('Update events name async')
        ;
    }

    /**
     * Execute Command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            '*************************************',
            '*    Send email async               *',
            '*************************************'
        ]);

        // will cause the TestMessageHandler to be called
        $this->bus->dispatch(new TestMessage('Look! I created a message!'));

        $output->writeln([
            '*************************************',
            '*          Email sent async         *',
            '*************************************'
        ]);

    }
}