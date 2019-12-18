<?php

namespace OsmScripts\Osm;

use Osm\Framework\Http\Returns;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `create:route` shell command class.
 *
 * @property string $route_method
 * @property string $route
 * @property string $route_name
 * @property string $class
 * @property Class_ $class_
 * @property string $method
 * @property bool $public
 */
abstract class RouteCommand extends AreaCommand
{
    public $has_route_method_argument = false;
    public $returns = null;

    #region Properties
    public function default($property) {
        switch ($property) {
            case 'route_method':
                if (!$this->has_route_method_argument) {
                    throw new \Exception("Provide value for 'route_method' property in default() method");
                }
                return $this->str->upper($this->input->getArgument('route-method'));
            case 'route': return $this->input->getArgument('route');
            case 'route_name': return $this->getRouteName();
            case 'class': return "{$this->module_->namespace}\\Controllers\\" . ucfirst($this->area);
            case 'class_': return new Class_(['name' => $this->class, 'module' => $this->module_]);
            case 'method': return $this->getMethod();
            case 'public': return $this->input->getOption('public');
        }

        return parent::default($property);
    }

    protected function getRouteName() {
        $result = '/' . ltrim(rtrim($this->route, '/'), '/_');
        if ($result == '/') {
            $result .= 'home';
        }
        return $result;
    }

    protected function getMethod() {
        return $this->input->getOption('method') ?: $this->getDefaultMethod();
    }

    protected function getDefaultMethod() {
        return $this->str->camel(substr($this->route_name,
            strrpos($this->route_name, '/') + 1));
    }
    #endregion

    abstract protected function renderControllerMethod();

    protected function configure() {
        parent::configure();
        $this->setDescription("Creates new HTTP route and controller method");

        $this->configureRouteMethod();
        $this->configureRoute();
        $this->configurePublic();
        $this->addOption('method', null, InputOption::VALUE_OPTIONAL,
            "Name of PHP controller method handling this route. If omitted, inferred from the last segment of route name");
    }

    protected function configureRouteMethod() {
        if ($this->has_route_method_argument) {
            $this->addArgument('route-method', InputArgument::REQUIRED,
                "HTTP method. Can be GET, POST or PUT or DELETE");
        }
    }

    protected function configureRoute() {
        $this->addArgument('route', InputArgument::REQUIRED, "Route path. Example: '/books/edit'");
    }

    protected function configurePublic() {
        $this->addOption('public', null, InputOption::VALUE_NONE,
            "If set, route is publicly accessible");
    }

    protected function handle() {
        $this->shell->cd($this->module_->path, function() {
            $this->createControllerMethod();
            $this->registerRoute();
        });
    }

    protected function createControllerMethod() {
        $filename = $this->class_->filename;
        $contents = is_file($filename) ? file_get_contents($filename) : $this->files->render('controller_class', [
            'namespace' => $this->class_->namespace,
            'class' => $this->class_->short_name,
        ]);

        $this->files->save($filename, $this->php->edit($contents, function() {
            $this->php->insertBefore($this->php->last('}'),
                $this->renderControllerMethod());
        }));
    }

    protected function registerRoute() {
        $filename = "config/{$this->area}/routes.php";
        $contents = is_file($filename) ? file_get_contents($filename) : $this->files->render('config_file');

        $this->files->save($filename, $this->php->edit($contents, function() {
            $this->php->use_($this->class);
            $this->php->use_(Returns::class);
            $this->php->insertBefore($this->php->last('];'), $this->files->render('route_config', [
                'route' => "{$this->route_method} {$this->route}",
                'class' => $this->class_->short_name,
                'method' => $this->method,
                'public' => $this->public,
                'returns' => $this->returns,
            ]));
        }));
    }
}