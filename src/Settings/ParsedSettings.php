<?php

namespace Legrisch\StatamicEnhancedGraphql\Settings;

use Statamic\Entries\Collection as EntriesCollection;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Taxonomy;

class ParsedSettings
{

  public static $settings;

  private static function getSettings()
  {
    if (!isset(self::$settings)) {
      self::$settings = Settings::read();
    }
    return self::$settings;
  }

  /**
   * @return EntriesCollection[]
   */
  public static function getCollections(): array
  {
    $handles = self::getSettings()['collections'] ?? [];
    return collect($handles)->map(function ($handle) {
      return Collection::findByHandle($handle);
    })->filter(function ($collection) {
      return $collection->entryBlueprints()->count() === 1;
    })->all();
  }

  /**
   * Returns an array of objects:
   * {
   *   "entry" => Entry
   *   "query_name" => string
   * }[]
   *
   * @return array
   */
  public static function getSingleEntryQueries(): array
  {
    $queries = self::getSettings()['single_entry_queries'] ?? [];
    return collect($queries)->filter(function ($query) {
      return ($query['enabled'] ?? false);
    })->map(function ($query) {
      $entryId = $query['entry'][0];
      return [
        "entry" => Entry::find($entryId),
        "query_name" => $query["query_name"]
      ];
    })->filter(function ($query) {
      return !!$query['entry'];
    })->all();
  }

  public static function getGlobalSets(): array
  {
    $handles = self::getSettings()['global_sets'] ?? [];
    return collect($handles)->map(function ($handle) {
      return GlobalSet::findByHandle($handle);
    })->all();
  }

  public static function getTaxonomies(): array
  {
    $handles = self::getSettings()['taxonomies'] ?? [];
    return collect($handles)->map(function ($handle) {
      return Taxonomy::findByHandle($handle);
    })->filter(function ($taxonomy) {
      return $taxonomy->termBlueprints()->count() === 1;
    })->all();
  }
}
