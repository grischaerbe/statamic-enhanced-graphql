<?php

namespace Legrisch\StatamicEnhancedGraphql;

use Statamic\UpdateScripts\UpdateScript;
 
class Updater extends UpdateScript
{
  public function shouldUpdate($newVersion, $oldVersion)
  {
    return true;
  }

  public function update()
  {
    Manager::buildClasses();
  }
}