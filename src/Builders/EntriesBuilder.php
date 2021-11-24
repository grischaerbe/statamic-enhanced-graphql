<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

use Statamic\Contracts\Entries\Collection as CollectionType;
use Statamic\Fields\Blueprint;
use Statamic\Facades\Collection;
use Statamic\GraphQL\Types\EntryType;
use Statamic\Support\Str;

class EntriesBuilder {
  private static function buildQueryName(CollectionType $collection, Blueprint $blueprint): string {
    return $collection->handle() . 'Entries';
  }

  private static function buildClassName(CollectionType $collection, Blueprint $blueprint): string {
    return Str::studly($collection->handle()) . 'EntriesQuery';
  }

  private static function parseTemplate(
    string $input,
    string $className,
    string $queryName,
    string $typeName,
    string $collectionHandle
  ): string
  {
    $input = str_replace('<%--CLASS_NAME--%>', $className, $input);
    $input = str_replace('<%--QUERY_NAME--%>', $queryName, $input);
    $input = str_replace('<%--TYPE_NAME--%>', $typeName, $input);
    $input = str_replace('<%--COLLECTION_HANDLE--%>', $collectionHandle, $input);
    return $input;
  }

  public static function build() {
    $collections = Collection::all();
    $collections->each(function ($collection) {
      $blueprints = $collection->entryBlueprints();
      if ($blueprints->count() === 1) {
          $blueprint = $blueprints->first();
          $typeName = EntryType::buildName($collection, $blueprints->first());

          $className = static::buildClassName($collection, $blueprint);
          $queryName = static::buildQueryName($collection, $blueprint);
          $inputSingleQuery = file_get_contents(__DIR__ . "/../templates/Entries.txt");
          $outputSingleQuery = static::parseTemplate($inputSingleQuery, $className, $queryName, $typeName, $collection->handle());
          file_put_contents(__DIR__ . "/../Queries/$className.php", $outputSingleQuery);
      }
    });
  }
}