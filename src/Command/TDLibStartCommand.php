<?php

namespace Yaroslavche\TDLibBundle\Command;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TDApi\LogConfiguration;
use TDApi\TDLibParameters;
use TDLib\JsonClient;

class TDLibWS implements MessageComponentInterface
{
    /** @var SymfonyStyle $io */
    protected $io;
    /** @var JsonClient $jsonClient */
    protected $jsonClient;
    /** @var bool $ready */
    protected $ready;

    /**
     * TDLibWS constructor.
     * @param SymfonyStyle $io
     * @param array $tdlibParameters
     * @param array $clientConfig
     */
    public function __construct(SymfonyStyle $io, array $tdlibParameters, array $clientConfig)
    {
        $this->ready = true;
        $this->io = $io;
        try {
            LogConfiguration::setLogVerbosityLevel(LogConfiguration::LVL_ERROR);
            $this->jsonClient = new JsonClient();
            $setTdlibParametersResponse = $this->jsonClient->query(json_encode([
                '@type' => 'setTdlibParameters',
                'parameters' => $tdlibParameters
            ]));
            $checkDatabaseEncryptionKeyResponse = $this->jsonClient->query(json_encode([
                '@type' => 'checkDatabaseEncryptionKey',
                'encryption_key' => $clientConfig['encryption_key'] ?? ''
            ]));
            /** @todo $this->jsonClient = (new BundleJsonClient($tdlibParameters, $clientConfig))->getJsonClient(); */
        } catch (Exception $e) {
            $this->ready = false;
            $io->error(get_class($e));
        }
        if ($this->ready) {
            $io->success('created');
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->io->writeln('onOpen');
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        if ($this->ready) {
            $response = $this->jsonClient->query($message);
            $from->send($response);
        } else {
            $from->send(json_encode(['@type' => 'error']));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->io->writeln('onClose');
    }

    public function onError(ConnectionInterface $conn, Exception $exception)
    {
        $this->io->writeln('onError ' . $exception->getMessage());
    }
}

class TDLibStartCommand extends Command
{
    protected static $defaultName = 'tdlib:start';

    protected function configure()
    {
        $this
            ->setDescription('Start socket server with TDLib JsonClient')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port', 8080)
            ->addOption('api_id', null, InputOption::VALUE_REQUIRED, 'API Id')
            ->addOption('api_hash', null, InputOption::VALUE_REQUIRED, 'API Hash')
            ->addOption('phone_number', null, InputOption::VALUE_REQUIRED, 'Phone number');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $port = $input->getOption('port');
        $phoneNumber = $input->getOption('phone_number');
        $apiId = $input->getOption('api_id');
        $apiHash = $input->getOption('api_hash');
        if (null === $phoneNumber || null === $apiId || null === $apiHash) {
            $io->error('Please fill required options. Type --help for view options list');
            return 1;
        }

        $tdlibParameters = [
            TDLibParameters::USE_TEST_DC => true,
            TDLibParameters::DATABASE_DIRECTORY => '/var/tmp/tdlib',
            TDLibParameters::FILES_DIRECTORY => '/var/tmp/tdlib',
            TDLibParameters::USE_FILE_DATABASE => true,
            TDLibParameters::USE_CHAT_INFO_DATABASE => true,
            TDLibParameters::USE_MESSAGE_DATABASE => true,
            TDLibParameters::USE_SECRET_CHATS => true,
            TDLibParameters::API_ID => $apiId,
            TDLibParameters::API_HASH => $apiHash,
            TDLibParameters::SYSTEM_LANGUAGE_CODE => 'en',
            TDLibParameters::DEVICE_MODEL => php_uname('s'),
            TDLibParameters::SYSTEM_VERSION => php_uname('v'),
            TDLibParameters::APPLICATION_VERSION => '0.0.1',
            TDLibParameters::ENABLE_STORAGE_OPTIMIZER => true,
            TDLibParameters::IGNORE_FILE_NAMES => true,
        ];
        $clientConfig = [
            'phone_number' => $phoneNumber
        ];

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new TDLibWS($io, $tdlibParameters, $clientConfig)
                )
            ),
            $port
        );

        $server->run();
    }
}
