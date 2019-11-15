<?php

namespace OsmScripts\Osm\Commands;

use OsmScripts\Osm\ModuleCommand;
use Symfony\Component\Console\Input\InputArgument;

/** @noinspection PhpUnused */

/**
 * `translate` shell command class.
 *
 * @property string $string
 * @property string $quoted_string
 */
class Translate extends ModuleCommand
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'string': return $this->input->getArgument('string');
            case 'quoted_string': return '"' .  addslashes($this->string) . '"';
        }

        return parent::default($property);
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this->setDescription("Adds string to translation file")
            ->addArgument('string', InputArgument::REQUIRED,
                "String to be translated");
    }

    protected function handle() {
        $this->shell->cd($this->module_->path, function() {
            $this->registerString();
        });
    }

    protected function registerString() {
        $filename = "config/translations/en_US.php";
        $contents = is_file($filename)
            ? file_get_contents($filename)
            : $this->files->render('config_file');

        if (mb_strrpos($contents, $this->quoted_string) !== false) {
            return;
        }

        $this->files->save($filename, $this->php->edit($contents, function() {
            $this->php->insertBefore($this->php->last('];'),
                "    {$this->quoted_string} => {$this->quoted_string},\n");
        }));
    }
}