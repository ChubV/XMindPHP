<?php

namespace XMindPHP;
use Chub\XMindPHP\Package;

/**
 * TopicTest
 *
 * @author Vladimir Chub <v@chub.com.ua>
 */
class TopicTest extends \PHPUnit_Framework_TestCase
{
	/** @var \Chub\XMindPHP\RootTopic */
	private $rootTopic;

	protected function setUp()
	{
		$package = new Package(__DIR__ . '/../res/cr.xmind');
		$this->rootTopic = $package->getRootTopic();

		$this->assertInstanceOf('Chub\\XMindPHP\\RootTopic', $this->rootTopic);
	}

	public function testRootTopic()
	{
		// has 2 topics
		$this->assertCount(2, $this->rootTopic->getTopics());

		// has detached topic
		$this->assertCount(1, $this->rootTopic->getDetachedTopics());

		// has no parent
		$this->assertNull($this->rootTopic->getParent());

		$this->assertEquals('comment', $this->rootTopic->getLabel());
		$this->assertEquals('note', $this->rootTopic->getNote());
		$this->assertEquals('15k05qolrg4gr21jd857vpdnlj', $this->rootTopic->getId());
		$this->assertEquals('test', $this->rootTopic->getTitle());
	}

	public function testDetachedTopic()
	{
		$dt = current($this->rootTopic->getDetachedTopics());

		$this->assertInstanceOf('Chub\\XMindPHP\\Topic', $dt);

		$this->assertCount(1, $dt->getTopics());

		$this->assertEquals($this->rootTopic->getId(), $dt->getParent()->getId());

		$this->assertEmpty($dt->getLabel());
		$this->assertNotEmpty($dt->getNote());
		$this->assertEquals('3tqml001h7l50c5sdmm9h51avv', $dt->getId());
		$this->assertEquals('test1', $dt->getTitle());
	}

	public function testFindBy()
	{
		$self = $this->rootTopic->findById('15k05qolrg4gr21jd857vpdnlj');
		$this->assertCount(1, $self);
		$this->assertEquals($this->rootTopic, current($self));

		$detached = $this->rootTopic->findByTitle('test1');
		$this->assertCount(1, $detached);
		$this->assertEquals('3tqml001h7l50c5sdmm9h51avv', current($detached)->getId());
	}
}
