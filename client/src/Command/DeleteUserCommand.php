<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:delete-user',
    description: 'Deletes a user',
)]
class DeleteUserCommand extends Command
{
    public function __construct(private ParameterBagInterface $params, private HttpClientInterface $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'User id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');

        try {
            $response = $this->client->request(
                'DELETE',
                $this->params->get('app.api_url_prefix') . 'user/' . $id
            );

            if ($response->getStatusCode() === 200) {
                $io->success('Successfully deleted a user with id: ' . $id);

                return Command::SUCCESS;
            }
        } catch (TransportExceptionInterface $e) {
        }

        $io->warning('Something went wrong.');

        return Command::FAILURE;
    }
}
