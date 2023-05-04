<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:edit-group',
    description: 'Changes a group',
)]
class EditGroupCommand extends Command
{
    public function __construct(private ParameterBagInterface $params, private HttpClientInterface $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Group id')
            ->addArgument('name', InputArgument::REQUIRED, 'New group name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');
        $name = $input->getArgument('name');

        try {
            $response = $this->client->request(
                'GET',
                $this->params->get('app.api_url_prefix') . 'group/' . $id
            );
            if ($response->getStatusCode() === 200) {
                $body = $response->toArray();
                unset($body['id']);
                unset($body['users']);
                if (!empty($name)) {
                    $body['name'] = $name;
                }

                $response = $this->client->request(
                    'PATCH',
                    $this->params->get('app.api_url_prefix') . 'group/' . $id,
                    [
                        'body' => $body,
                    ]
                );

                if ($response->getStatusCode() === 200) {
                    $io->success('Successfully updated a group with id: ' . $id);

                    return Command::SUCCESS;
                }
            }
        } catch (TransportExceptionInterface | DecodingExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
        }

        $io->warning('Something went wrong.');

        return Command::FAILURE;
    }
}
