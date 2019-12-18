<?php

namespace OsmScripts\Osm\Commands;

use Osm\Framework\Views\View;
use OsmScripts\Core\Script;
use OsmScripts\Osm\RouteCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:mustache` shell command class.
 *
 * @property string $id Template ID
 * @property string $view Blade template which renders the Mustache template
 */
class CreateMustache extends RouteCommand
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'route_method': return 'GET';
            case 'public': return true;
            case 'id': return $this->input->getArgument('id');
            case 'route': return $this->getRoute();
            case 'view': return $this->input->getOption('view')
                ?: $this->str->snake($this->str->studly($this->id));
        }

        return parent::default($property);
    }

    protected function getRoute() {
        return $this->input->getOption('route') ?: $this->getDefaultRoute();
    }

    protected function getDefaultRoute() {
        return "/templates/{$this->id}";
    }

    #endregion

    protected function configure() {
        parent::configure();
        $this->setDescription("Creates new dynamically loaded Mustache template");
        $this
            ->addArgument('id', InputArgument::REQUIRED, "Template ID")
            ->addOption('view', null, InputOption::VALUE_OPTIONAL,
                'Blade template which renders the Mustache template. If omitted, derived from template ID');
    }

    protected function configureRoute() {
        $this->addOption('route', null, InputOption::VALUE_OPTIONAL,
            "Route path. Example: '/templates/my-template'. If omitted, derived from template ID");
    }

    protected function configurePublic() {
        // all templates are public, no command-line option needed
    }

    protected function handle() {
        $this->shell->cd($this->module_->path, function() {
            $this->createControllerMethod();
            $this->registerRoute();
            $this->createView();
            $this->registerTemplate();
        });
    }

    protected function renderControllerMethod() {
        $this->php->use_(View::class);
        return $this->files->render('mustache_controller_method', [
            'method' => $this->method,
            'module' => $this->module,
            'view' => $this->view,
        ]);
    }

    protected function createView() {
        $filename = "{$this->area_->resource_path}/views/{$this->view}.blade.php";
        if (is_file($filename)) {
            return;
        }

        $this->files->save($filename, $this->files->render("mustache_view"));
    }

    protected function registerTemplate() {
        $filename = "{$this->area_->resource_path}/js/index.js";

        $contents = is_file($filename) ? file_get_contents($filename) : '';

        $this->files->save($filename, $this->js->edit($contents, function() {
            $this->js->import('Osm_Framework_Js/vars/templates');
            $this->js->add("templates.add('{$this->id}', {route: '{$this->route_method} {$this->route}'});\n");
        }));
    }

}