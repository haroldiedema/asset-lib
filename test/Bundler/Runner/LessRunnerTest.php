<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);
namespace Hostnet\Component\Resolver\Bundler\Runner;

use Hostnet\Component\Resolver\Bundler\ContentItem;
use Hostnet\Component\Resolver\Config\ConfigInterface;
use Hostnet\Component\Resolver\File;
use Hostnet\Component\Resolver\FileSystem\StringReader;
use Hostnet\Component\Resolver\Import\Nodejs\Executable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hostnet\Component\Resolver\Bundler\Runner\LessRunner
 */
class LessRunnerTest extends TestCase
{
    /**
     * @var LessRunner
     */
    private $less_runner;

    protected function setUp()
    {
        $config = $this->prophesize(ConfigInterface::class);
        $config->getNodeJsExecutable()->willReturn(new Executable('echo', __DIR__));
        $config->getProjectRoot()->willReturn('cwd');

        $this->less_runner = new LessRunner(
            $config->reveal()
        );
    }

    public function testExecute()
    {
        $item = new ContentItem(
            new File('foobar.js'),
            'foobar.js',
            new StringReader('')
        );

        $result = $this->less_runner->execute($item);
        self::assertContains('lessc.js', $result);
    }

    /**
     * @expectedException \Hostnet\Component\Resolver\Bundler\TranspileException
     */
    public function testExecuteWriteError()
    {
        $config = $this->prophesize(ConfigInterface::class);
        $config->getProjectRoot()->willReturn('cwd');
        $config->getNodeJsExecutable()->willReturn(new Executable('false', __DIR__));

        $item = new ContentItem(new File('foobar.js'), 'foobar.js', new StringReader(''));

        $listener = new LessRunner($config->reveal());
        $listener->execute($item);
    }
}
