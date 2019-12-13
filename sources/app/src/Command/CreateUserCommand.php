<?php
/**
 * Created by PhpStorm.
 * User: RomanHrb
 * Date: 13.12.2019
 * Time: 19:33
 */
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateUserCommand extends Command
{
// the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-user';

    private $login;

    private $requirePassword = false;

    public function __construct(bool $requirePassword = false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->requirePassword = $requirePassword;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
        ;
        $this->addArgument('login', $this->login, 'Login');
        $this->addArgument('password', $this->requirePassword ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User password');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
        $output->writeln('Login: '.$input->getArgument('login'));
        $output->writeln('User created !' . $input->getArgument('login'));

    }
}