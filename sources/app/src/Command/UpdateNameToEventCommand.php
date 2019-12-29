<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 13.12.2019
 * Time: 19:33
 */
declare(strict_types=1);

namespace App\Command;

use App\Message\CreateEventMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateNameToEventCommand extends Command
{
    /**
     * Command name
     */
    protected static $defaultName = 'event:name-update';

    /**
     * Argument event id
     *
     * @var $event_id
     */
    protected $event_id;

    /**
     * Argument event name
     *
     * @var $event_id
     */
    protected $event_name;

    /**
     * Message bus
     *
     * @var $event_id
     */
    protected $messageBus;

    /**
     * Class initiation
     * @param MessageBusInterface $messageBus
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setDescription('Command to update event name.')
            ->setHelp('Update events name')
        ;
        $this->addArgument('event_id', $this->event_id, 'Event Id');
        $this->addArgument('event_name', $this->event_name, 'Event Name');
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
            '*    Update Event Name Manually     *',
            '*************************************'
        ]);

        $id = (int) $input->getArgument('event_id');
        $name = (string) $input->getArgument('event_name');

        $output->writeln('Event Id: ' . $id);
        $output->writeln('Event Name: ' . $name);

        $output->writeln([
            '*************************************',
            '*    Run command Update Event Name  *',
            '*************************************'
        ]);

        $this->messageBus->dispatch(new CreateEventMessage($id, $name));
    }
}