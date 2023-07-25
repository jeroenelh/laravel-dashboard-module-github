<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Exception;
use Illuminate\Http\Request;
use Microit\DashboardModuleGithub\HttpStatusCodes;

class Webhook
{
    public readonly array $body;

    public function __construct(public readonly Request $request)
    {
        $body = $this->request->input();
        if (! is_array($body)) {
            throw new Exception('Input malformed');
        }

        $this->body = $body;
    }

    public function process(): void
    {
        $this->response(['status' => 'error', 'message' => 'Process not implemented for '.__CLASS__], HttpStatusCodes::STATUS_501);
    }

    public function response(array $data, HttpStatusCodes $statusCode = HttpStatusCodes::STATUS_200): void
    {
        header($statusCode->value);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}
