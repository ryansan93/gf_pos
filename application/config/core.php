<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Config File
 */

// Site Details
$config['site_version']          = "0.0.01";
$config['public_theme']          = "Default";
$config['admin_theme']           = "Admin";

// Pagination
$config['num_links']             = 8;
$config['full_tag_open']         = "<div class=\"pagination\">";
$config['full_tag_close']        = "</div>";

// Miscellaneous
$config['profiler']              = FALSE;
$config['error_delimeter_left']  = "";
$config['error_delimeter_right'] = "<br />";

$config['btn-collapse'] = '<span class="glyphicon glyphicon-chevron-right cursor-p btn-collapse"></span>';

/* untuk upload */
$config['upload_param'] = array(
		'upload_path'          => 'assets/image/real_image/',
		'allowed_types'        => 'jpg|jpeg|png',
		'max_size'             => 10240
);
$config['max_memo_length'] = 100;

$config['MathOperator'] = array('+','-','/','*','=','<=','>=','<','>');

/*STATUS SIMPAN*/
$config['g_status'] = array(
	0 => 'delete',
	1 => 'submit',
	2 => 'ack',
	3 => 'approve',
	4 => 'reject',
);

$config['judul_aplikasi'] = 'GRAHA FAMILY POS';
$config['nama_aplikasi'] = 'GRAHA FAMILY';

$config['paper_size'] = '58';
$config['print_jenis_bayar'] = 1;
