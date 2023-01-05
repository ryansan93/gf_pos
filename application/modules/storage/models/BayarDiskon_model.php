<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class BayarDiskon_model extends Conf {
	protected $table = 'bayar_diskon';

	public function jenis_kartu()
	{
		return $this->hasOne('\Model\Storage\Diskon_model', 'kode_diskon', 'diskon_kode');
	}
}