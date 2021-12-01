<?php

namespace Legrisch\StatamicEnhancedGraphql\Settings;

class ParsedSettings {

  public static $settings;

  private static function getSettings()
  {
    if (!isset(self::$settings)) {
      self::$settings = Settings::read();
    }
    return self::$settings;
  }

  public static function getEnabledCollections() {
    return self::getSettings()['collections'] ?? [];
  }

  public static function getSingleEntryQueries() {
    return self::getSettings()['single_entry_queries'] ?? [];
  }

  public static function getGlobalSetQueries() {
    return self::getSettings()['global_sets'] ?? [];
  }
}