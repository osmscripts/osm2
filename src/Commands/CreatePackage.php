<?php

namespace OsmScripts\Osm\Commands;

use Exception;
use OsmScripts\Core\Commands\CreatePackage as BaseCreatePackage;
use OsmScripts\Core\Script;

/** @noinspection PhpUnused */

/**
 * `create:package` shell command class.
 *
 * @property string $test_namespace
 * @property string $sample_namespace
 */
class CreatePackage extends BaseCreatePackage
{
    public $no_scripts = true;

    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'base_package': return 'osmphp/framework';
            case 'test_namespace': return "{$this->namespace}Tests\\";
            case 'sample_namespace': return "{$this->namespace}Samples\\";
        }

        return parent::default($property);
    }

    #endregion

    protected function configure() {
        parent::configure();
        $this
            ->setDescription("Creates new Composer package for Osm projects, " .
                "pushes it to the specified Git repo and installs it locally")
            ->setHelp(<<<EOT
Before running this command create empty repo on GitHub or other Git hosting provider. 
Pass URL of Git repo using --repo_url=REPO_URL syntax.

Also, before running this command commit and push all changes in all the packages in `vendor`.
directory.
EOT
            );
    }

    protected function createPackage() {
        $filename = "{$this->path}/composer.json";
        if (is_file($filename)) {
            throw new Exception("'{$filename}' already exists");
        }

        $this->files->save($filename, $this->files->render('composer_json', [
            'package' => $this->package,
            'namespace' => json_encode($this->namespace),
            'test_namespace' => json_encode($this->test_namespace),
            'sample_namespace' => json_encode($this->sample_namespace),
            'version_constraint' => $this->version_constraint,
        ]));

        $this->files->save("{$this->path}/.gitattributes",
            $this->files->render('.gitattributes'));
    }
}