<?php defined('BASEPATH') or exit('No direct script access allowed');

class SaldoAkhirKasir extends Public_Controller
{
    private $pathView = 'transaksi/saldo_akhir_kasir/';
    private $url;
    private $hakAkses;
    private $persen_ppn = 0;
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function modalSaldoAkhirKasir()
    {
        $m_sak = new \Model\Storage\SaldoAkhirKasir_model();
        $d_sak = $m_sak->where('tanggal', date('Y-m-d'))->where('user_id', $this->userid)->where('branch_kode', $this->kodebranch)->orderBy('id', 'desc')->first();

        $nominal = null;
        if ( $d_sak ) {
            $nominal = $d_sak->nominal;
        }

        $content['nominal'] = $nominal;
        $html = $this->load->view($this->pathView . 'modal_saldo_akhir_kasir', $content, TRUE);

        echo $html;
    }

    public function saveSaldoAkhirKasir()
    {
        $jmlUang = $this->input->post('jmlUang');

        try {
            $m_sak = new \Model\Storage\SaldoAkhirKasir_model();
            $now = $m_sak->getDate();

            $m_sak->tanggal = $now['waktu'];
            $m_sak->user_id = $this->userid;
            $m_sak->nominal = $jmlUang;
            $m_sak->branch_kode = $this->kodebranch;
            $m_sak->save();

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}