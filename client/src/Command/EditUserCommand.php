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
    name: 'app:edit-user',
    description: 'Changes a user',
)]
class EditUserCommand extends Command
{
    public function __construct(private ParameterBagInterface $params, private HttpClientInterface $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'User id')
            ->addArgument('name', InputArgument::OPTIONAL, 'New user name')
            ->addArgument('email', InputArgument::OPTIONAL, 'New user email')
            ->addArgument('group-id', InputArgument::OPTIONAL, 'New user group id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');
        $groupId = $input->getArgument('group-id');

        try {
            $response = $this->client->request(
                'GET',
                $this->params->get('app.api_url_prefix') . 'user/' . $id
            );
            if ($response->getStatusCode() === 200) {
                $body = $response->toArray();
                unset($body['id']);
                if (!empty($name)) {
                    $body['name'] = $name;
                }
                if (!empty($email)) {
                    $body['email'] = $email;
                }
                if (!empty($groupId)) {
                    $body['group'] = $groupId;
                }

                $response = $this->client->request(
                    'PATCH',
                    $this->params->get('app.api_url_prefix') . 'user/' . $id,
                    [
                        'body' => $body,
                    ]
                );

                if ($response->getStatusCode() === 200) {
                    $io->success('Successfully updated a user with id: ' . $id);

                    return Command::SUCCESS;
                }
            }
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
        }

        $io->warning('Something went wrong.');

        return Command::FAILURE;
    }
}
