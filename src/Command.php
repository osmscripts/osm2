<?php

namespace OsmScripts\Osm;

use Exception;
use Osm\Core\App;
use OsmScripts\Core\Command as BaseCommand;
use OsmScripts\Core\Files;
use OsmScripts\Core\Project;
use OsmScripts\Core\Script;
use OsmScripts\Core\Shell;
use OsmScripts\Core\Variables;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property Variables $variables Helper for managing script variables
 * @property Project $project Information about Composer project in current working directory
 * @property Files $files @required Helper for generating files.
 * @property Shell $shell @required Helper for running commands in local shell
 * @property App $app
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
            case 'files': return $this->files = $script->singleton(Files::class);
            case 'shell': return $this->shell = $script->singleton(Shell::class);

            case 'app': return $this->app = App::createApp([
                'base_path' => realpath($script->cwd . '/'),
                'env' => 'testing',
            ]);
        }

        return parent::__get($property);
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