<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Microit\DashboardModuleGithub\HttpStatusCodes;

class Webhook
{
    public readonly array $body;

    public function __construct(public readonly Request $request)
    {
        $body = $this->request->input();
        if (! is_array($body)) {
            $this->responseServerError('Input malformed');

            return;
        }

        $this->body = $body;
    }

    public function process(): void
    {
        $event = $this->request->header('X-GitHub-Event');
        if (is_array($event)) {
            $event = (string) $event[0];
        }
        $this->responseServerError('Process not implemented for '.($event ?? 'unknown'));
    }

    public function responseServerError(string $message): void
    {
        $this->response(['status' => 'error', 'message' => $message], HttpStatusCodes::STATUS_501);
        Log::error('Webhook error', ['Webhook' => __CLASS__, 'Message' => $message]);
    }

    public function response(array $data, HttpStatusCodes $statusCode = HttpStatusCodes::STATUS_200): void
    {
        header($statusCode->value);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}
