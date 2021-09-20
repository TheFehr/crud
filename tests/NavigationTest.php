<?php


namespace Orchid\Crud\Tests;

use Orchid\Crud\Tests\Fixtures\NoDisplayInNavigationResource;
use Orchid\Crud\Tests\Fixtures\PostResource;

class NavigationTest extends TestCase
{
    /**
     *
     */
    public function testNoDisplayResourceInNavigation(): void
    {
        $postResource = new PostResource();
        $noDisplayInNavigationResource = new NoDisplayInNavigationResource();
        $this->get(route('platform.resource.list', [
            'resource' => PostResource::uriKey(),
        ]))
            ->assertSee($postResource->singularLabel())
            ->assertDontSee($noDisplayInNavigationResource->singularLabel())
            ->assertOk();
    }
}
