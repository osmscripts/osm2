<?php

namespace OsmScripts\Osm\Commands;

use OsmScripts\Osm\Command;
use OsmScripts\Core\Script;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:module` shell command class.
 *
 */
class CreateModule extends Command
{
    #region Properties
    public function __get($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
        }

        return parent::__get($property);
    }
    #endregion

    protected function configure() {
        $this
            ->setDescription("Creates new Osm module")
            ->addArgument('module', InputArgument::REQUIRED,
            "Name of module to be created. Automatically prefixed by package root namespace.")
            ->addOption('sample', null, InputOption::VALUE_NONE,
                "If set, creates sample module in samples/ directory instead of real module in " .
                "src/ directory.")
            ->addOption('package', null, InputOption::VALUE_REQUIRED,
                "Name of Composer package in which script will be created, " .
                "should be in `{vendor}/{package}` format. If not set, \$package script variable is used",
                $this->variables->get('package'))
            ;
    }

    protected function handle() {
        // TODO: execute command logic
    }
}