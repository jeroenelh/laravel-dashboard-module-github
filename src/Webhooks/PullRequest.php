<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Microit\DashboardModuleGit\Models\Branch;
use Microit\DashboardModuleGit\Models\PullRequest as PullRequestModel;
use Microit\DashboardModuleGit\Models\Repository;
use Microit\DashboardModuleGit\Models\User;

class PullRequest extends Webhook
{
    protected ?PullRequestModel $pullRequest = null;

    protected ?Repository $repository = null;

    public function process(): void
    {
        $this->repository = $this->getRepository();

        assert(is_array($this->body['pull_request']));
        assert(is_int($this->body['pull_request']['id']));

        $this->pullRequest = new PullRequestModel(
            id: $this->body['pull_request']['id'],
            title: (string) $this->body['pull_request']['title'],
            number: (int) $this->body['pull_request']['number'],
            state: (string) $this->body['pull_request']['state'],
            repository: $this->repository,
            user: $this->getUser(),
            fromBranch: $this->getFromBranch(),
            toBranch: $this->getToBranch()
        );
    }

    private function getRepository(): Repository
    {
        assert(is_array($this->body['repository']));
        assert(is_array($this->body['repository']['owner']));

        return new Repository(
            id: (string) $this->body['repository']['id'],
            user: (string) $this->body['repository']['owner']['login'],
            name: (string) $this->body['repository']['name'],
            isPublic: ($this->body['repository']['private'] === false)
        );
    }

    private function getUser(): User
    {
        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['user']));

        return new User(
            id: (int) $this->body['pull_request']['user']['id'],
            name: (string) $this->body['pull_request']['user']['login']
        );
    }

    private function getFromBranch(): Branch
    {
        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['head']));
        assert(is_object($this->repository));

        return new Branch(
            name: (string) $this->body['pull_request']['head']['ref'],
            repository: $this->repository
        );
    }

    private function getToBranch(): Branch
    {
        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['base']));
        assert(is_object($this->repository));

        return new Branch(
            name: (string) $this->body['pull_request']['base']['ref'],
            repository: $this->repository
        );
    }
}
