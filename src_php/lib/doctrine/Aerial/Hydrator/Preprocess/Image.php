<?php
class Aerial_Hydrator_Preprocess_Image extends Doctrine_Record_Listener
{
	protected $method;
	protected $params;
	protected $column;

	public function __construct($column=null, $method=null, $params=null)
	{
		$this->column = $column;
		$this->method = "constrain";
		$this->params = $params;
	}

	public function preHydrate(Doctrine_Event $event)
	{
		$data = $event->data;
		$foo =  "$this->method";
		$data[$this->column] = $this->$foo($data);
		$event->data = $data;
	}

	public function resize($data)
	{
		$image = new Imagick();
		$image->readimageblob($event->data[$this->column]);
		$image->resizeimage($this->params['width'],$this->params['height'],null,null,false);
	}

	public function constrain($data)
	{
		$image = new Imagick();
		$image->readimageblob($data[$this->column]);
		$image->scaleimage($this->params['width'],$this->params['height'],true);
		return $image->getimageblob();
	}
}