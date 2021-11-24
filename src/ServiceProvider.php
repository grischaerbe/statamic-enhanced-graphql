<?php

namespace Legrisch\StatamicEnhancedGraphql;

use Statamic\Facades\Collection;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\GraphQL;
use Statamic\GraphQL\Types\EntryType;
use Facades\Statamic\GraphQL\TypeRegistrar;
use Statamic\GraphQL\Queries\EntriesQuery;
use Statamic\GraphQL\Queries\EntryQuery;
use Illuminate\Pagination\Paginator;
use Rebing\GraphQL\Support\Facades\GraphQL as FacadesGraphQL;
use Statamic\Support\Str;

class ServiceProvider extends AddonServiceProvider
{
    private static function buildQueryName(\Statamic\Contracts\Entries\Collection $collection, \Statamic\Fields\Blueprint $blueprint, bool $isCollection, bool $hasMultipleBlueprints) {
        if ($hasMultipleBlueprints) {
            if ($isCollection) {
                return $collection->handle() . Str::studly($blueprint->handle()) . 'Entries';
            } else {
                return $collection->handle() . Str::studly($blueprint->handle()) . 'Entry';
            }
        } else {
            if ($isCollection) {
                return $collection->handle() . 'Entries';
            } else {
                return $collection->handle() . 'Entry';
            }
        }
    }

    private function addCollectionQueries(): void
    {
        $collections = Collection::all();
        $collections->each(function ($collection) {
            $blueprints = $collection->entryBlueprints();
            if ($blueprints->count() > 1) {
                // TODO: add support for multiple blueprints
            } else {
                $typeName = EntryType::buildName($collection, $blueprints->first());
                $queryName = static::buildQueryName($collection, $blueprints->first(), true, false);
                $paginationTypeName = $typeName . 'Pagination';

                $args = (new EntriesQuery())->args();
                unset($args['collection']);

                // TODO: add support for filter
                unset($args['filter']);

                GraphQL::addQuery([
                    'name' => $queryName,
                    'type' => function() use ($typeName, $paginationTypeName) {
                        return FacadesGraphQL::paginate(GraphQL::type("$typeName!"), $paginationTypeName);
                    },
                    'args' => $args,
                    'resolve' => function ($root, $args) use ($collection) {
                        $q = new EntriesQuery();
                        $args['collection'] = [$collection->handle()];
                        Paginator::currentPageResolver(function () use ($args) {
                            return $args['page'] ?? 1;
                        });
                        return $q->resolve($root, $args);
                    },
                ]);
            }
        });
    }

    private function addEntryQueries(): void
    {
        $collections = Collection::all();
        $collections->each(function ($collection) {
            $blueprints = $collection->entryBlueprints();
            if ($blueprints->count() > 1) {
                // TODO: add support for multiple blueprints
            } else {
                $typeName = EntryType::buildName($collection, $blueprints->first());
                $queryName = static::buildQueryName($collection, $blueprints->first(), false, false);

                $args = (new EntryQuery())->args();
                unset($args['collection']);

                GraphQL::addQuery([
                    'name' => $queryName,
                    'type' => function() use ($typeName) {
                        return GraphQL::type("$typeName");
                    },
                    'args' => $args,
                    'resolve' => function ($root, $args) use ($collection) {
                        $q = new EntryQuery();
                        $args['collection'] = $collection->handle();
                        return $q->resolve($root, $args);
                    },
                ]);
            }
        });
    }

    private function registerTypes(): void
    {
        TypeRegistrar::register();
    }

    private function addQueries(): void
    {    
        $this->addCollectionQueries();
        $this->addEntryQueries();
    }

    public function bootAddon()
    {
        $this->registerTypes();
        $this->addQueries();
    }
}