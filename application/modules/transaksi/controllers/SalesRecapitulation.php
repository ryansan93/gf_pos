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

        $start_date = $params['start_date'].' 00:00:01';
        $end_date = $params['end_date'].' 23:59:59';
        $kode_branch = $this->kodebranch;

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                _data.tgl_trans,
                _data.mstatus,
                _data.member,
                _data.kode_pesanan,
                _data.kode_faktur,
                _data.kode_faktur_utama,
                _data.nama_waitress,
                _data.nama_kasir,
                _data.grand_total,
                max(_data.status_gabungan) as status_gabungan
            from (
                select 
                    j.tgl_trans,
                    j.mstatus,
                    j.member,
                    p.kode_pesanan as kode_pesanan,
                    j.kode_faktur as kode_faktur,
                    j.kode_faktur as kode_faktur_utama,
                    p.nama_user as nama_waitress,
                    j.nama_kasir as nama_kasir,
                    j.grand_total as grand_total,
                    0 as status_gabungan
                from jual j
                right join
                    pesanan p
                    on
                        j.pesanan_kode = p.kode_pesanan
                where
                    j.mstatus = 1

                union all

                select
                    j.tgl_trans,
                    j.mstatus,
                    _jg.member,
                    p.kode_pesanan as kode_pesanan,
                    j.kode_faktur as kode_faktur,
                    _jg.faktur_kode as kode_faktur_utama,
                    p.nama_user as nama_waitress,
                    _jg.nama_kasir as nama_kasir,
                    j.grand_total as grand_total,
                    1 as status_gabungan
                from jual_gabungan jg
                right join
                    (
                        select jg.*, j.member, j.nama_kasir as nama_kasir from jual_gabungan jg
                        right join
                            jual j
                            on
                                jg.faktur_kode = j.kode_faktur
                        where
                            j.mstatus = 1
                    ) _jg
                    on
                        jg.id = _jg.id
                right join
                    jual j
                    on
                        jg.faktur_kode_gabungan = j.kode_faktur
                right join
                    pesanan p
                    on
                        j.pesanan_kode = p.kode_pesanan
            ) _data
            where 
                _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                _data.nama_kasir is not null and
                SUBSTRING(_data.kode_pesanan, 1, 3) = '".$kode_branch."'
            group by
                _data.tgl_trans,
                _data.mstatus,
                _data.member,
                _data.kode_pesanan,
                _data.kode_faktur,
                _data.kode_faktur_utama,
                _data.nama_waitress,
                _data.nama_kasir,
                _data.grand_total
            order by
                _data.tgl_trans desc,
                _data.kode_pesanan desc,
                _data.kode_faktur desc
        ";
        $d_jual = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jual->count() > 0 ) {
            $data = $d_jual->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, TRUE);

        echo $html;
    }
}