<?php defined('BASEPATH') or exit('No direct script access allowed');

class SaldoAwalKasir extends Public_Controller
{
    private $pathView = 'transaksi/saldo_awal_kasir/';
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
        $this->hasAkses = hakAkses($this->url);
    }

    public function cekSaldoAwalKasir()
    {
        $status = 0;

        try {
            $m_sak = new \Model\Storage\SaldoAwalKasir_model();
            $d_sak = $m_sak->where('tanggal', date('Y-m-d'))->where('user_id', $this->userid)->where('branch_kode', $this->kodebranch)->first();

            if ( $d_sak ) {
                $status = 1;
            }

            $this->result['status'] = $status;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }        

        echo display_json( $this->result );

        // $status = 2;
        // if ( $this->session->userdata('isLogin') ) {
        // }

        // echo $status;
    }

    public function modalSaldoAwalKasir()
    {
        $m_sak = new \Model\Storage\SaldoAwalKasir_model();
        $d_sak = $m_sak->where('tanggal', date('Y-m-d'))->where('user_id', $this->userid)->where('branch_kode', $this->kodebranch)->orderBy('id', 'desc')->first();

        $nominal = null;
        if ( $d_sak ) {
            $nominal = $d_sak->nominal;
        }

        $content['nominal'] = $nominal;
        $html = $this->load->view($this->pathView . 'modal_saldo_awal_kasir', $content, TRUE);

        echo $html;
    }

    public function saveSaldoAwalKasir()
    {
        $jmlUang = $this->input->post('jmlUang');

        try {
            $m_sak = new \Model\Storage\SaldoAwalKasir_model();
            $m_sak->tanggal = date('Y-m-d');
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