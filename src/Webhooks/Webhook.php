<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        $event = Str::studly($event ?? 'unknown');
        $this->responseServerError('Process not implemented for '.$event, HttpStatusCodes::STATUS_501);
    }

    public function responseServerError(string $message, HttpStatusCodes $statusCode = HttpStatusCodes::STATUS_503): void
    {
        $this->response(['status' => 'error', 'message' => $message], $statusCode);
        Log::error('Webhook error', ['Webhook' => __CLASS__, 'Message' => $message]);
    }

    public function response(array $data, HttpStatusCodes $statusCode = HttpStatusCodes::STATUS_200): void
    {
        header($statusCode->value);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}
