<?php

namespace Bluebik\DbPasswordSecure\Console\Commands;

use Bluebik\Logger\LoggerFactory;
use Config;
use Crypt;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'db-password-secure:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set database info on .env file';

    /**
     * @var \Bluebik\Logger\LoggerFactory
     */
    private $logger;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->logger = LoggerFactory::create("command");
    }

    /**
     * @var array
     */
    protected $secureConfig = [
        'DB_PASSWORD_SECURE',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $path = base_path('.env');
        if (!file_exists($path)) {
            touch($path);
        }

        $this->logger->action(__METHOD__, 'Setup database (start)', [], true);
        $this->info('======  setup database (start) ======');
        $env['DB_HOST'] = $this->ask("database's host ", env('DB_HOST'));
        $env['DB_DATABASE'] = $this->ask("database's name ", env('DB_DATABASE'));
        $env['DB_USERNAME'] = $this->ask("database's username ", env('DB_USERNAME'));
        do {
            $env['DB_PASSWORD_SECURE'] = $this->secret("database's password ");
        } while ($this->secret("database's password (confirm) ") !== $env['DB_PASSWORD_SECURE']);

        $this->info('======  setup database (end) ======');
        $this->logger->action(__METHOD__, 'Setup database (end)', [], true);

        if (file_exists($path)) {

            foreach ($env as $key => $value) {

                if (in_array($key, $this->secureConfig)) {
                    $value = Crypt::encrypt($value);
                }

                $oldValue = env($key);

                file_put_contents($path, str_replace(
                    "$key=$oldValue",
                    "$key=$value",
                    file_get_contents($path)
                ));

            }
            $this->logger->action(__METHOD__, 'Write to .env file', [], true);
        }

    }
}
