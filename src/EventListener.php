<?php

namespace Legrisch\StatamicEnhancedGraphql;

class EventListener
{
    public function handle($event)
    {
        Manager::buildClasses();
    }
}