<?php

namespace App\Contracts;

use App\Models\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContainerRepositoryInterface
{
    public function findById(int $id): ?Container;

    public function findByVmid(int $vmid): ?Container;

    /**
     * @return Collection<int, Container>
     */
    public function allForUser(int $userId): Collection;

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Container;

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Container $container, array $attributes): Container;

    public function delete(Container $container): bool;

    public function nextAvailableVmid(): int;
}
