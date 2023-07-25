<?php

namespace Microit\DashboardModuleGithub\Webhooks;

class Ping extends Webhook
{
    public function process(): void
    {
        $this->response(['status' => 'ok', 'message' => 'Pong!']);
    }
}
