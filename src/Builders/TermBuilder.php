<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

use Legrisch\StatamicEnhancedGraphql\Settings\ParsedSettings;
use Statamic\Fields\Blueprint;
use Statamic\GraphQL\Types\TermType;
use Statamic\Support\Str;
use Statamic\Taxonomies\Taxonomy;

class TermBuilder
{
  private static function buildQueryName(Taxonomy $taxonomy): string
  {
    return $taxonomy->handle() . 'Term';
  }

  private static function buildClassName(Taxonomy $taxonomy): string
  {
    return Str::studly($taxonomy->handle()) . 'TermQuery';
  }

  public static function build()
  {
    $taxonomies = ParsedSettings::getTaxonomies();

    /** @var Taxonomy $taxonomy */
    foreach ($taxonomies as $taxonomy) {
      /** @var Blueprint $blueprint */
      $blueprint = $taxonomy->termBlueprints()->first();
      $typeName = TermType::buildName($taxonomy, $blueprint);
      $className = static::buildClassName($taxonomy);
      $queryName = static::buildQueryName($taxonomy);
      $input = file_get_contents(__DIR__ . "/../templates/Term.txt");
      $output = Replacer::replace($input, [
        "className" => $className,
        "queryName" => $queryName,
        "typeName" => $typeName,
        "taxonomyHandle" => $taxonomy->handle()
      ]);
      file_put_contents(__DIR__ . "/../Queries/$className.php", $output);
    }
  }
}
