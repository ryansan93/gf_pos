<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// require __DIR__ . '/vendor/autoload.php';

require_once('escpos/src/Mike42/Escpos/EscposImage.php');
require_once('escpos/src/Mike42/Escpos/Printer.php');
require_once('escpos/src/Mike42/Escpos/PrintConnectors/FilePrintConnector.php');

class Esc_pos
{
    function __construct()
    {
        parent::__construct();
    }
}
