<?php

namespace OsmScripts\Osm\Commands;

use Exception;
use Osm\Core\Packages\ComponentPool;
use Osm\Core\Packages\Package;
use OsmScripts\Osm\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:module` shell command class.
 *
 * @property string $component_pool
 * @property string $module
 * @property string $package
 *
 * @property Package $package_
 * @property ComponentPool $component_pool_
 * @property string $module_path_pattern
 * @property string $full_module_name
 */
class CreateModule extends Command
{
    #region Properties
    public function __get($property) {
        switch ($property) {
            case 'component_pool':
                return $this->component_pool = $this->input->getOption('sample') ? 'samples' : 'src';
            case 'module': return $this->module = $this->input->getArgument('module');
            case 'package': return $this->package = $this->input->getOption('package');

            case 'package_': return $this->package_ = $this->app->packages[$this->package];
            case 'component_pool_': return $this->component_pool_ = $this->getComponentPool();
            case 'module_path_pattern': return $this->module_path_pattern = $this->getModuleNamePattern();
            case 'full_module_name': return $this->full_module_name = $this->getFullModuleName();
        }

        return parent::__get($property);
    }

    protected function getComponentPool() {
        if (!isset($this->package_->component_pools[$this->component_pool])) {
            throw new Exception("Component pool '{$this->component_pool}' is not defined in " .
                "'extra/osm/component_pools' section of package's 'composer . json' file.");
        }

        return $this->package_->component_pools[$this->component_pool];
    }

    protected function getModuleNamePattern() {
        if (!$this->component_pool_->module_path) {
            throw new Exception(
                "'module_path' setting not defined in component pool '{$this->component_pool}' in " .
                "'extra/osm/component_pools' section of package's 'composer . json' file."
            );
        }

        return dirname($this->component_pool_->module_path);
    }

    protected function getFullModuleName() {
        if (!$this->component_pool_->namespace) {
            throw new Exception("namespace of '{$this->component_pool}' component pool is not defined.");
        }

        return strtr($this->component_pool_->namespace, '\\', '_') . "_{$this->module}";
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
        if (!$this->doesModuleNameMatchesPattern()) {
            throw new Exception("Module name '{$this->module}' doesn't match module name pattern " .
                "'{$this->module_path_pattern}' defined in 'extra/osm/component_pools' section of package's " .
                "'composer.json' file.");
        }

        $this->shell->cd($this->package_->path, function() {
            $this->createModule();
        });

        $this->shell->run("osm var module={$this->full_module_name}");
        $this->shell->run("php fresh");
    }

    protected function doesModuleNameMatchesPattern() {
        // adapted from https://stackoverflow.com/questions/13913796/php-glob-style-matching
        $expr = preg_replace_callback('/[\\\\^$.[\\]|()?*+{}\\-\\/]/', function ($matches) {
            switch ($matches[0]) {
                case '*':
                    return '.*';
                case '?':
                    return '.';
                default:
                    return '\\' . $matches[0];
            }
        }, $this->module_path_pattern);

        $expr = '/' . $expr . '/';

        return (bool)preg_match($expr, strtr($this->module, '_', '/'));
    }

    protected function createModule() {
        $filename = "{$this->component_pool}/" . strtr($this->module, '_', '/') . "/Module.php";
        if (is_file($filename)) {
            throw new Exception("'{$filename}' already exists");
        }

        $this->files->save($filename, $this->files->render('module_class', [
            'namespace' => strtr($this->full_module_name, '_', '\\'),
        ]));
    }
}