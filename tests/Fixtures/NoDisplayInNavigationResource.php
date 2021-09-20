<?php

namespace Orchid\Crud\Tests\Fixtures;

class NoDisplayInNavigationResource extends PostResource
{
    /**
     * Get the resource should be displayed in the navigation
     *
     * @return bool
     */
    public function navigationTitle(): bool
    {
        return false;
    }
}
