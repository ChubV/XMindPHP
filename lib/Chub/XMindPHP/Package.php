<?php
namespace Chub\XMindPHP;
use PhpOption\Option;

/**
 * XmindPackage.php
 *
 * @author Vladimir Chub <v@chub.com.ua>
 */
class Package
{
	private $file;
	private $rootTopic;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function getRootTopic()
	{
		$this->init();

		return $this->rootTopic;
	}

	private function init()
	{
		try {
			$xml = file_get_contents('zip://' . $this->file . '#content.xml');
			if (empty($xml)) {
				throw new RuntimeException('No content.xml found!');
			}
		} catch (\Exception $e) {
			throw new RuntimeException($e->getMessage());
		}

		$this->parse($xml);
	}

	private function parse($xml)
	{
		$xml = simplexml_load_string($xml);
		$this->rootTopic = new RootTopic();
		$topic = Option::fromValue($xml->sheet->topic)->getOrCall($this->generateException('No root topic found'));
		$this->parseTopic($topic, $this->rootTopic);
	}

	private function generateException($message)
	{
		return function () use ($message) {
			throw new RuntimeException($message);
		};
	}

	private function parseTopic(\SimpleXMLElement $xml, Topic $topic)
	{
		$topic->setNote($xml->notes->plain);
		$topic->setId((string)$xml->attributes()['id']);
		$topic->setTitle((string)$xml->title);
		$topic->setLabel((string)$xml->labels->label);

		if ($xml->children->count()) {
			/** @var \SimpleXMLElement $topics */
			foreach ($xml->children->children() as $topics) {
				if ($topics->getName() == 'topics') {
					$this->parseChildren($topics, $topic);
				}
			}
		}
	}

	private function parseChildren(\SimpleXMLElement $topics, Topic $topic)
	{
		$type = (string)$topics->attributes()['type'];
		if (!in_array($type, ['attached', 'detached'])) {
			return;
		}

		$children = [];
		/** @var \SimpleXMLElement $child */
		foreach ($topics->children() as $child) {
			$childTopic = new Topic();
			$childTopic->setParent($topic);
			$children[] = $childTopic;
			$this->parseTopic($child, $childTopic);
		}

		if ($type == 'attached') {
			$topic->setTopics($children);
		} else {
			/** @var RootTopic $topic */
			$topic->setDetachedTopics($children);
		}

	}
}
