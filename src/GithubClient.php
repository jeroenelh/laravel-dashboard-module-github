<?php

namespace Microit\DashboardModuleGithub;

use Exception;
use Illuminate\Support\Collection;
use Microit\DashboardModuleGit\Client;
use Microit\DashboardModuleGithub\Models\GithubRepository;

class GithubClient extends Client
{
    public const API_HOSTNAME = 'https://api.github.com/';

    public const API_VERSION = '2022-11-28';

    public function __construct(string $token)
    {
        parent::__construct(self::API_HOSTNAME);
        $this->addHeader('X-GitHub-Api-Version', self::API_VERSION);
        $this->addHeader('Authorization', 'Bearer '.$token);
    }

    public function getPublicRepositoriesOfUser(string $user): Collection
    {
        $repositoryInformationCollection = $this->request(sprintf('users/%s/repos', $user));

        return $this->processRepositoryResponse($repositoryInformationCollection);
    }

    public function getRepositoriesOfLoggedInUser(): Collection
    {
        $repositoryInformationCollection = $this->request('user/repos');

        return $this->processRepositoryResponse($repositoryInformationCollection);
    }

    public function getRepositoriesOfOrganization(string $organization): Collection
    {
        $repositoryInformationCollection = $this->request(sprintf('orgs/%s/repos', $organization));

        return $this->processRepositoryResponse($repositoryInformationCollection);
    }

    private function processRepositoryResponse(mixed $response): Collection
    {
        if (! is_array($response)) {
            throw new Exception('No repository information received');
        }

        return new Collection(array_map(function ($item) {
            assert(is_object($item));
            assert(is_object($item->owner) && is_string($item->owner->login));
            assert(is_string($item->name));
            assert(is_bool($item->private));

            return GithubRepository::fromAttributes([
                'id' => (string) $item->id,
                'user' => $item->owner->login,
                'name' => $item->name,
                'is_public' => (! $item->private),
            ]);
        }, $response));
    }
}
