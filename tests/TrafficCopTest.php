<?php

namespace Orchid\Crud\Tests;

use Orchid\Crud\Tests\Fixtures\PostResource;
use Orchid\Crud\Tests\Models\Post;

class TrafficCopTest extends TestCase
{
    public function testBaseCopResource(): void
    {
        $post = Post::factory()->create();
        $post->touch();
        $retrievedAt = $post->updated_at->subMinutes(5)->toJson();

        $postResource = new PostResource();
        $this
            ->followingRedirects()
            ->from(route('platform.resource.edit', [
                'resource' => PostResource::uriKey(),
                'id'       => $post,
            ]))
            ->post(route('platform.resource.edit', [
                'resource'      => PostResource::uriKey(),
                'id'            => $post,
                'method'        => 'update',
                '_retrieved_at' => $retrievedAt,
            ]), [
                'model'         => $post->toArray(),
            ])
            ->assertSee($postResource->trafficCopMessage())
            ->assertOk();
    }

    public function testEditSuccessCopResource(): void
    {
        $post = Post::factory()->create();
        $post->touch();
        $retrievedAt = $post->updated_at->addMinutes(5)->toJson();

        $postResource = new PostResource();
        $this
            ->followingRedirects()
            ->post(route('platform.resource.edit', [
                'resource'      => PostResource::uriKey(),
                'id'            => $post,
                'method'        => 'update',
                '_retrieved_at' => $retrievedAt,
            ]), [
                'model'         => $post->toArray(),
            ])
            ->assertDontSee($postResource->trafficCopMessage())
            ->assertOk();
    }
}
