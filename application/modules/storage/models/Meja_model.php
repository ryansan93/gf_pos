<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Meja_model extends Conf {
	protected $table = 'meja';
	protected $primaryKey = 'id';
    public $timestamps = false;

    public function meja_log()
	{
		return $this->hasMany('\Model\Storage\MejaLog_model', 'meja_id', 'id');
	}

	public function lantai()
	{
		return $this->hasOne('\Model\Storage\Lantai_model', 'id', 'lantai_id');
	}
}