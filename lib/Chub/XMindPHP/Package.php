<?php
namespace Chub\XMindPHP;

/**
 * XmindPackage.php
 *
 * @author Vladimir Chub <v@chub.com.ua>
 */
class Package
{
    private $file;
    private $rootTopic;
    private $isJson = false;

	private $initialized = false;

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
            try {
                /** check if file is 2021 xmind -> use json instead of xml */
                $data = file_get_contents('zip://' . $this->file . '#content.json');
                $this->isJson = true;
            } catch (\Exception $e) {
                $data = file_get_contents('zip://' . $this->file . '#content.xml');
                $this->isJson = false;
            }
            if (empty($data)) {
                throw new RuntimeException('No content.xml found!');
            }
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $this->parse($data);
	}

    private function parse($data)
    {
        if ($this->isJson) {
            $data = json_decode($data);
            if (!$data[0]->rootTopic) {
                throw new RuntimeException('No root topic found');
            } else {
                $topic = $data[0]->rootTopic;
            }
        } else {
            $data = simplexml_load_string($data);
            if (!$data->sheet->topic) {
                throw new RuntimeException('No root topic found');
            } else {
                $topic = $data->sheet->topic;
            }
        }
        $this->rootTopic = new RootTopic();

        $this->parseTopic($topic, $this->rootTopic);
    }

	private function generateException($message)
	{
		return function () use ($message) {
			throw new RuntimeException($message);
		};
	}

    private function parseTopic($data, Topic $topic)
    {

        if ($this->isJson) {
            $note = isset($data->notes) ? $data->notes->plain : null;
            !isset($note) ? : $topic->setNote($note);

            $topic->setId((string)$data->id);
            $topic->setTitle((string)$data->title);

            $label = isset($data->labels) ? (string) $data->labels->label : null;
            !isset($note) ? : $topic->setLabel($label);

            if (isset($data->children) && count((array)$data->children) > 0) {
                /** children can be attached or detached */
                foreach ($data->children as $typeOfChildren => $topics) {
                    foreach ($topics as $t) {
                        $t->type=$typeOfChildren;
                    }
                    $this->parseChildren($topics, $topic);
                }
            }
        } else {
            $topic->setNote($data->notes->plain);
            $topic->setId((string)$data->attributes()['id']);
            $topic->setTitle((string)$data->title);
            $topic->setLabel((string)$data->labels->label);

            if ($data->children->count()) {
                /** @var \SimpleXMLElement $topics */
                foreach ($data->children->children() as $topics) {
                    if ($topics->getName() == 'topics') {
                        $this->parseChildren($topics, $topic);
                    }
                }
            }
        }
	}

    private function parseChildren($topics, Topic $topic)
	{
        if ($this->isJson) {
            $children = [];
            foreach ($topics as $child) {
                $type = $child->type;
                if (!in_array($type, ['attached', 'detached'])) {
                    return;
                }
                $childTopic = new Topic();
                $childTopic->setParent($topic);
                $children[] = $childTopic;
                $this->parseTopic($child, $childTopic);
            }

        } else {

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
        }

        if ($type == 'attached') {
            $topic->setTopics($children);
        } else {
            /** @var RootTopic $topic */
            $topic->setDetachedTopics($children);
        }
    }
}
