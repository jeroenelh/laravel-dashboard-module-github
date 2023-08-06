<?php

namespace Microit\DashboardModuleGithub\Webhooks;

use Exception;
use Microit\DashboardModuleGit\Events\PullRequestClosed;
use Microit\DashboardModuleGit\Events\PullRequestEdited;
use Microit\DashboardModuleGit\Events\PullRequestOpened;
use Microit\DashboardModuleGit\Models\Branch;
use Microit\DashboardModuleGit\Models\PullRequest as PullRequestModel;
use Microit\DashboardModuleGit\Models\Repository;
use Microit\DashboardModuleGit\Models\User;
use Microit\DashboardModuleGithub\Models\GithubBranch;
use Microit\DashboardModuleGithub\Models\GithubPullRequest;
use Microit\DashboardModuleGithub\Models\GithubRepository;
use Microit\DashboardModuleGithub\Models\GithubUser;

class PullRequest extends Webhook
{
    protected ?PullRequestModel $pullRequest = null;

    protected ?Repository $repository = null;

    protected ?Branch $fromBranch = null;

    protected ?Branch $toBranch = null;

    protected ?User $triggerUser = null;

    protected ?User $pullRequestUser = null;

    public function process(): void
    {
        // Load pull request and trigger user and save it to the database
        $this->getPullRequest();
        $this->getTriggerUser();

        assert(is_a($this->pullRequest, PullRequestModel::class));
        assert(is_a($this->triggerUser, User::class));

        // Check action
        switch((string) $this->body['action']) {
            case 'opened':
                $event = new PullRequestOpened($this->pullRequest, $this->triggerUser);
                break;
            case 'closed':
                $event = new PullRequestClosed($this->pullRequest, $this->triggerUser);
                break;
            case 'edited':
                $event = new PullRequestEdited($this->pullRequest, $this->triggerUser);
                break;
            default:
                throw new Exception('Unknown action '.(string) $this->body['action']);
        }

        $event->notify();
    }

    protected function getPullRequest(): PullRequestModel
    {
        if (is_a($this->pullRequest, PullRequestModel::class)) {
            return $this->pullRequest;
        }

        assert(is_array($this->body['pull_request']));
        assert(is_int($this->body['pull_request']['id']));
        assert(is_array($this->body['pull_request']['user']));

        $this->pullRequest = GithubPullRequest::fromAttributes([
            'id' => $this->body['pull_request']['id'],
            'title' => (string) $this->body['pull_request']['title'],
            'number' => (int) $this->body['pull_request']['number'],
            'state' => (string) $this->body['pull_request']['state'],
            'repository_id' => $this->getRepository()->id,
            'user_id' => $this->getPullRequestUser()->id,
            'from_branch_id' => $this->getFromBranch()->id,
            'to_branch_id' => $this->getToBranch()->id,
        ]);

        return $this->pullRequest;
    }

    protected function getRepository(): Repository
    {
        if (is_a($this->repository, Repository::class)) {
            return $this->repository;
        }

        assert(is_array($this->body['repository']));
        assert(is_array($this->body['repository']['owner']));

        $this->repository = GithubRepository::fromAttributes([
            'id' => (string) $this->body['repository']['id'],
            'user' => (string) $this->body['repository']['owner']['login'],
            'name' => (string) $this->body['repository']['name'],
            'is_public' => ($this->body['repository']['private'] === false),
        ]);

        return $this->repository;
    }

    protected function getTriggerUser(): User
    {
        if (is_a($this->triggerUser, User::class)) {
            return $this->triggerUser;
        }

        assert(is_array($this->body['sender']));

        $this->triggerUser = GithubUser::fromAttributes([
            'id' => (int) $this->body['sender']['id'],
            'name' => (string) $this->body['sender']['login'],
            'avatar' => (string) $this->body['sender']['avatar_url'],
        ]);

        return $this->triggerUser;
    }

    protected function getPullRequestUser(): User
    {
        if (is_a($this->pullRequestUser, User::class)) {
            return $this->pullRequestUser;
        }

        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['user']));

        $this->pullRequestUser = GithubUser::fromAttributes([
            'id' => (int) $this->body['pull_request']['user']['id'],
            'name' => (string) $this->body['pull_request']['user']['login'],
            'avatar' => (string) $this->body['pull_request']['user']['avatar_url'],
        ]);

        return $this->pullRequestUser;
    }

    protected function getFromBranch(): Branch
    {
        if (is_a($this->fromBranch, Branch::class)) {
            return $this->fromBranch;
        }

        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['head']));
        assert(is_array($this->body['pull_request']['head']['user']));

        $this->fromBranch = GithubBranch::fromAttributes([
            'id' => (string) $this->body['pull_request']['head']['label'],
            'user' => (string) $this->body['pull_request']['head']['user']['login'],
            'name' => (string) $this->body['pull_request']['head']['ref'],
            'repository_id' => (int) $this->getRepository()->id,
        ]);

        return $this->fromBranch;
    }

    protected function getToBranch(): Branch
    {
        if (is_a($this->toBranch, Branch::class)) {
            return $this->toBranch;
        }

        assert(is_array($this->body['pull_request']));
        assert(is_array($this->body['pull_request']['base']));
        assert(is_array($this->body['pull_request']['base']['user']));

        $this->toBranch = GithubBranch::fromAttributes([
            'id' => (string) $this->body['pull_request']['base']['label'],
            'user' => (string) $this->body['pull_request']['base']['user']['login'],
            'name' => (string) $this->body['pull_request']['base']['ref'],
            'repository_id' => (int) $this->getRepository()->id,
        ]);

        return $this->toBranch;
    }
}
