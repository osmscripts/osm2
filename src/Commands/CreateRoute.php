<?php

namespace OsmScripts\Osm\Commands;

use OsmScripts\Osm\Class_;
use OsmScripts\Osm\RouteCommand;

/** @noinspection PhpUnused */

/**
 * `create:route` shell command class.
 *
 * @property string $route_method
 * @property string $route
 * @property string $route_name
 * @property string $class
 * @property Class_ $class_
 * @property string $method
 * @property bool $public
 */
class CreateRoute extends RouteCommand
{
    public $use_http_get = false;
    public $returns = 'JSON';

    protected function renderControllerMethod() {
        return $this->files->render('controller_method', [
            'method' => $this->method,
        ]);
    }
}