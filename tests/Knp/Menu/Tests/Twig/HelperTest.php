<?php

namespace Knp\Menu\Tests\Twig;

use Knp\Menu\Twig\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu));
    }

    public function testRenderMenuWithOptions()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array('firstClass' => 'test'))
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu, array('firstClass' => 'test')));
    }

    public function testRenderMenuWithRenderer()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with('custom')
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu, array(), 'custom'));
    }

    public function testRenderMenuByName()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render('default'));
    }

    public function testGetMenu()
    {
        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($menu, $helper->get('default'));
    }

    /**
     * @expectedException LogicException
     */
    public function testGetMenuWithBadReturnValue()
    {
        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue(new \stdClass()))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);
        $helper->get('default');
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetMenuWithoutProvider()
    {
        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $helper = new Helper($rendererProvider);
        $helper->get('default');
    }

    public function testGetMenuByPath()
    {
        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $child = $this->getMock('Knp\Menu\ItemInterface');
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->will($this->returnValue($child))
        ;
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($child, $helper->get('default', array('child')));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetMenuByInvalidPath()
    {
        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $child = $this->getMock('Knp\Menu\ItemInterface');
        $child->expects($this->any())
            ->method('getChild')
            ->will($this->returnValue(null))
        ;
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->will($this->returnValue($child))
        ;
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($child, $helper->get('default', array('child', 'invalid')));
    }

    public function testRenderMenuByPath()
    {
        $child = $this->getMock('Knp\Menu\ItemInterface');
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->will($this->returnValue($child))
        ;

        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($child, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render(array($menu, 'child')));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The array cannot be empty
     */
    public function testRenderByEmptyPath()
    {
        $helper = new Helper($this->getMock('Knp\Menu\Renderer\RendererProviderInterface'));
        $helper->render(array());
    }
}
