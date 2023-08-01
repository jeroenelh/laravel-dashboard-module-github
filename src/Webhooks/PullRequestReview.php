<?php

namespace Microit\DashboardModuleGithub\Webhooks;

class PullRequestReview extends PullRequest
{
    public function process(): void
    {
        // Load pull request and trigger user and save it to the database
        $this->getPullRequest();
        $this->getTriggerUser();
    }
}
