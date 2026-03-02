<?php

namespace App\Repositories;

use App\Contracts\ContainerRepositoryInterface;
use App\Models\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentContainerRepository implements ContainerRepositoryInterface
{
    public function findById(int $id): ?Container
    {
        return Container::query()->find($id);
    }

    public function findByVmid(int $vmid): ?Container
    {
        return Container::query()->where('vmid', $vmid)->first();
    }

    /**
     * @return Collection<int, Container>
     */
    public function allForUser(int $userId): Collection
    {
        return Container::query()->where('user_id', $userId)->get();
    }

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Container::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Container
    {
        return Container::query()->create($attributes);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(Container $container, array $attributes): Container
    {
        $container->update($attributes);

        return $container->fresh();
    }

    public function delete(Container $container): bool
    {
        return (bool) $container->delete();
    }

    public function nextAvailableVmid(): int
    {
        $max = Container::query()->max('vmid');
        $min = (int) config('proxmox.vmid_start', 1000);

        return max($min, (int) $max + 1);
    }
}
