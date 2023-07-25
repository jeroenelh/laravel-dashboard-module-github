<?php

namespace Microit\DashboardModuleGithub;

use Illuminate\Support\ServiceProvider;

class GithubServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }

    public function register(): void
    {
    }
}
