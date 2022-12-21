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

                if ( !empty($d_jual->kode_member) ) {
                    $m_member = new \Model\Storage\Member_model();
                    $d_member = $m_member->where('kode_member', $d_jual->kode_member)->with(['member_group'])->first()->toArray();

                    if ( !empty($d_member['member_group']) ) {
                        $member_group = $d_member['member_group']['nama'];
                    }
                }

                $data[ $d_jual->pesanan_kode ] = array(
                    'kode_pesanan' => $d_jual->pesanan_kode,
                    'kode_faktur' => $d_jual->kode_faktur,
                    'member_group' => $member_group,
                    'pelanggan' => $d_jual->member,
                    'kasir' => $d_jual->nama_kasir,
                    'total' => $value['jml_tagihan'],
                    'bayar' => $value['jml_bayar']
                );
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

                    $kode_faktur = $d_jual;

                    $m_jualg = new \Model\Storage\JualGabungan_model();
                    $d_jualg = $m_jualg->select('faktur_kode')->whereIn('faktur_kode_gabungan', $d_jual)->get();

                    if ( $d_jualg->count() > 0 ) {
                        $d_jualg = $d_jualg->toArray();

                        $kode_faktur = $d_jualg;
                    }

                    $m_bayar = new \Model\Storage\Bayar_model();
                    $d_bayar = $m_bayar->whereIn('faktur_kode', $kode_faktur)->where('mstatus', 1)->get();

                    if ( $d_bayar->count() > 0 ) {
                        $sudah_bayar = 1;
                    }
                }

                $member_group = null;

                if ( !empty($v_data['kode_member']) ) {
                    $m_member = new \Model\Storage\Member_model();
                    $d_member = $m_member->where('kode_member', $v_data['kode_member'])->with(['member_group'])->first()->toArray();

                    if ( !empty($d_member['member_group']) ) {
                        $member_group = $d_member['member_group']['nama'];
                    }
                }

                if ( $sudah_bayar == 0 ) {
                    $data[ $v_data['kode_pesanan'] ] = array(
                        'meja' => $v_data['meja']['nama_meja'],
                        'lantai' => $v_data['meja']['lantai']['nama_lantai'],
                        'kode_pesanan' => $v_data['kode_pesanan'],
                        'member_group' => $member_group,
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
                $d_bayar = $m_bayar->where('faktur_kode', $value['kode_faktur'])->where('mstatus', 1)->first();

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

        $content['akses'] = $this->hakAkses;
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
                j.member,
                j.kode_member,
                j.total,
                j.diskon,
                j.grand_total,
                j.lunas,
                j.ppn,
                j.service_charge
            from jual j
            left join
                (select * from bayar where mstatus = 1) b
                on
                    j.kode_faktur = b.faktur_kode
            where
                j.kode_faktur = '".$kode_faktur."' and
                j.mstatus = 1
        ";
        $d_jual = $m_jual->hydrateRaw( $sql );

        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            $total = $d_jual[0]['total'];
            $diskon = $d_jual[0]['diskon'];
            $grand_total = $d_jual[0]['grand_total'];
            $ppn = $d_jual[0]['ppn'];
            $service_charge = $d_jual[0]['service_charge'];

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

                    if ( !isset($detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]) ) {
                        $jual_item = null;
                        $jual_item[ $key_item ] = array(
                            'nama' => $v_ji['menu_nama'],
                            'jumlah' => $v_ji['jumlah'],
                            'total' => $v_ji['total']
                        );

                        $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key] = array(
                            'nama' => $v_ji['jp_nama'],
                            'jual_item' => $jual_item
                        );
                    } else {
                        if ( !isset($detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]) ) {
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total']
                            );
                        } else {
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['jumlah'] += $v_ji['jumlah'];
                            $detail[ $d_jual[0]['kode_faktur'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total'] += $v_ji['total'];
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
                    $total += $v_jg['total'];
                    $diskon += $v_jg['diskon'];
                    $grand_total += $v_jg['grand_total'];
                    $ppn += $v_jg['ppn'];
                    $service_charge += $v_jg['service_charge'];

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

                            if ( !isset($detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]) ) {
                                $jual_item = null;
                                $jual_item[ $key_item ] = array(
                                    'nama' => $v_jig['menu_nama'],
                                    'jumlah' => $v_jig['jumlah'],
                                    'total' => $v_jig['total']
                                );

                                $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key] = array(
                                    'nama' => $v_jig['jp_nama'],
                                    'jual_item' => $jual_item
                                );
                            } else {
                                if ( !isset($detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]) ) {
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item] = array(
                                        'nama' => $v_jig['menu_nama'],
                                        'jumlah' => $v_jig['jumlah'],
                                        'total' => $v_jig['total']
                                    );
                                } else {
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['jumlah'] += $v_jig['jumlah'];
                                    $detail[ $v_jg['faktur_kode_gabungan'] ]['jenis_pesanan'][$key]['jual_item'][$key_item]['total'] += $v_jig['total'];
                                }
                            }
                        }
                    }
                }
            }

            $data = array(
                'kode_faktur' => $d_jual[0]['kode_faktur'],
                'tgl_trans' => $d_jual[0]['tgl_trans'],
                'member' => $d_jual[0]['member'],
                'kode_member' => $d_jual[0]['kode_member'],
                'total' => $total,
                'diskon' => $diskon,
                'ppn' => $ppn,
                'service_charge' => $service_charge,
                'grand_total' => $grand_total,
                'lunas' => $d_jual[0]['lunas'],
                'detail' => $detail
            );
        }

        // cetak_r( $data, 1 );

        // $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->with(['jual_item', 'bayar', 'jual_gabungan'])->first()->toArray();

        // $data = null;
        // $jenis_pesanan = null;
        // foreach ($d_jual['jual_item'] as $k_ji => $v_ji) {  
        //     $key = $v_ji['jenis_pesanan'][0]['nama'].' | '.$v_ji['jenis_pesanan'][0]['kode'];
        //     $key_item = $v_ji['menu_nama'].' | '.$v_ji['menu_kode'];

        //     if ( !isset($jenis_pesanan[$key]) ) {
        //         $jual_item = null;
        //         $jual_item[ $key_item ] = array(
        //             'nama' => $v_ji['menu_nama'],
        //             'jumlah' => $v_ji['jumlah'],
        //             'total' => $v_ji['total']
        //         );

        //         $jenis_pesanan[$key] = array(
        //             'nama' => $v_ji['jenis_pesanan'][0]['nama'],
        //             'jual_item' => $jual_item
        //         );
        //     } else {
        //         if ( !isset($jenis_pesanan[$key]['jual_item'][$key_item]) ) {
        //             $jenis_pesanan[$key]['jual_item'][$key_item] = array(
        //                 'nama' => $v_ji['menu_nama'],
        //                 'jumlah' => $v_ji['jumlah'],
        //                 'total' => $v_ji['total']
        //             );
        //         } else {
        //             $jenis_pesanan[$key]['jual_item'][$key_item]['jumlah'] += $v_ji['jumlah'];
        //             $jenis_pesanan[$key]['jual_item'][$key_item]['total'] += $v_ji['total'];
        //         }
        //     }
        // }

        // $data = array(
        //     'kode_faktur' => $d_jual['kode_faktur'],
        //     'tgl_trans' => $d_jual['tgl_trans'],
        //     'member' => $d_jual['member'],
        //     'kode_member' => $d_jual['kode_member'],
        //     'total' => $d_jual['total'],
        //     'diskon' => $d_jual['diskon'],
        //     'ppn' => $d_jual['ppn'],
        //     'service_charge' => $d_jual['service_charge'],
        //     'grand_total' => $d_jual['grand_total'],
        //     'lunas' => $d_jual['lunas'],
        //     'jenis_pesanan' => $jenis_pesanan,
        //     'bayar' => $d_jual['bayar']
        // );

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

            $data[] = array(
                'kode_jenis_kartu' => 'saldo_member',
                'nama' => 'SALDO MEMBER',
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
                    // $m_bayar = new \Model\Storage\Bayar_model();
                    // $d_bayar_non_aktif = $m_bayar->select('id')->where('faktur_kode', $value['kode_faktur'])->where('mstatus', 0)->get();

                    // if ( $d_bayar_non_aktif->count() > 0 ) {
                    //     $d_bayar_non_aktif = $d_bayar_non_aktif->toArray();

                    //     cetak_r($d_jual_hutang, 1);

                    //     $d_bayar_hutang = $m_bayar_hutang->whereNotIn('id_header', $d_bayar_non_aktif)->where('faktur_kode', $value['kode_faktur'])->sum('bayar');
                    // } else {
                    //     $d_bayar_hutang = $m_bayar_hutang->where('faktur_kode', $value['kode_faktur'])->sum('bayar');
                    // }

                    $sql = "select sum(bayar) as total_bayar from bayar_hutang bh 
                        left join
                            bayar b 
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

                    $data[] = array(
                        'tgl_pesan' => !empty($value['pesanan']) ? $value['pesanan']['tgl_pesan'] : $value['tgl_trans'],
                        'faktur_kode' => $value['kode_faktur'],
                        'hutang' => $value['grand_total'],
                        'bayar' => $total_bayar
                    );
                }
            }
        }

        return $data;
    }

    public function modalMetodePembayaran()
    {
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
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->where('kode_faktur', $params['faktur_kode'])->first();

            $m_pesanan = new \Model\Storage\Pesanan_model();
            $d_pesanan = $m_pesanan->where('kode_pesanan', $d_jual->pesanan_kode)->first();

            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('faktur_kode', $params['faktur_kode'])->where('mstatus', 1)->first();
            if ( $d_bayar ) {
                $m_bayar->where('faktur_kode', $params['faktur_kode'])->where('mstatus', 1)->update(
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
            $m_bayar->faktur_kode = $params['faktur_kode'];
            $m_bayar->jml_tagihan = $params['jml_tagihan'];
            $m_bayar->jml_bayar = $params['jml_bayar'];
            $m_bayar->ppn = $params['ppn'];
            $m_bayar->service_charge = $params['service_charge'];
            $m_bayar->diskon = $params['diskon'];
            $m_bayar->total = $params['tot_belanja'];
            $m_bayar->mstatus = 1;
            $m_bayar->save();

            $id_header = $m_bayar->id;

            foreach ($params['dataMetodeBayar'] as $key => $value) {
                if ( !empty($value) ) {
                    $m_bayard = new \Model\Storage\BayarDet_model();
                    $m_bayard->id_header = $id_header;
                    $m_bayard->jenis_bayar = $value['nama'];
                    $m_bayard->kode_jenis_kartu = ($value['kode_jenis_kartu'] != 'saldo_member') ? $value['kode_jenis_kartu'] : '';
                    $m_bayard->nominal = $value['jumlah'];
                    $m_bayard->no_kartu = isset($value['no_kartu']) ? $value['no_kartu'] : null;
                    $m_bayard->nama_kartu = isset($value['nama_kartu']) ? $value['nama_kartu'] : null;
                    $m_bayard->save();

                    if ( $value['kode_jenis_kartu'] == 'saldo_member' ) {
                        $nominal = $value['jumlah'];

                        while ( $nominal > 0 ) {
                            $m_sm = new \Model\Storage\SaldoMember_model();
                            $d_sm = $m_sm->where('member_kode', $d_pesanan->kode_member)->where('sisa_saldo', '>', 0)->where('status', 1)->orderBy('id', 'asc')->first();

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

            if ( !empty($params['dataHutangBayar']) ) {
                foreach ($params['dataHutangBayar'] as $key => $value) {
                    $m_bayarh = new \Model\Storage\BayarHutang_model();
                    $m_bayarh->id_header = $id_header;
                    $m_bayarh->faktur_kode = $value['faktur_kode'];
                    $m_bayarh->hutang = $value['hutang'];
                    $m_bayarh->sudah_bayar = (isset($value['sudah_bayar']) && !empty($value['sudah_bayar']) && $value['sudah_bayar'] > 0) ? $value['sudah_bayar'] : 0;
                    $m_bayarh->bayar = $value['bayar'];
                    $m_bayarh->save();
                }
            }

            $m_jual = new \Model\Storage\Jual_model();
            if ( $params['jml_bayar'] >= $params['jml_tagihan'] ) {
                $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                    array(
                        'lunas' => 1
                    )
                );
            } else {
                $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                    array(
                        'lunas' => 0
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
                        'lunas' => 1
                    )
                );
            }

            $m_mejal = new \Model\Storage\MejaLog_model();
            $m_mejal->where('pesanan_kode', $d_pesanan->kode_pesanan)->where('meja_id', $d_pesanan->meja_id)->where('status', 1)->update(
                array('status' => 0)
            );

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
                    'hutang' => 1
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
        $content['data_bayar'] = $this->getDataPembayaran($kode_faktur);
        $content['data_hutang'] = $this->getDataHutangEdit($kode_faktur);
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['data_branch'] = array(
            'nama' => $this->namabranch,
            'alamat' => $this->alamatbranch,
            'telp' => $this->telpbranch,
            'nama_kasir' => $this->userdata['detail_user']['nama_detuser'],
            'waktu' => $now['waktu']
        );

        $data['view'] = $this->load->view($this->pathView . 'pembayaran_form_edit', $content, TRUE);

        $this->load->view($this->template, $data);
    }

    public function getDataHutangEdit($kode_faktur)
    {
        $m_bayar = new \Model\Storage\Bayar_model();
        $d_bayar = $m_bayar->where('faktur_kode', $kode_faktur)->where('mstatus', 1)->first()->toArray();

        $m_bayar_hutang = new \Model\Storage\BayarHutang_model();
        $d_bayar_hutang = $m_bayar_hutang->where('id_header', $d_bayar['id'])->get();

        
        $data = null;
        if ( $d_bayar_hutang->count() > 0 ) {
            $d_bayar_hutang = $d_bayar_hutang->toArray();

            foreach ($d_bayar_hutang as $key => $value) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->with(['pesanan'])->first()->toArray();

                $data[] = array(
                    'tgl_pesan' => !empty($d_jual['pesanan']) ? $d_jual['pesanan']['tgl_pesan'] : $d_jual['tgl_trans'],
                    'faktur_kode' => $value['faktur_kode'],
                    'hutang' => $value['hutang'],
                    'sudah_bayar' => $value['sudah_bayar'],
                    'bayar' => $value['bayar']
                );
            }
        }

        return $data;
    }

    public function getDataPembayaran($kode_faktur)
    {
        $m_bayar = new \Model\Storage\Bayar_model();
        $d_bayar = $m_bayar->where('faktur_kode', $kode_faktur)->where('mstatus', 1)->first();

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

            $dataMetodeBayar = null;
            $dataHutangBayar = null;
            
            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('faktur_kode', $kode_faktur)->where('mstatus', 1)->with(['bayar_det', 'bayar_hutang'])->first();

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
                            'faktur_kode' => $kode_faktur,
                            'hutang' => $v_bh['hutang'],
                            'sudah_bayar' => $v_bh['sudah_bayar'],
                            'bayar' => $v_bh['bayar']
                        );
                    }
                }
            }

            $this->result['status'] = 1;
            $this->result['content'] = array(
                'dataMetodeBayar' => $dataMetodeBayar,
                'dataHutangBayar' => $dataHutangBayar
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

    public function modalGabungBill()
    {
        $params = $this->input->get('params');

        $pesanan_kode = $params['pesanan_kode'];

        $m_pesanan_utama = new \Model\Storage\Pesanan_model();
        $d_pesanan_utama = $m_pesanan_utama->where('kode_pesanan', $pesanan_kode)->where('mstatus', 1)->with(['meja'])->first()->toArray();

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
            'kode_pesanan' => $d_pesanan_utama['kode_pesanan'],
            'member_group' => $member_group,
            'pelanggan' => $d_pesanan_utama['member'],
            'kasir' => $d_pesanan_utama['nama_user'],
            'total' => $d_pesanan_utama['grand_total']
        );

        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();
        $today = $now['tanggal'];

        $start_date = $today.' 00:00:00';
        $end_date = $today.' 23:59:59';

        $m_pesanan = new \Model\Storage\Pesanan_model();
        $d_pesanan = $m_pesanan->where('kode_pesanan', '<>', $pesanan_kode)->whereBetween('tgl_pesan', [$start_date, $end_date])->where('mstatus', 1)->with(['meja'])->get();

        $content['pesanan_kode'] = $pesanan_kode;
        $content['data_utama'] = $data_utama;
        $content['data_belum_bayar'] = $this->getDataBelumBayar( $d_pesanan );

        $html = $this->load->view($this->pathView . 'modal_gabung_bill', $content, TRUE);

        echo $html;
    }

    public function saveBillGabung()
    {
        $params = $this->input->post('params');

        try {
            $data_utama = $params['data_utama'];
            $data = $params['data'];

            $m_jual_utama = new \Model\Storage\Jual_model();
            $d_jual_utama = $m_jual_utama->where('pesanan_kode', $data_utama['kode_pesanan'])->where('mstatus', 1)->where('utama', 1)->first()->toArray();

            if ( !empty($data) ) {
                foreach ($data as $key => $value) {
                    $m_jual = new \Model\Storage\Jual_model();
                    $d_jual = $m_jual->where('pesanan_kode', $value['kode_pesanan'])->where('mstatus', 1)->where('utama', 1)->first()->toArray();

                    $m_jual_gabungan = new \Model\Storage\JualGabungan_model();
                    $m_jual_gabungan->faktur_kode = $d_jual_utama['kode_faktur'];
                    $m_jual_gabungan->faktur_kode_gabungan = $d_jual['kode_faktur'];
                    $m_jual_gabungan->jml_tagihan = $value['total'];
                    $m_jual_gabungan->save();
                }
            }

            $this->result['status'] = 1;
            $this->result['content'] = array('kode' => exEncrypt( $d_jual_utama['kode_faktur'] ));
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}