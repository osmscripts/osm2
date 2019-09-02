<?php

namespace OsmScripts\Osm;

use Osm\Core\Modules\BaseModule;
use Osm\Core\Packages\Package;
use Symfony\Component\Console\Input\InputOption;

/**
 * @property string $module
 * @property BaseModule $module_
 * @property Package $package_
 * @property string $module_path
 */
class ModuleCommand extends Command
{
    #region Properties
    public function __get($property) {
        switch ($property) {
            case 'module': return $this->module = $this->input->getOption('module');
            case 'module_': return $this->module_ = $this->app->modules[$this->module];
            case 'package_': return $this->package_ = $this->module_->package_;
            case 'module_path':
                return $this->module_path = mb_substr($this->module_->path, mb_strlen($this->package_->path) + 1);
        }

        return parent::__get($property);
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this->addOption('module', null,InputOption::VALUE_REQUIRED,
            "Name of Osm module this command should operate in. Should be " .
            "Several_Capitalized_Words_Delimited_By_Underscore. If not set, \$module script variable is used",
            $this->variables->get('module')
        );
    }
}