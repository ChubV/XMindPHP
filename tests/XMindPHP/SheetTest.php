<?php
namespace XMindPHP;

use Chub\XMindPHP\Package;

/**
 * SheetTest
 *
 * @author Vladimir Chub <v@chub.com.ua>
 * @covers \Chub\XMindPHP\Package
 */
class SheetTest extends \PHPUnit_Framework_TestCase
{
	public function testIterator()
	{
		$package = new Package(__DIR__ . '/../res/sheets.xmind');
		$it = $package->getSheetsIterator();

		$this->assertInstanceOf('ArrayIterator', $it);
		$this->assertEquals(2, $it->count());
		$sheet1 = $it->current();
		$it->next();
		$sheet2 = $it->current();
		$this->assertEquals('testSheet1', $sheet1->getTitle());
		$this->assertEquals('testSheet2', $sheet2->getTitle());
		$this->assertEquals('test', $sheet1->getRootTopic()->getTitle());
		$this->assertEquals('test2', $sheet2->getRootTopic()->getTitle());
	}
}
