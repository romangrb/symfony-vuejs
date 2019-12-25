<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 13.12.2019
 * Time: 19:33
 */
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Enqueue\Client\Message;
use Enqueue\Client\ProducerInterface;

class AddDescriptionToEventCommand extends Command
{
    /**
     * Command name
     */
    protected static $defaultName = 'event:validate';

    /**
     * Argument event id
     *
     * @var $event_id
     */
    protected $event_id;

    /**
     * Producer
     *
     * @var $producer
     */
    protected $producer;

    /**
     * Queue priority
     *
     * @var $priority
     */
    protected $priority;

    /**
     * Class initiation
     *
     * @param ProducerInterface $producer
     */
    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
        parent::__construct();
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            ->setDescription('Command to validate event of valiance.')
            ->setHelp('Add description to event')
        ;
        $this->addArgument('event_id', $this->event_id, 'Event Id');
        $this->addArgument('priority', $this->priority, 'priority');
        ;
    }

    /**
     * Execute Command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            '*************************************',
            '*    Check Event Valiance Manually  *',
            '*************************************'
        ]);
        $output->writeln('Event Id: ' . $input->getArgument('event_id'));

        $output->writeln([
            '*************************************',
            '*Run command event-violation-command*',
            '*************************************'
        ]);

//        $this->producer->sendEvent('command-name', 'Something has happened');

        $properties = [
            'priority' => $input->getArgument('priority')
        ];
        $message = new Message($input->getArgument('event_id'), $properties);

//        $this->producer->sendEvent('aBarTopic', $message);
        $this->producer->sendCommand('event-violation-command', $message);
    }
}