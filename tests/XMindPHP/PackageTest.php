<?php
namespace XMindPHP;

use Chub\XMindPHP\Package;

/**
 * PackageTest
 *
 * @author Vladimir Chub <v@chub.com.ua>
 * @covers \Chub\XMindPHP\Package
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Chub\XMindPHP\RuntimeException
	 * @dataProvider illegalFilesProvider
	 */
	public function testOpenIllegalFileShouldThrowException($file)
	{
		$package = new Package($file);
		$package->getSheet(0);
	}

	public function testOpenLegalFileShouldReturnSheetWithRootTopic()
	{
		$package = new Package(__DIR__ . '/../res/cr.xmind');
		$rootTopic = $package->getSheet(0)->getRootTopic();

		$this->assertInstanceOf('Chub\\XMindPHP\\RootTopic', $rootTopic);
		$this->assertInstanceOf('Chub\\XMindPHP\\Topic', $rootTopic);
	}

	public function illegalFilesProvider()
	{
		$d = function ($file) {
			return __DIR__ . '/../res/' . $file;
		};

		return [[$d('xxx')], [$d('x.txt')], [$d('x.zip')]];
	}
}
