<?php

namespace Legrisch\StatamicEnhancedGraphql;

use Legrisch\StatamicEnhancedGraphql\Builders\EntriesBuilder;
use Legrisch\StatamicEnhancedGraphql\Builders\EntryBuilder;
use Legrisch\StatamicEnhancedGraphql\Builders\SetBuilder;
use Legrisch\StatamicEnhancedGraphql\Builders\SingleEntryBuilder;

class Manager
{
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
      
    EntriesBuilder::build();
    EntryBuilder::build();
    SetBuilder::build();
    SingleEntryBuilder::build();
  }
}