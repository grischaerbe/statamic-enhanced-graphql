<?php

namespace Legrisch\StatamicEnhancedGraphql;

class EventListener
{
    public function handle(\Statamic\Events\BlueprintSaved $event)
    {
        ClassBuilder::buildClasses();
    }
}