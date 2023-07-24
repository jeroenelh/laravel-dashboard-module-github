<?php
namespace Microit\DashboardModuleGithub;

use Illuminate\Support\ServiceProvider;

class GithubServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        exit("TEST BOOT");
    }

    public function register(): void
    {
        exit("TEST REGISTER");
    }
}
