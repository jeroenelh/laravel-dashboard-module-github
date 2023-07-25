<?php

namespace Microit\DashboardModuleGithub;

enum HttpStatusCodes: string
{
    case STATUS_200 = 'HTTP/1.1 200 Ok';

    case STATUS_400 = 'HTTP/1.1 400 Bad Request';
    case STATUS_404 = 'HTTP/1.1 404 Not Found';

    case STATUS_500 = 'HTTP/1.1 500 Internal Server Error';
    case STATUS_501 = 'HTTP/1.1 501 Not Implemented';
    case STATUS_502 = 'HTTP/1.1 502 Bad Gateway';
    case STATUS_503 = 'HTTP/1.1 503 Service Unavailable';
    case STATUS_504 = 'HTTP/1.1 504 Gateway Timeout';
}
