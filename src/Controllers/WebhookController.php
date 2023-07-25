<?php

namespace Microit\DashboardModuleGithub\Controllers;

use Exception;
use Illuminate\Http\Request;
use Microit\DashboardModuleGithub\Webhooks\Webhook;

class WebhookController
{
    public function webhook(Request $request): void
    {
        $event = $request->header('X-GitHub-Event', null);

        if (! is_string($event)) {
            throw new Exception('Unknown github webhook event');
        }

        $eventClass = '\Microit\DashboardModuleGithub\Webhooks\\'.$event;
        if (class_exists($eventClass)) {
            /** @var Webhook $eventClass */
            $class = new $eventClass($request);
            $class->process();
        }
    }
}
