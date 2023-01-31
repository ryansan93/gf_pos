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
        $this->hakAkses = hakAkses($this->url);
    }

    public function modalListBayar()
    {
        try {
            $m_conf = new \Model\Storage\Conf();
            $now = $m_conf->getDate();
            $today = $now['tanggal'];
            // $today = '2023-01-02';

            $start_date = $today.' 00:00:00';
            $end_date = $today.' 23:59:59';

            $kasir = $this->userid;
            $kode_branch = $this->kodebranch;
            // $kasir = 'USR2207003';

            $m_pesanan = new \Model\Storage\Pesanan_model();
            $d_pesanan = $m_pesanan->whereBetween('tgl_pesan', [$start_date, $end_date])->where('branch', $kode_branch)->where('mstatus', 1)->with(['meja'])->get();
            // $d_pesanan = $m_pesanan->where('tgl_pesan', '>=', '2022-10-12')->where('mstatus', 1)->get();

            $data_bayar = $this->getDataBayar($start_date, $end_date, $kode_branch);
            $data_belum_bayar = $this->getDataBelumBayar($d_pesanan);

            $content['data'] = array(
                'data_bayar' => $data_bayar,
                'data_belum_bayar' => $data_belum_bayar
            );
            $content['akses_waitress'] = hakAkses('/transaksi/Penjualan');
            $content['akses_kasir'] = hakAkses('/transaksi/Pembayaran');
            $content['today'] = $today;

            $html = $this->load->view($this->pathView . 'modal_list_bayar', $content, TRUE);
            
            $this->result['html'] = $html;
            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function getDataBayar($start_date, $end_date, $branch)
    {
        $data = null;

        $m_bayar = new \Model\Storage\Bayar_model();
        $d_bayar = $m_bayar->whereBetween('tgl_trans', [$start_date, $end_date])->where('mstatus', 1)->get();

        if ( $d_bayar->count() > 0 ) {
            $d_bayar = $d_bayar->toArray();

            foreach ($d_bayar as $key => $value) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('kode_faktur', $value['faktur_kode'])->where('mstatus', 1)->with(['pesanan'])->first();

                $member_group = null;
                $member = $value['member'];

                if ( !empty($value['member_kode']) ) {
                    $m_member = new \Model\Storage\Member_model();
                    $d_member = $m_member->where('kode_member', $value['member_kode'])->with(['member_group'])->first()->toArray();

                    $member = $d_member['nama'];

                    if ( !empty($d_member['member_group']) ) {
                        $member_group = $d_member['member_group']['nama'];
                    }
                }

                $data[ $value['id'] ] = array(
                    'kode_pesanan' => !empty($d_jual) ? $d_jual->pesanan_kode : null,
                    'kode_faktur' => !empty($d_jual) ? $d_jual->kode_faktur : null,
                    'member_group' => $member_group,
                    'pelanggan' => $member,
                    'kasir' => $value['nama_kasir'],
                    'total' => $value['jml_tagihan'],
                    'bayar' => $value['jml_bayar']
                );
            }
        }

        return $data;
    }

    public function getDataBelumBayar($_data)
    {
        // $data = null;

        // foreach ($_data as $k_data => $v_data) {
        //     if ( !isset($data[ $v_data['kode_pesanan'] ]) ) {
        //         $m_jual = new \Model\Storage\Jual_model();
        //         $d_jual = $m_jual->select('kode_faktur')->where('pesanan_kode', $v_data['kode_pesanan'])->where('mstatus', 1)->get();

        //         $sudah_bayar = 0;
        //         if ( $d_jual->count() > 0 ) {
        //             $d_jual = $d_jual->toArray();

        //             $kode_faktur = $d_jual;

        //             $m_jualg = new \Model\Storage\JualGabungan_model();
        //             $d_jualg = $m_jualg->select('faktur_kode')->whereIn('faktur_kode_gabungan', $d_jual)->get();

        //             if ( $d_jualg->count() > 0 ) {
        //                 $d_jualg = $d_jualg->toArray();

        //                 $kode_faktur = $d_jualg;
        //             }

        //             $m_bayar = new \Model\Storage\Bayar_model();
        //             $d_bayar = $m_bayar->whereIn('faktur_kode', $kode_faktur)->where('mstatus', 1)->get();

        //             if ( $d_bayar->count() > 0 ) {
        //                 $sudah_bayar = 1;
        //             }
        //         }

        //         $member_group = null;

        //         if ( !empty($v_data['kode_member']) ) {
        //             $m_member = new \Model\Storage\Member_model();
        //             $d_member = $m_member->where('kode_member', $v_data['kode_member'])->with(['member_group'])->first()->toArray();

        //             if ( !empty($d_member['member_group']) ) {
        //                 $member_group = $d_member['member_group']['nama'];
        //             }
        //         }

        //         if ( $sudah_bayar == 0 ) {
        //             $data[ $v_data['kode_pesanan'] ] = array(
        //                 'meja' => $v_data['meja']['nama_meja'],
        //                 'lantai' => $v_data['meja']['lantai']['nama_lantai'],
        //                 'kode_pesanan' => $v_data['kode_pesanan'],
        //                 'member_group' => $member_group,
        //                 'pelanggan' => $v_data['member'],
        //                 'kasir' => $v_data['nama_user'],
        //                 'total' => $v_data['grand_total']
        //             );
        //         }
        //     }
        // }

        // return $data;

        $data = null;

        foreach ($_data as $k_data => $v_data) {
            if ( !isset($data[ $v_data['kode_pesanan'] ]) ) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('pesanan_kode', $v_data['kode_pesanan'])->where('lunas', 0)->where('mstatus', 1)->get();

                $sudah_bayar = 0;
                if ( $d_jual->count() > 0 ) {
                    $d_jual = $d_jual->toArray();

                    foreach ($d_jual as $k_jual => $v_jual) {
                        $m_jualg = new \Model\Storage\JualGabungan_model();
                        $sql = "
                            select * from jual_gabungan jg
                            right join
                                jual j
                                on
                                    jg.faktur_kode = j.kode_faktur
                            where
                                j.mstatus = 1 and
                                jg.faktur_kode_gabungan = '".$v_jual['kode_faktur']."'
                        ";
                        $d_jualg = $m_jualg->hydrateRaw( $sql );

                        if ( $d_jualg->count() == 0 ) {
                            $m_bayar = new \Model\Storage\Bayar_model();
                            $d_bayar = $m_bayar->where('faktur_kode', $v_jual['kode_faktur'])->where('mstatus', 1)->first();

                            if ( empty($d_bayar) ) {
                                $member_group = null;

                                if ( !empty($v_jual['kode_member']) ) {
                                    $m_member = new \Model\Storage\Member_model();
                                    $d_member = $m_member->where('kode_member', $v_jual['kode_member'])->with(['member_group'])->first();

                                    if ( $d_member ) {
                                        if ( !empty($d_member['member_group']) ) {
                                            $member_group = $d_member['member_group']['nama'];
                                        }
                                    }
                                }

                                $data[ $v_jual['kode_faktur'] ] = array(
                                    'meja' => $v_data['meja']['nama_meja'],
                                    'lantai' => $v_data['meja']['lantai']['nama_lantai'],
                                    'kode_faktur' => $v_jual['kode_faktur'],
                                    'kode_pesanan' => $v_data['kode_pesanan'],
                                    'member_group' => $member_group,
                                    'pelanggan' => $v_jual['member'],
                                    'kasir' => $v_data['nama_user'],
                                    'total' => $v_jual['grand_total']
                                );
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function modalListBill()
    {
        $params = $this->input->get('params');

        $pesanan_kode = $params['pesanan_kode'];
        $kode_faktur = null;

        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('pesanan_kode', $pesanan_kode)->where('mstatus', 1)->get();

        $data = null;
        $bayar_utama = 0;
        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            foreach ($d_jual as $key => $value) {
                $bayar = 0;

                $m_bayar = new \Model\Storage\Bayar_model();
                $d_bayar = $m_bayar->where('faktur_kode', $value['kode_faktur'])->where('mstatus', 1)->first();

                if ( $d_bayar ) {
                    $bayar = 1;
                    $bayar_utama = 1;
                }

                if ( $value['utama'] == 1 ) {
                    $kode_faktur = $value['kode_faktur'];
                }

                $data[] = array(
                    'lunas' => $value['lunas'],
                    'hutang' => $value['hutang'],
                    'kode_faktur' => $value['kode_faktur'],
                    'member' => $value['member'],
                    'grand_total' => $value['grand_total'],
                    'bayar' => $bayar
                );
            }
        }

        $content['pesanan_kode'] = $pesanan_kode;
        $content['kode_faktur'] = $kode_faktur;
        $content['data'] = $data;
        $content['bayar_utama'] = $bayar_utama;

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
                        'ppn' => $data_main['grand_total_ppn'],
                        'service_charge' => $data_main['grand_total_sc'],
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
                    $m_juali->ppn = $v_ji['ppn'];
                    $m_juali->service_charge = $v_ji['sc'];
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
                    $m_jual->ppn = $v_ds['grand_total_ppn'];
                    $m_jual->service_charge = $v_ds['grand_total_sc'];
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
                        $m_juali->ppn = $v_ji['ppn'];
                        $m_juali->service_charge = $v_ji['sc'];
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

    public function getDataKategoriPembayaran($_kode_faktur)
    {
        $data = null;

        $m_kjk = new \Model\Storage\KategoriJenisKartu_model();
        $d_kjk = $m_kjk->get();

        if ( $d_kjk->count() > 0 ) {
            $d_kjk = $d_kjk->toArray();

            foreach ($d_kjk as $k_kjk => $v_kjk) {
                $m_bayar = new \Model\Storage\Bayar_model();
                $sql = "
                    select
                        kjk.id,
                        kjk.nama,
                        sum(bd.nominal) as nominal
                    from bayar_det bd
                    right join
                        (select * from bayar where mstatus = 1) b
                        on
                            bd.id_header = b.id
                    right join
                        jenis_kartu jk
                        on
                            jk.kode_jenis_kartu = bd.kode_jenis_kartu
                    right join
                        kategori_jenis_kartu kjk
                        on
                            kjk.id = jk.kategori_jenis_kartu_id
                    where
                        b.faktur_kode = '".$_kode_faktur."' and
                        kjk.id = ".$v_kjk['id']."
                    group by
                        kjk.id,
                        kjk.nama
                ";
                $d_bayar = $m_bayar->hydrateRaw( $sql );
                if ( $d_bayar->count() > 0 ) {
                    $d_bayar = $d_bayar->toArray();

                    $data[ $v_kjk['id'] ] = array(
                        'id' => $d_bayar[0]['id'],
                        'nama' => $d_bayar[0]['nama'],
                        'nominal' => $d_bayar[0]['nominal']
                    );
                } else {
                    $data[ $v_kjk['id'] ] = array(
                        'id' => $v_kjk['id'],
                        'nama' => $v_kjk['nama'],
                        'nominal' => 0
                    );
                }
            }
        }

        return $data;
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

        $content['akses'] = $this->hakAkses;
        $content['data'] = $this->getDataPenjualan($kode_faktur);
        $content['data_hutang'] = $this->getDataHutang($kode_faktur);
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['kategori_pembayaran'] = $this->getDataKategoriPembayaran($kode_faktur);
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

    public function getPpn($kodeBranch)
    {

        $m_ppn = new \Model\Storage\Ppn_model();
        $now = $m_ppn->getDate();
        $d_ppn = $m_ppn->where('branch_kode', $kodeBranch)->where('tgl_berlaku', '<=', $now['tanggal'])->where('mstatus', 1)->first();

        $nilai = 0;
        if ( $d_ppn ) {
            $nilai = $d_ppn->nilai;
        }

        return $nilai;
    }

    public function getServiceCharge($kodeBranch)
    {
        $m_sc = new \Model\Storage\ServiceCharge_model();
        $now = $m_sc->getDate();
        $d_sc = $m_sc->where('branch_kode', $kodeBranch)->where('tgl_berlaku', '<=', $now['tanggal'])->where('mstatus', 1)->first();

        $nilai = 0;
        if ( $d_sc ) {
            $nilai = $d_sc->nilai;
        }

        return $nilai;
    }

    public function getDataPenjualan($kode_faktur)
    {
        $data = null;
        $detail = null;

        $m_jual = new \Model\Storage\Jual_model();
        $sql = "
            select 
                j.kode_faktur,
                j.tgl_trans,
                j.branch as kode_branch,
                brc.nama as nama_branch,
                brc.alamat as alamat_branch,
                brc.telp as telp_branch,
                j.kasir,
                j.nama_kasir,
                j.member,
                j.kode_member,
                j.total,
                b.diskon,
                j.grand_total,
                j.lunas,
                j.ppn,
                j.service_charge,
                m.nama_meja
            from jual j
            left join
                (select * from bayar where mstatus = 1) b
                on
                    j.kode_faktur = b.faktur_kode
            right join
                branch brc
                on
                    j.branch = brc.kode_branch
            right join
                pesanan p
                on
                    j.pesanan_kode = p.kode_pesanan
            right join
                meja m
                on
                    m.id = p.meja_id
            where
                j.kode_faktur = '".trim($kode_faktur)."' and
                j.mstatus = 1
        ";
        $d_jual = $m_jual->hydrateRaw( $sql );

        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            $total = 0;
            $diskon = !empty($d_jual[0]['diskon']) ? $d_jual[0]['diskon'] : 0;
            $grand_total = 0;
            $service_charge = 0;
            $ppn = 0;
            $service_charge_include = 0;
            $ppn_include = 0;
            $include = 0;
            $exclude = 0;

            $m_juali = new \Model\Storage\JualItem_model();
            $sql_juali = "
                select ji.*, jp.nama as jp_nama, jp.kode as jp_kode from jual_item ji
                right join
                    jenis_pesanan jp
                    on
                        ji.kode_jenis_pesanan = jp.kode
                where
                    ji.faktur_kode = '".$d_jual[0]['kode_faktur']."'
            ";
            $d_juali = $m_juali->hydrateRaw( $sql_juali );
            if ( $d_juali->count() > 0 ) {
                $d_juali = $d_juali->toArray();

                $detail[ $d_jual[0]['kode_faktur'] ]['kode'] = $d_jual[0]['kode_faktur'];
                $detail[ $d_jual[0]['kode_faktur'] ]['member'] = $d_jual[0]['member'];
                $detail[ $d_jual[0]['kode_faktur'] ]['kode_member'] = $d_jual[0]['kode_member'];
                foreach ($d_juali as $k_ji => $v_ji) {
                    $key = $v_ji['jp_nama'].' | '.$v_ji['jp_kode'];
                    $key_item = $v_ji['menu_nama'].' | '.$v_ji['menu_kode'];

                    $m_jp = new \Model\Storage\JenisPesanan_model();
                    $d_jp = $m_jp->where('kode', $v_ji['jp_kode'])->first();

                    $include = $d_jp->include;
                    $exclude = $d_jp->exclude;

                    $m_hm = new \Model\Storage\HargaMenu_model();
                    $d_hm = $m_hm->where('menu_kode', $v_ji['menu_kode'])->where('jenis_pesanan_kode', $v_ji['kode_jenis_pesanan'])->orderBy('id', 'desc')->first();

                    if ( !isset($detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]) ) {
                        $jual_item = null;
                        $jual_item[ $key_item ] = array(
                            'nama' => $v_ji['menu_nama'],
                            'jumlah' => $v_ji['jumlah'],
                            'total' => $v_ji['total'],
                            'total_show' => $v_ji['total']
                        );

                        $service_charge += ($exclude == 1) ? $v_ji['service_charge'] : 0;
                        $ppn += ($exclude == 1) ? $v_ji['ppn'] : 0;
                        $total += $v_ji['total'];
                        $service_charge_include += $v_ji['service_charge'];
                        $ppn_include += $v_ji['ppn'];

                        $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key] = array(
                            'nama' => $v_ji['jp_nama'],
                            'jual_item' => $jual_item
                        );
                    } else {
                        if ( !isset($detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]) ) {
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total'],
                                'total_show' => $v_ji['total']
                            );

                            $service_charge += ($exclude == 1) ? $v_ji['service_charge'] : 0;
                            $ppn += ($exclude == 1) ? $v_ji['ppn'] : 0;
                            $total += $v_ji['total'];
                            $service_charge_include += $v_ji['service_charge'];
                            $ppn_include += $v_ji['ppn'];
                        } else {
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['jumlah'] += $v_ji['jumlah'];
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total'] += $v_ji['total'];
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total_show'] += $v_ji['total'];

                            $service_charge += ($exclude == 1) ? $v_ji['service_charge'] : 0;
                            $ppn += ($exclude == 1) ? $v_ji['ppn'] : 0;
                            $total += $v_ji['total'];
                            $service_charge_include += $v_ji['service_charge'];
                            $ppn_include += $v_ji['ppn'];
                        }
                    }
                }
            }

            $m_jual_gabungan = new \Model\Storage\JualGabungan_model();
            $sql_jg = "
                select jg.faktur_kode_gabungan, j.member, j.kode_member, j.total, j.diskon, j.grand_total, j.ppn, j.service_charge from jual_gabungan jg
                right join
                    (select * from jual where mstatus = 1) j
                    on
                        jg.faktur_kode_gabungan = j.kode_faktur
                where
                    jg.faktur_kode = '".$d_jual[0]['kode_faktur']."'
            ";
            $d_jual_gabungan = $m_jual->hydrateRaw( $sql_jg );

            if ( $d_jual_gabungan->count() > 0 ) {
                $d_jual_gabungan = $d_jual_gabungan->toArray();


                foreach ($d_jual_gabungan as $k_jg => $v_jg) {
                    $diskon += $v_jg['diskon'];

                    $m_jualig = new \Model\Storage\JualItem_model();
                    $sql_jualig = "
                        select ji.*, jp.nama as jp_nama, jp.kode as jp_kode from jual_item ji
                        right join
                            jenis_pesanan jp
                            on
                                ji.kode_jenis_pesanan = jp.kode
                        where
                            ji.faktur_kode = '".$v_jg['faktur_kode_gabungan']."'
                    ";
                    $d_jualig = $m_jualig->hydrateRaw( $sql_jualig );
                    if ( $d_jualig->count() > 0 ) {
                        $d_jualig = $d_jualig->toArray();

                        $detail[ $v_jg['faktur_kode_gabungan'] ]['kode'] = $v_jg['faktur_kode_gabungan'];
                        $detail[ $v_jg['faktur_kode_gabungan'] ]['member'] = $v_jg['member'];
                        $detail[ $v_jg['faktur_kode_gabungan'] ]['kode_member'] = $v_jg['kode_member'];
                        foreach ($d_jualig as $k_jig => $v_jig) {
                            $key = $v_jig['jp_nama'].' | '.$v_jig['jp_kode'];
                            $key_item = $v_jig['menu_nama'].' | '.$v_jig['menu_kode'];

                            $m_jp = new \Model\Storage\JenisPesanan_model();
                            $d_jp = $m_jp->where('kode', $v_ji['jp_kode'])->first();

                            $include = $d_jp->include;
                            $exclude = $d_jp->exclude;

                            $m_hm = new \Model\Storage\HargaMenu_model();
                            $d_hm = $m_hm->where('menu_kode', $v_ji['menu_kode'])->where('jenis_pesanan_kode', $v_ji['kode_jenis_pesanan'])->orderBy('id', 'desc')->first();

                            if ( !isset($detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]) ) {
                                $jual_item = null;
                                $jual_item[ $key_item ] = array(
                                    'nama' => $v_jig['menu_nama'],
                                    'jumlah' => $v_jig['jumlah'],
                                    'total' => $v_jig['total'],
                                    'total_show' => $v_jig['total']
                                );

                                $service_charge += ($exclude == 1) ? $v_jig['service_charge'] : 0;
                                $ppn += ($exclude == 1) ? $v_jig['ppn'] : 0;
                                $total += $v_jig['total'];
                                $service_charge_include += $v_jig['service_charge'];
                                $ppn_include += $v_jig['ppn'];

                                $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key] = array(
                                    'nama' => $v_jig['jp_nama'],
                                    'jual_item' => $jual_item
                                );
                            } else {
                                if ( !isset($detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]) ) {
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item] = array(
                                        'nama' => $v_jig['menu_nama'],
                                        'jumlah' => $v_jig['jumlah'],
                                        'total' => $v_jig['total'],
                                        'total_show' => $v_jig['total']
                                    );

                                    $service_charge += ($exclude == 1) ? $v_jig['service_charge'] : 0;
                                    $ppn += ($exclude == 1) ? $v_jig['ppn'] : 0;
                                    $total += $v_jig['total'];
                                    $service_charge_include += $v_jig['service_charge'];
                                    $ppn_include += $v_jig['ppn'];
                                } else {
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['jumlah'] += $v_jig['jumlah'];
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total'] += $v_jig['total'];
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total_show'] += $v_jig['total'];

                                    $service_charge += ($d_jp->include == 1) ? $v_jig['service_charge'] : 0;
                                    $ppn += ($d_jp->include == 1) ? $v_jig['ppn'] : 0;
                                    $total += $v_jig['total'];
                                    $service_charge_include += $v_jig['service_charge'];
                                    $ppn_include += $v_jig['ppn'];
                                }
                            }
                        }
                    }
                }
            }

            $grand_total = 0;
            if ( $include == 1 ) {
                $grand_total = $total-$diskon;
            }

            if ( $exclude == 1 ) {
                $grand_total = ($total + $ppn + $service_charge)-$diskon;
            }


            $data = array(
                'kode_faktur' => $d_jual[0]['kode_faktur'],
                'tgl_trans' => $d_jual[0]['tgl_trans'],
                'member' => $d_jual[0]['member'],
                'kode_member' => $d_jual[0]['kode_member'],
                'kode_branch' => $d_jual[0]['kode_branch'],
                'nama_branch' => $d_jual[0]['nama_branch'],
                'alamat_branch' => $d_jual[0]['alamat_branch'],
                'telp_branch' => $d_jual[0]['telp_branch'],
                'kasir' => $d_jual[0]['kasir'],
                'nama_kasir' => $d_jual[0]['nama_kasir'],
                'nama_meja' => $d_jual[0]['nama_meja'],
                'total' => $total,
                'diskon' => $diskon,
                'ppn' => $ppn,
                'service_charge' => $service_charge,
                'ppn_include' => $ppn_include,
                'service_charge_include' => $service_charge_include,
                'grand_total' => $grand_total,
                'lunas' => $d_jual[0]['lunas'],
                'jenis_bayar_include' => $include,
                'jenis_bayar_exclude' => $exclude,
                'detail' => $detail
            );
        }

        return $data;
    }

    public function getJenisKartu()
    {
        $m_jenis_kartu = new \Model\Storage\JenisKartu_model();
        $d_jenis_kartu = $m_jenis_kartu->where('status', 1)->orderBy('urut', 'asc')->get();

        $data = null;
        if ( $d_jenis_kartu->count() > 0 ) {
            $d_jenis_kartu = $d_jenis_kartu->toArray();

            // $data[] = array(
            //     'kode_jenis_kartu' => null,
            //     'nama' => 'TUNAI',
            //     'status' => 1
            // );

            // $data[] = array(
            //     'kode_jenis_kartu' => 'saldo_member',
            //     'nama' => 'SALDO MEMBER',
            //     'status' => 1
            // );

            foreach ($d_jenis_kartu as $key => $value) {
                $kode_jenis_kartu = $value['kode_jenis_kartu'];

                // if ( $value['nama'] == 'TUNAI' ) {
                //     $kode_jenis_kartu = null;
                // }

                // if ( $value['nama'] == 'SALDO MEMBER' ) {
                //     $kode_jenis_kartu = 'saldo_member';
                // }

                $data[] = array(
                    'kode_jenis_kartu' => $kode_jenis_kartu,
                    'kategori_jenis_kartu_id' => $value['kategori_jenis_kartu_id'],
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

        $date = '2023-01-19 00:00:01';

        $data = null;
        if ( !empty($d_jual['kode_member']) ) {
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual_hutang = $m_jual->where('tgl_trans', '>=', $date)->where('kode_member', $d_jual['kode_member'])->where('kode_faktur', '<>', $kode_faktur)->where('hutang', 1)->where('mstatus', 1)->with(['pesanan'])->get();

            if ( $d_jual_hutang->count() > 0 ) {
                $d_jual_hutang = $d_jual_hutang->toArray();

                foreach ($d_jual_hutang as $key => $value) {
                    $sql = "select sum(bayar) as total_bayar from bayar_hutang bh 
                        left join
                            (
                                select * from bayar where mstatus = 1
                            ) b 
                            on
                                bh.id_header = b.id
                        where
                            b.mstatus = 1 and
                            bh.faktur_kode = '".$value['kode_faktur']."'
                    ";

                    $m_bayar_hutang = new \Model\Storage\BayarHutang_model();
                    $d_bayar_hutang = $m_bayar_hutang->hydrateRaw($sql);

                    $total_bayar = 0;
                    if ( $d_bayar_hutang->count() > 0 ) {
                        $total_bayar = $d_bayar_hutang->toArray()[0]['total_bayar'];
                    }

                    if ( $value['grand_total'] > $total_bayar ) {
                        $data[] = array(
                            'tgl_pesan' => !empty($value['pesanan']) ? $value['pesanan']['tgl_pesan'] : $value['tgl_trans'],
                            'faktur_kode' => $value['kode_faktur'],
                            'hutang' => $value['grand_total'],
                            'bayar' => $total_bayar
                        );
                    }
                }
            }
        }

        return $data;
    }

    public function modalMetodePembayaran()
    {
        $kode_faktur = $this->input->get('kode_faktur');
        $params = $this->input->get('params');
        
        $saldo_member = 0;
        if ( !empty($params['member_kode']) ) {
            if ( empty($params['faktur_kode']) ) {
                $m_sm = new \Model\Storage\SaldoMember_model();
                $saldo_member = $m_sm->where('member_kode', $params['member_kode'])->where('sisa_saldo', '>', 0)->where('status', 1)->sum('sisa_saldo');
            } else {
                $m_bayar = new \Model\Storage\Bayar_model();
                $d_bayar = $m_bayar->where('faktur_kode', $params['faktur_kode'])->where('mstatus', 1)->first();

                if ( $d_bayar ) {
                    $m_smt = new \Model\Storage\SaldoMemberTrans_model();
                    $saldo_member += $m_smt->where('id_trans', $d_bayar->id)->where('tbl_name', $m_bayar->getTable())->sum('nominal');

                    $m_sm = new \Model\Storage\SaldoMember_model();
                    $saldo_member += $m_sm->where('member_kode', $params['member_kode'])->where('sisa_saldo', '>', 0)->where('status', 1)->sum('sisa_saldo');
                }
            }
        }

        $hutang = $params['hutang'];

        $data_metode_bayar[] = array(
            'nama' => $params['nama'],
            'kode_jenis_kartu' => $params['kode_jenis_kartu'],
            'no_kartu' => null,
            'nama_kartu' => null,
            'jumlah' => 0
        );

        $_data_diskon = null;
        if ( isset($params['data_diskon']) && !empty($params['data_diskon']) ) {
            $_data_diskon = $params['data_diskon'];
        }

        $data_diskon = $this->hitDiskon($kode_faktur, $data_metode_bayar, $_data_diskon);

        $sisa_tagihan = 0;
        if ( $data_diskon['jenis_harga_exclude'] == 1 ) {
            $sisa_tagihan = ($data_diskon['total_belanja'] + $data_diskon['total_ppn'] + $data_diskon['total_service_charge']) - $params['total_bayar'];
        } 
        if ( $data_diskon['jenis_harga_include'] == 1 ) {
            $sisa_tagihan = $data_diskon['total_belanja'] - $params['total_bayar'];
        }

        $sisa_tagihan = $sisa_tagihan + $hutang;

        $content['sisa_tagihan'] = ($sisa_tagihan > 0) ? $sisa_tagihan : 0;
        $content['kode_faktur'] = $kode_faktur;
        $content['saldo_member'] = $saldo_member;
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
            $d_bayar = $m_bayar->where('id', $params['id'])->where('mstatus', 1)->first();
            if ( $d_bayar ) {
                $m_bayar->where('id', $params['id'])->where('mstatus', 1)->update(
                    array(
                        'mstatus' => 0
                    )
                );

                $m_smt = new \Model\Storage\SaldoMemberTrans_model();
                $d_smt = $m_smt->where('id_trans', $d_bayar->id)->where('tbl_name', $m_bayar->getTable())->get();

                if ( $d_smt->count() > 0 ) {
                    $d_smt = $d_smt->toArray();

                    foreach ($d_smt as $k_smt => $v_smt) {
                        $m_sm = new \Model\Storage\SaldoMember_model();
                        $d_sm = $m_sm->where('id', $v_smt['id_header'])->first();

                        if ( $d_sm ) {
                            $m_sm = new \Model\Storage\SaldoMember_model();
                            $m_sm->where('id', $v_smt['id_header'])->update(
                                array(
                                    'sisa_saldo' => ($d_sm->sisa_saldo + $v_smt['nominal'])
                                )
                            );
                        }

                        $m_smt = new \Model\Storage\SaldoMemberTrans_model();
                        $m_smt->where('id_header', $v_smt['id_header'])->where('nominal', $v_smt['nominal'])->where('id_trans', $v_smt['id_trans'])->where('tbl_name', $v_smt['tbl_name'])->delete();
                    }
                }
            }

            $m_bayar = new \Model\Storage\Bayar_model();
            $now = $m_bayar->getDate();

            $m_bayar->tgl_trans = $now['waktu'];
            $m_bayar->faktur_kode = (isset($params['faktur_kode']) && !empty($params['faktur_kode'])) ? $params['faktur_kode'] : null;
            $m_bayar->jml_tagihan = $params['jml_tagihan'];
            $m_bayar->jml_bayar = $params['jml_bayar'];
            $m_bayar->ppn = $params['ppn'];
            $m_bayar->service_charge = $params['service_charge'];
            $m_bayar->diskon = $params['diskon'];
            $m_bayar->total = $params['tot_belanja'];
            $m_bayar->member_kode = $params['member_kode'];
            $m_bayar->member = $params['member'];
            $m_bayar->kasir = $this->userid;
            $m_bayar->nama_kasir = $this->userdata['detail_user']['nama_detuser'];
            $m_bayar->mstatus = 1;
            $m_bayar->save();

            $id_header = $m_bayar->id;

            foreach ($params['dataMetodeBayar'] as $key => $value) {
                if ( !empty($value) ) {
                    $m_bayard = new \Model\Storage\BayarDet_model();
                    $m_bayard->id_header = $id_header;
                    $m_bayard->jenis_bayar = $value['nama'];
                    $m_bayard->kode_jenis_kartu = $value['kode_jenis_kartu'];
                    $m_bayard->nominal = $value['jumlah'];
                    $m_bayard->no_kartu = isset($value['no_kartu']) ? $value['no_kartu'] : null;
                    $m_bayard->nama_kartu = isset($value['nama_kartu']) ? $value['nama_kartu'] : null;
                    $m_bayard->save();

                    if ( $value['kode_jenis_kartu'] == 'saldo_member' ) {
                        $nominal = $value['jumlah'];

                        while ( $nominal > 0 ) {
                            $m_sm = new \Model\Storage\SaldoMember_model();
                            $d_sm = $m_sm->where('member_kode', $params['member_kode'])->where('sisa_saldo', '>', 0)->where('status', 1)->orderBy('id', 'asc')->first();

                            if ( $d_sm ) {
                                $sisa_saldo = $d_sm->sisa_saldo;

                                if ( $sisa_saldo > $nominal ) {
                                    $m_smt = new \Model\Storage\SaldoMemberTrans_model();
                                    $m_smt->id_header = $d_sm->id;
                                    $m_smt->nominal = $nominal;
                                    $m_smt->id_trans = $m_bayar->id;
                                    $m_smt->tbl_name = $m_bayar->getTable();
                                    $m_smt->save();

                                    $sisa_saldo -= $nominal;
                                    $nominal = 0;
                                } else {
                                    $m_smt = new \Model\Storage\SaldoMemberTrans_model();
                                    $m_smt->id_header = $d_sm->id;
                                    $m_smt->nominal = $sisa_saldo;
                                    $m_smt->id_trans = $m_bayar->id;
                                    $m_smt->tbl_name = $m_bayar->getTable();
                                    $m_smt->save();

                                    $nominal -= $sisa_saldo;
                                    $sisa_saldo = 0;
                                }

                                $m_sm = new \Model\Storage\SaldoMember_model();
                                $m_sm->where('id', $d_sm->id)->update(
                                    array(
                                        'sisa_saldo' => $sisa_saldo
                                    )
                                );
                            } else {
                                $nominal = 0;
                            }
                        }
                    }
                }
            }

            if ( isset($params['dataDiskon']) && !empty($params['dataDiskon']) ) {
                foreach ($params['dataDiskon'] as $key => $value) {
                    if ( !empty($value) ) {
                        $m_bayard = new \Model\Storage\BayarDiskon_model();
                        $m_bayard->id_header = $id_header;
                        $m_bayard->diskon_kode = $value['kode'];
                        $m_bayard->nilai = $value['nominal'];
                        $m_bayard->save();
                    }
                }
            }

            if ( !empty($params['dataHutangBayar']) ) {
                foreach ($params['dataHutangBayar'] as $key => $value) {
                    $m_bayarh = new \Model\Storage\BayarHutang_model();
                    $m_bayarh->id_header = $id_header;
                    $m_bayarh->faktur_kode = $value['faktur_kode'];
                    $m_bayarh->hutang = $value['hutang'];
                    $m_bayarh->sudah_bayar = (isset($value['sudah_bayar']) && !empty($value['sudah_bayar']) && $value['sudah_bayar'] > 0) ? $value['sudah_bayar'] : 0;
                    $m_bayarh->bayar = $value['bayar'];
                    $m_bayarh->save();

                    if ( $value['bayar'] >= $value['hutang'] ) {
                        $m_jual = new \Model\Storage\Jual_model();
                        $m_jual->where('kode_faktur', $value['faktur_kode'])->update(
                            array(
                                'lunas' => 1
                            )
                        );
                    } else {
                        $m_jual = new \Model\Storage\Jual_model();
                        $m_jual->where('kode_faktur', $value['faktur_kode'])->update(
                            array(
                                'lunas' => 0
                            )
                        );
                    }
                }
            }

            if ( isset($params['faktur_kode']) && !empty($params['faktur_kode']) ) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('kode_faktur', $params['faktur_kode'])->first();

                $m_pesanan = new \Model\Storage\Pesanan_model();
                $d_pesanan = $m_pesanan->where('kode_pesanan', $d_jual->pesanan_kode)->first();

                $m_jual = new \Model\Storage\Jual_model();
                if ( $params['jml_bayar'] >= $params['jml_tagihan'] ) {
                    $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                        array(
                            'lunas' => 1,
                            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
                            'kasir' => $this->userid
                        )
                    );
                } else {
                    $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                        array(
                            'lunas' => 0,
                            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
                            'kasir' => $this->userid
                        )
                    );
                }

                $m_jualg = new \Model\Storage\JualGabungan_model();
                $d_jualg = $m_jualg->select('faktur_kode_gabungan')->where('faktur_kode', $params['faktur_kode'])->get();
                if ( $d_jualg->count() > 0 ) {
                    $d_jualg = $d_jualg->toArray();

                    $m_jual = new \Model\Storage\Jual_model();
                    $m_jual->whereIn('kode_faktur', $d_jualg)->update(
                        array(
                            'lunas' => 1,
                            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
                            'kasir' => $this->userid
                        )
                    );
                }

                $m_mejal = new \Model\Storage\MejaLog_model();
                $m_mejal->where('pesanan_kode', $d_pesanan->kode_pesanan)->where('meja_id', $d_pesanan->meja_id)->where('status', 1)->update(
                    array('status' => 0)
                );
            }

            $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_bayar, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['content'] = array('id_bayar' => $id_header);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function getDataPenjualanAfterSave($kode_faktur, $id_bayar)
    {
        $data = null;
        $detail = null;

        $m_jual = new \Model\Storage\Jual_model();
        $sql = "
            select 
                j.kode_faktur,
                j.tgl_trans,
                j.branch as kode_branch,
                brc.nama as nama_branch,
                brc.alamat as alamat_branch,
                brc.telp as telp_branch,
                j.nama_kasir,
                j.member,
                j.kode_member,
                j.total,
                b.diskon,
                b.tgl_trans as tgl_bayar,
                b.jml_bayar,
                b.hutang,
                b.bayar as bayar_hutang,
                j.grand_total,
                j.lunas,
                j.ppn,
                j.service_charge,
                m.nama_meja,
                j.print_nota
            from jual j
            left join
                (
                    select 
                        b1.*, sum(bh.bayar) as hutang, sum(bh.bayar) as bayar
                    from bayar b1
                    left join
                        bayar_hutang bh
                        on
                            b1.id = bh.id_header
                    where 
                        b1.mstatus = 1
                    group by
                        b1.id, 
                        b1.tgl_trans,
                        b1.faktur_kode,
                        b1.jml_tagihan,
                        b1.jml_bayar,
                        b1.jenis_bayar,
                        b1.jenis_kartu_kode,
                        b1.no_bukti,
                        b1.kode_bayar_non_kasir,
                        b1.mstatus,
                        b1.ppn,
                        b1.service_charge,
                        b1.diskon,
                        b1.total,
                        b1.member_kode,
                        b1.member,
                        b1.kasir,
                        b1.nama_kasir
                ) b
                on
                    j.kode_faktur = b.faktur_kode
            right join
                branch brc
                on
                    j.branch = j.branch
            right join
                pesanan p
                on
                    j.pesanan_kode = p.kode_pesanan
            right join
                meja m
                on
                    m.id = p.meja_id
            where
                j.kode_faktur = '".trim($kode_faktur)."' and
                j.mstatus = 1
        ";
        $d_jual = $m_jual->hydrateRaw( $sql );

        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            $total = 0;
            $diskon = !empty($d_jual[0]['diskon']) ? $d_jual[0]['diskon'] : 0;
            $grand_total = 0;
            $service_charge = 0;
            $ppn = 0;
            $service_charge_include = 0;
            $ppn_include = 0;
            $include = 0;
            $exclude = 0;

            $m_juali = new \Model\Storage\JualItem_model();
            $sql_juali = "
                select ji.*, jp.nama as jp_nama, jp.kode as jp_kode from jual_item ji
                right join
                    jenis_pesanan jp
                    on
                        ji.kode_jenis_pesanan = jp.kode
                where
                    ji.faktur_kode = '".$d_jual[0]['kode_faktur']."'
            ";
            $d_juali = $m_juali->hydrateRaw( $sql_juali );
            if ( $d_juali->count() > 0 ) {
                $d_juali = $d_juali->toArray();

                $detail[ $d_jual[0]['kode_faktur'] ]['kode'] = $d_jual[0]['kode_faktur'];
                $detail[ $d_jual[0]['kode_faktur'] ]['member'] = $d_jual[0]['member'];
                $detail[ $d_jual[0]['kode_faktur'] ]['kode_member'] = $d_jual[0]['kode_member'];
                foreach ($d_juali as $k_ji => $v_ji) {
                    $key = $v_ji['jp_nama'].' | '.$v_ji['jp_kode'];
                    $key_item = $v_ji['menu_nama'].' | '.$v_ji['menu_kode'];

                    $m_jp = new \Model\Storage\JenisPesanan_model();
                    $d_jp = $m_jp->where('kode', $v_ji['jp_kode'])->first();

                    $include = $d_jp->include;
                    $exclude = $d_jp->exclude;

                    $m_hm = new \Model\Storage\HargaMenu_model();
                    $d_hm = $m_hm->where('menu_kode', $v_ji['menu_kode'])->where('jenis_pesanan_kode', $v_ji['kode_jenis_pesanan'])->orderBy('id', 'desc')->first();

                    if ( !isset($detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]) ) {
                        $jual_item = null;
                        $jual_item[ $key_item ] = array(
                            'nama' => $v_ji['menu_nama'],
                            'jumlah' => $v_ji['jumlah'],
                            'total' => $v_ji['total'],
                            'total_show' => $v_ji['total']
                        );

                        $service_charge += ($d_jp->exclude == 1) ? $v_ji['service_charge'] : 0;
                        $ppn += ($d_jp->exclude == 1) ? $v_ji['ppn'] : 0;
                        $total += $v_ji['total'];
                        $service_charge_include += $v_ji['service_charge'];
                        $ppn_include += $v_ji['ppn'];

                        $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key] = array(
                            'nama' => $v_ji['jp_nama'],
                            'jual_item' => $jual_item
                        );
                    } else {
                        if ( !isset($detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]) ) {
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total'],
                                'total_show' => $v_ji['total']
                            );

                            $service_charge += ($d_jp->exclude == 1) ? $v_ji['service_charge'] : 0;
                            $ppn += ($d_jp->exclude == 1) ? $v_ji['ppn'] : 0;
                            $total += $v_ji['total'];
                            $service_charge_include += $v_ji['service_charge'];
                            $ppn_include += $v_ji['ppn'];
                        } else {
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['jumlah'] += $v_ji['jumlah'];
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total'] += $v_ji['total'];
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total_show'] += $v_ji['total'];

                            $service_charge += ($d_jp->exclude == 1) ? $v_ji['service_charge'] : 0;
                            $ppn += ($d_jp->exclude == 1) ? $v_ji['ppn'] : 0;
                            $total += $v_ji['total'];
                            $service_charge_include += $v_ji['service_charge'];
                            $ppn_include += $v_ji['ppn'];
                        }
                    }
                }
            }

            $m_jual_gabungan = new \Model\Storage\JualGabungan_model();
            $sql_jg = "
                select jg.faktur_kode_gabungan, j.member, j.kode_member, j.total, j.diskon, j.grand_total, j.ppn, j.service_charge from jual_gabungan jg
                right join
                    (select * from jual where mstatus = 1) j
                    on
                        jg.faktur_kode_gabungan = j.kode_faktur
                where
                    jg.faktur_kode = '".$d_jual[0]['kode_faktur']."'
            ";
            $d_jual_gabungan = $m_jual->hydrateRaw( $sql_jg );

            if ( $d_jual_gabungan->count() > 0 ) {
                $d_jual_gabungan = $d_jual_gabungan->toArray();


                foreach ($d_jual_gabungan as $k_jg => $v_jg) {
                    $diskon += $v_jg['diskon'];

                    $m_jualig = new \Model\Storage\JualItem_model();
                    $sql_jualig = "
                        select ji.*, jp.nama as jp_nama, jp.kode as jp_kode from jual_item ji
                        right join
                            jenis_pesanan jp
                            on
                                ji.kode_jenis_pesanan = jp.kode
                        where
                            ji.faktur_kode = '".$v_jg['faktur_kode_gabungan']."'
                    ";
                    $d_jualig = $m_jualig->hydrateRaw( $sql_jualig );
                    if ( $d_jualig->count() > 0 ) {
                        $d_jualig = $d_jualig->toArray();

                        $detail[ $v_jg['faktur_kode_gabungan'] ]['kode'] = $v_jg['faktur_kode_gabungan'];
                        $detail[ $v_jg['faktur_kode_gabungan'] ]['member'] = $v_jg['member'];
                        $detail[ $v_jg['faktur_kode_gabungan'] ]['kode_member'] = $v_jg['kode_member'];
                        foreach ($d_jualig as $k_jig => $v_jig) {
                            $key = $v_jig['jp_nama'].' | '.$v_jig['jp_kode'];
                            $key_item = $v_jig['menu_nama'].' | '.$v_jig['menu_kode'];

                            $m_jp = new \Model\Storage\JenisPesanan_model();
                            $d_jp = $m_jp->where('kode', $v_ji['jp_kode'])->first();

                            $include = $d_jp->include;
                            $exclude = $d_jp->exclude;

                            $m_hm = new \Model\Storage\HargaMenu_model();
                            $d_hm = $m_hm->where('menu_kode', $v_ji['menu_kode'])->where('jenis_pesanan_kode', $v_ji['kode_jenis_pesanan'])->orderBy('id', 'desc')->first();

                            if ( !isset($detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]) ) {
                                $jual_item = null;
                                $jual_item[ $key_item ] = array(
                                    'nama' => $v_jig['menu_nama'],
                                    'jumlah' => $v_jig['jumlah'],
                                    'total' => $v_jig['total'],
                                    'total_show' => $v_jig['total']
                                );

                                $service_charge += ($d_jp->exclude == 1) ? $v_jig['service_charge'] : 0;
                                $ppn += ($d_jp->exclude == 1) ? $v_jig['ppn'] : 0;
                                $total += $v_jig['total'];
                                $service_charge_include += $v_jig['service_charge'];
                                $ppn_include += $v_jig['ppn'];

                                $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key] = array(
                                    'nama' => $v_jig['jp_nama'],
                                    'jual_item' => $jual_item
                                );
                            } else {
                                if ( !isset($detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]) ) {
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item] = array(
                                        'nama' => $v_jig['menu_nama'],
                                        'jumlah' => $v_jig['jumlah'],
                                        'total' => $v_jig['total'],
                                        'total_show' => $v_jig['total']
                                    );

                                    $service_charge += ($d_jp->exclude == 1) ? $v_jig['service_charge'] : 0;
                                    $ppn += ($d_jp->exclude == 1) ? $v_jig['ppn'] : 0;
                                    $total += $v_jig['total'];
                                    $service_charge_include += $v_jig['service_charge'];
                                    $ppn_include += $v_jig['ppn'];
                                } else {
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['jumlah'] += $v_jig['jumlah'];
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total'] += $v_jig['total'];
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total_show'] += $v_jig['total'];

                                    $service_charge += ($d_jp->exclude == 1) ? $v_jig['service_charge'] : 0;
                                    $ppn += ($d_jp->exclude == 1) ? $v_jig['ppn'] : 0;
                                    $total += $v_jig['total'];
                                    $service_charge_include += $v_jig['service_charge'];
                                    $ppn_include += $v_jig['ppn'];
                                }
                            }
                        }
                    }
                }
            }

            $m_bayar = new \Model\Storage\Bayar_model();
            $sql = "
                select
                    kjk.id,
                    kjk.nama,
                    data.jumlah
                from
                    kategori_jenis_kartu kjk 
                left join
                    (
                        select 
                            jk.kategori_jenis_kartu_id,
                            sum(bd.nominal) as jumlah
                        from bayar b 
                        right join
                            bayar_det bd 
                            on
                                b.id = bd.id_header 
                        right join  
                            jenis_kartu jk 
                            on
                                jk.kode_jenis_kartu = bd.kode_jenis_kartu 
                        where
                            b.faktur_kode = '".trim($kode_faktur)."' and
                            b.mstatus = 1
                        group by
                            jk.kategori_jenis_kartu_id
                    ) data
                    on
                        kjk.id = data.kategori_jenis_kartu_id
            ";
            $d_kjk = $m_bayar->hydrateRaw( $sql );
            if ( $d_kjk->count() > 0 ) {
                $d_kjk = $d_kjk->toArray();
            }

            $d_jb = null;
            $d_bayar_hutang = null;
            if ( !empty($id_bayar) ) {
                $m_bayar = new \Model\Storage\Bayar_model();
                $sql = "
                    select bd.jenis_bayar, bd.kode_jenis_kartu, sum(bd.nominal) as jumlah from bayar b 
                    right join
                        bayar_det bd 
                        on
                            b.id = bd.id_header 
                    where
                        b.id = ".$id_bayar."
                    group by
                        bd.jenis_bayar, 
                        bd.kode_jenis_kartu
                ";
                $d_jb = $m_bayar->hydrateRaw( $sql );
                if ( $d_jb->count() > 0 ) {
                    $d_jb = $d_jb->toArray();
                }

                $m_bayar = new \Model\Storage\Bayar_model();
                $sql = "
                    select sum(bd.nominal) as nominal from bayar b 
                    right join
                        bayar_det bd 
                        on
                            b.id = bd.id_header 
                    where
                        b.id = ".$id_bayar."
                    group by
                        bd.id_header
                ";
                $d_bayar_hutang = $m_bayar->hydrateRaw( $sql );
                if ( $d_bayar_hutang->count() > 0 ) {
                    $d_bayar_hutang = $d_bayar_hutang->toArray();
                }
            }

            $data = array(
                'kode_faktur' => $d_jual[0]['kode_faktur'],
                'tgl_trans' => $d_jual[0]['tgl_trans'],
                'tgl_bayar' => $d_jual[0]['tgl_bayar'],
                'nama_kasir' => $d_jual[0]['nama_kasir'],
                'member' => $d_jual[0]['member'],
                'kode_member' => $d_jual[0]['kode_member'],
                'kode_branch' => $d_jual[0]['kode_branch'],
                'nama_branch' => $d_jual[0]['nama_branch'],
                'alamat_branch' => $d_jual[0]['alamat_branch'],
                'telp_branch' => $d_jual[0]['telp_branch'],
                'nama_meja' => $d_jual[0]['nama_meja'],
                'print_nota' => $d_jual[0]['print_nota'],
                'total' => $total,
                'diskon' => $diskon,
                'ppn' => $ppn,
                'service_charge' => $service_charge,
                'ppn_include' => $ppn_include,
                'service_charge_include' => $service_charge_include,
                'grand_total' => ($total + $ppn + $service_charge + $d_jual[0]['hutang'])-$diskon,
                'jml_bayar' => $d_jual[0]['jml_bayar'],
                'hutang' => $d_jual[0]['hutang'],
                'bayar_hutang' => $d_bayar_hutang[0]['nominal'],
                'lunas' => $d_jual[0]['lunas'],
                'kategori_jenis_kartu' => $d_kjk,
                'jenis_bayar' => $d_jb,
                'jenis_bayar_include' => $include,
                'jenis_bayar_exclude' => $exclude,
                'detail' => $detail
            );
        }

        return $data;
    }

    public function printNota()
    {
        $params = $this->input->post('params');

        try {
            $jenis = isset($params['jenis']) ? $params['jenis'] : null;
            $data = $this->getDataPenjualanAfterSave( $params['faktur_kode'], $params['id_bayar'] );

            function buatBaris3Kolom($kolom1, $kolom2, $kolom3, $jenis) {
                // Mengatur lebar setiap kolom (dalam satuan karakter)
                if ( $jenis == 'header' ) {
                    $lebar_kolom_1 = 10;
                    $lebar_kolom_2 = 3;
                    $lebar_kolom_3 = 33;
                }
                if ( $jenis == 'center' ) {
                    $lebar_kolom_1 = 6;
                    $lebar_kolom_2 = 30;
                    $lebar_kolom_3 = 10;
                }
                if ( $jenis == 'footer' ) {
                    $lebar_kolom_1 = 33;
                    $lebar_kolom_2 = 3;
                    $lebar_kolom_3 = 10;
                }
     
                // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
                $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
                $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
                $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);
     
                // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
                $kolom1Array = explode("\n", $kolom1);
                $kolom2Array = explode("\n", $kolom2);
                $kolom3Array = explode("\n", $kolom3);
     
                // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
                $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array));
     
                // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
                $hasilBaris = array();
     
                // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
                for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {
                    if ( $jenis == 'header' ) {
                        // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                        $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                        $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ");
                    }
                    if ( $jenis == 'center' ) {
                        // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                        $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                        $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
                    }
                    if ( $jenis == 'footer' ) {
                        // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                        $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ", STR_PAD_LEFT);
                        $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
                    }
     
                    // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                    $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                }
     
                // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                return implode($hasilBaris, "\n") . "\n";
            }

            $m_ps = new \Model\Storage\PrinterStation_model();
            $d_ps = $m_ps->where('nama', 'CASHIER')->first();

            $m_printer = new \Model\Storage\Printer_model();
            $d_printer = $m_printer->where('printer_station_id', $d_ps->id)->where('branch_kode', $this->kodebranch)->where('status', 1)->first();

            // Enter the share name for your USB printer here
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector($d_printer->sharing_name);
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            // $printer -> text('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');

            $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
            // $printer -> selectPrintMode(32);
            // $printer -> setTextSize(2, 1);
            $printer -> text($data['nama_branch']."\n");

            $printer -> initialize();
            $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer -> text($data['alamat_branch']."\n");
            $printer -> text('Telp.'. $data['telp_branch']."\n");
            $printer -> text("\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $kode_faktur = $data['kode_faktur'];
            if ( stristr($jenis, 'hutang') !== false ) {
                $kode_faktur = $data['kode_faktur'].' (CL)';
            }

            $printer -> text(buatBaris3Kolom('No. Bill', ':', $kode_faktur, 'header'));
            $printer -> text(buatBaris3Kolom('Kasir', ':', $data['nama_kasir'], 'header'));
            $printer -> text(buatBaris3Kolom('Tanggal', ':', substr($data['tgl_trans'], 0, 19), 'header'));

            $printer -> text('------------------------------------------------'."\n");

            $jml_member = 1;
            foreach ($data['detail'] as $k_det => $v_det) {
                if ( $jml_member > 1 ) {
                    $printer -> text("\n");
                }

                $printer -> text(buatBaris3Kolom('Member', ':', $v_det['member'], 'header'));
                $printer -> text('------------------------------------------------'."\n");

                $printer -> initialize();
                foreach ($v_det['jenis_pesanan'] as $k_jp => $v_jp) {
                    $printer -> text($v_jp['nama']."\n");
                    foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                        $printer -> text(buatBaris3Kolom($v_ji['jumlah'].'X', $v_ji['nama'], angkaRibuan($v_ji['total_show']), 'center'));
                    }
                }

                $jml_member++;
            }

            if ( stristr($jenis, 'hutang') === false ) {
                $printer -> text('------------------------------------------------'."\n");
                $printer -> text(buatBaris3Kolom('Total Belanja.', '=', angkaRibuan($data['total']), 'footer'));
                $printer -> text(buatBaris3Kolom('Disc.', '=', '('.angkaRibuan($data['diskon']).')', 'footer'));
                $printer -> text(buatBaris3Kolom('Service Charge.', '=', angkaRibuan($data['service_charge']), 'footer'));
                $printer -> text(buatBaris3Kolom('PB1.', '=', angkaRibuan($data['ppn']), 'footer'));
                $printer -> text(buatBaris3Kolom('CL.', '=', angkaRibuan($data['hutang']), 'footer'));
                $printer -> text(buatBaris3Kolom('Total Bayar.', '=', angkaRibuan($data['grand_total']), 'footer'));
                $printer -> text(buatBaris3Kolom('Jumlah Bayar.', '=', angkaRibuan($data['jml_bayar']), 'footer'));
                $kembalian = (($data['jml_bayar'] - $data['grand_total']) > 0) ? $data['jml_bayar'] - $data['grand_total'] : 0;
                $printer -> text(buatBaris3Kolom('Kembalian.', '=', angkaRibuan($kembalian), 'footer'));
                $printer -> text(buatBaris3Kolom('', '', '----------', 'footer'));
                foreach ($data['kategori_jenis_kartu'] as $k_kjk => $v_kjk) {
                    $printer -> text(buatBaris3Kolom(ucfirst($v_kjk['nama']).'.', '=', angkaRibuan($v_kjk['jumlah']), 'footer'));
                }

                if ( $data['jenis_bayar_include'] == 1 ) {
                    $printer -> initialize();
                    $printer -> text("\n\n");
                    $printer -> text(buatBaris3Kolom('Price Include of Service Charge.', '=', angkaRibuan($data['service_charge_include']), 'footer'));
                    $printer -> text(buatBaris3Kolom('Price Include of PB1.', '=', angkaRibuan($data['ppn_include']), 'footer'));
                }
            } else {
                $printer -> text('------------------------------------------------'."\n");
                $printer -> text(buatBaris3Kolom('Total.', '=', angkaRibuan($data['grand_total']), 'footer'));
                $printer -> text(buatBaris3Kolom('Bayar.', '=', angkaRibuan($data['bayar_hutang']), 'footer'));
                $printer -> text('Pembayaran -------------------------------------'."\n");
                foreach ($data['jenis_bayar'] as $k_jb => $v_jb) {
                    $printer -> text($v_jb['jenis_bayar']."\n");                    
                }
            }
            $printer -> text('------------------------------------------------'."\n");
            $printer->setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer -> text("*** TERIMA KASIH ***");

            if ( $data['print_nota'] == 1 ) {
                $printer -> text("\n\n");
                $printer -> initialize();
                $printer -> selectPrintMode(32);
                $printer -> setTextSize(2, 1);
                $printer -> text("RE-PRINT BILL");
            }

            $printer -> feed(3);
            $printer -> cut();
            $printer -> close();

            $m_jual = new \Model\Storage\Jual_model();
            $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                array(
                    'print_nota' => 1
                )
            );

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function printDraft()
    {
        $params = $this->input->post('params');

        try {
            $data = $this->getDataPenjualanAfterSave( $params['faktur_kode'], null );

            function buatBaris3Kolom($kolom1, $kolom2, $kolom3, $jenis) {
                // Mengatur lebar setiap kolom (dalam satuan karakter)
                if ( $jenis == 'header' ) {
                    $lebar_kolom_1 = 10;
                    $lebar_kolom_2 = 3;
                    $lebar_kolom_3 = 33;
                }
                if ( $jenis == 'center' ) {
                    $lebar_kolom_1 = 6;
                    $lebar_kolom_2 = 30;
                    $lebar_kolom_3 = 10;
                }
                if ( $jenis == 'footer' ) {
                    $lebar_kolom_1 = 33;
                    $lebar_kolom_2 = 3;
                    $lebar_kolom_3 = 10;
                }
     
                // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
                $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
                $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
                $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);
     
                // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
                $kolom1Array = explode("\n", $kolom1);
                $kolom2Array = explode("\n", $kolom2);
                $kolom3Array = explode("\n", $kolom3);
     
                // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
                $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array));
     
                // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
                $hasilBaris = array();
     
                // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
                for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {
                    if ( $jenis == 'header' ) {
                        // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                        $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                        $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ");
                    }
                    if ( $jenis == 'center' ) {
                        // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                        $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                        $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
                    }
                    if ( $jenis == 'footer' ) {
                        // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                        $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ", STR_PAD_LEFT);
                        $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
                    }
     
                    // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                    $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                }
     
                // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                return implode($hasilBaris, "\n") . "\n";
            }

            $m_ps = new \Model\Storage\PrinterStation_model();
            $d_ps = $m_ps->where('nama', 'CASHIER')->first();

            $m_printer = new \Model\Storage\Printer_model();
            $d_printer = $m_printer->where('printer_station_id', $d_ps->id)->where('branch_kode', $this->kodebranch)->where('status', 1)->first();

            // Enter the share name for your USB printer here
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector($d_printer->sharing_name);
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer -> text($data['nama_branch']."\n");

            $printer -> initialize();
            $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer -> text($data['alamat_branch']."\n");
            $printer -> text('Telp.'. $data['telp_branch']."\n");
            $printer -> text("\n");

            $kode_faktur = $data['kode_faktur'];

            $printer -> initialize();
            $printer -> text(buatBaris3Kolom('No. Bill', ':', $kode_faktur, 'header'));
            $printer -> text(buatBaris3Kolom('Kasir', ':', $data['nama_kasir'], 'header'));
            $printer -> text(buatBaris3Kolom('Tanggal', ':', substr($data['tgl_trans'], 0, 19), 'header'));

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();
            $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text('DRAFT');

            $printer -> initialize();
            $printer -> text('------------------------------------------------'."\n");

            $jml_member = 1;
            foreach ($data['detail'] as $k_det => $v_det) {
                if ( $jml_member > 1 ) {
                    $printer -> text("\n");
                }

                $printer -> text(buatBaris3Kolom('Member', ':', $v_det['member'], 'header'));
                $printer -> text('------------------------------------------------'."\n");

                $printer -> initialize();
                foreach ($v_det['jenis_pesanan'] as $k_jp => $v_jp) {
                    $printer -> text($v_jp['nama']."\n");
                    foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                        $printer -> text(buatBaris3Kolom($v_ji['jumlah'].'X', $v_ji['nama'], angkaRibuan($v_ji['total_show']), 'center'));
                    }
                }

                $jml_member++;
            }

            $printer -> text('------------------------------------------------'."\n");
            $printer -> text(buatBaris3Kolom('Total Belanja.', '=', angkaRibuan($data['total']), 'footer'));
            $printer -> text(buatBaris3Kolom('Disc.', '=', '('.angkaRibuan($data['diskon']).')', 'footer'));
            $printer -> text(buatBaris3Kolom('Service Charge.', '=', angkaRibuan($data['service_charge']), 'footer'));
            $printer -> text(buatBaris3Kolom('PB1.', '=', angkaRibuan($data['ppn']), 'footer'));
            // $printer -> text(buatBaris3Kolom('CL.', '=', angkaRibuan($data['hutang']), 'footer'));
            $printer -> text(buatBaris3Kolom('Total Bayar.', '=', angkaRibuan($data['grand_total']), 'footer'));
            // $printer -> text(buatBaris3Kolom('Jumlah Bayar.', '=', angkaRibuan($data['jml_bayar']), 'footer'));
            // $kembalian = (($data['jml_bayar'] - $data['grand_total']) > 0) ? $data['jml_bayar'] - $data['grand_total'] : 0;
            // $printer -> text(buatBaris3Kolom('Kembalian.', '=', angkaRibuan($kembalian), 'footer'));
            // $printer -> text(buatBaris3Kolom('', '', '----------', 'footer'));
            // foreach ($data['kategori_jenis_kartu'] as $k_kjk => $v_kjk) {
            //     $printer -> text(buatBaris3Kolom(ucfirst($v_kjk['nama']).'.', '=', angkaRibuan($v_kjk['jumlah']), 'footer'));
            // }

            if ( $data['jenis_bayar_include'] == 1 ) {
                $printer -> initialize();
                $printer -> text("\n\n");
                $printer -> text(buatBaris3Kolom('Price Include of Service Charge.', '=', angkaRibuan($data['service_charge_include']), 'footer'));
                $printer -> text(buatBaris3Kolom('Price Include of PB1.', '=', angkaRibuan($data['ppn_include']), 'footer'));
            }

            $printer -> text('------------------------------------------------'."\n");
            // $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
            // $printer -> text("*** INI BUKAN BUKTI PEMBAYARAN ***");

            $printer -> feed(3);
            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function saveHutang()
    {
        $params = $this->input->post('params');

        try {
            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('faktur_kode', $params['faktur_kode'])->where('mstatus', 1)->first();
            if ( $d_bayar ) {
                $m_bayar->where('faktur_kode', $params['faktur_kode'])->where('mstatus', 1)->update(
                    array(
                        'mstatus' => 0
                    )
                );
            }

            $m_jual = new \Model\Storage\Jual_model();
            $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                array(
                    'hutang' => 1,
                    'remark' => $params['alasan'],
                    'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
                    'kasir' => $this->userid
                )
            );

            $d_jual = $m_jual->where('kode_faktur', $params['faktur_kode'])->first();

            $m_pesanan = new \Model\Storage\Pesanan_model();
            $d_pesanan = $m_pesanan->where('kode_pesanan', $d_jual->pesanan_kode)->first();

            $m_mejal = new \Model\Storage\MejaLog_model();
            $m_mejal->where('pesanan_kode', $d_pesanan->kode_pesanan)->where('meja_id', $d_pesanan->meja_id)->where('status', 1)->update(
                array('status' => 0)
            );

            $deskripsi_log_gaktifitas = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jual, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan sebagai hutang.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function pembayaranFormEdit($_kode_faktur)
    {
        $kode_faktur = exDecrypt( $_kode_faktur );

        $this->add_external_js(
            array(
                "assets/select2/js/select2.min.js",
                "assets/html2canvas/html2canvas.min.js",
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

        $content['akses'] = $this->hakAkses;
        $content['data'] = $this->getDataPenjualanAfterSave($kode_faktur, null);
        $content['data_bayar'] = $this->getDataPembayaran($kode_faktur);
        $content['data_hutang'] = $this->getDataHutangEdit($kode_faktur);
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['kategori_pembayaran'] = $this->getDataKategoriPembayaran($kode_faktur);
        $content['data_branch'] = array(
            'nama' => $this->namabranch,
            'alamat' => $this->alamatbranch,
            'telp' => $this->telpbranch,
            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
            'waktu' => $now['waktu']
        );

        // cetak_r($content['data_hutang']);

        $data['view'] = $this->load->view($this->pathView . 'pembayaran_form_edit', $content, TRUE);

        $this->load->view($this->template, $data);
    }

    public function getDataHutangEdit($kode_faktur)
    {
        $m_bayar = new \Model\Storage\Bayar_model();
        $d_bayar = $m_bayar->where('faktur_kode', $kode_faktur)->where('mstatus', 1)->first()->toArray();

        $sql = "
            select bd.jenis_bayar, bd.kode_jenis_kartu, sum(bd.nominal) as jumlah from bayar b 
            right join
                bayar_det bd 
                on
                    b.id = bd.id_header 
            where
                b.id = ".$d_bayar['id']."
            group by
                bd.jenis_bayar, 
                bd.kode_jenis_kartu
        ";
        $d_jb = $m_bayar->hydrateRaw( $sql );

        $jenis_bayar = null;
        if ( $d_jb->count() > 0 ) {
            $jenis_bayar = $d_jb->toArray();
        }

        $m_bayar_hutang = new \Model\Storage\BayarHutang_model();
        $d_bayar_hutang = $m_bayar_hutang->where('id_header', $d_bayar['id'])->get();
        
        $data = null;
        if ( $d_bayar_hutang->count() > 0 ) {
            $d_bayar_hutang = $d_bayar_hutang->toArray();

            foreach ($d_bayar_hutang as $key => $value) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('kode_faktur', $value['faktur_kode'])->with(['pesanan'])->first()->toArray();

                $data_hutang = $this->getDataPenjualan($value['faktur_kode']);

                $data[] = array(
                    'tgl_pesan' => !empty($d_jual['pesanan']) ? $d_jual['pesanan']['tgl_pesan'] : $d_jual['tgl_trans'],
                    'faktur_kode' => $value['faktur_kode'],
                    'hutang' => $value['hutang'],
                    'sudah_bayar' => $value['sudah_bayar'],
                    'bayar' => $value['bayar'],
                    'jenis_bayar' => $jenis_bayar,
                    'data' => $data_hutang
                );
            }
        }

        return $data;
    }

    public function getDataPembayaran($kode_faktur)
    {
        $m_bayar = new \Model\Storage\Bayar_model();
        $d_bayar = $m_bayar->where('faktur_kode', $kode_faktur)->where('mstatus', 1)->with(['bayar_diskon'])->first();

        $data = null;
        if ( $d_bayar ) {
            $data = $d_bayar->toArray();
        }

        return $d_bayar;
    }

    public function loadDetailPembayaran()
    {
        $params = $this->input->post('params');
        try {
            $kode_faktur = exDecrypt( $params['faktur_kode'] );
            $id = exDecrypt( $params['id'] );

            $dataMetodeBayar = null;
            $dataHutangBayar = null;
            $dataDiskon = null;
            
            $m_bayar = new \Model\Storage\Bayar_model();
            if ( !empty($kode_faktur) ) {
                $d_bayar = $m_bayar->where('faktur_kode', $kode_faktur)->where('mstatus', 1)->with(['bayar_det', 'bayar_hutang', 'bayar_diskon'])->first();
            } else {
                $d_bayar = $m_bayar->where('id', $id)->where('mstatus', 1)->with(['bayar_det', 'bayar_hutang', 'bayar_diskon'])->first();
            }

            if ( $d_bayar ) {
                $d_bayar = $d_bayar->toArray();

                foreach ($d_bayar['bayar_det'] as $k_bd => $v_bd) {
                    $dataMetodeBayar[] = array(
                        'nama' => $v_bd['jenis_bayar'],
                        'kode_jenis_kartu' => $v_bd['kode_jenis_kartu'],
                        'no_kartu' => $v_bd['no_kartu'],
                        'nama_kartu' => $v_bd['jenis_kartu']['nama'],
                        'jumlah' => $v_bd['nominal']
                    );
                }

                if ( !empty($d_bayar['bayar_hutang']) ) {
                    foreach ($d_bayar['bayar_hutang'] as $k_bh => $v_bh) {
                        $dataHutangBayar = array(
                            'faktur_kode' => $d_bayar['faktur_kode'],
                            'hutang' => $v_bh['hutang'],
                            'sudah_bayar' => $v_bh['sudah_bayar'],
                            'bayar' => $v_bh['bayar']
                        );
                    }
                }

                if ( !empty($d_bayar['bayar_diskon']) ) {
                    foreach ($d_bayar['bayar_diskon'] as $k_bd => $v_bd) {
                        $dataDiskon[] = array(
                            'diskon_kode' => $v_bd['diskon_kode'],
                            'nilai' => $v_bd['nilai']
                        );
                    }
                }
            }

            $this->result['status'] = 1;
            $this->result['content'] = array(
                'dataMetodeBayar' => $dataMetodeBayar,
                'dataHutangBayar' => $dataHutangBayar,
                'dataDiskon' => $dataDiskon
            );
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deletePembayaran()
    {
        $params = $this->input->post('params');

        try {
            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('faktur_kode', $params['faktur_kode'])->where('mstatus', 1)->first();
            if ( $d_bayar ) {
                $m_bayar->where('faktur_kode', $params['faktur_kode'])->where('mstatus', 1)->update(
                    array(
                        'mstatus' => 0
                    )
                );

                $m_jual = new \Model\Storage\Jual_model();
                $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                    array(
                        'lunas' => 0
                    )
                );

                $m_smt = new \Model\Storage\SaldoMemberTrans_model();
                $d_smt = $m_smt->where('id_trans', $d_bayar->id)->where('tbl_name', $m_bayar->getTable())->get();

                if ( $d_smt->count() > 0 ) {
                    $d_smt = $d_smt->toArray();

                    foreach ($d_smt as $k_smt => $v_smt) {
                        $m_sm = new \Model\Storage\SaldoMember_model();
                        $d_sm = $m_sm->where('id', $v_smt['id_header'])->first();

                        if ( $d_sm ) {
                            $m_sm = new \Model\Storage\SaldoMember_model();
                            $m_sm->where('id', $v_smt['id_header'])->update(
                                array(
                                    'sisa_saldo' => ($d_sm->sisa_saldo + $v_smt['nominal'])
                                )
                            );
                        }

                        $m_smt = new \Model\Storage\SaldoMemberTrans_model();
                        $m_smt->where('id_header', $v_smt['id_header'])->where('nominal', $v_smt['nominal'])->where('id_trans', $v_smt['id_trans'])->where('tbl_name', $v_smt['tbl_name'])->delete();
                    }
                }

                $m_jualg = new \Model\Storage\JualGabungan_model();
                $d_jualg = $m_jualg->select('faktur_kode_gabungan')->where('faktur_kode', $params['faktur_kode'])->get();
                if ( $d_jualg->count() > 0 ) {
                    $d_jualg = $d_jualg->toArray();

                    $m_jual = new \Model\Storage\Jual_model();
                    $m_jual->whereIn('kode_faktur', $d_jualg)->update(
                        array(
                            'lunas' => 0
                        )
                    );
                }

                $m_jualg->where('faktur_kode', $params['faktur_kode'])->delete();
            }

            $deskripsi_log_gaktifitas = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_bayar, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function getDataBelumBayarGabungBill($_data)
    {
        $data = null;

        foreach ($_data as $k_data => $v_data) {
            if ( !isset($data[ $v_data['kode_pesanan'] ]) ) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('pesanan_kode', $v_data['kode_pesanan'])->where('mstatus', 1)->get();

                $sudah_bayar = 0;
                if ( $d_jual->count() > 0 ) {
                    $d_jual = $d_jual->toArray();

                    foreach ($d_jual as $k_jual => $v_jual) {
                        $m_jualg = new \Model\Storage\JualGabungan_model();
                        $sql = "
                            select * from jual_gabungan jg
                            right join
                                jual j
                                on
                                    jg.faktur_kode = j.kode_faktur
                            where
                                j.mstatus = 1 and
                                jg.faktur_kode_gabungan = '".$v_jual['kode_faktur']."'
                        ";
                        $d_jualg = $m_jualg->hydrateRaw( $sql );

                        if ( $d_jualg->count() == 0 ) {
                            $m_bayar = new \Model\Storage\Bayar_model();
                            $d_bayar = $m_bayar->where('faktur_kode', $v_jual['kode_faktur'])->where('mstatus', 1)->first();

                            if ( empty($d_bayar) ) {
                                $member_group = null;

                                if ( !empty($v_jual['kode_member']) ) {
                                    $m_member = new \Model\Storage\Member_model();
                                    $d_member = $m_member->where('kode_member', $v_jual['kode_member'])->with(['member_group'])->first();

                                    if ( $d_member ) {
                                        if ( !empty($d_member['member_group']) ) {
                                            $member_group = $d_member['member_group']['nama'];
                                        }
                                    }
                                }

                                $data[ $v_jual['kode_faktur'] ] = array(
                                    'meja' => $v_data['meja']['nama_meja'],
                                    'lantai' => $v_data['meja']['lantai']['nama_lantai'],
                                    'kode_faktur' => $v_jual['kode_faktur'],
                                    'kode_pesanan' => $v_data['kode_pesanan'],
                                    'member_group' => $member_group,
                                    'pelanggan' => $v_jual['member'],
                                    'kasir' => $v_jual['nama_kasir'],
                                    'total' => $v_jual['grand_total']
                                );
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function modalGabungBill()
    {
        $params = $this->input->get('params');

        $faktur_kode = $params['faktur_kode'];

        $m_jual_utama = new \Model\Storage\Jual_model();
        $d_jual_utama = $m_jual_utama->where('kode_faktur', $faktur_kode)->where('mstatus', 1)->first()->toArray();

        $m_pesanan_utama = new \Model\Storage\Pesanan_model();
        $d_pesanan_utama = $m_pesanan_utama->where('kode_pesanan', $d_jual_utama['pesanan_kode'])->where('mstatus', 1)->with(['meja'])->first()->toArray();

        $member_group = null;
        if ( !empty($d_pesanan_utama['kode_member']) ) {
            $m_member = new \Model\Storage\Member_model();
            $d_member = $m_member->where('kode_member', $d_pesanan_utama['kode_member'])->with(['member_group'])->first()->toArray();

            if ( !empty($d_member['member_group']) ) {
                $member_group = $d_member['member_group']['nama'];
            }
        }
        $data_utama = array(
            'meja' => $d_pesanan_utama['meja']['nama_meja'],
            'lantai' => $d_pesanan_utama['meja']['lantai']['nama_lantai'],
            'kode_faktur' => $faktur_kode,
            'kode_pesanan' => $d_pesanan_utama['kode_pesanan'],
            'member_group' => $member_group,
            'pelanggan' => $d_pesanan_utama['member'],
            'kasir' => $d_pesanan_utama['nama_user'],
            'total' => $d_pesanan_utama['grand_total']
        );

        $m_jual_gabungan = new \Model\Storage\JualGabungan_model();
        $sql = "
            select j.* from jual_gabungan jg
            right join
                jual j
                on
                    jg.faktur_kode_gabungan = j.kode_faktur
            where
                jg.faktur_kode = '".$faktur_kode."'
        ";
        $d_jual_gabungan = $m_jual_gabungan->hydrateRaw( $sql );

        $data_gabungan = null;
        if ( $d_jual_gabungan->count() > 0 ) {
            $d_jual_gabungan = $d_jual_gabungan->toArray();
            foreach ($d_jual_gabungan as $k_jg => $v_jg) {
                $m_pesanan_gabungan = new \Model\Storage\Pesanan_model();
                $d_pesanan_gabungan = $m_pesanan_gabungan->where('kode_pesanan', $v_jg['pesanan_kode'])->where('mstatus', 1)->with(['meja'])->first()->toArray();

                $member_group = null;
                if ( !empty($v_jg['kode_member']) ) {
                    $m_member = new \Model\Storage\Member_model();
                    $d_member = $m_member->where('kode_member', $v_jg['kode_member'])->with(['member_group'])->first()->toArray();

                    if ( !empty($d_member['member_group']) ) {
                        $member_group = $d_member['member_group']['nama'];
                    }
                }

                $data_gabungan[ $v_jg['kode_faktur'] ] = array(
                    'meja' => $d_pesanan_gabungan['meja']['nama_meja'],
                    'lantai' => $d_pesanan_gabungan['meja']['lantai']['nama_lantai'],
                    'kode_faktur' => $v_jg['kode_faktur'],
                    'kode_pesanan' => $v_jg['pesanan_kode'],
                    'member_group' => $member_group,
                    'pelanggan' => $v_jg['member'],
                    'kasir' => $v_jg['nama_kasir'],
                    'total' => $v_jg['grand_total']
                );
            }
        }

        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();
        $today = $now['tanggal'];

        $start_date = $today.' 00:00:00';
        $end_date = $today.' 23:59:59';

        $m_pesanan = new \Model\Storage\Pesanan_model();
        $d_pesanan = $m_pesanan->where('kode_pesanan', '<>', $d_jual_utama['pesanan_kode'])->whereBetween('tgl_pesan', [$start_date, $end_date])->where('mstatus', 1)->with(['meja'])->get();

        $content['pesanan_kode'] = $d_jual_utama['pesanan_kode'];
        $content['data_utama'] = $data_utama;
        $content['data_gabungan'] = $data_gabungan;
        $content['data_belum_bayar'] = $this->getDataBelumBayarGabungBill( $d_pesanan );

        $html = $this->load->view($this->pathView . 'modal_gabung_bill', $content, TRUE);

        echo $html;
    }

    public function saveBillGabung()
    {
        $params = $this->input->post('params');

        try {
            $data_utama = $params['data_utama'];
            $data = isset($params['data']) ? $params['data'] : null;

            if ( !empty($data) ) {
                foreach ($data as $key => $value) {
                    $m_jual_gabungan = new \Model\Storage\JualGabungan_model();
                    $m_jual_gabungan->faktur_kode = $data_utama['kode_faktur'];
                    $m_jual_gabungan->faktur_kode_gabungan = $value['kode_faktur'];
                    $m_jual_gabungan->jml_tagihan = $value['total'];
                    $m_jual_gabungan->save();
                }
            }

            $this->result['status'] = 1;
            $this->result['content'] = array('kode' => exEncrypt( $data_utama['kode_faktur'] ));
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalMemberSplitBill()
    {
        $m_jual = new \Model\Storage\Jual_model();
        $now = $m_jual->getDate();

        $start_date = $now['tanggal'].' 00:00:00';
        $end_date = $now['tanggal'].' 23:59:59';

        $sql = "
            select 
                j.member,
                j.kode_member,
                mbr.nama_grup,
                mbr.group_id
            from jual j
            left join
                (
                    select m.kode_member, m.nama, mg.nama as nama_grup, mg.id as group_id from member m
                    left join
                        member_group mg
                        on
                            m.member_group_id = mg.id
                ) mbr
                on
                    j.kode_member = mbr.kode_member
            where
                j.tgl_trans between '".$start_date."' and '".$end_date."' and
                j.mstatus = 1
            group by
                j.member,
                j.kode_member,
                mbr.nama_grup,
                mbr.group_id
        ";

        $d_jual = $m_jual->hydrateRaw( $sql );

        $data = null;
        if ( $d_jual->count() > 0 ) {
            $data = $d_jual->toArray();
        }

        // $content['akses'] = $this->hasAkses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_member_split_bill', $content, TRUE);

        echo $html;
    }

    public function modalDiskon()
    {
        $kode_faktur = $this->input->get('kode_faktur');

        $m_diskon = new \Model\Storage\Diskon_model();
        $now = $m_diskon->getDate();

        $today = $now['tanggal'];
        $jam = $now['jam'];

        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first();

        $member = 0;
        if ( !empty($d_jual->kode_member) ) {
            $member = 1;
        }

        if ( $member == 1 ) {
            $d_diskon = $m_diskon->where('start_date', '<=', $today)
                                 ->where('end_date', '>=', $today)
                                 ->where('start_time', '<=', $jam)
                                 ->where('end_time', '>=', $jam)
                                 ->where('member', 1)
                                 ->get();
        } else {
            $d_diskon = $m_diskon->where('start_date', '<=', $today)
                                 ->where('end_date', '>=', $today)
                                 ->where('start_time', '<=', $jam)
                                 ->where('end_time', '>=', $jam)
                                 ->where('non_member', 1)
                                 ->get();
        }

        $data = null;
        if ( $d_diskon->count() > 0 ) {
            $d_diskon = $d_diskon->toArray();
            foreach ($d_diskon as $key => $value) {
                $data[] = $d_diskon[$key];
            }
        }

        $content['kode_faktur'] = $kode_faktur;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_diskon', $content, TRUE);

        echo $html;
    }

    public function getDataDiskon()
    {
        $params = $this->input->post('params');

        try {
            $data_metode_bayar = isset($params['data_metode_bayar']) ? $params['data_metode_bayar'] : null;
            $_data_diskon = isset($params['data_diskon']) ? $params['data_diskon'] : null;
            $data_diskon = $this->hitDiskon( $params['kode_faktur'], $data_metode_bayar, $_data_diskon );

            $this->result['status'] = 1;
            $this->result['content'] = $data_diskon;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitDiskon($_kode_faktur, $_data_metode_bayar, $_data_diskon)
    {
        $data_diskon = null;

        $data_metode_bayar = (isset($_data_metode_bayar) && !empty($_data_metode_bayar) && ( !empty($_data_metode_bayar[0]) || !empty($_data_metode_bayar[count($_data_metode_bayar) - 1]) )) ? $_data_metode_bayar : null;

        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        $today = $now['tanggal'];

        $kode_faktur = $_kode_faktur;

        $tot_belanja = 0;
        $tot_diskon = 0;
        $tot_ppn = 0;
        $tot_sc = 0;
        $jenis_harga_exclude = 0;
        $jenis_harga_include = 0;
        
        $m_jual = new \Model\Storage\Jual_model();
        $sql = "
            select 
                jual_utama.branch,
                jual.kode_faktur_utama as kode_faktur,
                ji.kode_jenis_pesanan,
                jp.exclude,
                jp.include,
                sum(ji.jumlah) as jumlah, 
                sum(ji.total) as total,
                /* case 
                    when jp.exclude = 1 then
                        sum(ji.total)
                    when jp.include = 1 then
                        sum(ji.total) + ISNULL(sum(ji.ppn), 0) + ISNULL(sum(ji.service_charge), 0)
                end as total, */
                ISNULL(sum(ji.ppn), 0) as nilai_ppn, 
                ISNULL(sum(ji.service_charge), 0) as nilai_service_charge, 
                max(m.ppn) as ppn, 
                max(m.service_charge) as service_charge
            from jual_item ji
            right join
                (
                    select j.kode_faktur as kode_faktur_utama, j.kode_faktur as kode_faktur from jual j where j.kode_faktur = '".$kode_faktur."'
                    UNION ALL
                    select jg.faktur_kode as kode_faktur_utama, jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode = '".$kode_faktur."'
                ) jual
                on
                    jual.kode_faktur = ji.faktur_kode 
            right join
                jual jual_utama
                on
                    jual_utama.kode_faktur = jual.kode_faktur_utama
            right join
                menu m
                on
                    m.kode_menu = ji.menu_kode
            right join
                jenis_pesanan jp
                on
                    jp.kode = ji.kode_jenis_pesanan
            where
                ji.jumlah > 0
            group by
                ji.kode_jenis_pesanan,
                jp.exclude,
                jp.include,
                jual_utama.branch,
                jual.kode_faktur_utama
        ";
        // $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first();
        $d_jual = $m_jual->hydrateRaw( $sql );
        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            foreach ($d_jual as $k_jual => $v_jual) {
                $ppn = 0;
                if ( $v_jual['ppn'] == 1 ) {
                    $m_ppn = new \Model\Storage\Ppn_model();
                    $d_ppn = $m_ppn->where('branch_kode', $v_jual['branch'])
                                   ->where('tgl_berlaku', '<=', $today)
                                   ->where('mstatus', 1)
                                   ->first();
                    if ( $d_ppn ) {
                        if ( $d_ppn->nilai > 0 ) {
                            $ppn = $d_ppn->nilai/100;
                        }
                    }
                }

                $sc = 0;
                if ( $v_jual['service_charge'] == 1 ) {
                    $m_sc = new \Model\Storage\ServiceCharge_model();
                    $d_sc = $m_sc->where('branch_kode', $v_jual['branch'])
                                   ->where('tgl_berlaku', '<=', $today)
                                   ->where('mstatus', 1)
                                   ->first();

                    if ( $d_sc ) {
                        if ( $d_sc->nilai > 0 ) {
                            $sc = $d_sc->nilai/100;
                        }
                    }
                }

                $tot_belanja = $v_jual['total'];
                $tot_diskon = 0;
                $tot_ppn = 0;
                $tot_sc = 0;
                $jenis_harga_exclude = $v_jual['exclude'];
                $jenis_harga_include = $v_jual['include'];

                if ( !empty($_data_diskon) ) {
                    foreach ($_data_diskon as $k_dd => $v_dd) {
                        $m_diskon = new \Model\Storage\Diskon_model();
                        $d_diskon = $m_diskon->where('kode', $v_dd)->first();

                        if ( $d_diskon->diskon_tipe == 1 ) {
                            $tot_diskon_by_kode = 0;

                            $hitung = 0;
                            if ( !empty($data_metode_bayar) ) {
                                foreach ($data_metode_bayar as $k_dmb => $v_dmb) {
                                    if ( !empty($v_dmb) ) {
                                        $m_djk = new \Model\Storage\DiskonJenisKartu_model();
                                        $d_djk = $m_djk->where('diskon_kode', $v_dd)->where('jenis_kartu_kode', $v_dmb['kode_jenis_kartu'])->first();

                                        if ( $d_djk ) {
                                            $hitung = 1;

                                            break;
                                        }
                                    }
                                }
                            }

                            if ( $hitung == 1 ) {
                                if ( $d_diskon->status_ppn == 1 ) {
                                    $ppn = ($d_diskon->ppn > 0) ? $d_diskon->ppn/100 : 0;
                                }

                                if ( $d_diskon->status_service_charge == 1 ) {
                                    $sc = ($d_diskon->service_charge > 0) ? $d_diskon->service_charge/100 : 0;
                                }

                                if ( $tot_belanja > $d_diskon->min_beli ) {
                                    if ( $d_diskon->diskon_jenis == 'persen' ) {
                                        $diskon = ($d_diskon->diskon > 0) ? ($tot_belanja * ($d_diskon->diskon/100)) : 0;
                                        $tot_diskon += $diskon;
                                        $tot_diskon_by_kode += $diskon;
                                        $tot_belanja -= $diskon;
                                    } else {
                                        $diskon = $d_diskon->diskon;
                                        $tot_diskon += $diskon;
                                        $tot_diskon_by_kode += $diskon;
                                        $tot_belanja -= $diskon;
                                    }
                                }

                                if ( $v_jual['exclude'] == 1 ) {
                                    $tot_sc = $tot_belanja*$sc;
                                    $tot_ppn = ($tot_belanja + $tot_sc)*$ppn;
                                }

                                $data_diskon[ $v_dd ] = array(
                                    'kode' => $v_dd,
                                    'nominal' => $tot_diskon_by_kode
                                );
                            }
                        }

                        if ( $d_diskon->diskon_tipe == 2 ) {
                            $tot_diskon_by_kode = 0;

                            $hitung = 0;
                            if ( !empty($data_metode_bayar) ) {
                                foreach ($data_metode_bayar as $k_dmb => $v_dmb) {
                                    if ( !empty($v_dmb) ) {
                                        $m_djk = new \Model\Storage\DiskonJenisKartu_model();
                                        $d_djk = $m_djk->where('diskon_kode', $v_dd)->where('jenis_kartu_kode', $v_dmb['kode_jenis_kartu'])->first();

                                        if ( $d_djk ) {
                                            $hitung = 1;

                                            break;
                                        }
                                    }
                                }
                            }

                            if ( $hitung == 1 ) {
                                if ( $d_diskon->status_ppn == 1 ) {
                                    $ppn = ($d_diskon->ppn > 0) ? $d_diskon->ppn/100 : 0;
                                }

                                if ( $d_diskon->status_service_charge == 1 ) {
                                    $sc = ($d_diskon->service_charge > 0) ? $d_diskon->service_charge/100 : 0;
                                }

                                $m_dm = new \Model\Storage\DiskonMenu_model();
                                $sql = "
                                    select 
                                        dm.menu_kode,
                                        case
                                            when ji.total > 0 and dm.diskon > 0 then
                                                case
                                                    when dm.diskon_jenis = 'persen' then
                                                        ji.total * (dm.diskon / 100)
                                                    else
                                                        ji.total - dm.diskon
                                                end
                                            else
                                                0
                                        end as diskon
                                    from diskon_menu dm
                                    right join
                                        (
                                            select 
                                                ji.menu_kode, 
                                                ji.menu_nama, 
                                                ji.kode_jenis_pesanan,
                                                jp.exclude,
                                                jp.include,
                                                sum(ji.jumlah) as jumlah, 
                                                case 
                                                    when jp.exclude = 1 then
                                                        sum(ji.total)
                                                    when jp.include = 1 then
                                                        sum(ji.total) + sum(ji.ppn) + sum(ji.service_charge)
                                                end as total
                                            from jual_item ji
                                            right join
                                                (
                                                    select j.kode_faktur as kode_faktur from jual j where j.kode_faktur = '".$kode_faktur."'
                                                    UNION ALL
                                                    select jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode = '".$kode_faktur."'
                                                ) jual
                                                on
                                                    jual.kode_faktur = ji.faktur_kode 
                                            right join
                                                menu m
                                                on
                                                    m.kode_menu = ji.menu_kode
                                            right join
                                                jenis_pesanan jp
                                                on
                                                    jp.kode = ji.kode_jenis_pesanan
                                            where
                                                ji.jumlah > 0
                                            group by
                                                ji.kode_jenis_pesanan,
                                                jp.exclude,
                                                jp.include,
                                                ji.menu_kode, 
                                                ji.menu_nama
                                        ) ji
                                        on
                                            dm.menu_kode = ji.menu_kode
                                    where
                                        dm.diskon_kode = '".$v_dd."'
                                ";
                                $d_dm = $m_dm->hydrateRaw( $sql );

                                if ( $d_dm->count() > 0 ) {
                                    $d_dm = $d_dm->toArray();

                                    $idx = 0;
                                    foreach ($d_dm as $k_dm => $v_dm) {
                                        $diskon = $v_dm['diskon'];

                                        $tot_diskon += $diskon;
                                        $tot_diskon_by_kode += $diskon;
                                        $tot_belanja -= $diskon;

                                        $idx++;
                                        if ( count($d_dm) == $idx ) {
                                            if ( $v_jual['exclude'] == 1 ) {
                                                $tot_sc = $tot_belanja*$sc;
                                                $tot_ppn = ($tot_belanja + $tot_sc)*$ppn;
                                            }
                                        }
                                    }

                                    $data_diskon[ $v_dd ] = array(
                                        'kode' => $v_dd,
                                        'nominal' => $tot_diskon_by_kode
                                    );
                                }
                            }
                        }

                        if ( $d_diskon->diskon_tipe == 3 ) {
                        }
                    }
                } else {
                    $tot_ppn = $v_jual['nilai_ppn'];
                    $tot_sc = $v_jual['nilai_service_charge'];
                }
            }
        }

        $_data_diskon = array(
            'data_diskon' => $data_diskon,
            'total_belanja' => ($tot_belanja > 0) ? $tot_belanja : 0,
            'total_diskon' => $tot_diskon,
            'total_service_charge' => $tot_sc,
            'total_ppn' => $tot_ppn,
            'jenis_harga_exclude' => $jenis_harga_exclude,
            'jenis_harga_include' => $jenis_harga_include
        );

        return $_data_diskon;
    }

    public function formFakturHutang()
    {
        $params = $this->input->get('params');

        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        $content['data'] = $this->getDataPenjualan($params);
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['kategori_pembayaran'] = $this->getDataKategoriPembayaran($params);
        $content['data_branch'] = array(
            'kode' => $content['data']['kode_branch'],
            'nama' => $content['data']['nama_branch'],
            'alamat' => $content['data']['alamat_branch'],
            'telp' => $content['data']['telp_branch'],
            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
            'waktu' => $content['data']['tgl_trans']
        );

        $html = $this->load->view($this->pathView . 'form_faktur_hutang', $content, TRUE);

        echo $html;
    }

    public function exportPdf($kode_faktur, $html)
    {
        $this->load->library('PDFGenerator');

        $res_view_html = '<html>';
            $res_view_html .= '<head>';
                $res_view_html .= '<style type="text/css">';
                    $res_view_html .= '.col-xs-12{width:100%}.col-xs-11{width:91.66666667%}.col-xs-10{width:83.33333333%}.col-xs-9{width:75%}.col-xs-8{width:66.66666667%}.col-xs-7{width:58.33333333%}.col-xs-6{width:50%}.col-xs-5{width:41.66666667%}.col-xs-4{width:33.33333333%}.col-xs-3{width:25%}.col-xs-2{width:16.66666667%}.col-xs-1{width:8.33333333%}';
                    $res_view_html .= ".text-center {text-align:center}";
                    $res_view_html .= ".text-right {text-align:right}";
                    $res_view_html .= ".font10 {font-size:10px}";
                    $res_view_html .= ".table {width: 100%}";
                    $res_view_html .= ".body {width:100%;display: flex;justify-content: center;}";
                $res_view_html .= '</style>';
                $res_view_html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            $res_view_html .= '</head>';
            $res_view_html .= '<body>';
            $res_view_html .= $html;
            $res_view_html .= '</body>';
        $res_view_html .= '</html>';

        $this->pdfgenerator->generate($res_view_html, $kode_faktur);
    }

    public function pembayaranFormHutang($_kode_member)
    {
        $kode_member = exDecrypt( $_kode_member );

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

        $m_member = new \Model\Storage\Member_model();
        $d_member = $m_member->where('kode_member', $kode_member)->first();

        $content['akses'] = $this->hakAkses;
        $content['data'] = array(
            'kode_member' => $d_member->kode_member,
            'member' => $d_member->nama
        );
        $content['data_hutang'] = $this->getDataHutangByMember($kode_member);
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['data_branch'] = array(
            'nama' => $this->namabranch,
            'alamat' => $this->alamatbranch,
            'telp' => $this->telpbranch,
            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
            'waktu' => $now['waktu']
        );

        $data['view'] = $this->load->view($this->pathView . 'pembayaran_form_hutang', $content, TRUE);

        $this->load->view($this->template, $data);
    }

    public function getDataHutangByMember($kode_member, $id = null)
    {
        $date = '2023-01-19 00:00:01';

        $data = null;

        if ( empty($id) ) {
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual_hutang = $m_jual->where('tgl_trans', '>=', $date)->where('kode_member', $kode_member)->where('hutang', 1)->where('mstatus', 1)->with(['pesanan'])->get();

            if ( $d_jual_hutang->count() > 0 ) {
                $d_jual_hutang = $d_jual_hutang->toArray();

                foreach ($d_jual_hutang as $key => $value) {
                    $sql = "select sum(bayar) as total_bayar from bayar_hutang bh 
                        left join
                            (
                                select * from bayar where mstatus = 1
                            ) b 
                            on
                                bh.id_header = b.id
                        where
                            b.mstatus = 1 and
                            bh.faktur_kode = '".$value['kode_faktur']."'
                    ";

                    $m_bayar_hutang = new \Model\Storage\BayarHutang_model();
                    $d_bayar_hutang = $m_bayar_hutang->hydrateRaw($sql);

                    $total_bayar = 0;
                    if ( $d_bayar_hutang->count() > 0 ) {
                        $total_bayar = $d_bayar_hutang->toArray()[0]['total_bayar'];
                    }

                    if ( $value['grand_total'] > $total_bayar ) {
                        $data[] = array(
                            'tgl_pesan' => !empty($value['pesanan']) ? $value['pesanan']['tgl_pesan'] : $value['tgl_trans'],
                            'faktur_kode' => $value['kode_faktur'],
                            'hutang' => $value['grand_total'],
                            'bayar' => $total_bayar
                        );
                    }
                }
            }
        } else {
            $m_bayar_hutang = new \Model\Storage\BayarHutang_model();
            $d_bayar_hutang = $m_bayar_hutang->where('id_header', $id)->get();

            $sql = "
                select bd.jenis_bayar, bd.kode_jenis_kartu, sum(bd.nominal) as jumlah from bayar b 
                right join
                    bayar_det bd 
                    on
                        b.id = bd.id_header 
                where
                    b.id = ".$id."
                group by
                    bd.jenis_bayar, 
                    bd.kode_jenis_kartu
            ";
            $d_jb = $m_bayar_hutang->hydrateRaw( $sql );

            $jenis_bayar = null;
            if ( $d_jb->count() > 0 ) {
                $jenis_bayar = $d_jb->toArray();
            }
            
            $data = null;
            if ( $d_bayar_hutang->count() > 0 ) {
                $d_bayar_hutang = $d_bayar_hutang->toArray();

                foreach ($d_bayar_hutang as $key => $value) {
                    $m_jual = new \Model\Storage\Jual_model();
                    $d_jual = $m_jual->where('kode_faktur', $value['faktur_kode'])->with(['pesanan'])->first()->toArray();

                    $data_hutang = $this->getDataPenjualan($value['faktur_kode']);

                    $data[] = array(
                        'tgl_pesan' => !empty($d_jual['pesanan']) ? $d_jual['pesanan']['tgl_pesan'] : $d_jual['tgl_trans'],
                        'faktur_kode' => $value['faktur_kode'],
                        'hutang' => $value['hutang'],
                        'sudah_bayar' => $value['sudah_bayar'],
                        'bayar' => $value['bayar'],
                        'jenis_bayar' => $jenis_bayar,
                        'data' => $data_hutang
                    );
                }
            }
        }

        return $data;
    }

    public function pembayaranFormHutangEdit($id)
    {
        $id = exDecrypt( $id );

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

        $m_bayar = new \Model\Storage\Bayar_model();
        $d_bayar = $m_bayar->where('id', $id)->first();

        $m_member = new \Model\Storage\Member_model();
        $d_member = $m_member->where('kode_member', $d_bayar->member_kode)->first();

        $content['akses'] = $this->hakAkses;
        $content['data'] = array(
            'id' => $id,
            'kode_member' => $d_member->kode_member,
            'member' => $d_member->nama,
            'grand_total' => $d_bayar->jml_tagihan,
            'jml_bayar' => $d_bayar->jml_bayar
        );
        $content['data_hutang'] = $this->getDataHutangByMember($d_bayar->member_kode, $id);
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['data_branch'] = array(
            'nama' => $this->namabranch,
            'alamat' => $this->alamatbranch,
            'telp' => $this->telpbranch,
            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
            'waktu' => $now['waktu']
        );

        $data['view'] = $this->load->view($this->pathView . 'pembayaran_form_hutang_edit', $content, TRUE);

        $this->load->view($this->template, $data);
    }

    public function tes()
    {
        cetak_r( $data = $this->getDataPenjualanAfterSave( $params['faktur_kode'] ) );
        // phpinfo();

        // if (!extension_loaded('imagick')){
        //     echo 'imagick not installed';
        // }
    }
}