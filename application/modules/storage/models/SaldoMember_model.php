<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class SaldoMember_model extends Conf {
	protected $table = 'saldo_member';
	protected $primaryKey = 'id';

	public function member()
	{
		return $this->hasOne('\Model\Storage\Member_model', 'kode_member', 'member_kode');
	}
}