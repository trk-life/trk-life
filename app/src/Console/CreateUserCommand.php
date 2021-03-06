<?php

namespace TrkLife\Console;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use TrkLife\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TrkLife\Entity\User;
use TrkLife\Exception\ValidationException;

/**
 * Class CreateUserCommand
 *
 * Command for creating a new user
 *
 * @package TrkLife\Console
 * @author George Webb <george@webb.uno>
 */
class CreateUserCommand extends Command
{
    /**
     * DI container
     *
     * @var Container
     */
    private $c;

    /**
     * CreateUserCommand constructor.
     *
     * @param null|string $name     The command name
     * @param Container $c          DI container
     */
    public function __construct($name, Container $c)
    {
        parent::__construct($name);
        $this->c = $c;
    }

    /**
     * Configure arguments for command
     */
    protected function configure()
    {
        $this->setName('user:create')
            ->setDescription('Create a new user')
            ->addArgument('email', InputArgument::REQUIRED, 'Email address')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
            ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
            ->addArgument('role', InputArgument::OPTIONAL, 'User\'s role (admin or user):', 'user');
    }

    /**
     * Execute the create user command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating new trk.life user.</info>');

        $user = new User();
        $user->set('email', $input->getArgument('email'));

        if (!$user->set('password', $input->getArgument('password'))) {
            $output->writeln('<comment>Password must be at least 8 characters long.</comment>');
            $output->writeln('<error>Exiting.</error>');
            return;
        }

        $user->set('first_name', $input->getArgument('first_name'));
        $user->set('last_name', $input->getArgument('last_name'));
        $user->set('role', $input->getArgument('role'));
        $user->set('status', User::STATUS_ACTIVE);

        try {
            // Save the user
            $this->c->EntityManager->persist($user);
            $this->c->EntityManager->flush();
        } catch (ValidationException $e) {
            $output->writeln('<comment>Validation errors found:</comment>');

            foreach ($e->validation_messages as $message) {
                $output->writeln("<comment>    $message</comment>");
            }

            $output->writeln('<error>Exiting.</error>');
            return;
        } catch (UniqueConstraintViolationException $e) {
            $output->writeln('<comment>Email address is already registered.</comment>');
            $output->writeln('<error>Exiting.</error>');
            return;
        }

        $output->writeln('<info>User created successfully.</info>');
    }
}
