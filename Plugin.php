<?php namespace Matchish\Demo;

use Cms\Classes\Controller;
use Cms\Twig\Extension as CmsExtention;
use RainLab\Translate\Classes\Translator;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['RainLab.Translate', 'Excodus.TranslateExtended'];


    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }

    /**
     * Lets extend the page filter.
     * @return array
     */
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'page' => [$this, 'pageFilter']
            ]
        ];
    }

    /**
     * Extends the classic page filter
     * @param  string $url
     * @return string
     */
    public function pageFilter($name, $parameters = [], $routePersistence = true)
    {
        $controller = app(Controller::class);
        $original = (new CmsExtention($controller))->pageFilter($name, $parameters, $routePersistence);
        $parsedUrl = parse_url($original);
        return Url::to($this->getPathInLocale($parsedUrl['path']));
    }

    public function getPathInLocale($path, $locale = null, $prefixDefaultLocale = null)
    {
        $locale = $locale ?: Translator::instance()->getLocale();
        return Translator::instance()->getPathInLocale($path, $locale, $prefixDefaultLocale);
    }
}
