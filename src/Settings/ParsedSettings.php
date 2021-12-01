<?php

namespace Legrisch\StatamicEnhancedGraphql\Settings;

use Statamic\Entries\Collection as EntriesCollection;
use Statamic\Facades\Collection;

class ParsedSettings {

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
  public static function getCollections(): array {
    $handles = self::getSettings()['collections'] ?? [];
    return collect($handles)->map(function($handle) {
      return Collection::findByHandle($handle);
    })->filter(function ($collection) {
      return $collection->entryBlueprints()->count() === 1;
    })->all();
  }

  public static function getSingleEntryQueries() {
    return self::getSettings()['single_entry_queries'] ?? [];
  }

  public static function getGlobalSetQueries() {
    return self::getSettings()['global_sets'] ?? [];
  }
}