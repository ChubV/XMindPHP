<?php

namespace Chub\XMindPHP;

/**
 * Sheet
 *
 * @author Vladimir Chub <v@chub.com.ua>
 */
class Sheet
{
	/** @var RootTopic */
	private $rootTopic;
	/** @var string $title */
	private $title;
	private $id;

	/**
	 * @param mixed $id
	 *
	 * @return Sheet
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return RootTopic
	 */
	public function getRootTopic()
	{
		return $this->rootTopic;
	}

	/**
	 * @param RootTopic $rt
	 *
	 * @return Sheet
	 */
	public function setRootTopic(RootTopic $rt)
	{
		$this->rootTopic = $rt;

		return $this;
	}

	/**
	 * @param string $title
	 *
	 * @return Sheet
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
}
