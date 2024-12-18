<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestMailerCommand extends Command
{
    private $mailer;

    protected static $defaultName = 'app:test-mailer';

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName) // Définit le nom de la commande
            ->setDescription('Send a test email using Symfony Mailer.')
            ->setHelp('This command allows you to send a test email...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('test@example.com')
            ->to('recipient@example.com')
            ->subject('Test Email')
            ->text('This is a test email.');

        $this->mailer->send($email);

        $output->writeln('Email sent! Check MailHog.');

        return Command::SUCCESS;
    }
}
