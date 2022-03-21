<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

use Legrisch\StatamicEnhancedGraphql\Settings\ParsedSettings;
use Statamic\GraphQL\Types\EntryType;
use Statamic\Support\Str;

class SingleEntryBuilder
{
  private static function buildClassName($queryName): string
  {
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

  public static function build()
  {

    $singleEntryQueries = ParsedSettings::getSingleEntryQueries();

    foreach ($singleEntryQueries as $singleEntryQuery) {
      /** @var \Statamic\Entries\Entry $entry */
      $entry = $singleEntryQuery["entry"];

      $blueprint = $entry->blueprint();
      $collection = $entry->collection();

      $typeName = EntryType::buildName($collection, $blueprint);
      $queryName = $singleEntryQuery['query_name'];
      $className = static::buildClassName($queryName);
      $input = file_get_contents(__DIR__ . "/../templates/SingleEntry.txt");
      $output = Replacer::replace($input, [
        "className" => $className,
        "queryName" => $queryName,
        "typeName" => $typeName,
        "entryId" => $entry->id(),
      ]);
      file_put_contents(__DIR__ . "/../Queries/$className.php", $output);
    }
  }
}
