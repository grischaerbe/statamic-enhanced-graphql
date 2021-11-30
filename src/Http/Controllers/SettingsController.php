<?php

namespace Legrisch\StatamicEnhancedGraphql\Http\Controllers;

use Legrisch\StatamicEnhancedGraphql\Settings\Settings;
use Illuminate\Http\Request;
use Legrisch\StatamicEnhancedGraphql\Manager;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Config;
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
      'values'    => $fields->values(),
      'meta'      => $fields->meta(),
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
            'type' => 'collections',
            'icon' => 'collections',
            'label' => 'Collections',
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