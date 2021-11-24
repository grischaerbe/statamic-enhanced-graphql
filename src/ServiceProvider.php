<?php

namespace Legrisch\StatamicEnhancedGraphql;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\GraphQL;
use Facades\Statamic\GraphQL\TypeRegistrar;
use Illuminate\Support\Facades\Log;
use Statamic\Events\BlueprintDeleted;
use Statamic\Events\BlueprintSaved;
use Statamic\Events\CollectionDeleted;
use Statamic\Events\CollectionSaved;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{

    protected $listen = [
        BlueprintSaved::class => [
            EventListener::class,
        ],
        BlueprintDeleted::class => [
            EventListener::class,
        ],
        CollectionDeleted::class => [
            EventListener::class,
        ],
        CollectionSaved::class => [
            EventListener::class,
        ],
    ];

    protected $updateScripts = [
        Updater::class,
    ];

    private function registerTypes(): void
    {
        TypeRegistrar::register();
    }

    private function registerQueries(): void
    {
        foreach (glob(__DIR__ . "/Queries/*.php") as $filename)
        {
            $className = basename($filename, '.php');
            try {
                GraphQL::addQuery("Legrisch\StatamicEnhancedGraphql\Queries\\$className");
            } catch (\Throwable $th) {
                Log::error("Adding the GraphQL queries failed.");
            }
        }
    }

    public function bootAddon()
    {
        $this->registerTypes();
        $this->registerQueries();

        Statamic::afterInstalled(function () {
            ClassBuilder::buildClasses();
        });
    }
}