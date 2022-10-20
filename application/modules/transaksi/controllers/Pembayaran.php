<?php defined('BASEPATH') or exit('No direct script access allowed');

class Pembayaran extends Public_Controller
{
    private $pathView = 'transaksi/pembayaran/';
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

    public function modalListBayar()
    {
        try {
            $today = date('Y-m-d');

            $start_date = $today.' 00:00:00';
            $end_date = $today.' 23:59:59';

            $kasir = $this->userid;
            // $kasir = 'USR2207003';

            $m_pesanan = new \Model\Storage\Pesanan_model();
            // $d_pesanan = $m_pesanan->whereBetween('tgl_trans', [$start_date, $end_date])->where('mstatus', 1)->with(['jual_item', 'jual_diskon', 'bayar'])->get();
            $d_pesanan = $m_pesanan->where('tgl_pesan', '>=', '2022-10-12')->where('mstatus', 1)->get();

            $data_bayar = ($d_pesanan->count() > 0) ? $this->getDataBayar($d_pesanan) : null;
            $data_belum_bayar = ($d_pesanan->count() > 0) ? $this->getDataBelumBayar($d_pesanan) : null;

            $content['data'] = array(
                'data_bayar' => $data_bayar,
                'data_belum_bayar' => $data_belum_bayar
            );

            $html = $this->load->view($this->pathView . 'modal_list_bayar', $content, TRUE);
            
            $this->result['html'] = $html;
            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function getDataBayar($_data)
    {
        $data = null;
        foreach ($_data as $k_data => $v_data) {
            if ( !isset($data[ $v_data['kode_pesanan'] ]) ) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->select('kode_faktur')->where('pesanan_kode', $v_data['kode_pesanan'])->where('mstatus', 1)->get();

                $sudah_bayar = 0;
                if ( $d_jual->count() > 0 ) {
                    $d_jual = $d_jual->toArray();

                    $m_bayar = new \Model\Storage\Bayar_model();
                    $d_bayar = $m_bayar->whereIn('faktur_kode', $d_jual)->get();

                    if ( $d_bayar->count() > 0 ) {
                        $sudah_bayar = 1;
                    }
                }

                if ( $sudah_bayar == 1 ) {
                    $data[ $v_data['kode_pesanan'] ] = array(
                        'kode_pesanan' => $v_data['kode_pesanan'],
                        'pelanggan' => $v_data['member'],
                        'kasir' => $v_data['nama_user'],
                        'total' => $v_data['grand_total']
                    );
                }
            }
        }

        return $data;
    }

    public function getDataBelumBayar($_data)
    {
        $data = null;
        foreach ($_data as $k_data => $v_data) {
            if ( !isset($data[ $v_data['kode_pesanan'] ]) ) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->select('kode_faktur')->where('pesanan_kode', $v_data['kode_pesanan'])->where('mstatus', 1)->get();

                $sudah_bayar = 0;
                if ( $d_jual->count() > 0 ) {
                    $d_jual = $d_jual->toArray();

                    $m_bayar = new \Model\Storage\Bayar_model();
                    $d_bayar = $m_bayar->whereIn('faktur_kode', $d_jual)->get();

                    if ( $d_bayar->count() > 0 ) {
                        $sudah_bayar = 1;
                    }
                }

                if ( $sudah_bayar == 0 ) {
                    $data[ $v_data['kode_pesanan'] ] = array(
                        'kode_pesanan' => $v_data['kode_pesanan'],
                        'pelanggan' => $v_data['member'],
                        'kasir' => $v_data['nama_user'],
                        'total' => $v_data['grand_total']
                    );
                }
            }
        }

        return $data;
    }

    public function modalListBill()
    {
        $params = $this->input->get('params');

        $pesanan_kode = $params['pesanan_kode'];

        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('pesanan_kode', $pesanan_kode)->where('mstatus', 1)->get();

        $data = null;
        $bayar = 0;
        if ( $d_jual->count() > 0 ) {
            $data = $d_jual->toArray();

            foreach ($data as $key => $value) {
                $m_bayar = new \Model\Storage\Bayar_model();
                $d_bayar = $m_bayar->where('faktur_kode', $value['kode_faktur'])->first();

                if ( $d_bayar ) {
                    $bayar = 1;
                }
            }
        }

        $content['pesanan_kode'] = $pesanan_kode;
        $content['data'] = $data;
        $content['bayar'] = $bayar;

        $html = $this->load->view($this->pathView . 'modal_list_bill', $content, TRUE);

        echo $html;
    }

    public function modalSplitBill()
    {
        $params = $this->input->get('params');

        $pesanan_kode = $params['pesanan_kode'];

        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('pesanan_kode', $pesanan_kode)->where('mstatus', 1)->where('utama', 1)->with(['jual_item'])->first()->toArray();
        $d_jual_split = $m_jual->where('pesanan_kode', $pesanan_kode)->where('mstatus', 1)->where('utama', 0)->with(['jual_item'])->get()->toArray();

        $content['pesanan_kode'] = $pesanan_kode;
        $content['data_utama'] = $d_jual;
        $content['data_split'] = $d_jual_split;

        $html = $this->load->view($this->pathView . 'modal_split_bill', $content, TRUE);

        echo $html;
    }

    public function modalSplitBillMember()
    {
        $content = null;

        $html = $this->load->view($this->pathView . 'modal_split_bill_member', $content, TRUE);

        echo $html;
    }

    public function modalJumlahSplit()
    {
        $params = $this->input->get('params');

        $pesanan_item_kode = $params['pesanan_item_kode'];
        $jumlah = $params['jumlah'];

        $m_juali = new \Model\Storage\JualItem_model();
        $d_juali = $m_juali->where('pesanan_item_kode', $pesanan_item_kode)->with(['jual_item_detail'])->first()->toArray();

        $content['data'] = $d_juali;
        $content['jumlah'] = $jumlah;

        $html = $this->load->view($this->pathView . 'modal_jumlah_split', $content, TRUE);

        echo $html;
    }

    public function saveSplitBill()
    {
        $params = $this->input->post('params');

        try {
            $pesanan_kode = $params['pesanan_kode'];
            $data_main = isset($params['data_main']) ? $params['data_main'] : null;
            $data_split = isset($params['data_split']) ? $params['data_split'] : null;

            // HAPUS SPLIT BILL
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->select('kode_faktur')->where('pesanan_kode', $pesanan_kode)->where('utama', 0)->get();

            if ( $d_jual->count() > 0 ) {
                $d_jual = $d_jual->toArray();

                $m_juali = new \Model\Storage\JualItem_model();
                $d_juali = $m_juali->select('kode_faktur_item')->whereIn('faktur_kode', $d_jual)->get();

                if ( $d_juali->count() > 0 ) {
                    $d_juali = $d_juali->toArray();

                    $m_jualid = new \Model\Storage\JualItemDetail_model();
                    $m_jualid->whereIn('faktur_item_kode', $d_juali)->delete();

                    $m_juali->whereIn('faktur_kode', $d_jual)->delete();
                }

                $m_jual->where('pesanan_kode', $pesanan_kode)->where('utama', 0)->delete();
            }
            // END - HAPUS SPLIT BILL

            // FAKTUR MAIN
            if ( !empty($data_main) ) {
                $m_jual = new \Model\Storage\Jual_model();
                $now = $m_jual->getDate();

                $kode_faktur = $data_main['faktur_kode'];

                $m_jual->where('kode_faktur', $kode_faktur)->update(
                    array(
                        'tgl_trans' => $now['waktu'],
                        'kasir' => $this->userid,
                        'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
                        'total' => $data_main['grand_total'],
                        'diskon' => 0,
                        'ppn' => 0,
                        'grand_total' => $data_main['grand_total'],
                        'lunas' => 0,
                        'mstatus' => 1,
                        'pesanan_kode' => $pesanan_kode,
                        'utama' => 1,
                        'hutang' => 0
                    )
                );

                $m_juali = new \Model\Storage\JualItem_model();
                $d_juali = $m_juali->select('kode_faktur_item')->where('faktur_kode', $kode_faktur)->get();

                if ( $d_juali->count() > 0 ) {
                    $d_juali = $d_juali->toArray();

                    $m_jualid = new \Model\Storage\JualItemDetail_model();
                    $m_jualid->whereIn('faktur_item_kode', $d_juali)->delete();

                    $m_juali->where('faktur_kode', $kode_faktur)->delete();
                }

                foreach ($data_main['jual_item'] as $k_ji => $v_ji) {
                    $m_juali = new \Model\Storage\JualItem_model();

                    $kode_faktur_item = $m_juali->getNextKode('FKI');
                    $m_juali->kode_faktur_item = $kode_faktur_item;
                    $m_juali->faktur_kode = $kode_faktur;
                    $m_juali->kode_jenis_pesanan = $v_ji['kode_jenis_pesanan'];
                    $m_juali->menu_nama = $v_ji['menu_nama'];
                    $m_juali->menu_kode = $v_ji['menu_kode'];
                    $m_juali->jumlah = $v_ji['jumlah'];
                    $m_juali->harga = $v_ji['harga'];
                    $m_juali->total = $v_ji['total'];
                    $m_juali->request = $v_ji['request'];
                    $m_juali->pesanan_item_kode = $v_ji['pesanan_item_kode'];
                    $m_juali->save();

                    if ( !empty($v_ji['jual_item_detail']) ) {
                        foreach ($v_ji['jual_item_detail'] as $k_jid => $v_jid) {
                            $m_jualid = new \Model\Storage\JualItemDetail_model();
                            $m_jualid->faktur_item_kode = $kode_faktur_item;
                            $m_jualid->menu_nama = $v_jid['menu_nama'];
                            $m_jualid->menu_kode = $v_jid['menu_kode'];
                            $m_jualid->jumlah = 1;
                            $m_jualid->save();
                        }
                    }
                }

                $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first();

                $deskripsi_log_gaktifitas = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/update', $d_jual, $deskripsi_log_gaktifitas );
            }
            // END - FAKTUR MAIN

            // FAKTUR SPLIT
            if ( !empty($data_split) ) {
                foreach ($data_split as $k_ds => $v_ds) {
                    $m_jual = new \Model\Storage\Jual_model();
                    $now = $m_jual->getDate();

                    $kode_faktur = $m_jual->getNextKode('FAK');
                    $m_jual->kode_faktur = $kode_faktur;
                    $m_jual->tgl_trans = $now['waktu'];
                    $m_jual->branch = $this->kodebranch;
                    $m_jual->member = $v_ds['member'];
                    $m_jual->kode_member = $v_ds['kode_member'];
                    $m_jual->kasir = $this->userid;
                    $m_jual->nama_kasir = $this->userdata['detail_user']['nama_detuser'];
                    $m_jual->total = $v_ds['grand_total'];
                    $m_jual->diskon = 0;
                    $m_jual->ppn = 0;
                    $m_jual->grand_total = $v_ds['grand_total'];
                    $m_jual->lunas = 0;
                    $m_jual->mstatus = 1;
                    $m_jual->pesanan_kode = $pesanan_kode;
                    $m_jual->utama = 0;
                    $m_jual->hutang = 0;
                    $m_jual->save();

                    foreach ($v_ds['jual_item'] as $k_ji => $v_ji) {
                        $m_juali = new \Model\Storage\JualItem_model();

                        $kode_faktur_item = $m_juali->getNextKode('FKI');
                        $m_juali->kode_faktur_item = $kode_faktur_item;
                        $m_juali->faktur_kode = $kode_faktur;
                        $m_juali->kode_jenis_pesanan = $v_ji['kode_jenis_pesanan'];
                        $m_juali->menu_nama = $v_ji['menu_nama'];
                        $m_juali->menu_kode = $v_ji['menu_kode'];
                        $m_juali->jumlah = $v_ji['jumlah'];
                        $m_juali->harga = $v_ji['harga'];
                        $m_juali->total = $v_ji['total'];
                        $m_juali->request = $v_ji['request'];
                        $m_juali->pesanan_item_kode = $v_ji['pesanan_item_kode'];
                        $m_juali->save();

                        if ( !empty($v_ji['jual_item_detail']) ) {
                            foreach ($v_ji['jual_item_detail'] as $k_jid => $v_jid) {
                                $m_jualid = new \Model\Storage\JualItemDetail_model();
                                $m_jualid->faktur_item_kode = $kode_faktur_item;
                                $m_jualid->menu_nama = $v_jid['menu_nama'];
                                $m_jualid->menu_kode = $v_jid['menu_kode'];
                                $m_jualid->jumlah = 1;
                                $m_jualid->save();
                            }
                        }
                    }

                    $deskripsi_log_gaktifitas = 'di-simpan oleh ' . $this->userdata['detail_user']['nama_detuser'];
                    Modules::run( 'base/event/save', $m_jual, $deskripsi_log_gaktifitas );
                }
            }
            // END - FAKTUR SPLIT
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function pembayaranForm($_kode_faktur)
    {
        $kode_faktur = exDecrypt( $_kode_faktur );

        $this->add_external_js(
            array(
                "assets/select2/js/select2.min.js",
                "assets/transaksi/pembayaran/js/pembayaran.js",
            )
        );
        $this->add_external_css(
            array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/pembayaran/css/pembayaran.css",
            )
        );
        $data = $this->includes;

        $m_jual = new \Model\Storage\Jual_model();
        $now = $m_jual->getDate();

        $content['akses'] = $this->hasAkses;
        $content['data'] = $this->getDataPenjualan($kode_faktur);
        $content['data_hutang'] = $this->getDataHutang($kode_faktur);
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['data_branch'] = array(
            'nama' => $this->namabranch,
            'alamat' => $this->alamatbranch,
            'telp' => $this->telpbranch,
            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
            'waktu' => $now['waktu']
        );

        $data['view'] = $this->load->view($this->pathView . 'pembayaran_form', $content, TRUE);

        $this->load->view($this->template, $data);
    }

    public function getDataPenjualan($kode_faktur)
    {
        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->with(['jual_item', 'bayar'])->first()->toArray();

        $data = null;
        $jenis_pesanan = null;
        foreach ($d_jual['jual_item'] as $k_ji => $v_ji) {  
            $key = $v_ji['jenis_pesanan'][0]['nama'].' | '.$v_ji['jenis_pesanan'][0]['kode'];
            $key_item = $v_ji['menu_nama'].' | '.$v_ji['menu_kode'];

            if ( !isset($jenis_pesanan[$key]) ) {
                $jual_item = null;
                $jual_item[ $key_item ] = array(
                    'nama' => $v_ji['menu_nama'],
                    'jumlah' => $v_ji['jumlah'],
                    'total' => $v_ji['total']
                );

                $jenis_pesanan[$key] = array(
                    'nama' => $v_ji['jenis_pesanan'][0]['nama'],
                    'jual_item' => $jual_item
                );
            } else {
                if ( !isset($jenis_pesanan[$key]['jual_item'][$key_item]) ) {
                    $jenis_pesanan[$key]['jual_item'][$key_item] = array(
                        'nama' => $v_ji['menu_nama'],
                        'jumlah' => $v_ji['jumlah'],
                        'total' => $v_ji['total']
                    );
                } else {
                    $jenis_pesanan[$key]['jual_item'][$key_item]['jumlah'] += $v_ji['jumlah'];
                    $jenis_pesanan[$key]['jual_item'][$key_item]['total'] += $v_ji['total'];
                }
            }
        }

        $data = array(
            'kode_faktur' => $d_jual['kode_faktur'],
            'tgl_trans' => $d_jual['tgl_trans'],
            'member' => $d_jual['member'],
            'kode_member' => $d_jual['kode_member'],
            'total' => $d_jual['total'],
            'diskon' => $d_jual['diskon'],
            'ppn' => $d_jual['ppn'],
            'grand_total' => $d_jual['grand_total'],
            'lunas' => $d_jual['lunas'],
            'jenis_pesanan' => $jenis_pesanan,
            'bayar' => $d_jual['bayar']
        );

        return $data;
    }

    public function getJenisKartu()
    {
        $m_jenis_kartu = new \Model\Storage\JenisKartu_model();
        $d_jenis_kartu = $m_jenis_kartu->where('status', 1)->get();

        $data = null;
        if ( $d_jenis_kartu->count() > 0 ) {
            $d_jenis_kartu = $d_jenis_kartu->toArray();

            $data[] = array(
                'kode_jenis_kartu' => null,
                'nama' => 'TUNAI',
                'status' => 1
            );

            foreach ($d_jenis_kartu as $key => $value) {
                $data[] = array(
                    'kode_jenis_kartu' => $value['kode_jenis_kartu'],
                    'nama' => $value['nama'],
                    'status' => $value['status']
                );
            }
        }

        return $data;
    }

    public function getDataHutang($kode_faktur)
    {
        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first()->toArray();

        $data = null;
        if ( !empty($d_jual['kode_member']) ) {
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual_hutang = $m_jual->where('kode_member', $d_jual['kode_member'])->where('kode_faktur', '<>', $kode_faktur)->where('lunas', 0)->with(['pesanan'])->get();

            if ( $d_jual_hutang->count() > 0 ) {
                $d_jual_hutang = $d_jual_hutang->toArray();

                foreach ($d_jual_hutang as $key => $value) {
                    $m_bayar_hutang = new \Model\Storage\BayarHutang_model();
                    $d_bayar_hutang = $m_bayar_hutang->where('faktur_kode', $value['kode_faktur'])->sum('bayar');

                    $data[] = array(
                        'tgl_pesan' => !empty($value['pesanan']) ? $value['pesanan']['tgl_pesan'] : $value['tgl_trans'],
                        'faktur_kode' => $value['kode_faktur'],
                        'hutang' => $value['grand_total'],
                        'bayar' => $d_bayar_hutang
                    );
                }
            }
        }

        return $data;
    }

    public function modalMetodePembayaran()
    {
        $params = $this->input->get('params');

        $content['data'] = $params;

        $html = $this->load->view($this->pathView . 'modal_metode_pembayaran', $content, TRUE);

        echo $html;
    }

    public function modalPembayaran()
    {
        $params = $this->input->get('params');

        $content['data'] = $params;

        $html = $this->load->view($this->pathView . 'modal_pembayaran', $content, TRUE);

        echo $html;
    }

    public function savePembayaran()
    {
        $params = $this->input->post('params');

        try {
            $m_bayar = new \Model\Storage\Bayar_model();
            $now = $m_bayar->getDate();

            $m_bayar->tgl_trans = $now['waktu'];
            $m_bayar->faktur_kode = $params['faktur_kode'];
            $m_bayar->jml_tagihan = $params['jml_tagihan'];
            $m_bayar->jml_bayar = $params['jml_bayar'];
            $m_bayar->save();

            $id_header = $m_bayar->id;

            foreach ($params['dataMetodeBayar'] as $key => $value) {
                $m_bayard = new \Model\Storage\BayarDet_model();
                $m_bayard->id_header = $id_header;
                $m_bayard->jenis_bayar = $value['nama'];
                $m_bayard->kode_jenis_kartu = $value['kode_jenis_kartu'];
                $m_bayard->nominal = $value['jumlah'];
                $m_bayard->no_kartu = isset($value['no_kartu']) ? $value['no_kartu'] : null;
                $m_bayard->nama_kartu = isset($value['nama_kartu']) ? $value['nama_kartu'] : null;
                $m_bayard->save();
            }

            if ( !empty($params['dataHutangBayar']) ) {
                foreach ($params['dataHutangBayar'] as $key => $value) {
                    $m_bayarh = new \Model\Storage\BayarHutang_model();
                    $m_bayarh->id_header = $id_header;
                    $m_bayarh->faktur_kode = $value['faktur_kode'];
                    $m_bayarh->hutang = $value['hutang'];
                    $m_bayarh->bayar = $value['bayar'];
                    $m_bayard->save();
                }
            }

            if ( $params['jml_bayar'] >= $params['jml_tagihan'] ) {
                $m_jual = new \Model\Storage\Jual_model();
                $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                    array(
                        'lunas' => 1
                    )
                );
            }

            $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_bayar, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            // $this->result['content'] = array('data' => $data);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function saveHutang()
    {
        $params = $this->input->post('params');

        try {
            $m_jual = new \Model\Storage\Jual_model();
            $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                array(
                    'hutang' => 1
                )
            );

            $d_jual = $m_jual->where('kode_faktur', $params['faktur_kode'])->first();

            $deskripsi_log_gaktifitas = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jual, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan sebagai hutang.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}