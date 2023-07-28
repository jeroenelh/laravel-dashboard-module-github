<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Exception;
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
        $this->responseServerError('Process not implemented for '.__CLASS__);
    }

    public function responseServerError($message): void
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
