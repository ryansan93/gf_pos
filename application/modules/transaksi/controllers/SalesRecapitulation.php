<?php defined('BASEPATH') or exit('No direct script access allowed');

class SalesRecapitulation extends Public_Controller
{
    private $pathView = 'transaksi/sales_recapitulation/';
    private $url;
    private $hakAkses;
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index()
    {
        // if ( $this->hakAkses['a_view'] == 1 ) {
            $this->load->library('Mobile_Detect');
            $detect = new Mobile_Detect();

            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    "assets/transaksi/pembayaran/js/pembayaran.js",
                    "assets/transaksi/sales_recapitulation/js/sales-recapitulation.js"
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    "assets/transaksi/sales_recapitulation/css/sales-recapitulation.css"
                )
            );
            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['kode_branch'] = $this->kodebranch;

            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $kode_branch = $this->kodebranch;

        $m_conf = new \Model\Storage\Conf();
        $sql = "
        ";
        $d_jual = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jual->count() > 0 ) {
        }
    }
}