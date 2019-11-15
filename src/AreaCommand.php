<?php

namespace OsmScripts\Osm;

use Osm\Framework\Areas\Area;
use Symfony\Component\Console\Input\InputOption;

/**
 * @property string $area
 * @property Area $area_
 */
class AreaCommand extends ModuleCommand
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'area': return $this->input->getOption('area');
            case 'area_': return $this->app->areas[$this->area];
        }

        return parent::default($property);
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this->addOption('area', null,InputOption::VALUE_REQUIRED,
            "Name of area this command should operate in. " .
            "If not set, \$area script variable is used. " .
            "If \$area script variable is not set, 'frontend' area is assumed.",
            $this->variables->get('area') ?: 'frontend'
        );
    }

}