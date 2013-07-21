<?php

namespace Chub\XMindPHP;

/**
 * Topic.php
 *
 * @author Vladimir Chub <v@chub.com.ua>
 */
class Topic
{
	private $topics = [];
	private $parent;
	private $label;
	private $note;
	private $title;
	private $id;

	/**
	 * @param mixed $id
	 *
	 * @return Topic
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
	 * @param mixed $label
	 *
	 * @return Topic
	 */
	public function setLabel($label)
	{
		$this->label = $label;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param mixed $note
	 *
	 * @return Topic
	 */
	public function setNote($note)
	{
		$this->note = $note;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getNote()
	{
		return $this->note;
	}

	/**
	 * @param mixed $parent
	 *
	 * @return Topic
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * @return Topic
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param mixed $title
	 *
	 * @return Topic
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param Topic[] $topics
	 *
	 * @return Topic
	 */
	public function setTopics($topics)
	{
		$this->topics = $topics;

		return $this;
	}

	/**
	 * @return Topic[]
	 */
	public function getTopics()
	{
		return $this->topics;
	}

	/**
	 * @return Topic[]
	 */
	public function getAllTopics()
	{
		return $this->topics;
	}

	public function __call($name, $arguments)
	{
		if (strpos($name, 'findBy') !== 0) {
			throw new \BadMethodCallException($name);
		}
		$param = lcfirst(substr($name, 6));
		if (!in_array($param, ['title', 'id', 'note', 'label'])) {
			throw new \BadMethodCallException("$param is not allowed to findBy");
		}
		$res = [];
		$find = current($arguments);

		if ($this->$param == $find) {
			$res[] = $this;
		}
		foreach($this->getAllTopics() as $child) {
			$res = array_merge($res, $child->{$name}($find));
		}

		return $res;
	}
}
