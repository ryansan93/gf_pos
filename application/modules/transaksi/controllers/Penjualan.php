<?php defined('BASEPATH') or exit('No direct script access allowed');

class Penjualan extends Public_Controller
{
    private $pathView = 'transaksi/penjualan/';
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
                    "assets/master/member_group/js/member-group.js",
                    "assets/master/member/js/member.js",
                    "assets/transaksi/penjualan/js/penjualan.js",
                    "assets/transaksi/pembayaran/js/pembayaran.js",
                    // "assets/transaksi/saldo_awal_kasir/js/saldo-awal-kasir.js",
                    // "assets/transaksi/penjualan/js/penjualan.js",
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    "assets/master/member_group/css/member-group.css",
                    "assets/master/member/css/member.css",
                    "assets/transaksi/penjualan/css/penjualan.css",
                    "assets/transaksi/pembayaran/css/pembayaran.css",
                    // "assets/transaksi/saldo_awal_kasir/css/saldo-awal-kasir.css",
                    // "assets/transaksi/penjualan/css/penjualan.css",
                )
            );
            $data = $this->includes;

            $isMobile = true;
            if ( $detect->isMobile() ) {
                $isMobile = true;
            }

            // exec("cd websocket\server && forever stopall 2>&1");
            // exec("cd websocket\server && forever start index.js 2>&1");

            $content['akses'] = $this->hakAkses;
            $content['isMobile'] = $isMobile;
            $content['persen_ppn'] = $this->getPpn( $this->kodebranch );
            $content['service_charge'] = $this->getServiceCharge( $this->kodebranch );
            $content['kode_branch'] = $this->kodebranch;

            $content['kategori'] = $this->getKategori();

            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
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

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->get();

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function getJenisPesanan()
    {
        $m_jp = new \Model\Storage\JenisPesanan_model();
        $d_jp = $m_jp->get();

        $data = null;
        if ( $d_jp->count() > 0 ) {
            $data = $d_jp->toArray();
        }

        return $data;
    }

    public function getLantai()
    {
        $m_lantai = new \Model\Storage\Lantai_model();
        $d_lantai = $m_lantai->with(['meja'])->get();

        $data = null;
        if ( $d_lantai->count() > 0 ) {
            $d_lantai = $d_lantai->toArray();
            foreach ($d_lantai as $k_lantai => $v_lantai) {
                $key_lantai = $v_lantai['nama_lantai'].' | '.$v_lantai['id'];
                $data[ $key_lantai ] = array(
                    'id' => $v_lantai['id'],
                    'nama' => $v_lantai['nama_lantai']
                );
            }
        }

        return $data;
    }

    public function listMeja()
    {
        $params = $this->input->get('params');

        $m_lantai = new \Model\Storage\Lantai_model();
        $d_lantai = $m_lantai->where('id', $params['lantai_id'])->with(['meja'])->first();

        $start_date = date('Y-m-d').' 00:00:00';
        $end_date = date('Y-m-d').' 23:59:59';

        $data = null;
        if ( $d_lantai ) {
            $d_lantai = $d_lantai->toArray();

            $key_lantai = $d_lantai['nama_lantai'].' | '.$d_lantai['id'];
            $data[ $key_lantai ] = array(
                'id' => $d_lantai['id'],
                'nama' => $d_lantai['nama_lantai'],
                'list_meja' => array()
            );
            foreach ($d_lantai['meja'] as $k_meja => $v_meja) {
                $m_mejal = new \Model\Storage\MejaLog_model();
                $d_mejal = $m_mejal->whereBetween('tgl_trans', [$start_date, $end_date])->where('meja_id', $v_meja['id'])->orderBy('tgl_trans', 'desc')->first();

                $aktif = 0;
                // if ( $d_mejal ) {
                //     $aktif = $d_mejal->status;
                // }

                $key_meja = $v_meja['nama_meja'].' | '.$v_meja['id'];
                $data[ $key_lantai ]['list_meja'][] = array(
                    'id' => $v_meja['id'],
                    'nama' => $v_meja['nama_meja'],
                    'aktif' => $aktif
                );
            }
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'list_meja', $content, TRUE);

        echo $html;
    }

    public function modalPilihBranch()
    {
        $content['branch'] = $this->getBranch();

        $html = $this->load->view($this->pathView . 'modal_pilih_branch', $content, TRUE);

        echo $html;
    }

    public function modalJenisPesanan()
    {
        $content['jenis_pesanan'] = $this->getJenisPesanan();

        $html = $this->load->view($this->pathView . 'modal_jenis_pesanan', $content, TRUE);

        echo $html;
    }

    public function modalMeja()
    {
        $content['lantai'] = $this->getLantai();

        $html = $this->load->view($this->pathView . 'modal_meja', $content, TRUE);

        echo $html;
    }

    public function modalPilihMember()
    {
        $html = $this->load->view($this->pathView . 'modal_pilih_member', null, TRUE);

        echo $html;
    }

    public function getDataMemberGroup()
    {
        $m_member_group = new \Model\Storage\MemberGroup_model();
        $d_member_group = $m_member_group->where('status', 1)->orderBy('nama', 'desc')->get();

        $data = null;
        if ( $d_member_group->count() > 0 ) {
            $data = $d_member_group->toArray();
        }

        return $data;
    }

    public function modalNonMember()
    {
        $content['member_group'] = $this->getDataMemberGroup();
        
        $html = $this->load->view($this->pathView . 'modal_non_member', $content, TRUE);

        echo $html;
    }

    public function modalMember()
    {
        $m_member = new \Model\Storage\Member_model();
        $d_member = $m_member->get();

        $data = null;
        if ( $d_member->count() > 0 ) {
            $data = $d_member->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_member', $content, TRUE);

        echo $html;
    }

    public function addMember()
    {
        $html = $this->load->view($this->pathView . 'add_member', null, TRUE);

        echo $html;
    }

    public function saveMember()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();

            $kode_member = $m_member->getNextId();

            $m_member->kode_member = $kode_member;
            $m_member->nama = $params['nama'];
            $m_member->no_telp = $params['no_telp'];
            $m_member->alamat = $params['alamat'];
            $m_member->privilege = $params['privilege'];
            $m_member->save();

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_member, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di simpan.';
            $this->result['content'] = array(
                                            'kode_member' => $d_member['kode_member'],
                                            'nama' => $d_member['nama'],
                                        );
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function getKategori()
    {
        $m_kategori_menu = new \Model\Storage\KategoriMenu_model();
        $d_kategori_menu = $m_kategori_menu->where('status', 1)->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_kategori_menu->count() > 0 ) {
            $data = $d_kategori_menu->toArray();
        }

        return $data;
    }

    public function getMenu()
    {
        $id_kategori = $this->input->get('id_kategori');
        $jenis_pesanan = $this->input->get('jenis_pesanan');
        $branch_kode = $this->input->get('branch_kode');

        $m_menu = new \Model\Storage\Menu_model();
        $sql = "
            select menu.id, menu.kode_menu, menu.nama, menu.deskripsi, hm.harga as harga_jual, menu.kategori_menu_id, count(pm.kode_paket_menu) as jml_paket from menu menu
                left join
                    (
                    select * from harga_menu where id in (
                        select max(id) as id from harga_menu group by jenis_pesanan_kode, menu_kode
                    )) hm 
                    on
                        menu.kode_menu = hm.menu_kode 
                left join
                    paket_menu pm
                    on
                        menu.kode_menu = pm.menu_kode
                where
                    menu.kategori_menu_id = ".$id_kategori." and
                    hm.jenis_pesanan_kode = '".$jenis_pesanan."' and
                    menu.branch_kode = '".trim($branch_kode)."'
            group by menu.id, menu.kode_menu, menu.nama, menu.deskripsi, hm.harga, menu.kategori_menu_id, hm.jenis_pesanan_kode
            order by menu.nama asc
        ";
        $d_menu = $m_menu->hydrateRaw($sql);

        $data = null;
        if ( $d_menu->count() > 0 ) {
            $data = $d_menu->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'list_menu', $content, TRUE);

        echo $html;
    }

    public function modalPaketMenu()
    {
        $menu_kode = $this->input->get('menu_kode');

        $m_pm = new \Model\Storage\PaketMenu_model();
        $d_pm = $m_pm->where('menu_kode', $menu_kode)->with(['isi_paket_menu'])->get();

        $data = null;
        if ( $d_pm->count() > 0 ) {
            $data = $d_pm->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_paket_menu', $content, TRUE);

        echo $html;
    }

    public function jumlahPesanan()
    {
        $html = $this->load->view($this->pathView . 'jumlah_pesanan', null, TRUE);

        echo $html;
    }

    public function modalDiskon()
    {
        $kode_member = $this->input->get('kode_member');

        $today = date('Y-m-d');

        $m_diskon = new \Model\Storage\Diskon_model();
        $d_diskon = $m_diskon->where('start_date', '<=', $today)->where('end_date', '>=', $today)->with(['detail'])->get();

        $data = null;
        if ( $d_diskon->count() > 0 ) {
            $d_diskon = $d_diskon->toArray();
            foreach ($d_diskon as $key => $value) {
                foreach ($value['detail'] as $k_det => $v_det) {
                    if ( !empty($kode_member) ) {
                        if ( $v_det['member'] == 1 ) {
                            $data[] = $d_diskon[$key];
                        }
                    } else  {
                        if ( $v_det['non_member'] == 1 ) {
                            $data[] = $d_diskon[$key];
                        }
                    }
                }
            }
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_diskon', $content, TRUE);

        echo $html;
    }

    public function modalPilihPrivilege()
    {
        $html = $this->load->view($this->pathView . 'modal_pilih_privilege', null, TRUE);

        echo $html;
    }

    public function savePesanan()
    {
        $params = $this->input->post('params');

        try {
            $m_pesanan = new \Model\Storage\Pesanan_model();
            $now = $m_pesanan->getDate();

            $waktu = $now['waktu'];

            $kode_pesanan = $m_pesanan->getNextKode($this->kodebranch);
            $m_pesanan->kode_pesanan = $kode_pesanan;
            $m_pesanan->tgl_pesan = $waktu;
            $m_pesanan->branch = $this->kodebranch;
            $m_pesanan->member = $params['member'];
            $m_pesanan->kode_member = $params['kode_member'];
            $m_pesanan->user_id = $this->userid;
            $m_pesanan->nama_user = $this->userdata['detail_user']['nama_detuser'];
            $m_pesanan->total = $params['sub_total'];
            $m_pesanan->diskon = $params['diskon'];
            $m_pesanan->ppn = $params['ppn'];
            $m_pesanan->service_charge = $params['service_charge'];
            $m_pesanan->grand_total = $params['grand_total'];
            $m_pesanan->status = 1;
            $m_pesanan->mstatus = 1;
            $m_pesanan->meja_id = $params['meja_id'];
            $m_pesanan->privilege = $params['privilege'];
            $m_pesanan->save();

            foreach ($params['list_pesanan'] as $k_lp => $v_lp) {
                foreach ($v_lp['list_menu'] as $k_lm => $v_lm) {
                    $m_pesanani = new \Model\Storage\PesananItem_model();

                    $kode_pesanan_item = $m_pesanani->getNextKode('PSI');
                    $m_pesanani->kode_pesanan_item = $kode_pesanan_item;
                    $m_pesanani->pesanan_kode = $kode_pesanan;
                    $m_pesanani->kode_jenis_pesanan = $v_lp['kode_jp'];
                    $m_pesanani->menu_nama = $v_lm['nama_menu'];
                    $m_pesanani->menu_kode = $v_lm['kode_menu'];
                    $m_pesanani->jumlah = $v_lm['jumlah'];
                    $m_pesanani->harga = $v_lm['harga'];
                    $m_pesanani->total = $v_lm['total'];
                    $m_pesanani->request = $v_lm['request'];
                    $m_pesanani->save();

                    if ( !empty($v_lm['detail_menu']) ) {
                        foreach ($v_lm['detail_menu'] as $k_dm => $v_dm) {
                            $m_pesananid = new \Model\Storage\PesananItemDetail_model();
                            $m_pesananid->pesanan_item_kode = $kode_pesanan_item;
                            $m_pesananid->menu_nama = $v_dm['nama_menu'];
                            $m_pesananid->menu_kode = $v_dm['kode_menu'];
                            $m_pesananid->jumlah = $v_dm['jumlah'];
                            $m_pesananid->save();
                        }
                    }
                }
            }

            $m_mejal = new \Model\Storage\MejaLog_model();
            $m_mejal->pesanan_kode = $kode_pesanan;
            $m_mejal->tgl_trans = $waktu;
            $m_mejal->meja_id = $params['meja_id'];
            $m_mejal->status = 1;
            $m_mejal->save();

            $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_pesanan, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['content'] = array('kode_pesanan' => $kode_pesanan);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function savePenjualan()
    {
        $params = $this->input->post('params');
        $kode_pesanan = $this->input->post('kode_pesanan');

        $result = $this->execSavePenjualan( $params, $kode_pesanan );

        display_json( $result );
    }

    public function execSavePenjualan($params, $kode_pesanan)
    {
        try {
            $m_pesanan = new \Model\Storage\Pesanan_model();
            $d_pesanan = $m_pesanan->where('kode_pesanan', $kode_pesanan)->with(['pesanan_item'])->first()->toArray();

            $m_jual = new \Model\Storage\Jual_model();
            $now = $m_jual->getDate();

            $kode_faktur = $m_jual->getNextKode('FAK');
            $m_jual->kode_faktur = $kode_faktur;
            $m_jual->tgl_trans = $now['waktu'];
            $m_jual->branch = $d_pesanan['branch'];
            $m_jual->member = $d_pesanan['member'];
            $m_jual->kode_member = $d_pesanan['kode_member'];
            $m_jual->kasir = $this->userid;
            $m_jual->nama_kasir = $this->userdata['detail_user']['nama_detuser'];
            $m_jual->total = $d_pesanan['total'];
            $m_jual->diskon = $d_pesanan['diskon'];
            $m_jual->ppn = $d_pesanan['ppn'];
            $m_jual->service_charge = $d_pesanan['service_charge'];
            $m_jual->grand_total = $d_pesanan['grand_total'];
            $m_jual->lunas = 0;
            $m_jual->mstatus = 1;
            $m_jual->pesanan_kode = $kode_pesanan;
            $m_jual->utama = 1;
            $m_jual->hutang = 0;
            $m_jual->save();

            foreach ($d_pesanan['pesanan_item'] as $k_pi => $v_pi) {
                $m_juali = new \Model\Storage\JualItem_model();

                $kode_faktur_item = $m_juali->getNextKode('FKI');
                $m_juali->kode_faktur_item = $kode_faktur_item;
                $m_juali->faktur_kode = $kode_faktur;
                $m_juali->kode_jenis_pesanan = $v_pi['kode_jenis_pesanan'];
                $m_juali->menu_nama = $v_pi['menu_nama'];
                $m_juali->menu_kode = $v_pi['menu_kode'];
                $m_juali->jumlah = $v_pi['jumlah'];
                $m_juali->harga = $v_pi['harga'];
                $m_juali->total = $v_pi['total'];
                $m_juali->request = $v_pi['request'];
                $m_juali->pesanan_item_kode = $v_pi['kode_pesanan_item'];
                $m_juali->save();

                foreach ($v_pi['pesanan_item_detail'] as $k_pid => $v_pid) {
                    $m_jualid = new \Model\Storage\JualItemDetail_model();
                    $m_jualid->faktur_item_kode = $kode_faktur_item;
                    $m_jualid->menu_nama = $v_pid['menu_nama'];
                    $m_jualid->menu_kode = $v_pid['menu_kode'];
                    $m_jualid->jumlah = $v_pid['jumlah'];
                    $m_jualid->save();
                }
            }

            // foreach ($params['list_pesanan'] as $k_lp => $v_lp) {
            //     foreach ($v_lp['list_menu'] as $k_lm => $v_lm) {
            //         $m_juali = new \Model\Storage\JualItem_model();

            //         $kode_faktur_item = $m_juali->getNextKode('FKI');
            //         $m_juali->kode_faktur_item = $kode_faktur_item;
            //         $m_juali->faktur_kode = $kode_faktur;
            //         $m_juali->kode_jenis_pesanan = $v_lp['kode_jp'];
            //         $m_juali->menu_nama = $v_lm['nama_menu'];
            //         $m_juali->menu_kode = $v_lm['kode_menu'];
            //         $m_juali->jumlah = $v_lm['jumlah'];
            //         $m_juali->harga = $v_lm['harga'];
            //         $m_juali->total = $v_lm['total'];
            //         $m_juali->request = $v_lm['request'];
            //         $m_juali->save();

            //         if ( !empty($v_lm['detail_menu']) ) {
            //             foreach ($v_lm['detail_menu'] as $k_dm => $v_dm) {
            //                 $m_jualid = new \Model\Storage\JualItemDetail_model();
            //                 $m_jualid->faktur_item_kode = $kode_faktur_item;
            //                 $m_jualid->menu_nama = $v_dm['nama_menu'];
            //                 $m_jualid->menu_kode = $v_dm['kode_menu'];
            //                 $m_jualid->jumlah = $v_dm['jumlah'];
            //                 $m_jualid->save();
            //             }
            //         }
            //     }
            // }

            if ( !empty($params['list_diskon']) ) {
                foreach ($params['list_diskon'] as $k_ld => $v_ld) {
                    $m_juald = new \Model\Storage\JualDiskon_model();
                    $m_juald->faktur_kode = $kode_faktur;
                    $m_juald->diskon_kode = $v_ld['kode_diskon'];
                    $m_juald->diskon_nama = $v_ld['nama_diskon'];
                    $m_juald->save();
                }
            }

            $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jual, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['content'] = array('kode_faktur' => $kode_faktur);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function deletePenjualan()
    {
        $params = $this->input->post('params');

        $result = $this->execDeletePenjualan( $params );

        display_json( $result );
    }

    public function execDeletePenjualan($params)
    {
        try {
            $m_jual = new \Model\Storage\Jual_model();
            $m_jual->where('kode_faktur', $params)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_jual = $m_jual->where('kode_faktur', $params)->first();
            
            $deskripsi_log_gaktifitas = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jual, $deskripsi_log_gaktifitas );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function deletePembayaran()
    {
        $params = $this->input->post('params');

        try {
            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('id', $params)->first();

            $total_bayar = $m_bayar->where('id', '<>', $params)->where('faktur_kode', $d_bayar->faktur_kode)->sum('jml_bayar');

            if ( $d_bayar->jml_tagihan > $total_bayar ) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('kode_faktur', $d_bayar->faktur_kode)->update(
                    array(
                        'lunas' => 0
                    )
                );
            }

            $m_bayar->where('id', $params)->delete();

            $deskripsi_log_gaktifitas = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_bayar, $deskripsi_log_gaktifitas );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalPilihBayar()
    {
        $content['data'] = null;

        $html = $this->load->view($this->pathView . 'modal_pilih_bayar', $content, TRUE);

        echo $html;
    }

    public function modalPembayaran()
    {
        $content['data'] = null;

        $html = $this->load->view($this->pathView . 'modal_pembayaran', $content, TRUE);

        echo $html;
    }

    public function modalJenisKartu()
    {
        $m_jenis_kartu = new \Model\Storage\JenisKartu_model();
        $_d_jenis_kartu = $m_jenis_kartu->where('status', 1)->get();

        $d_jenis_kartu = null;
        if ( $_d_jenis_kartu->count() > 0 ) {
            $d_jenis_kartu = $_d_jenis_kartu->toArray();
        }

        $content['data'] = $d_jenis_kartu;

        $html = $this->load->view($this->pathView . 'modal_jenis_kartu', $content, TRUE);

        echo $html;
    }

    public function jumlahBayar()
    {
        $html = $this->load->view($this->pathView . 'jumlah_bayar', null, TRUE);

        echo $html;
    }

    public function noBuktiKartu()
    {
        $html = $this->load->view($this->pathView . 'no_bukti_kartu', null, TRUE);

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
            $m_bayar->jenis_bayar = $params['jenis_bayar'];
            $m_bayar->jenis_kartu_kode = $params['jenis_kartu_kode'];
            $m_bayar->no_bukti = $params['no_bukti'];
            $m_bayar->save();

            if ( $params['jml_bayar'] >= $params['sisa_tagihan'] ) {
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

    public function getDataNota($kode_faktur)
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

    public function getDataCheckList($kode_faktur)
    {
        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->with(['jual_item', 'bayar'])->first()->toArray();

        $data = null;
        $jenis_pesanan = null;
        foreach ($d_jual['jual_item'] as $k_ji => $v_ji) {  
            $key = $v_ji['jenis_pesanan'][0]['nama'].' | '.$v_ji['jenis_pesanan'][0]['kode'];
            $key_item = $v_ji['kode_faktur_item'].' | '.$v_ji['menu_nama'].' | '.$v_ji['menu_kode'];

            $jual_item_detail = null;
            foreach ($v_ji['jual_item_detail'] as $k_jid => $v_jid) {
                $jual_item_detail[ $v_jid['menu_kode'] ] = array(
                    'menu_kode' => $v_jid['menu_kode'],
                    'menu_nama' => $v_jid['menu_nama']
                );
            }

            if ( !isset($jenis_pesanan[$key]) ) {
                $jual_item = null;
                $jual_item[ $key_item ] = array(
                    'nama' => $v_ji['menu_nama'],
                    'jumlah' => $v_ji['jumlah'],
                    'total' => $v_ji['total'],
                    'detail' => $jual_item_detail
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
                        'total' => $v_ji['total'],
                        'detail' => $jual_item_detail
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

    public function printNota()
    {
        $params = $this->input->post('params');

        if ( $this->config->item('paper_size') == '58' ) {
            $result = $this->printNota58($params);
        } else {
            $result = $this->printNota80($params);
        }

        display_json( $result );
    }

    public function printNota58($params)
    {
        // $params = json_decode($this->input->post('params'), 1);
        try {
            $data = $this->getDataNota( $params );

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("COD\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("FRIED CHICKEN\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> text($this->alamatbranch."\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> text("Telp. ".$this->telpbranch."\n\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%5.40s %1.05s %13.40s','No. Transaksi',':', $data['kode_faktur']);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13.5s %1.05s %-13.40s','Kasir',':', $this->userdata['detail_user']['nama_detuser']);
            $printer -> text("$lineKasir\n");

            if ( $this->config->item('print_jenis_bayar') == 1 ) {
                $jenis_bayar = ($data['bayar'][0]['jenis_bayar'] == 'tunai') ? 'TUNAI' : $data['bayar'][ count($data['bayar']) -1 ]['jenis_kartu']['nama'];
                $lineBayar = sprintf('%-13.5s %1.05s %-13.40s','Bayar',':', $jenis_bayar);
                $printer -> text("$lineBayar\n");
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("================================\n");
            // $printer -> textRaw("--------------------------------\n");
            foreach ($data['jenis_pesanan'] as $k_jp => $v_jp) {
                // $printer = new Mike42\Escpos\Printer($connector);
                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                $printer -> textRaw($v_jp['nama']."\n");

                foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                    /* NOTE : TABLE
                    $line = sprintf('%-13.40s %3.0f %-3.40s %9.40s %-2.40s %13.40s',$row['item_code'] , $row['item_qty'], $row['kali'], $n1,$row['hasil'], $n2); 
                    */
                    $line = sprintf('%-28s %13.40s',$v_ji['nama'].' @ '.angkaRibuan($v_ji['jumlah']), angkaDecimal($v_ji['total']));
                    $printer -> text("$line\n");
                }
            }
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineTotal = sprintf('%18s %13.40s','Total Belanja. =', angkaDecimal($data['total']));
            $printer -> text("$lineTotal\n");
            // $lineTotal = sprintf('%18s %13.40s','PPN (11%).','=', angkaDecimal($data['ppn']));
            // $printer -> text("$lineTotal\n");
            $lineDisc = sprintf('%18s %13.40s','Disc. =', '('.angkaDecimal($data['diskon']).')');
            $printer -> text("$lineDisc\n");
            $lineTotal = sprintf('%18s %13.40s','Total Bayar. =', angkaDecimal($data['grand_total']));
            $printer -> text("$lineTotal\n");
            $lineTunai = sprintf('%18s %13.40s','Uang Tunai. =', angkaDecimal($data['bayar'][ count($data['bayar']) -1 ]['jml_bayar']));
            $printer -> text("$lineTunai\n");
            $lineKembalian = sprintf('%18s %13.40s','Kembalian. =', angkaDecimal($data['bayar'][ count($data['bayar']) -1 ]['jml_bayar'] - $data['grand_total']));
            $printer -> text("$lineKembalian\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("--------------------------------\n");

            // $printer = new Mike42\Escpos\Printer($connector);
            // $printer -> setJustification(1);
            // $printer -> selectPrintMode(1);
            // $printer -> textRaw("Kalau Tidak Bisa Ambil Hatinya\n");

            // $printer = new Mike42\Escpos\Printer($connector);
            // $printer -> setJustification(1);
            // $printer -> selectPrintMode(1);
            // $printer -> textRaw("Ambil Saja Hikmahnya :D :D :D\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> textRaw("Selamat Menikmati\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> textRaw("*** TERIMA KASIH ***\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            // $printer -> textRaw($data['bayar'][ count($data['bayar']) -1 ]['tgl_trans']."\n");

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $printer -> textRaw($now['waktu']."\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        return $this->result;
    }

    public function printNota80($params)
    {
        // $params = json_decode($this->input->post('params'), 1);
        try {
            $data = $this->getDataNota( $params );

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("COD\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("FRIED CHICKEN\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> text($this->alamatbranch."\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> text("Telp. ".$this->telpbranch."\n\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%5.40s %1.05s %13.40s','No. Transaksi',':', $data['kode_faktur']);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13.5s %1.05s %-13.40s','Kasir',':', $this->userdata['detail_user']['nama_detuser']);
            $printer -> text("$lineKasir\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("==========================================\n");
            // $printer -> textRaw("--------------------------------\n");
            foreach ($data['jenis_pesanan'] as $k_jp => $v_jp) {
                // $printer = new Mike42\Escpos\Printer($connector);
                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                $printer -> textRaw($v_jp['nama']."\n");

                foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                    /* NOTE : TABLE
                    $line = sprintf('%-13.40s %3.0f %-3.40s %9.40s %-2.40s %13.40s',$row['item_code'] , $row['item_qty'], $row['kali'], $n1,$row['hasil'], $n2); 
                    */
                    $line = sprintf('%-46s %13.40s',$v_ji['nama'].' @ '.angkaRibuan($v_ji['jumlah']), angkaDecimal($v_ji['total']));
                    $printer -> text("$line\n");
                }
            }
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("------------------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineTotal = sprintf('%46s %13.40s','Total Belanja. =', angkaDecimal($data['total']));
            $printer -> text("$lineTotal\n");
            // $lineTotal = sprintf('%46s %13.40s','PPN (11%).','=', angkaDecimal($data['ppn']));
            // $printer -> text("$lineTotal\n");
            $lineDisc = sprintf('%46s %13.40s','Disc. =', '('.angkaDecimal($data['diskon']).')');
            $printer -> text("$lineDisc\n");
            $lineTotal = sprintf('%46s %13.40s','Total Bayar. =', angkaDecimal($data['grand_total']));
            $printer -> text("$lineTotal\n");
            $lineTunai = sprintf('%46s %13.40s','Uang Tunai. =', angkaDecimal($data['bayar'][ count($data['bayar']) -1 ]['jml_bayar']));
            $printer -> text("$lineTunai\n");
            $lineKembalian = sprintf('%46s %13.40s','Kembalian. =', angkaDecimal($data['bayar'][ count($data['bayar']) -1 ]['jml_bayar'] - $data['grand_total']));
            $printer -> text("$lineKembalian\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("------------------------------------------\n");

            // $printer = new Mike42\Escpos\Printer($connector);
            // $printer -> setJustification(1);
            // $printer -> selectPrintMode(1);
            // $printer -> textRaw("Kalau Tidak Bisa Ambil Hatinya\n");

            // $printer = new Mike42\Escpos\Printer($connector);
            // $printer -> setJustification(1);
            // $printer -> selectPrintMode(1);
            // $printer -> textRaw("Ambil Saja Hikmahnya :D :D :D\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> textRaw("Selamat Menikmati\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> textRaw("*** TERIMA KASIH ***\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            // $printer -> textRaw($data['bayar'][ count($data['bayar']) -1 ]['tgl_trans']."\n");

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $printer -> textRaw($now['waktu']."\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        return $this->result;
    }

    public function printCheckList()
    {
        $params = $this->input->post('params');

        if ( $this->config->item('paper_size') == '58' ) {
            $result = $this->printCheckList58($params);
        } else {
            $result = $this->printCheckList80($params);
        }

        display_json( $result );
    }

    public function printCheckList58($params)
    {
        // $params = json_decode($this->input->post('params'), 1);
        // $params = $this->input->post('params');

        try {
            $data = $this->getDataCheckList( $params );

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("CHECK LIST ORDER\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%-13s %1.05s %-15s','No. Transaksi',':', $data['kode_faktur']);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13s %1.05s %-15s','Pelanggan',':', $data['member']);
            $printer -> text("$lineKasir\n");

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $lineTanggal = sprintf('%-13s %1.05s %-15s','Tanggal',':', $now['waktu']);
            $printer -> text("$lineTanggal\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("\n================================\n\n");
            // $printer -> textRaw("--------------------------------\n");
            foreach ($data['jenis_pesanan'] as $k_jp => $v_jp) {
                // $printer = new Mike42\Escpos\Printer($connector);
                $printer -> setJustification(0);
                $printer -> selectPrintMode(0);
                $printer -> textRaw($v_jp['nama']."\n");

                foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                    /* NOTE : TABLE
                    $line = sprintf('%-13.40s %3.0f %-3.40s %9.40s %-2.40s %13.40s',$row['item_code'] , $row['item_qty'], $row['kali'], $n1,$row['hasil'], $n2); 
                    */
                    $line = sprintf('%0s %20s',$v_ji['nama'], angkaRibuan($v_ji['jumlah']).' x');
                    $printer -> selectPrintMode(0);
                    $printer -> text("$line\n");

                    if ( !empty($v_ji['detail']) ) {
                        foreach ($v_ji['detail'] as $k_det => $v_det) {
                            $line_detail = sprintf('%2s %13s','', $v_det['menu_nama']);
                            $printer -> selectPrintMode(1);
                            $printer -> text("$line_detail\n");
                        }
                    }
                }
            }
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function printCheckList80($params)
    {
        // $params = json_decode($this->input->post('params'), 1);
        // $params = $this->input->post('params');

        try {
            $data = $this->getDataCheckList( $params );

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("CHECK LIST ORDER\n\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%-13s %1.05s %-15s','No. Transaksi',':', $data['kode_faktur']);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13s %1.05s %-15s','Pelanggan',':', $data['member']);
            $printer -> text("$lineKasir\n");

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $lineTanggal = sprintf('%-13s %1.05s %-15s','Tanggal',':', $now['waktu']);
            $printer -> text("$lineTanggal\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("\n==========================================\n\n");
            // $printer -> textRaw("--------------------------------\n");
            foreach ($data['jenis_pesanan'] as $k_jp => $v_jp) {
                // $printer = new Mike42\Escpos\Printer($connector);
                $printer -> setJustification(0);
                $printer -> selectPrintMode(0);
                $printer -> textRaw($v_jp['nama']."\n");

                foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                    /* NOTE : TABLE
                    $line = sprintf('%-13.40s %3.0f %-3.40s %9.40s %-2.40s %13.40s',$row['item_code'] , $row['item_qty'], $row['kali'], $n1,$row['hasil'], $n2); 
                    */
                    $line = sprintf('%-28s %13.40s',$v_ji['nama'], angkaRibuan($v_ji['jumlah']).' x');
                    $printer -> selectPrintMode(0);
                    $printer -> text("$line\n");

                    if ( !empty($v_ji['detail']) ) {
                        foreach ($v_ji['detail'] as $k_det => $v_det) {
                            $line_detail = sprintf('%2s %13s','', $v_det['menu_nama']);
                            $printer -> selectPrintMode(1);
                            $printer -> text("$line_detail\n");
                        }
                    }
                }
            }
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("------------------------------------------\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        return $this->result;
    }

    public function modalListBayar()
    {
        try {
            $today = date('Y-m-d');
            // $today = '2022-09-15';

            $start_date = $today.' 00:00:00';
            $end_date = $today.' 23:59:59';

            $kasir = $this->userid;
            // $kasir = 'USR2207003';

            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->whereBetween('tgl_trans', [$start_date, $end_date])->where('kasir', $kasir)->where('mstatus', 1)->with(['jual_item', 'jual_diskon', 'bayar'])->get();

            $data_bayar = ($d_jual->count() > 0) ? $this->getDataBayar($d_jual) : null;
            $data_belum_bayar = ($d_jual->count() > 0) ? $this->getDataBelumBayar($d_jual) : null;

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

    public function modalDetailFaktur()
    {
        $kode_faktur = $this->input->post('kode_faktur');

        try {
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->where('mstatus', 1)->with(['jual_item', 'jual_diskon', 'bayar'])->first()->toArray();

            $content['data'] = $d_jual;

            $html = $this->load->view($this->pathView . 'modal_detail_faktur', $content, TRUE);

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
            if ( $v_data['lunas'] == 1 ) {
                $stts_salah_bayar = false;
                $salah_bayar = 0;
                if ( !empty($v_data['bayar']) ) {
                    foreach ($v_data['bayar'] as $k_bayar => $v_bayar) {
                        if ( $v_bayar['jml_tagihan'] >= $v_bayar['jml_bayar'] ) {
                            $stts_salah_bayar = true;
                        } else {
                            $stts_salah_bayar = false;
                        }

                        $salah_bayar += $v_bayar['jml_bayar'];
                    }
                }

                $data[ $v_data['kode_faktur'] ] = array(
                    'kode_faktur' => $v_data['kode_faktur'],
                    'pelanggan' => $v_data['member'],
                    'total' => $v_data['grand_total'],
                    'salah_bayar' => ($stts_salah_bayar == true && $salah_bayar > 0) ? $salah_bayar - $v_data['grand_total'] : 0
                );
            }
        }

        return $data;
    }

    public function getDataBelumBayar($_data)
    {
        $data = null;
        foreach ($_data as $k_data => $v_data) {
            if ( $v_data['lunas'] == 0 ) {
                $kurang_bayar = 0;
                if ( !empty($v_data['bayar']) ) {
                    foreach ($v_data['bayar'] as $k_bayar => $v_bayar) {
                        $kurang_bayar += $v_bayar['jml_bayar'];
                    }
                }

                $data[ $v_data['kode_faktur'] ] = array(
                    'kode_faktur' => $v_data['kode_faktur'],
                    'pelanggan' => $v_data['member'],
                    'total' => $v_data['grand_total'],
                    'kurang_bayar' => $v_data['grand_total'] - $kurang_bayar
                );
            }
        }

        return $data;
    }

    public function modalHelp()
    {
        $html = $this->load->view($this->pathView . 'modal_help', null, TRUE);

        echo $html;
    }

    public function printTes()
    {
        try {
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("\n\nPRINT TEST\n\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function cekPinOtorisasi()
    {
        $pin = $this->input->post('pin');

        try {
            $m_po = new \Model\Storage\PinOtorisasi_model();
            $d_po = $m_po->where('pin', $pin)->where('status', 1)->first();

            if ( $d_po ) {
                $this->result['status'] = 1;
            } else {
                $this->result['message'] = "PIN Otorisasi yang anda masukkan tidak di temukan.";
            }
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function getDataClosingShift($tanggal, $kasir)
    {
        // $tanggal = '2022-09-15';
        // $kasir = 'USR2207003';

        $start_date = substr($tanggal, 0, 10).' 00:00:00';
        $end_date = substr($tanggal, 0, 10).' 23:59:59';

        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->whereBetween('tgl_trans', [$start_date, $end_date])->where('kasir', $kasir)->with(['jual_item', 'bayar'])->get();

        $data = null;
        $data_detail_transaksi = null;
        $data_detail_pembayaran = null;
        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            $data_detail_transaksi['grand_total'] = 0;
            $data_detail_transaksi['grand_total_jumlah'] = 0;
            $data_detail_pembayaran['grand_total'] = 0;

            $data_detail_transaksi['detail']['item_terjual']['nama'] = 'item terjual';
            $data_detail_transaksi['detail']['item_terjual']['jumlah'] = 0;
            $data_detail_transaksi['detail']['item_terjual']['total'] = 0;

            $data_detail_transaksi['detail']['item_belum_bayar']['nama'] = 'item belum bayar';
            $data_detail_transaksi['detail']['item_belum_bayar']['jumlah'] = 0;
            $data_detail_transaksi['detail']['item_belum_bayar']['total'] = 0;

            $data_detail_transaksi['detail']['item_batal']['nama'] = 'item batal';
            $data_detail_transaksi['detail']['item_batal']['jumlah'] = 0;
            $data_detail_transaksi['detail']['item_batal']['total'] = 0;
            foreach ($d_jual as $k_jual => $v_jual) {
                if ( !empty($v_jual['bayar']) ) {
                    foreach ($v_jual['bayar'] as $k_bayar => $v_bayar) {
                        if ( $v_jual['mstatus'] == 1 && $v_jual['lunas'] == 1 ) {
                            if ( $v_bayar['jml_tagihan'] <= $v_bayar['jml_bayar'] ) {
                                if ( $v_bayar['jenis_bayar'] == 'tunai' ) {
                                    $key_bayar = $v_bayar['jenis_bayar'];
                                    if ( !isset( $data_detail_pembayaran['detail'][ $key_bayar ] ) ) {
                                        $data_detail_pembayaran['detail'][ $key_bayar ] = array(
                                            'nama' => 'TUNAI',
                                            'bayar' => $v_bayar['jml_bayar'],
                                            'tagihan' => $v_bayar['jml_tagihan'],
                                            'kembalian' => $v_bayar['jml_bayar'] - $v_bayar['jml_tagihan']
                                        );
                                    } else {
                                        $data_detail_pembayaran['detail'][ $key_bayar ]['bayar'] += $v_bayar['jml_bayar'];
                                        $data_detail_pembayaran['detail'][ $key_bayar ]['tagihan'] += $v_bayar['jml_tagihan'];
                                        $data_detail_pembayaran['detail'][ $key_bayar ]['kembalian'] += $v_bayar['jml_bayar'] - $v_bayar['jml_tagihan'];
                                    }
                                } else {
                                    $key_bayar = $v_bayar['jenis_bayar'].' | '.$v_bayar['jenis_kartu_kode'];
                                    if ( !isset( $data_detail_pembayaran['detail'][ $key_bayar ] ) ) {
                                        $data_detail_pembayaran['detail'][ $key_bayar ] = array(
                                            'nama' => $v_bayar['jenis_kartu']['nama'],
                                            'bayar' => $v_bayar['jml_bayar']
                                        );
                                    } else {
                                        $data_detail_pembayaran['detail'][ $key_bayar ]['bayar'] += $v_bayar['jml_bayar'];
                                    }
                                }

                                $data_detail_pembayaran['grand_total'] += $v_bayar['jml_tagihan'];
                            }
                        }
                    }
                }

                foreach ($v_jual['jual_item'] as $k_ji => $v_ji) {
                    // LUNAS
                    if ( $v_jual['mstatus'] == 1 ) {
                        if ( !isset($data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ]) ) {
                            $data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total']
                            );
                        } else {
                            $data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ]['jumlah'] += $v_ji['jumlah'];
                            $data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ]['total'] += $v_ji['total'];
                        }
                        $data_detail_transaksi['detail']['item_terjual']['jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['detail']['item_terjual']['total'] += $v_ji['total'];

                        $data_detail_transaksi['grand_total_jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['grand_total'] += $v_ji['total'];
                    }
                    // BELUM LUNAS
                    if ( $v_jual['mstatus'] == 1 && $v_jual['lunas'] == 0 ) {
                        if ( !isset($data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ]) ) {
                            $data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total']
                            );
                        } else {
                            $data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ]['jumlah'] += $v_ji['jumlah'];
                            $data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ]['total'] += $v_ji['total'];
                        }
                        $data_detail_transaksi['detail']['item_belum_bayar']['jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['detail']['item_belum_bayar']['total'] += $v_ji['total'];

                        $data_detail_transaksi['grand_total_jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['grand_total'] += $v_ji['total'];
                    }
                    // BATAL
                    if ( $v_jual['mstatus'] == 0 ) {
                        if ( !isset($data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ]) ) {
                            $data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total']
                            );
                        } else {
                            $data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ]['jumlah'] += $v_ji['jumlah'];
                            $data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ]['total'] += $v_ji['total'];
                        }
                        $data_detail_transaksi['detail']['item_batal']['jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['detail']['item_batal']['total'] += $v_ji['total'];
                    }
                }
            }
        }

        $data = array(
            'detail_transaksi' => $data_detail_transaksi,
            'detail_pembayaran' => $data_detail_pembayaran
        );

        return $data;
    }

    public function printClosingShift()
    {
        if ( $this->config->item('paper_size') == '58' ) {
            $result = $this->printClosingShift58();
        } else {
            $result = $this->printClosingShift80();
        }

        display_json( $result );
    }

    public function printClosingShift58()
    {
        try {
            $data = $this->getDataClosingShift( date('Y-m-d'), $this->userid );

            $nama_user = $this->userdata['detail_user']['nama_detuser'];

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> text("LAPORAN SHIFT\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%-13s %1.05s %-15s','Kasir',':', $nama_user);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13s %1.05s %-15s','Tanggal',':', $now['waktu']);
            $printer -> text("$lineKasir\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL TRANSAKSI\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("================================\n");

            foreach ($data['detail_transaksi']['detail'] as $k_data => $v_dt) {
                $total = 0;
                $jumlah = 0;

                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                $printer -> textRaw(strtoupper($v_dt['nama'])."\n");

                if ( isset($v_dt['detail']) ) {
                    foreach ($v_dt['detail'] as $k_det => $v_det) {
                        $line1 = sprintf('%-28s %13.40s', $v_det['nama'], '');
                        $printer -> text("$line1\n");
                        $line2 = sprintf('%-28s %13.40s', angkaRibuan($v_det['jumlah']), angkaDecimal($v_det['total']));
                        $printer -> text("$line2\n");

                        $total += $v_det['total'];
                        $jumlah += $v_det['jumlah'];
                    }

                    $printer -> setJustification(1);
                    $printer -> selectPrintMode(8);
                    $printer -> text("--------------------------------\n");
                    $printer -> setJustification(0);
                    $printer -> selectPrintMode(1);
                    $line_total = sprintf('%28s %13.40s', 'TOTAL ('.angkaRibuan($jumlah).')', angkaDecimal($total));
                    $printer -> text("$line_total\n");
                }

                $printer -> textRaw("\n");
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%28s %13.40s','GRAND TOTAL ('.angkaRibuan($data['detail_transaksi']['grand_total_jumlah']).')', angkaDecimal($data['detail_transaksi']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL PEMBAYARAN\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("================================\n");

            foreach ($data['detail_pembayaran']['detail'] as $k_dp => $v_dp) {
                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                if ( stristr($k_dp, 'tunai') !== FALSE ) {
                    // $printer -> textRaw(strtoupper($v_dp['nama'])."\n");

                    // if ( isset($v_dp) ) {
                    //     foreach ($v_dp as $k_det => $v_det) {
                    //         if ( stristr($k_det, 'nama') === FALSE ) {
                    //             $line = sprintf('%-28s %13.40s', strtoupper($k_det), angkaDecimal($v_det));
                    //             $printer -> text("$line\n");
                    //         }
                    //     }
                    // }
                    $line = sprintf('%-28s %13.40s', strtoupper($v_dp['nama']), angkaDecimal($v_dp['tagihan']));
                    $printer -> text("$line\n");
                } else {
                    $line = sprintf('%-28s %13.40s', strtoupper($v_dp['nama']), angkaDecimal($v_dp['bayar']));
                    $printer -> text("$line\n");
                }

                $printer -> textRaw("\n");
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%28s %13.40s','GRAND TOTAL', angkaDecimal($data['detail_pembayaran']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        return $this->result;
    }

    public function printClosingShift80()
    {
        try {
            $data = $this->getDataClosingShift( date('Y-m-d'), $this->userid );

            $nama_user = $this->userdata['detail_user']['nama_detuser'];

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> text("LAPORAN SHIFT\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%-13s %1.05s %-15s','Kasir',':', $nama_user);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13s %1.05s %-15s','Tanggal',':', $now['waktu']);
            $printer -> text("$lineKasir\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL TRANSAKSI\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("==========================================\n");

            foreach ($data['detail_transaksi']['detail'] as $k_data => $v_dt) {
                $total = 0;
                $jumlah = 0;

                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                $printer -> textRaw(strtoupper($v_dt['nama'])."\n");

                if ( isset($v_dt['detail']) ) {
                    foreach ($v_dt['detail'] as $k_det => $v_det) {
                        $line1 = sprintf('%-46s %13.40s', $v_det['nama'], '');
                        $printer -> text("$line1\n");
                        $line2 = sprintf('%-46s %13.40s', angkaRibuan($v_det['jumlah']), angkaDecimal($v_det['total']));
                        $printer -> text("$line2\n");

                        $total += $v_det['total'];
                        $jumlah += $v_det['jumlah'];
                    }

                    $printer -> setJustification(1);
                    $printer -> selectPrintMode(8);
                    $printer -> text("------------------------------------------\n");
                    $printer -> setJustification(0);
                    $printer -> selectPrintMode(1);
                    $line_total = sprintf('%46s %13.40s', 'TOTAL ('.angkaRibuan($jumlah).')', angkaDecimal($total));
                    $printer -> text("$line_total\n");
                }

                $printer -> textRaw("\n");
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("------------------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%46s %13.40s','GRAND TOTAL ('.angkaRibuan($data['detail_transaksi']['grand_total_jumlah']).')', angkaDecimal($data['detail_transaksi']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL PEMBAYARAN\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("==========================================\n");

            foreach ($data['detail_pembayaran']['detail'] as $k_dp => $v_dp) {
                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                if ( stristr($k_dp, 'tunai') !== FALSE ) {
                    // $printer -> textRaw(strtoupper($v_dp['nama'])."\n");

                    // if ( isset($v_dp) ) {
                    //     foreach ($v_dp as $k_det => $v_det) {
                    //         if ( stristr($k_det, 'nama') === FALSE ) {
                    //             $line = sprintf('%-28s %13.40s', strtoupper($k_det), angkaDecimal($v_det));
                    //             $printer -> text("$line\n");
                    //         }
                    //     }
                    // }
                    $line = sprintf('%-46s %13.40s', strtoupper($v_dp['nama']), angkaDecimal($v_dp['tagihan']));
                    $printer -> text("$line\n");
                } else {
                    $line = sprintf('%-46s %13.40s', strtoupper($v_dp['nama']), angkaDecimal($v_dp['bayar']));
                    $printer -> text("$line\n");
                }

                $printer -> textRaw("\n");
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("------------------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%28s %13.40s','GRAND TOTAL', angkaDecimal($data['detail_pembayaran']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        return $this->result;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_pesanan = new \Model\Storage\Pesanan_model();
            $d_pesanan = $m_pesanan->where('kode_pesanan', $params['pesanan_kode'])->with(['pesanan_item', 'meja'])->first();

            $jenis_pesanan = null;
            $nama_jenis_pesanan = null;

            $kode_member = null;
            $member = null;

            $meja_id = null;
            $meja = null;

            $data = null;
            if ( $d_pesanan ) {
                $d_pesanan = $d_pesanan->toArray();

                $pesanan_item = null;
                foreach ($d_pesanan['pesanan_item'] as $k_ji => $v_ji) {
                    $jenis_pesanan = $v_ji['kode_jenis_pesanan'];
                    $nama_jenis_pesanan = $v_ji['jenis_pesanan'][0]['nama'];

                    $key_jp = $v_ji['kode_jenis_pesanan'];
                    $pesanan_item[$key_jp]['kode'] = $v_ji['kode_jenis_pesanan'];
                    $pesanan_item[$key_jp]['nama'] = $v_ji['jenis_pesanan'][0]['nama'];

                    $key_ji = $k_ji;
                    $pesanan_item[$key_jp]['detail'][$key_ji] = array(
                        'kode_pesanan_item' => $v_ji['kode_pesanan_item'],
                        'pesanan_kode' => $v_ji['pesanan_kode'],
                        'kode_jenis_pesanan' => $v_ji['kode_jenis_pesanan'],
                        'menu_nama' => $v_ji['menu_nama'],
                        'menu_kode' => $v_ji['menu_kode'],
                        'jumlah' => $v_ji['jumlah'],
                        'harga' => $v_ji['harga'],
                        'total' => $v_ji['total'],
                        'request' => $v_ji['request'],
                        'pesanan_item_detail' => $v_ji['pesanan_item_detail'],
                        'proses' => $v_ji['proses']
                    );
                }
                $pesanan_diskon = null;

                $kode_member = $d_pesanan['kode_member'];
                $member = $d_pesanan['member'];

                $meja_id = $d_pesanan['meja']['id'];
                $meja = $d_pesanan['meja']['lantai']['nama_lantai'].' - '.$d_pesanan['meja']['nama_meja'];

                $data = array(
                    'kode_pesanan' => $d_pesanan['kode_pesanan'],
                    'tgl_pesan' => $d_pesanan['tgl_pesan'],
                    'branch' => $d_pesanan['branch'],
                    'member' => $d_pesanan['member'],
                    'kode_member' => $d_pesanan['kode_member'],
                    'user_id' => $d_pesanan['user_id'],
                    'nama_user' => $d_pesanan['nama_user'],
                    'total' => $d_pesanan['total'],
                    'diskon' => $d_pesanan['diskon'],
                    'ppn' => $d_pesanan['ppn'],
                    'grand_total' => $d_pesanan['grand_total'],
                    'status' => $d_pesanan['status'],
                    'mstatus' => $d_pesanan['mstatus'],
                    'pesanan_item' => $pesanan_item,
                    'pesanan_diskon' => $pesanan_diskon
                );
            }

            $content['data'] = $data;

            $html = $this->load->view($this->pathView . 'detail_pesanan', $content, TRUE);

            $content = array(
                'html' => $html,
                'pesanan_kode' => $data['kode_pesanan'],
                'jenis_pesanan' => $jenis_pesanan,
                'nama_jenis_pesanan' => $nama_jenis_pesanan,
                'kode_member' => $kode_member,
                'member' => $member,
                'meja_id' => $meja_id,
                'meja' => $meja
            );

            $this->result['status'] = 1;
            $this->result['content'] = $content;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function editPesanan()
    {
        $params = $this->input->post('params');

        try {
            $kode_pesanan = $params['pesanan_kode'];

            $m_pesanan = new \Model\Storage\Pesanan_model();
            $now = $m_pesanan->getDate();

            $d_pesanan = $m_pesanan->where('kode_pesanan', $kode_pesanan)->first();

            $m_pesanan->where('kode_pesanan', $kode_pesanan)->update(
                array(
                    'branch' => $this->kodebranch,
                    'member' => $params['member'],
                    'kode_member' => $params['kode_member'],
                    'user_id' => $this->userid,
                    'nama_user' => $this->userdata['detail_user']['nama_detuser'],
                    'total' => $params['sub_total'],
                    'diskon' => $params['diskon'],
                    'ppn' => $params['ppn'],
                    'service_charge' => $params['service_charge'],
                    'grand_total' => $params['grand_total'],
                    'meja_id' => $params['meja_id'],
                    'mstatus' => 1,
                    'privilege' => $params['privilege'],
                )
            );

            $m_pesanani = new \Model\Storage\PesananItem_model();
            $d_pesanani = $m_pesanani->select('kode_pesanan_item')->where('pesanan_kode', $kode_pesanan)->get()->toArray();

            $m_pesananid = new \Model\Storage\PesananItemDetail_model();
            $m_pesananid->whereIn('pesanan_item_kode', $d_pesanani)->delete();
            $m_pesanani->where('pesanan_kode', $kode_pesanan)->delete();

            foreach ($params['list_pesanan'] as $k_lp => $v_lp) {
                foreach ($v_lp['list_menu'] as $k_lm => $v_lm) {
                    $m_pesanani = new \Model\Storage\PesananItem_model();

                    $kode_pesanan_item = $m_pesanani->getNextKode('FKI');
                    $m_pesanani->kode_pesanan_item = $kode_pesanan_item;
                    $m_pesanani->pesanan_kode = $kode_pesanan;
                    $m_pesanani->kode_jenis_pesanan = $v_lp['kode_jp'];
                    $m_pesanani->menu_nama = $v_lm['nama_menu'];
                    $m_pesanani->menu_kode = $v_lm['kode_menu'];
                    $m_pesanani->jumlah = $v_lm['jumlah'];
                    $m_pesanani->harga = $v_lm['harga'];
                    $m_pesanani->total = $v_lm['total'];
                    $m_pesanani->request = $v_lm['request'];
                    $m_pesanani->proses = isset($v_lm['proses']) ? $v_lm['proses'] : null;
                    $m_pesanani->save();

                    if ( !empty($v_lm['detail_menu']) ) {
                        foreach ($v_lm['detail_menu'] as $k_dm => $v_dm) {
                            $m_pesananid = new \Model\Storage\PesananItemDetail_model();
                            $m_pesananid->pesanan_item_kode = $kode_pesanan_item;
                            $m_pesananid->menu_nama = $v_dm['nama_menu'];
                            $m_pesananid->menu_kode = $v_dm['kode_menu'];
                            $m_pesananid->jumlah = $v_dm['jumlah'];
                            $m_pesananid->save();
                        }
                    }
                }
            }

            if ( !empty($params['list_diskon']) ) {
                $m_pesanand = new \Model\Storage\PesananDiskon_model();
                $m_pesanand->where('pesanan_kode', $kode_pesanan)->delete();

                foreach ($params['list_diskon'] as $k_ld => $v_ld) {
                    $m_pesanand = new \Model\Storage\PesananDiskon_model();
                    $m_pesanand->pesanan_kode = $kode_pesanan;
                    $m_pesanand->diskon_kode = $v_ld['kode_diskon'];
                    $m_pesanand->diskon_nama = $v_ld['nama_diskon'];
                    $m_pesanand->save();
                }
            }

            if ( !empty($params['waste']) ) {
                $m_waste = new \Model\Storage\Waste_model();
                foreach ($params['waste'] as $k_waste => $v_waste) {
                    $m_waste = new \Model\Storage\PesananDiskon_model();
                    $m_waste->pesanan_kode = $kode_pesanan;
                    $m_waste->menu_kode = $v_ld['menu_kode'];
                    $m_waste->jumlah = $v_ld['jumlah'];
                    $m_waste->save();
                }
            }

            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->where('pesanan_kode', $kode_pesanan)->get();

            if ( $d_jual->count() > 0 ) {
                $d_jual = $d_jual->toArray();

                foreach ($d_jual as $k_jual => $v_jual) {
                    $this->execDeletePenjualan( $v_jual['kode_faktur'] );
                }
            }

            $m_mejal = new \Model\Storage\MejaLog_model();
            $m_mejal->where('pesanan_kode', $kode_pesanan)->update(
                array(
                    'meja_id' => $params['meja_id']
                )
            );

            $this->execSavePenjualan( $params, $kode_pesanan );

            $deskripsi_log_gaktifitas = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_pesanan, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['content'] = array('kode_pesanan' => $kode_pesanan);
            $this->result['message'] = 'Data berhasil di ubah.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function tes()
    {
        // $kasir = 'USR2207003';
        // $date = '2022-09-12';

        // $data = $this->getDataClosingShift( $date, $kasir );

        // cetak_r($data);

        $out = '';
        $err = '';

        exec("cd assets\websocket\server && node index.js 2>&1", $out, $err);

        echo "<pre>";
        print_r($out);
        echo "</pre>";
        echo "<pre>";
        print_r($err);
        echo "</pre>";
    }
}