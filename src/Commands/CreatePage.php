<?php

namespace OsmScripts\Osm\Commands;

use OsmScripts\Core\Script;
use OsmScripts\Osm\RouteCommand;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:page` shell command class.
 *
 * @property string $layer
 * @property string $css_modifier
 * @property string $js_class
 */
class CreatePage extends RouteCommand
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'route_method': return 'GET';
            case 'layer': return strtr(substr($this->route_name, 1), '/-', '__');
            case 'css_modifier': return strtr($this->route_name, '/', '-');
            case 'js_class': return $this->input->getOption('js-class') ?: ucfirst($this->method);
        }

        return parent::default($property);
    }
    #endregion

    protected function configure() {
        parent::configure();
        $this
            ->addOption('js-class', null, InputOption::VALUE_OPTIONAL,
                "Name of JS controller handling JS events. If omitted, inferred from the last segment of route name");
    }

    protected function renderControllerMethod() {
        return $this->files->render('page_controller_method', [
            'method' => $this->method,
            'layer' => $this->layer,
        ]);
    }

    protected function getDefaultMethod() {
        return parent::getDefaultMethod() . 'Page';
    }

    protected function handle() {
        $this->shell->cd($this->module_->path, function() {
            $this->createControllerMethod();
            $this->registerRoute();
            $this->createLayer();
            $this->createJsController();
            $this->registerJsController();
        });
    }

    protected function createLayer() {
        $filename = "{$this->area_->resource_path}/layers/{$this->layer}.php";
        if (is_file($filename)) {
            return;
        }

        $this->files->save($filename, $this->files->render("page_layer", [
            'css_modifier' => $this->css_modifier,
        ]));
    }

    protected function createJsController() {
        $filename = "{$this->area_->resource_path}/js/{$this->js_class}.js";
        if (is_file($filename)) {
            return;
        }

        $this->files->save($filename, $this->files->render("js_page_controller", [
            'class' => $this->js_class,
        ]));
    }

    protected function registerJsController() {
        $filename = "{$this->area_->resource_path}/js/index.js";

        $contents = is_file($filename) ? file_get_contents($filename) : '';

        $this->files->save($filename, $this->js->edit($contents, function() {
            $this->js->import('Osm_Framework_Js/vars/macaw');
            $this->js->import("./{$this->js_class}");
            $this->js->add("macaw.controller('body.{$this->css_modifier}', {$this->js_class});\n");
        }));
    }
}