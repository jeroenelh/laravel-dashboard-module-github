<?php

namespace Microit\DashboardModuleGithub\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Microit\DashboardModuleGithub\Webhooks\Webhook;

class WebhookController
{
    public function webhook(Request $request): void
    {
        $event = $request->header('X-GitHub-Event');

        if (! is_string($event)) {
            throw new Exception('Unknown github webhook event');
        }

        $class = new Webhook($request);
        $event = Str::studly($event);
        $eventClass = '\Microit\DashboardModuleGithub\Webhooks\\'.$event;
        if (class_exists($eventClass)) {
            /** @var Webhook $eventClass */
            $class = new $eventClass($request);
        }
        $class->process();
    }
}
