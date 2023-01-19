<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require __DIR__ . '/vendor/autoload.php';

require_once(EscposImage);
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class Esc_pos
{
    function __construct()
    {
        parent::__construct();
    }
}
