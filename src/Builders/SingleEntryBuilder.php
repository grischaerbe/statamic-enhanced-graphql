<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

use Legrisch\StatamicEnhancedGraphql\Settings\ParsedSettings;
use Statamic\Contracts\Entries\Collection as CollectionType;
use Statamic\Fields\Blueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\GraphQL\Types\EntryType;
use Statamic\Support\Str;

class SingleEntryBuilder {
  private static function buildClassName($queryName): string {
    return Str::studly($queryName) . 'SingleEntryQuery';
  }

  private static function parseTemplate(
    string $input,
    string $className,
    string $queryName,
    string $typeName,
    string $entryId
  ): string
  {
    $input = str_replace('<%--CLASS_NAME--%>', $className, $input);
    $input = str_replace('<%--QUERY_NAME--%>', $queryName, $input);
    $input = str_replace('<%--TYPE_NAME--%>', $typeName, $input);
    $input = str_replace('<%--ENTRY_ID--%>', $entryId, $input);
    return $input;
  }

  public static function build() {

    $singleEntryQueries = ParsedSettings::getSingleEntryQueries();

    foreach ($singleEntryQueries as $singleEntryQuery) {
      if (!($singleEntryQuery['enabled'] ?? false)) {
        continue;
      }
      $entryId = $singleEntryQuery['entry'][0];
      $entry = Entry::find($entryId);
      $blueprint = $entry->blueprint();
      $collection = $entry->collection();

      $typeName = EntryType::buildName($collection, $blueprint);
      $queryName = $singleEntryQuery['query_name'];
      $className = static::buildClassName($queryName);
      $inputSingleQuery = file_get_contents(__DIR__ . "/../templates/SingleEntry.txt");
      $outputSingleQuery = static::parseTemplate($inputSingleQuery, $className, $queryName, $typeName, $entryId);
      file_put_contents(__DIR__ . "/../Queries/$className.php", $outputSingleQuery);
    }
  }
}