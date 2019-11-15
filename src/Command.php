<?php

namespace OsmScripts\Osm;

use Exception;
use Osm\Core\App;
use OsmScripts\Core\Command as BaseCommand;
use OsmScripts\Core\Files;
use OsmScripts\Core\Hints\PackageHint;
use OsmScripts\Core\Js;
use OsmScripts\Core\Php;
use OsmScripts\Core\Project;
use OsmScripts\Core\Script;
use OsmScripts\Core\Shell;
use OsmScripts\Core\Str;
use OsmScripts\Core\Utils;
use OsmScripts\Core\Variables;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property Variables $variables Helper for managing script variables
 * @property Project $project Information about Composer project in current working directory
 * @property Files $files @required Helper for generating files.
 * @property Shell $shell @required Helper for running commands in local shell
 * @property Str $str @required
 * @property Php $php @required
 * @property Js $js @required
 * @property Utils $utils @required
 * @property string $env
 * @property App $app
 * @property string $project_package
 */
class Command extends BaseCommand
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'variables': return $script->singleton(Variables::class);
            case 'project': return new Project(['path' => $script->cwd]);
            case 'files': return $script->singleton(Files::class);
            case 'shell': return $script->singleton(Shell::class);
            case 'php': return $script->singleton(Php::class);
            case 'js': return $script->singleton(Js::class);
            case 'utils': return $script->singleton(Utils::class);
            case 'str': return $script->singleton(Str::class);
            case 'env': return 'testing';
            case 'app': return App::createApp([
                'base_path' => realpath($script->cwd . '/'),
                'env' => $this->env,
            ])->boot();
            case 'project_package': $this->getProjectPackage();
        }

        return parent::default($property);
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

    protected function getProjectPackage() {
        /* @var PackageHint $json */
        $json = $this->utils->readJsonOrFail($this->app->path('composer.json'));
        return $json->name;
    }
}