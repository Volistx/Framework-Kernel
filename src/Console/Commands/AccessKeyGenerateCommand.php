<?php

namespace Volistx\FrameworkKernel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Volistx\FrameworkKernel\Enums\AccessRule;
use Volistx\FrameworkKernel\Helpers\SHA256Hasher;
use Volistx\FrameworkKernel\Models\AccessToken;
use Volistx\FrameworkKernel\Repositories\AccessTokenRepository;

class AccessKeyGenerateCommand extends Command
{
    private AccessTokenRepository $accessTokenRepository;

    public function __construct(AccessTokenRepository $accessTokenRepository)
    {
        parent::__construct();
        $this->accessTokenRepository = $accessTokenRepository;
    }
    protected $signature = 'access-key:generate';

    protected $description = 'Create an access key';

    public function handle(): void
    {
        $key = Str::random(64);
        $salt = Str::random(16);

        $this->accessTokenRepository->Create([
            'key'           => substr($key, 0, 32),
            'secret'        => SHA256Hasher::make(substr($key, 32), ['salt' => $salt]),
            'secret_salt'   => $salt,
            'permissions'   => ['*'],
            'ip_rule'       => AccessRule::NONE,
            'ip_range'      => [],
            'country_rule'  => AccessRule::NONE,
            'country_range' => [],
        ]);

        $this->info('Your access key is created: '.$key);
    }
}
