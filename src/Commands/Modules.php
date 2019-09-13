<?php

namespace OsmScripts\Osm\Commands;


use OsmScripts\Osm\Command;

/** @noinspection PhpUnused */

/**
 * `modules` shell command class.
 */
class Modules extends Command
{
    public function __get($property) {
        switch ($property) {
            case 'env': return $this->env = 'testing';
        }
        return parent::__get($property);
    }

    protected function configure() {
        $this->setDescription("Lists installed Osm modules");
    }

    protected function handle() {
        foreach ($this->app->modules as $module) {
            $this->output->writeln($module->name);
        }
    }
}