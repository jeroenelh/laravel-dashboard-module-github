<?php

namespace Microit\DashboardModuleGithub\Controllers;

use Illuminate\Http\Request;

class WebhookController
{
    public function webhook(Request $request): void
    {
        echo var_export($request->input(), 1);
    }
}
