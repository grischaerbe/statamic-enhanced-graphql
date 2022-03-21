<?php

namespace Legrisch\StatamicEnhancedGraphql\Http\Controllers;

use Illuminate\Http\Request;
use Legrisch\StatamicEnhancedGraphql\Manager;
use Legrisch\StatamicEnhancedGraphql\Settings\Settings;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Taxonomy;
use Statamic\Facades\User;
use Statamic\Http\Controllers\CP\CpController;

class SettingsController extends CpController
{
  public function __construct(Request $request)
  {
    parent::__construct($request);
  }

  public function index(Request $request)
  {
    if (!User::current()->can('manage graphql queries')) {
      // TODO naive Permissions handling
      return;
    }

    $blueprint = $this->formBlueprint();
    $fields = $blueprint->fields();

    $values = Settings::read(false);

    $fields = $fields->addValues($values);

    $fields = $fields->preProcess();

    return view('statamic-enhanced-graphql::settings', [
      'blueprint' => $blueprint->toPublishArray(),
      'values' => $fields->values(),
      'meta' => $fields->meta(),
    ]);
  }

  public function update(Request $request)
  {
    if (!User::current()->can('manage graphql queries')) {
      // TODO naive Permissions handling
      return;
    }

    $blueprint = $this->formBlueprint();
    $fields = $blueprint->fields()->addValues($request->all());

    // Perform validation. Like Laravel's standard validation, if it fails,
    // a 422 response will be sent back with all the validation errors.
    $fields->validate();

    // Perform post-processing. This will convert values the Vue components
    // were using into values suitable for putting into storage.
    $values = $fields->process()->values();

    Settings::write($values->toArray());

    Manager::buildClasses();
  }

  protected function formBlueprint()
  {

    $globalSets = GlobalSet::all();
    $globalSetsOptions = array();
    $globalSets->each(function ($globalSet) use (&$globalSetsOptions) {
      $globalSetsOptions[$globalSet->handle()] = $globalSet->title();
    });

    $taxonomies = Taxonomy::all();
    $taxonomiesOptions = array();
    $taxonomies->each(function ($taxonomy) use (&$taxonomiesOptions) {
      $taxonomiesOptions[$taxonomy->handle()] = $taxonomy->title();
    });

    $collections = Collection::all();
    $collectionsOptions = array();
    $collections->each(function ($collection) use (&$collectionsOptions) {
      $collectionsOptions[$collection->handle()] = $collection->title();
    });

    return Blueprint::makeFromSections([
      'collections' => [
        'handle' => 'collections',
        'fields' => [
          'collections_section' => [
            'type' => 'section',
            'display' => 'Collections',
            'instructions' => 'Transform Collections to GraphQL queries.',
          ],
          'collections' => [
            'display' => 'Collections',
            'options' => $collectionsOptions,
            'multiple' => true,
            'clearable' => true,
            'searchable' => true,
            'taggable' => false,
            'push_tags' => false,
            'cast_booleans' => false,
            'type' => 'select',
            'icon' => 'select',
            'listable' => 'hidden',
            'instructions_position' => 'above',
          ],
        ]
      ],
      'global_sets' => [
        'handle' => 'global_sets',
        'display' => 'Global Sets',
        'fields' => [
          'global_sets_section' => [
            'type' => 'section',
            'display' => 'Global Sets',
            'instructions' => 'Transform Global Sets to GraphQL queries.',
          ],
          'global_sets' => [
            'display' => 'Global Sets',
            'options' => $globalSetsOptions,
            'multiple' => true,
            'clearable' => true,
            'searchable' => true,
            'taggable' => false,
            'push_tags' => false,
            'cast_booleans' => false,
            'type' => 'select',
            'icon' => 'select',
            'listable' => 'hidden',
            'instructions_position' => 'above',
          ],
        ]
      ],
      'taxonomies' => [
        'handle' => 'taxonomies',
        'display' => 'Taxonomies',
        'fields' => [
          'taxonomies_section' => [
            'type' => 'section',
            'display' => 'Taxonomies',
            'instructions' => 'Transform Taxonomies to GraphQL queries.',
          ],
          'taxonomies' => [
            'display' => 'Taxonomies',
            'options' => $taxonomiesOptions,
            'multiple' => true,
            'clearable' => true,
            'searchable' => true,
            'taggable' => false,
            'push_tags' => false,
            'cast_booleans' => false,
            'type' => 'select',
            'icon' => 'select',
            'listable' => 'hidden',
            'instructions_position' => 'above',
          ],
        ]
      ],
      'single_entry_queries' => [
        'handle' => 'single_entry_queries',
        'display' => 'Single Entry Queries',
        'fields' => [
          'single_entry_queries_section' => [
            'type' => 'section',
            'display' => 'Single Entry Queries',
            'instructions' => 'Transform individual entries to GraphQL queries.',
          ],
          'single_entry_queries' => [
            'type' => 'replicator',
            'display' => 'Single Entry Queries',
            'sets' => [
              'entry' => [
                'display' => 'Single Entry Query',
                'fields' => [
                  'entry' => [
                    'handle' => 'entry',
                    'field' => [
                      'max_items' => '1',
                      'create' => false,
                      'mode' => 'default',
                      'display' => 'Entry',
                      'type' => 'entries',
                      'icon' => 'entries',
                      'width' => 50,
                      'listable' => 'hidden',
                      'instructions_position' => 'above',
                      'validate' => ['required']
                    ]
                  ],
                  'query_name' => [
                    'handle' => 'query_name',
                    'field' => [
                      'input_type' => 'text',
                      'antlers' => false,
                      'display' => "Query Name",
                      'type' => "text",
                      'icon' => "text",
                      'width' => 50,
                      'listable' => "visible",
                      'validate' => ['required', 'alphadash']
                    ],
                  ],
                ]
              ]
            ]
          ],
        ],
      ],
    ]);
  }
}
