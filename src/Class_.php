<?php

namespace OsmScripts\Osm;

use Osm\Core\Modules\BaseModule;
use OsmScripts\Core\Project;

/**
 * @property string $name
 * @property BaseModule $module
 *
 * @property string $filename
 * @property string $namespace
 * @property string $short_name
 * @property string $relative_name
 * @property int $last_delimiter_pos
 */
class Class_ extends Project
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'relative_name':
                return mb_substr($this->name, mb_strlen($this->module->namespace) + 1);
            case 'filename':
                return strtr($this->relative_name, '\\', '/') . ".php";
            case 'last_delimiter_pos': return mb_strrpos($this->name, '\\');
            case 'namespace': return mb_substr($this->name, 0, $this->last_delimiter_pos);
            case 'short_name': return mb_substr($this->name, $this->last_delimiter_pos + 1);
        }

        return parent::default($property);
    }
    #endregion
}