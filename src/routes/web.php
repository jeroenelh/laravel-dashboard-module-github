<?php

use Illuminate\Support\Facades\Route;

Route::post('/webhooks/git/github', [
    \Microit\DashboardModuleGithub\Controllers\WebhookController::class, 'webhook',
]);
