<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

use Legrisch\StatamicEnhancedGraphql\Settings\ParsedSettings;
use Statamic\Globals\GlobalSet;
use Statamic\GraphQL\Types\GlobalSetType;
use Statamic\Support\Str;

class SetBuilder
{

  private static function buildQueryName(GlobalSet $globalSet): string
  {
    return $globalSet->handle() . 'GlobalSet';
  }

  private static function buildClassName(GlobalSet $globalSet): string
  {
    return Str::studly($globalSet->handle()) . 'GlobalSet';
  }

  public static function build()
  {

    $globalSets = ParsedSettings::getGlobalSets();

    /** @var GlobalSet $globalSet */
    foreach ($globalSets as $globalSet) {
      $typeName = GlobalSetType::buildName($globalSet);
      $className = static::buildClassName($globalSet);
      $queryName = static::buildQueryName($globalSet);
      $input = file_get_contents(__DIR__ . "/../templates/Set.txt");
      $output = Replacer::replace($input, [
        "className" => $className,
        "queryName" => $queryName,
        "typeName" => $typeName,
        "setHandle" => $globalSet->handle()
      ]);
      file_put_contents(__DIR__ . "/../Queries/$className.php", $output);
    }
  }
}
