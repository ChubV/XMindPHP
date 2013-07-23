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
	/** @var Sheet[] */
	private $sheets;

	private $initialized = false;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function getSheet($index)
	{
		$this->init();

		return isset($this->sheets[$index])?$this->sheets[$index]:null;
	}

	/**
	 * @return \ArrayIterator
	 */
	public function getSheetsIterator()
	{
		$this->init();

		return new \ArrayIterator($this->sheets);
	}

	private function init()
	{
		if ($this->initialized) {
			return;
		}

		try {
			$xml = file_get_contents('zip://' . $this->file . '#content.xml');
			if (empty($xml)) {
				throw new RuntimeException('No content.xml found!');
			}
		} catch (\Exception $e) {
			throw new RuntimeException($e->getMessage());
		}

		$this->parse($xml);
		$this->initialized = true;
	}

	private function parse($xml)
	{
		$xml = simplexml_load_string($xml);
		/** @var \SimpleXmlElement $sheet */
		foreach ($xml->children() as $sheet) {
			if ($sheet->getName() == 'sheet') {
				$this->parseSheet($sheet);
			}
		}
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

	private function parseSheet(\SimpleXMLElement $xml)
	{
		$rt = new RootTopic();
		$sheet = new Sheet();
		$sheet->setRootTopic($rt)->setTitle((string)($xml->title))->setId((string)($xml->attributes()['id']));
		$topic = Option::fromValue($xml->topic)->getOrCall($this->generateException('No root topic found'));
		$this->parseTopic($topic, $rt);

		$this->sheets[] = $sheet;
	}
}
