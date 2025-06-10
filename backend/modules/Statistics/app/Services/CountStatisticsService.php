<?php

declare(strict_types=1);

namespace Modules\Statistics\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class CountStatisticsService implements StatisticsServiceInterface
{
    protected Collection $models;

    public function for(string|array $model): static
    {
        $models = Arr::wrap($model);

        $this->models = collect();

        foreach ($models as $modalName) {
            $model = new $modalName;

            if (! $model instanceof Model) {
                throw new RuntimeException('Giving value is not instance of Eloquent Model');
            }

            $this->models->add($model);
        }

        return $this;
    }

    public function apply(array $handlers): Collection
    {
        $handlersCollection = collect($handlers);

        // When defined default array 0 - count - 1, it will apply all handlers to each
        // model
        if (array_is_list($handlersCollection->toArray())) {
            return $this->applyHandlersToAllModels($handlersCollection)->collapse();
        }

        $result = collect();

        $handlersCollection->each(function ($handler, $key) use ($result) {
            // when in not default array, defined handler without assoc key,
            // it will apply all handlers to each model
            if ($handler instanceof StatisticsHandlerInterface && is_int($key)) {
                $result->add($this->applyHandlerToAllModels($handler));
            }

            // when defined handlers to specific model by it key, it will apply only related
            // handlers
            if (is_string($key)) {
                $result->add(
                    $this->applyHandlersToSpecificModel(collect([$handler])->flatten(), $key),
                );
            }
        });

        return $result->collapse();
    }

    protected function applyHandlersToSpecificModel(Collection $handlers, string $modelKey): Collection
    {
        $model = $this->models->first(function (Model $model) use ($modelKey) {
            return get_class($model) === $modelKey;
        });

        return $handlers->map(function (StatisticsHandlerInterface $handler) use ($model) {
            return $this->applyHandlerToModel($handler, $model);
        })->collapse();
    }

    protected function applyHandlersToAllModels(Collection $handlers): Collection
    {
        return $handlers->map(function (StatisticsHandlerInterface $handler) {
            return $this->applyHandlerToAllModels($handler);
        });
    }

    protected function applyHandlerToAllModels(StatisticsHandlerInterface $handler): Collection
    {
        return $this->models->map(function (Model $model) use ($handler) {
            return $this->applyHandlerToModel($handler, $model);
        })->collapse();
    }

    protected function applyHandlerToModel(StatisticsHandlerInterface $handler, Model $model): array
    {
        $classBaseName = Str::snake(class_basename($model));

        $model = in_array(SoftDeletes::class, class_uses($model), true)
            ? $model->withTrashed()
            : $model;

        $query = $handler->handle($model->newQuery());

        return [
            $classBaseName.':'.$handler->identifier() => $query->count(),
        ];
    }
}
