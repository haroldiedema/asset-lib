<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);
namespace Hostnet\Component\Resolver\EventListener;

use Hostnet\Component\Resolver\Bundler\ContentItem;
use Hostnet\Component\Resolver\Bundler\ContentState;
use Hostnet\Component\Resolver\Event\AssetEvent;
use Hostnet\Component\Resolver\File;
use Hostnet\Component\Resolver\FileSystem\StringReader;
use Hostnet\Component\Resolver\Import\Nodejs\Executable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hostnet\Component\Resolver\EventListener\CleanCssListener
 */
class CleanCssListenerTest extends TestCase
{
    /**
     * @var CleanCssListener
     */
    private $clean_css_listener;

    protected function setUp()
    {
        $this->clean_css_listener = new CleanCssListener(new Executable('echo', __DIR__));
    }

    public function testOnPreWrite()
    {
        $item = new ContentItem(new File('foobar.css'), 'foobar.css', new StringReader(''));
        $item->transition(ContentState::PROCESSED, 'foobar');

        $this->clean_css_listener->onPreWrite(new AssetEvent($item));

        self::assertSame(ContentState::PROCESSED, $item->getState()->current());
        self::assertContains('cleancss.js', $item->getContent());
    }

    public function testOnPreWriteNotCss()
    {
        $item = new ContentItem(new File('foobar.js'), 'foobar.js', new StringReader(''));
        $item->transition(ContentState::PROCESSED, 'foobar');

        $this->clean_css_listener->onPreWrite(new AssetEvent($item));

        self::assertSame(ContentState::PROCESSED, $item->getState()->current());
        self::assertContains('foobar', $item->getContent());
    }

    /**
     * @expectedException \Hostnet\Component\Resolver\Bundler\TranspileException
     */
    public function testOnPreWriteError()
    {
        $item = new ContentItem(new File('foobar.css'), 'foobar.css', new StringReader(''));
        $item->transition(ContentState::PROCESSED, 'foobar');

        $listener = new CleanCssListener(new Executable('false', __DIR__));
        $listener->onPreWrite(new AssetEvent($item));
    }
}