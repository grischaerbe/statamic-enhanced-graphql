<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

use Statamic\Facades\GlobalSet;
use Statamic\GraphQL\Types\GlobalSetType;
use Statamic\Support\Str;

class SetBuilder {

  private static function buildQueryName(string $setHandle): string {
    return $setHandle . 'GlobalSet';
  }

  private static function buildClassName(string $setHandle): string {
    return Str::studly($setHandle) . 'GlobalSet';
  }

  private static function parseTemplate(
    string $input,
    string $className,
    string $queryName,
    string $typeName,
    string $setHandle
  ): string
  {
    $input = str_replace('<%--CLASS_NAME--%>', $className, $input);
    $input = str_replace('<%--QUERY_NAME--%>', $queryName, $input);
    $input = str_replace('<%--TYPE_NAME--%>', $typeName, $input);
    $input = str_replace('<%--SET_HANDLE--%>', $setHandle, $input);
    return $input;
  }

  public static function build() {
    $sets = GlobalSet::all();
    $sets->each(function ($set) {
      $handle = $set->handle();
      $typeName = GlobalSetType::buildName($set);
      $input = file_get_contents(__DIR__ . "/../templates/Set.txt");
      $className = static::buildClassName($handle);
      $queryName = static::buildQueryName($handle);
      $output = static::parseTemplate($input, $className, $queryName, $typeName, $handle);
      file_put_contents(__DIR__ . "/../Queries/$className.php", $output);
    });
  }
}