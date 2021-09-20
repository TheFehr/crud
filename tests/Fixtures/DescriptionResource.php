<?php

namespace Orchid\Crud\Tests\Fixtures;

class DescriptionResource extends PostResource
{
    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
    }
}
