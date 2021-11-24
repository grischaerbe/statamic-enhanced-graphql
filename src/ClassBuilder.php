<?php

namespace Legrisch\StatamicEnhancedGraphql;

use Statamic\Facades\Collection;
use Statamic\GraphQL\Types\EntryType;
use Statamic\Support\Str;

class ClassBuilder
{
  private static function buildQueryName(\Statamic\Contracts\Entries\Collection $collection, \Statamic\Fields\Blueprint $blueprint, bool $isCollection, bool $hasMultipleBlueprints) {
    if ($hasMultipleBlueprints) {
      if ($isCollection) {
        return $collection->handle() . Str::studly($blueprint->handle()) . 'Entries';
      } else {
        return $collection->handle() . Str::studly($blueprint->handle()) . 'Entry';
      }
    } else {
      if ($isCollection) {
        return $collection->handle() . 'Entries';
      } else {
        return $collection->handle() . 'Entry';
      }
    }
  }

  private static function buildClassName(\Statamic\Contracts\Entries\Collection $collection, \Statamic\Fields\Blueprint $blueprint, bool $isCollection, bool $hasMultipleBlueprints) {
    if ($hasMultipleBlueprints) {
      if ($isCollection) {
        return Str::studly($collection->handle()) . Str::studly($blueprint->handle()) . 'EntriesQuery';
      } else {
        return Str::studly($collection->handle()) . Str::studly($blueprint->handle()) . 'EntryQuery';
      }
    } else {
      if ($isCollection) {
        return Str::studly($collection->handle()) . 'EntriesQuery';
      } else {
        return Str::studly($collection->handle()) . 'EntryQuery';
      }
    }
  }

  private static function buildFromTemplate(string $input, string $className, string $queryName, string $typeName, string $collectionHandle) {
    $input = str_replace('<%--CLASSNAME--%>', $className, $input);
    $input = str_replace('<%--QUERYNAME--%>', $queryName, $input);
    $input = str_replace('<%--TYPENAME--%>', $typeName, $input);
    $input = str_replace('<%--COLLECTION_HANDLE--%>', $collectionHandle, $input);
    return $input;
  }

  private static function clearDirectory() {
    $files = glob(__DIR__ . '/Queries/*');
    foreach($files as $file)
    {
      if(is_file($file)) {
        unlink($file);
      }
    }
  }

  public static function buildClasses(): void
  {
      static::clearDirectory();
      
      $collections = Collection::all();
      $collections->each(function ($collection) {
          $blueprints = $collection->entryBlueprints();
          if ($blueprints->count() === 1) {
              $blueprint = $blueprints->first();
              $typeName = EntryType::buildName($collection, $blueprints->first());

              $classNameSingleQuery = static::buildClassName($collection, $blueprint, false, false);
              $queryNameSingleQuery = static::buildQueryName($collection, $blueprint, false, false);
              $inputSingleQuery = file_get_contents(__DIR__ . "/templates/Entry.txt");
              $outputSingleQuery = static::buildFromTemplate($inputSingleQuery, $classNameSingleQuery, $queryNameSingleQuery, $typeName, $collection->handle());
              file_put_contents(__DIR__ . "/Queries/$classNameSingleQuery.php", $outputSingleQuery);

              $classNameCollectionQuery = static::buildClassName($collection, $blueprint, true, false);
              $queryNameCollectionQuery = static::buildQueryName($collection, $blueprint, true, false);
              $inputCollectionQuery = file_get_contents(__DIR__ . "/templates/Entries.txt");
              $outputCollectionQuery = static::buildFromTemplate($inputCollectionQuery, $classNameCollectionQuery, $queryNameCollectionQuery, $typeName, $collection->handle());
              file_put_contents(__DIR__ . "/Queries/$classNameCollectionQuery.php", $outputCollectionQuery);
          }
      });
  }
}