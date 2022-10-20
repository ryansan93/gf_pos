<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JualDiskon_model extends Conf {
	protected $table = 'jual_diskon';
	public $timestamps = false;

	public function diskon()
	{
		return $this->hasOne('\Model\Storage\Diskon_model', 'diskon_kode', 'kode')->with(['detail']);
	}
}