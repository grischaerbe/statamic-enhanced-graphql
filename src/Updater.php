<?php

namespace Legrisch\StatamicEnhancedGraphql;

use Legrisch\StatamicEnhancedGraphql\ClassBuilder;
use Statamic\UpdateScripts\UpdateScript;
 
class Updater extends UpdateScript
{
  public function shouldUpdate($newVersion, $oldVersion)
  {
    return true;
  }

  public function update()
  {
    ClassBuilder::buildClasses();
  }
}