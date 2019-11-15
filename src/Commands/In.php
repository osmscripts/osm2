<?php

namespace OsmScripts\Osm\Commands;

use Osm\Core\Modules\BaseModule;
use OsmScripts\Osm\Command;
use Symfony\Component\Console\Input\InputArgument;

/** @noinspection PhpUnused */

/**
 * `module` shell command class.
 *
 * @property string $path
 * @property string $relative_path
 */
class In extends Command
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'path': return $this->input->getArgument('path');
            case 'relative_path': return $this->getRelativePath();
        }

        return parent::default($property);
    }

    protected function getRelativePath() {
        $result = realpath($this->path);
        if (mb_strpos($result, $this->app->path()) !== 0) {
            throw new \Exception("'$result' is outside of the project directory");
        }

        return mb_substr(strtr($result, '\\', '/'), mb_strlen($this->app->path()) + 1);
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this->setDescription("Set variables based on given path")
            ->addArgument('path', InputArgument::REQUIRED,
                "Path inside the module");
    }

    protected function handle() {
        $variables = array_merge(
            $this->setPackage(),
            $this->setModule()
        );
        $this->shell->run('osm var ' . implode(' ', $variables));
    }

    protected function setPackage() {
        foreach ($this->app->packages as $package) {
            if (!$package->path) {
                continue;
            }

            if (mb_strpos($this->relative_path, $package->path) === 0) {
                return ["package={$package->name}"];
            }
        }

        return ["package="];
    }

    protected function setModule() {
        foreach ($this->app->modules as $module) {
            if (mb_strpos($this->relative_path, $module->path) === 0) {
                return array_merge(["module={$module->name}"],
                    $this->setArea(mb_substr($this->relative_path,
                    mb_strlen($module->path) + 1)));
            }
        }

        return [];
    }

    protected function setArea($modulePath) {
        foreach ($this->app->areas as $area) {
            if ($area->resource_path && mb_strpos($modulePath, $area->resource_path) === 0) {
                return ["area={$area->name}"];
            }
        }

        return [];
    }
}