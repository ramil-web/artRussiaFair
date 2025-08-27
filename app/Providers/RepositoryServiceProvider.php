<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $repositories = $this->getRepositories();

        foreach ($repositories as $repository) {
                $this->app->bind($repository['interface'], $repository['implementation']);
        }
    }


    private function getRepositories(): array
    {
        return config('repositories') ?? [];
    }
}
