<?php

namespace OsmScripts\Osm;

use Exception;
use OsmScripts\Core\Command as BaseCommand;
use OsmScripts\Core\Project;
use OsmScripts\Core\Script;
use OsmScripts\Core\Variables;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property Variables $variables Helper for managing script variables
 * @property Project $project Information about Composer project in current working directory
 */
class Command extends BaseCommand
{
    #region Properties
    public function __get($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'variables': return $this->variables = $script->singleton(Variables::class);
            case 'project': return $this->project = new Project(['path' => $script->cwd]);
        }

        return null;
    }
    #endregion

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->verifyOsmProject($output);
        parent::execute($input, $output);
    }

    protected function verifyOsmProject(OutputInterface $output) {
        $packages = $this->project->packages;

        if (!isset($packages['osmphp/framework'])) {
            throw new Exception("Before running this command, make directory of your " .
                "Osm project a current directory");
        }
    }
}