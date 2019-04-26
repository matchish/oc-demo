<?php

namespace Matchish\Demo\Tests\Unit;

use App;
use Config;
use Cms\Classes\Theme;
use October\Rain\Filesystem\Filesystem;
use October\Rain\Halcyon\Datasource\FileDatasource;
use October\Rain\Halcyon\Datasource\Resolver;
use October\Rain\Halcyon\Model;
use RainLab\Translate\Models\Locale;
use RainLab\Translate\Models\Locale as LocaleModel;
use Route;
use Event;
use PluginTestCase;

class TwigTest extends PluginTestCase {

    public function setUp()
    {
        parent::setUp();

        Route::group(['prefix' => $this->app->getLocale(), 'middleware' => 'web'], function() {
            Route::any('{slug}', 'Cms\Classes\CmsController@run')->where('slug', '(.*)?');
        });

        $this->seedSampleSourceAndData();

        Config::set('cms.themesPath', __DIR__ . '/../fixtures/themes');
        Config::set('cms.activeTheme', 'test');
        Event::flush('cms.theme.getActiveTheme');
        Theme::resetCache();
        Theme::setActiveTheme('test');
    }

    protected function seedSampleSourceAndData()
    {
        LocaleModel::unguard();

        LocaleModel::firstOrCreate([
            'code' => 'fr',
            'name' => 'French',
            'is_enabled' => 1
        ]);

        LocaleModel::firstOrCreate([
            'code' => 'jp',
            'name' => 'Japan',
            'is_enabled' => 1,
            'is_default' => 1,
        ]);

        LocaleModel::reguard();

    }

    /**
     * Return Twig environment
     *
     * @return Twig_Environment
     */
    private function getTwig()
    {
        return App::make('twig.environment');
    }

    /**
     * @dataProvider pageParamsProvider
     */
    public function testPageFilter($locale, $name, $params, $expected) {
        $this->app->setLocale($locale);
        $twig = $this->getTwig();
        $params = json_encode($params);
        $template = "{{ '{$name}'|page ({$params}) }}";
        $twigTemplate = $twig->createTemplate($template);
        $url = $twigTemplate->render([]);
        $this->assertEquals($expected, $url);

    }

    public function pageParamsProvider()
    {
        return [
            ['jp', 'withslug', ['slug' => 'slug'], 'http://localhost/withslug/slug'],
            ['fr', 'withslug', ['slug' => 'slug'], 'http://localhost/fr/withslug/slug'],
        ];
    }

}
