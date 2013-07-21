<?php

namespace Chub\XMindPHP;

/**
 * RootTopic.php
 *
 * @author Vladimir Chub <v@chub.com.ua>
 */
class RootTopic extends Topic
{
    private $detachedTopics;

	/**
	 * @param Topic[] $detachedTopics
	 *
	 * @return RootTopic
	 */
	public function setDetachedTopics($detachedTopics)
	{
		$this->detachedTopics = $detachedTopics;

		return $this;
	}

	/**
	 * @param Topic $topic
	 *
	 * @return RootTopic
	 */
	public function addDetachedTopic(Topic $topic)
	{
		$this->detachedTopics[$topic->getId()] = $topic;

		return $this;
	}

	/**
	 * @return Topic[]
	 */
	public function getDetachedTopics()
	{
		return $this->detachedTopics;
	}

	/**
	 * @return Topic[]
	 */
	public function getAllTopics()
	{
		return $this->detachedTopics + $this->getTopics();
	}
}
