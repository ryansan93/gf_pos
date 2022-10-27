<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Lantai_model extends Conf {
	protected $table = 'lantai';
	protected $primaryKey = 'id';
    public $timestamps = false;

    public function meja()
	{
		return $this->hasMany('\Model\Storage\Meja_model', 'lantai_id', 'id');
	}
}