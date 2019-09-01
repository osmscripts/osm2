<?php

namespace OsmScripts\Osm\Commands;


use OsmScripts\Osm\Command;

/** @noinspection PhpUnused */

/**
 * `packages` shell command class.
 */
class Packages extends Command
{
    protected function configure() {
        $this->setDescription("Lists installed Osm packages");
    }

    protected function handle() {
        foreach ($this->app->packages as $package) {
            if (empty($package->component_pools)) {
                continue;
            }

            $this->output->writeln($package->name);
        }
    }
}