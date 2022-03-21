<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

use Legrisch\StatamicEnhancedGraphql\Settings\ParsedSettings;
use Statamic\Entries\Collection;
use Statamic\Fields\Blueprint;
use Statamic\GraphQL\Types\EntryType;
use Statamic\Support\Str;

class EntriesBuilder
{
  private static function buildQueryName(Collection $collection, Blueprint $blueprint): string
  {
    return $collection->handle() . 'Entries';
  }

  private static function buildClassName(Collection $collection, Blueprint $blueprint): string
  {
    return Str::studly($collection->handle()) . 'EntriesQuery';
  }

  public static function build()
  {
    $collections = ParsedSettings::getCollections();

    foreach ($collections as $collection) {
      $blueprint = $collection->entryBlueprints()->first();
      $typeName = EntryType::buildName($collection, $blueprint);
      $className = static::buildClassName($collection, $blueprint);
      $queryName = static::buildQueryName($collection, $blueprint);
      $input = file_get_contents(__DIR__ . "/../templates/Entries.txt");
      $output = Replacer::replace($input, [
        "className" => $className,
        "queryName" => $queryName,
        "typeName" => $typeName,
        "collectionHandle" => $collection->handle()
      ]);
      file_put_contents(__DIR__ . "/../Queries/$className.php", $output);
    }
  }
}
