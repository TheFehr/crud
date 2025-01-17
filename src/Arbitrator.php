<?php

namespace Orchid\Crud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Orchid\Filters\Filterable;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Facades\Dashboard;

class Arbitrator
{
    /**
     * The registered resource names.
     *
     * @var Collection
     */
    protected $resources;

    /**
     * Arbitrator constructor.
     */
    public function __construct()
    {
        $this->resources = collect();
    }

    /**
     * Register the given resources.
     *
     * @param string[] $resources
     *
     * @return Arbitrator
     */
    public function resources(array $resources): Arbitrator
    {
        $this->resources = $this->resources
            ->merge($resources)
            ->map(function ($resource) {
                return is_string($resource) ? app($resource) : $resource;
            })
            ->map(function (Resource $resource) {
                return $this->checkResource($resource);
            });

        return $this;
    }

    /**
     * @param \Orchid\Crud\Resource $resource
     *
     * @return \Orchid\Crud\Resource
     */
    public function checkResource(Resource $resource): Resource
    {
        $isEloquent = is_subclass_of($resource::$model, Model::class);

        abort_unless($isEloquent, 501, sprintf('The resource "%s" must specify the Eloquent class to generate.', get_class($resource)));

        $exist = collect(trait_uses_recursive($resource::$model))->has([
            Filterable::class,
        ]);

        abort_unless($exist, 501, sprintf('The model "%s" must have the required orchid/platform traits.', $resource::$model));

        return $resource;
    }

    /**
     * Registers all the resources
     */
    public function boot(): void
    {
        $this->resources
<<<<<<< HEAD
            ->filter(function (Resource $resource) {
                return \Auth::user()->hasAccess($resource::permission());
            })
            ->groupBy(function (Resource $resource) {
                return $resource->navigationTitle();
            })
=======
            ->groupBy(function (Resource $resource) {
                return $resource::navigationTitle();
            })
>>>>>>> 2b8405b (Allow custom title for the resources in the navigation)
            ->sort()
            ->values()
            ->each(function (Collection $resourceGroup) {
                $resourceGroup->sort(function (Resource $resource) {
<<<<<<< HEAD
                    return [$resource::sort(), $resource->label()];
                })
                    ->values()
                    ->sort(function ($resource, $resource2) {
                        return strnatcmp($resource->label(), $resource2->label());
=======
                    return [$resource::sort(), $resource::label()];
                })
                    ->values()
                    ->sort(function ($resource, $resource2) {
                        return strnatcmp($resource::label(), $resource2::label());
>>>>>>> 2b8405b (Allow custom title for the resources in the navigation)
                    })
                    ->values()
                    ->each(function (Resource $resource, $key) {
                        $this
                            ->registerPermission($resource)
                            ->registerMenu($resource, $key);
                    });
            });
    }

    /**
     * @param string $key
     *
     * @return Resource|null
     */
    public function find(string $key): ?Resource
    {
        return $this->resources->filter(function (Resource $resource) use ($key) {
            return $resource->uriKey() === $key;
        })->first();
    }

    /**
     * @param string $key
     *
     * @return Resource
     */
    public function findOrFail(string $key): Resource
    {
        $resource = $this->find($key);

        abort_if($resource === null, 404);

        return $resource;
    }

    /**
     * @param Resource $resource
     * @param int      $key
     *
     * @return Arbitrator
     */
    private function registerMenu(Resource $resource, int $key): Arbitrator
    {
        if ($resource::navigationTitle() === false) {
            return $this;
        }

        $title = $resource::navigationTitle() ?? __('Resources');
        View::composer('platform::dashboard', function () use ($resource, $key, $title) {
            $title = Menu::make()
                ->canSee($key === 0)
                ->title($title)
                ->sort($resource::sort());

            $menu = Menu::make($resource::label())
                ->icon($resource::icon())
                ->route('platform.resource.list', [$resource::uriKey()])
                ->active($this->activeMenu($resource))
                ->permission($resource::permission())
                ->sort($resource::sort());

            Dashboard::registerMenuElement(\Orchid\Platform\Dashboard::MENU_MAIN, $title);
            Dashboard::registerMenuElement(\Orchid\Platform\Dashboard::MENU_MAIN, $menu);
        });

        return $this;
    }

    /**
     * @param Resource $resource
     *
     * @return Arbitrator
     */
    private function registerPermission(Resource $resource): Arbitrator
    {
        if ($resource::permission() === null) {
            return $this;
        }

        $exist = Dashboard::getPermission()->flatten(1)->map(function ($permission) {
            return $permission['slug'];
        })->contains($resource::permission());

        if ($exist === true) {
            return $this;
        }

        Dashboard::registerPermissions(
            ItemPermission::group('CRUD')
                ->addPermission($resource::permission(), $resource->label())
        );

        return $this;
    }

    /**
     * @param Resource $resource
     *
     * @return array
     */
    private function activeMenu(Resource $resource): array
    {
        return [
            route('platform.resource.list', [
                'resource' => $resource->uriKey(),
            ]),
            route('platform.resource.create', [
                'resource' => $resource->uriKey(),
            ]),
            route('platform.resource.view', [
                'resource' => $resource::uriKey(),
                'id'       => '*',
            ]),
            route('platform.resource.edit', [
                'resource' => $resource->uriKey(),
                'id'       => '*',
            ]),
        ];
    }
}
