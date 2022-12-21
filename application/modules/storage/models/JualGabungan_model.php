<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JualGabungan_model extends Conf {
	protected $table = 'jual_gabungan';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function jual_item()
	{
		return $this->hasMany('\Model\Storage\JualItem_model', 'faktur_kode', 'kode_faktur')->with(['jual_item_detail', 'jenis_pesanan']);
	}
}