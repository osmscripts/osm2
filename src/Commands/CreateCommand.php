<?php

namespace OsmScripts\Osm\Commands;

use Exception;
use OsmScripts\Osm\Class_;
use OsmScripts\Osm\ModuleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:command` shell command class.
 *
 * @property string $command @required Name of the command to be created
 * @property string $class
 * @property Class_ $class_
 */
class CreateCommand extends ModuleCommand
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'command': return $this->input->getArgument('cmd');
            case 'class': return "{$this->module_->namespace}\\Commands\\{$this->getClass()}";
            case 'class_': return new Class_(['name' => $this->class, 'module' => $this->module_]);
        }

        return parent::default($property);
    }

    protected function getClass() {
        return $this->input->getOption('class')
            ?: implode(array_map('ucfirst', explode(' ', strtr($this->command, ':_-', '   '))));
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this
            ->setDescription("Creates new console command")
            ->addArgument('cmd', InputArgument::REQUIRED, "Name of command to be created")
            ->addOption('class', null, InputOption::VALUE_OPTIONAL,
                "Name of command PHP class. If omitted, inferred from command name");

    }

    protected function handle() {
        $this->shell->cd($this->module_->path, function() {
            $this->createCommand();
            $this->registerCommand();
        });

        $this->shell->run("php fresh");
    }

    protected function createCommand() {
        if (is_file($this->class_->filename)) {
            throw new Exception("'{$this->class_->filename}' already exists");
        }

        $this->files->save($this->class_->filename, $this->files->render('command_class', [
            'command' => $this->command,
            'namespace' => $this->class_->namespace,
            'class' => $this->class_->short_name,
        ]));
    }

    protected function registerCommand() {
        $filename = "config/console_commands.php";
        $contents = is_file($filename) ? file_get_contents($filename) : $this->files->render('config_file');

        $this->files->save($filename, $this->php->edit($contents, function() {
            $this->php->insertBefore($this->php->last('];'), $this->files->render('command_config', [
                'command' => $this->command,
                'class' => $this->class,
            ]));
        }));
    }
}