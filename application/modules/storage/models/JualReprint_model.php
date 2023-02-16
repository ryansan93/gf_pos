<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JualReprint_model extends Conf {
	protected $table = 'jual_reprint';
	protected $primaryKey = 'id';
	public $timestamps = false;
}