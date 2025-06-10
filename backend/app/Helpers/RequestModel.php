<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class RequestModel
{
    public function __construct(
        private mixed $id,
        private string $bindingField = 'id',
    ) {}

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelName
     * @return TModel|Model
     */
    public function bind(string $modelName, array $columns = ['*']): Model
    {
        if (! is_subclass_of($modelName, Model::class)) {
            throw new InvalidArgumentException('Not model class given');
        }

        if ($this->bindingField !== 'id') {
            return $modelName::query()->where($this->bindingField, $this->id)->firstOrFail($columns);
        }

        return $modelName::query()->findOrFail($this->id, $columns);
    }
}
