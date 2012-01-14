<?php

namespace Test\Jentin\Mvc\View;

use \Jentin\Mvc\View\Renderer,
    \Jentin\Core\Plugin\PluginBroker;

/**
 * RendererTest
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{

    private $renderer;

    protected function setUp()
    {
        $this->renderer = new Renderer();
    }


    /**
     * @uses test.tpl
     *
     * @return Renderer
     */
    public function testRender()
    {
        $pathArr = array(FIXTURE_DIR, 'modules', 'Default', 'Index', 'View');
        $path = implode(DIRECTORY_SEPARATOR, $pathArr);

        $renderer = $this->renderer;
        $renderer->setTemplatePath($path);
        $actual = $renderer->render('test', array('name' => 'World'));
        $expected = 'Hello World!';

        $this->assertEquals($actual, $expected);

        return $renderer;
    }


    /**
     * @uses test-plugin.tpl
     * @depends testRender
     */
    public function testRenderWithPlugin(Renderer $renderer)
    {
        $plugins = array(
            'myviewhelper' => '\Test\Jentin\Mvc\View\MyViewHelper'
        );
        $pluginBroker = new PluginBroker($plugins);
        $renderer->setPluginBroker($pluginBroker);

        $actual = $renderer->render('test-plugin');
        $expected = 'MyViewHelper has been invoked!';

        $this->assertEquals($actual, $expected);

        return $renderer;
    }


    /**
     * @expectedException \Jentin\Mvc\View\RendererException
     * @depends testRenderWithPlugin
     */
    public function testRenderTemplateNotFound(Renderer $renderer)
    {
        $renderer->render('template-not-exist');
    }


    /**
     * @uses test.tpl
     * @depends testRender
     */
    public function testRenderWithEscapedVars(Renderer $renderer)
    {
        $actual = $renderer->render('test', array('name' => '"World" & "Universe"'));
        $expected = 'Hello &quot;World&quot; &amp; &quot;Universe&quot;!';
        $this->assertEquals($actual, $expected);
    }

}


class MyViewHelper
{
    public function __invoke()
    {
        return 'MyViewHelper has been invoked!';
    }
}