<?php

namespace Spatie\LaravelData\Resolvers;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\NameMapper;
use Spatie\LaravelData\Mappers\ProvidedNameMapper;

class NameMappersResolver
{
    public static function create(array $ignoredMappers = []): self
    {
        return new self($ignoredMappers);
    }

    public function __construct(protected array $ignoredMappers = [])
    {
    }

    public function execute(
        Collection $attributes
    ): array {
        return [
            'inputNameMapper' => $this->resolveInputNameMapper($attributes),
            'outputNameMapper' => $this->resolveOutputNameMapper($attributes),
        ];
    }

    protected function resolveInputNameMapper(
        Collection $attributes
    ): ?NameMapper {
        /** @var MapInputName|MapName|null $mapper */
        $mapper = $attributes->first(fn (object $attribute) => $attribute instanceof MapInputName)
            ?? $attributes->first(fn (object $attribute) => $attribute instanceof MapName);

        if ($mapper) {
            return $this->resolveMapper($mapper->input);
        }

        return $this->resolveDefaultNameMapper(config('data.name_mapping_strategy.input'));
    }

    protected function resolveOutputNameMapper(
        Collection $attributes
    ): ?NameMapper {
        /** @var MapOutputName|MapName|null $mapper */
        $mapper = $attributes->first(fn (object $attribute) => $attribute instanceof MapOutputName)
            ?? $attributes->first(fn (object $attribute) => $attribute instanceof MapName);

        if ($mapper) {
            return $this->resolveMapper($mapper->output);
        }

        return $this->resolveDefaultNameMapper(config('data.name_mapping_strategy.output'));
    }

    protected function resolveMapper(string|int|NameMapper $value): ?NameMapper
    {
        $mapper = $this->resolveMapperClass($value);

        foreach ($this->ignoredMappers as $ignoredMapper) {
            if ($mapper instanceof $ignoredMapper) {
                return null;
            }
        }

        return $mapper;
    }

    protected function resolveMapperClass(int|string|NameMapper $value): NameMapper
    {
        if (is_int($value)) {
            return new ProvidedNameMapper($value);
        }

        if ($value instanceof NameMapper) {
            return $value;
        }

        if (is_a($value, NameMapper::class, true)) {
            return resolve($value);
        }

        return new ProvidedNameMapper($value);
    }

    protected function resolveDefaultNameMapper(
        ?string $value,
    ): ?NameMapper {
        if ($value === null) {
            return null;
        }

        return $this->resolveMapperClass($value);
    }
}