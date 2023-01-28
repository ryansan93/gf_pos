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
        $this->hakAkses = hakAkses($this->url);
    }

    public function cekSaldoAwalKasir()
    {
        $status = 0;

        try {
            if ( $this->hakAkses['a_submit'] == 1 ) {
                $m_sak = new \Model\Storage\SaldoAwalKasir_model();
                $now = $m_sak->getDate();

                $start_date = $now['tanggal'].' 00:00:01';
                $end_date = $now['tanggal'].' 23:59:59';

                $d_sak = $m_sak->whereBetween('tanggal', [$start_date, $end_date])->where('user_id', $this->userid)->where('branch_kode', $this->kodebranch)->orderBy('id', 'desc')->first();

                if ( $d_sak ) {
                    $status = 1;
                    if ( hasAkses('transaksi/Pembayaran') ) {
                        $status = 2;
                    }
                }
            } else {
                if ( hasAkses('transaksi/Pembayaran') ) {
                    $status = 2;
                } else if ( hasAkses('transaksi/Penjualan') ) {
                    if ( hakAkses('/transaksi/Penjualan')['a_submit'] == 1 ) {
                        $status = 1;
                    }
                } else if ( !hasAkses('transaksi/SaldoAwalKasir') ) {
                    $status = 3;
                }
            }

            $this->result['status'] = $status;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }        

        echo display_json( $this->result );
    }

    public function modalSaldoAwalKasir()
    {
        $m_sak = new \Model\Storage\SaldoAwalKasir_model();
        $now = $m_sak->getDate();
        $d_sak = $m_sak->where('tanggal', date('Y-m-d'))->where('user_id', $this->userid)->where('branch_kode', $this->kodebranch)->orderBy('id', 'desc')->first();

        $nominal = null;
        if ( $d_sak ) {
            $nominal = $d_sak->nominal;
        }

        $content['nominal'] = $nominal;
        $content['waktu'] = $now['waktu'];
        $html = $this->load->view($this->pathView . 'modal_saldo_awal_kasir', $content, TRUE);

        echo $html;
    }

    public function saveSaldoAwalKasir()
    {
        $jmlUang = $this->input->post('jmlUang');

        try {
            $m_sak = new \Model\Storage\SaldoAwalKasir_model();
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