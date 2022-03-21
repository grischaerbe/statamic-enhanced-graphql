<?php

namespace Legrisch\StatamicEnhancedGraphql\Builders;

class Replacer
{

  /**
   * Provide an associative array with replacement keys:
   * - className
   * - queryName
   * - typeName
   * - collectionHandle
   * - setHandle
   * - entryId
   * - taxonomyHandle
   *
   * @param string $source
   * @param array $replacements
   * @return string
   */
  public static function replace(string $source, array $replacements): string
  {
    if (isset($replacements['className'])) {
      $source = str_replace('CLASS_NAME', $replacements['className'], $source);
    }
    if (isset($replacements['queryName'])) {
      $source = str_replace('QUERY_NAME', $replacements['queryName'], $source);
    }
    if (isset($replacements['typeName'])) {
      $source = str_replace('TYPE_NAME', $replacements['typeName'], $source);
    }
    if (isset($replacements['collectionHandle'])) {
      $source = str_replace('COLLECTION_HANDLE', $replacements['collectionHandle'], $source);
    }
    if (isset($replacements['setHandle'])) {
      $source = str_replace('SET_HANDLE', $replacements['setHandle'], $source);
    }
    if (isset($replacements['entryId'])) {
      $source = str_replace('ENTRY_ID', $replacements['entryId'], $source);
    }
    if (isset($replacements['taxonomyHandle'])) {
      $source = str_replace('TAXONOMY_HANDLE', $replacements['taxonomyHandle'], $source);
    }

    return $source;
  }
}
