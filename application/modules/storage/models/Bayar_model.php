<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Bayar_model extends Conf {
	protected $table = 'bayar';

	public function bayar_det()
	{
		return $this->hasMany('\Model\Storage\BayarDet_model', 'id_header', 'id');
	}

	public function jenis_kartu()
	{
		return $this->hasOne('\Model\Storage\JenisKartu_model', 'kode_jenis_kartu', 'jenis_kartu_kode');
	}
}