<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Bayar_model extends Conf {
	protected $table = 'bayar';

	public function bayar_det()
	{
		return $this->hasMany('\Model\Storage\BayarDet_model', 'id_header', 'id')->with(['jenis_kartu']);
	}

	public function bayar_hutang()
	{
		return $this->hasMany('\Model\Storage\BayarHutang_model', 'id_header', 'id');
	}

	public function bayar_diskon()
	{
		return $this->hasMany('\Model\Storage\BayarDiskon_model', 'id_header', 'id')->with(['diskon']);
	}

	public function jenis_kartu()
	{
		return $this->hasOne('\Model\Storage\JenisKartu_model', 'kode_jenis_kartu', 'jenis_kartu_kode');
	}
}