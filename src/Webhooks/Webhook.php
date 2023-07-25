<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Exception;

class Webhook
{
    public function __construct(public readonly array $request)
    {
    }

    public function process(): void
    {
        throw new Exception('Process for '.__CLASS__.' not implemented');
    }
}
