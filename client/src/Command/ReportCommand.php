<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
    name: 'app:report',
    description: 'Shows a report with users and groups',
)]
class ReportCommand extends Command
{
    public function __construct(private ParameterBagInterface $params, private HttpClientInterface $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('group-id', null, InputOption::VALUE_OPTIONAL, 'Group id to filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $groupId = $groupId = $input->getOption('group-id');

        $url = $this->params->get('app.api_url_prefix') . 'group/';
        if (!empty($groupId)) {
            $url = $this->params->get('app.api_url_prefix') . 'group/' . $groupId;
        }

        try {
            $response = $this->client->request(
                'GET',
                $url
            );
            if ($response->getStatusCode() === 200) {
                $list = $response->toArray();
                if (!empty($groupId)) {
                    $list = [$list];
                }

                $table = new Table($output);
                $table->setHeaders([
                    'id',
                    'name',
                    'email',
                    'group-id',
                    'group-name',
                ]);
                foreach ($list as $group) {
                    foreach ($group['users'] as $j => $user) {
                        $table->addRow([$user['id'], $user['name'], $user['email'], $group['id'], $group['name']]);
                    }
                }

                $table->render();

                return Command::SUCCESS;
            }
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
        }

        $io->warning('Something went wrong.');

        return Command::FAILURE;
    }
}
