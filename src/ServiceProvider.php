<?php

namespace Legrisch\StatamicEnhancedGraphql;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\GraphQL;
use Illuminate\Support\Facades\Log;
use Statamic\Events\BlueprintDeleted;
use Statamic\Events\BlueprintSaved;
use Statamic\Events\CollectionDeleted;
use Statamic\Events\CollectionSaved;
use Statamic\Events\GlobalSetDeleted;
use Statamic\Statamic;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;

class EntryPolicy
{
  public function edit($user)
  {
    return $user->hasPermission("manage graphql queries");
  }
}


class ServiceProvider extends AddonServiceProvider
{

    protected $routes = [
        'cp' => __DIR__ . '/routes/cp.php',
      ];

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
        GlobalSetDeleted::class => [
            EventListener::class,
        ],
    ];

    protected $updateScripts = [
        Updater::class,
    ];

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

        parent::boot();

        $this->loadViewsFrom(__DIR__ . '/../resources/views/', 'statamic-enhanced-graphql');

        Nav::extend(function ($nav) {
        $nav->content('Enhanced GraphQL')
            ->section('Tools')
            ->can('manage graphql queries')
            ->route('legrisch.statamic-enhanced-graphql.index')
            ->icon('cache');
        });
        
        Permission::register('manage graphql queries')
            ->label('Manage GraphQL Queries');
    
        $this->registerQueries();

        Statamic::afterInstalled(function () {
            Manager::buildClasses();
        });
    }
}