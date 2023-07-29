<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Microit\DashboardModuleGit\Models\Branch;
use Microit\DashboardModuleGit\Models\PullRequest as PullRequestModel;
use Microit\DashboardModuleGit\Models\Repository;
use Microit\DashboardModuleGit\Models\User;
use Microit\DashboardModuleGithub\Models\GithubBranch;
use Microit\DashboardModuleGithub\Models\GithubRepository;
use Microit\DashboardModuleGithub\Models\GithubUser;

class PullRequest extends Webhook
{
    protected ?PullRequestModel $pullRequest = null;

    protected ?Repository $repository = null;

    protected ?User $user = null;

    public function process(): void
    {
        $this->repository = $this->getRepository();
        $this->user = $this->getUser();

        assert(is_array($this->body['pull_request']));
        assert(is_int($this->body['pull_request']['id']));
        assert(is_array($this->body['pull_request']['user']));

        $pullRequestUser = GithubUser::fromAttributes([
            'id' => (int) $this->body['pull_request']['user']['id'],
            'name' => (string) $this->body['pull_request']['user']['login'],
        ]);

        $this->pullRequest = new PullRequestModel(
            id: $this->body['pull_request']['id'],
            title: (string) $this->body['pull_request']['title'],
            number: (int) $this->body['pull_request']['number'],
            state: (string) $this->body['pull_request']['state'],
            repository: $this->repository,
            user: $pullRequestUser,
            fromBranch: $this->getFromBranch(),
            toBranch: $this->getToBranch()
        );
    }

    private function getRepository(): Repository
    {
        assert(is_array($this->body['repository']));
        assert(is_array($this->body['repository']['owner']));

        return GithubRepository::fromAttributes([
            'id' => (string) $this->body['repository']['id'],
            'user' => (string) $this->body['repository']['owner']['login'],
            'name' => (string) $this->body['repository']['name'],
            'is_public' => ($this->body['repository']['private'] === false),
        ]);
    }

    private function getUser(): User
    {
        assert(is_array($this->body['sender']));

        return GithubUser::fromAttributes([
            'id' => (int) $this->body['sender']['id'],
            'name' => (string) $this->body['sender']['login'],
        ]);
    }

    private function getFromBranch(): Branch
    {
        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['head']));
        assert(is_array($this->body['pull_request']['head']['user']));
        assert(is_object($this->repository));

        return GithubBranch::fromAttributes([
            'name' => (string) $this->body['pull_request']['head']['ref'],
            'user' => (string) $this->body['pull_request']['head']['user']['login'],
            'repository' => $this->repository->id,
        ]);
    }

    private function getToBranch(): Branch
    {
        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['base']));
        assert(is_array($this->body['pull_request']['base']['user']));
        assert(is_object($this->repository));

        return GithubBranch::fromAttributes([
            'name' => (string) $this->body['pull_request']['base']['ref'],
            'user' => (string) $this->body['pull_request']['base']['user']['login'],
            'repository' => $this->repository->id,
        ]);
    }
}
