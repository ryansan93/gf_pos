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
            $content['jenis'] = $this->getJenis();

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
        $d_lantai = $m_lantai->where('branch_kode', $this->kodebranch)->where('mstatus', 1)->with(['meja'])->get();

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
        $now = $m_lantai->getDate();
        $d_lantai = $m_lantai->where('id', $params['lantai_id'])->with(['meja'])->first();

        $start_date = $now['tanggal'].' 00:00:00';
        $end_date = $now['tanggal'].' 23:59:59';

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
                if ( $d_mejal ) {
                    if ( $d_lantai['kontrol_meja'] == 1 ) {
                        $aktif = $d_mejal->status;
                    }
                }

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

    public function getJenis()
    {
        // $m_jm = new \Model\Storage\JenisMenu_model();
        // $d_jm = $m_jm->where('status', 1)->orderBy('nama', 'asc')->get();

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select jm.id, jm.nama from kategori_menu_user kmu
            right join
                menu m 
                on
                    kmu.kategori_menu_id = m.kategori_menu_id 
            right join
                jenis_menu jm 
                on
                    jm.id = m.jenis_menu_id 
            where
                kmu.user_id = '".$this->userid."' and
                jm.status = 1
            group by
                jm.id, jm.nama
        ";
        $d_jm = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jm->count() > 0 ) {
            $data = $d_jm->toArray();
        }

        return $data;
    }

    public function getMenu()
    {
        $id_jenis = $this->input->get('id_jenis');
        $jenis_pesanan = $this->input->get('jenis_pesanan');
        $branch_kode = $this->input->get('branch_kode');

        $m_menu = new \Model\Storage\Menu_model();
        $sql = "
            select 
                m.id, 
                m.kode_menu, 
                m.nama, 
                m.deskripsi, 
                m.ppn, 
                m.service_charge, 
                hm.harga as harga_jual, 
                m.jenis_menu_id, 
                count(pm.kode_paket_menu) as jml_paket 
            from menu m
            left join
                (
                select * from harga_menu where id in (
                    select max(id) as id from harga_menu group by jenis_pesanan_kode, menu_kode
                )) hm 
                on
                    m.kode_menu = hm.menu_kode 
            left join
                paket_menu pm
                on
                    m.kode_menu = pm.menu_kode
            where
                m.jenis_menu_id = ".$id_jenis." and
                hm.jenis_pesanan_kode = '".$jenis_pesanan."' and
                m.branch_kode = '".trim($branch_kode)."' and
                m.status = 1
            group by 
                m.id, 
                m.kode_menu, 
                m.nama, 
                m.deskripsi, 
                m.ppn, 
                m.service_charge, 
                hm.harga, 
                m.jenis_menu_id, 
                hm.jenis_pesanan_kode
            order by m.nama asc
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
                    $m_pesanani->ppn = $v_lm['ppn'];
                    $m_pesanani->service_charge = $v_lm['service_charge'];
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
            Modules::run( 'base/event/save', $m_pesanan, $deskripsi_log_gaktifitas, $kode_pesanan );
            
            $this->result['status'] = 1;
            $this->result['content'] = array('kode_pesanan' => $kode_pesanan);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deletePesanan()
    {
        $params = $this->input->post('params');

        $result = $this->execDeletePesanan  ( $params );

        display_json( $result );
    }

    public function execDeletePesanan($params)
    {
        try {
            $m_jual = new \Model\Storage\Jual_model();
            $m_jual->where('pesanan_kode', $params)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_jual = $m_jual->where('pesanan_kode', $params)->first();
            
            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jual, $deskripsi_log, $params );

            $m_pesanan = new \Model\Storage\Pesanan_model();
            $m_pesanan->where('kode_pesanan', $params)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_pesanan = $m_pesanan->where('kode_pesanan', $params)->first();

            $m_pi = new \Model\Storage\PesananItem_model();
            $d_pi = $m_pi->where('pesanan_kode', $params)->with(['pesanan_item_detail'])->get();

            $m_wm = new \Model\Storage\WasteMenu_model();
            $now = $m_wm->getDate();

            $m_wm->tanggal = $now['tanggal'];
            $m_wm->branch_kode = $this->kodebranch;
            $m_wm->save();

            if ( $d_pi->count() > 0 ) {
                $d_pi = $d_pi->toArray();

                foreach ($d_pi as $k_pi => $v_pi) {
                    $m_wmi = new \Model\Storage\WasteMenuItem_model();
                    $m_wmi->id_header = $m_wm->id;
                    $m_wmi->menu_kode = $v_pi['menu_kode'];
                    $m_wmi->jumlah = $v_pi['jumlah'];
                    $m_wmi->pesanan_kode = $params;
                    $m_wmi->user_id = $this->userid;
                    $m_wmi->nama_user = $this->userdata['detail_user']['nama_detuser'];
                    $m_wmi->save();
                    if ( !empty($v_pi['pesanan_item_detail']) ) {
                        foreach ($v_pi['pesanan_item_detail'] as $k_pid => $v_pid) {
                            $m_wmi = new \Model\Storage\WasteMenuItem_model();
                            $m_wmi->id_header = $m_wm->id;
                            $m_wmi->menu_kode = $v_pid['menu_kode'];
                            $m_wmi->jumlah = $v_pi['jumlah'];
                            $m_wmi->pesanan_kode = $params;
                            $m_wmi->user_id = $this->userid;
                            $m_wmi->nama_user = $this->userdata['detail_user']['nama_detuser'];
                            $m_wmi->save();
                        }
                    }
                }
            }
            
            $deskripsi = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_pesanan, $deskripsi, $params );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function savePenjualan()
    {
        $params = $this->input->post('params');
        $kode_pesanan = $this->input->post('kode_pesanan');

        $result = $this->execSavePenjualan( $params, $kode_pesanan );

        display_json( $result );
    }

    public function execSavePenjualan($params, $kode_pesanan, $kode_faktur_asal = null)
    {
        try {
            $m_pesanan = new \Model\Storage\Pesanan_model();
            $d_pesanan = $m_pesanan->where('kode_pesanan', $kode_pesanan)->with(['pesanan_item'])->first()->toArray();

            $lunas = 0;
            $mstatus = 1;
            $utama = 1;
            $hutang = 0;
            if ( !empty($kode_faktur_asal) ) {
                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('kode_faktur', $kode_faktur_asal)->first();

                $lunas = $d_jual->lunas;
                $mstatus = $d_jual->mstatus;
                $utama = $d_jual->utama;
                $hutang = $d_jual->hutang;
            }

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
            $m_jual->service_charge = $d_pesanan['service_charge'];
            $m_jual->ppn = $d_pesanan['ppn'];
            $m_jual->grand_total = $d_pesanan['grand_total'];
            $m_jual->lunas = $lunas;
            $m_jual->mstatus = $mstatus;
            $m_jual->pesanan_kode = $kode_pesanan;
            $m_jual->utama = $utama;
            $m_jual->hutang = $hutang;
            $m_jual->kode_faktur_asal = $kode_faktur_asal;
            $m_jual->save();

            $total = 0;
            $total_service_charge = 0;
            $total_ppn = 0;
            $grand_total = 0;

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
                $m_juali->ppn = $v_pi['ppn'];
                $m_juali->service_charge = $v_pi['service_charge'];
                $m_juali->save();

                foreach ($v_pi['pesanan_item_detail'] as $k_pid => $v_pid) {
                    $m_jualid = new \Model\Storage\JualItemDetail_model();
                    $m_jualid->faktur_item_kode = $kode_faktur_item;
                    $m_jualid->menu_nama = $v_pid['menu_nama'];
                    $m_jualid->menu_kode = $v_pid['menu_kode'];
                    $m_jualid->jumlah = $v_pid['jumlah'];
                    $m_jualid->save();
                }

                $m_jp = new \Model\Storage\JenisPesanan_model();
                $d_jp = $m_jp->where('kode', $v_pi['kode_jenis_pesanan'])->first();

                if ( $d_jp->exclude == 1 ) {
                    $total += $v_pi['total'];
                    $total_service_charge += $v_pi['service_charge'];
                    $total_ppn += $v_pi['ppn'];
                    $grand_total = $total + $total_service_charge + $total_ppn;
                }

                if ( $d_jp->include == 1 ) {
                    $total += $v_pi['total'] - ($v_pi['service_charge'] + $v_pi['ppn']);
                    $total_service_charge += $v_pi['service_charge'];
                    $total_ppn += $v_pi['ppn'];
                    $grand_total += $v_pi['total'];
                }
            }

            if ( !empty($params['list_diskon']) ) {
                foreach ($params['list_diskon'] as $k_ld => $v_ld) {
                    $m_juald = new \Model\Storage\JualDiskon_model();
                    $m_juald->faktur_kode = $kode_faktur;
                    $m_juald->diskon_kode = $v_ld['kode_diskon'];
                    $m_juald->diskon_nama = $v_ld['nama_diskon'];
                    $m_juald->save();
                }
            }

            $m_jual = new \Model\Storage\Jual_model();
            $m_jual->where('kode_faktur', $kode_faktur)->update(
                array(
                    'total' => $total,
                    'service_charge' => $total_service_charge,
                    'ppn' => $total_ppn,
                    'grand_total' => $grand_total
                )
            );

            $m_pesanan = new \Model\Storage\Pesanan_model();
            $m_pesanan->where('kode_pesanan', $kode_pesanan)->update(
                array(
                    'total' => $total,
                    'service_charge' => $total_service_charge,
                    'ppn' => $total_ppn,
                    'grand_total' => $grand_total
                )
            );

            $d_jual_old = $m_jual->whereNotIn('kode_faktur', [$kode_faktur])->where('pesanan_kode', $kode_pesanan)->orderBy('kode_faktur', 'desc')->first();
            if ( $d_jual_old ) {
                $kode_faktur_old = $d_jual_old->kode_faktur;

                $m_jg = new \Model\Storage\JualGabungan_model();
                $m_jg->where('faktur_kode', $kode_faktur_old)->update(
                    array('faktur_kode' => $kode_faktur)
                );
                $m_jg->where('faktur_kode_gabungan', $kode_faktur_old)->update(
                    array(
                        'faktur_kode_gabungan' => $kode_faktur,
                        'jml_tagihan' => $d_pesanan['grand_total']
                    )
                );
            }

            $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jual, $deskripsi_log_gaktifitas, $kode_faktur );
            
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
            Modules::run( 'base/event/update', $d_jual, $deskripsi_log_gaktifitas, $params );

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
        $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->with(['jual_item'])->first()->toArray();

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

    public function mappingDataCheckList($kode_pesanan, $kategori_menu_id, $kode_branch, $kode_faktur)
    {
        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        // $sql = "
        //     select * from 
        //     (
        //         select 
        //             ji.menu_kode, 
        //             m.nama as nama_menu, 
        //             km.id as kategori_menu_id, 
        //             km.nama as kategori_menu_nama, 
        //             ji.jumlah as jumlah_menu, 
        //             ISNULL(ji_print.jumlah, 0) as jumlah_print, 
        //             (ji.jumlah - ISNULL(ji_print.jumlah, 0)) as jumlah, 
        //             ji.request, 
        //             jp.kode as kode_jenis_pesanan, 
        //             jp.nama as nama_jenis_pesanan
        //         from jual_item ji
        //         right join
        //             jenis_pesanan jp
        //             on
        //                 ji.kode_jenis_pesanan = jp.kode
        //         right join
        //             menu m 
        //             on
        //                 ji.menu_kode = m.kode_menu 
        //         right join
        //             branch b
        //             on
        //                 m.branch_kode = b.kode_branch
        //         right join
        //             (
        //                 select * from kategori_menu where print_cl = 1
        //             ) km
        //             on
        //                 km.id = m.kategori_menu_id 
        //         right join
        //             jual j
        //             on
        //                 ji.faktur_kode = j.kode_faktur 
        //         left outer join
        //             (
        //                 select
        //                     ji.menu_kode,
        //                     CASE
        //                         when ji.jumlah is null then 0
        //                         when ji.jumlah > 0 then ji.jumlah
        //                     END as jumlah,
        //                     ji.request
        //                 from 
        //                 (
        //                     select
        //                         ji.menu_kode,
        //                         ji.jumlah,
        //                         ji.request
        //                     from jual_item ji
        //                     right join
        //                         jual j
        //                         on
        //                             j.kode_faktur = ji.faktur_kode 
        //                     right join
        //                         (
        //                             select max(kode_faktur) as kode_faktur, pesanan_kode from jual where print_cl = 1 group by pesanan_kode
        //                         ) jprint
        //                         on
        //                             jprint.kode_faktur = j.kode_faktur 
        //                     where
        //                         j.pesanan_kode = '".$kode_pesanan."' and
        //                         j.mstatus = 0 and
        //                         j.print_cl = 1
        //                 ) ji
        //             ) ji_print
        //             on
        //                 ji.menu_kode = ji_print.menu_kode and
        //                 ji.request like ji_print.request
        //         where
        //             (ji_print.menu_kode is null or ji.jumlah > ji_print.jumlah ) and
        //             j.pesanan_kode = '".$kode_pesanan."' and
        //             j.mstatus = 1 and
        //             km.id = '".$kategori_menu_id."' and
        //             b.kode_branch = '".$kode_branch."'

        //         union all
                    
        //         select 
        //             jid.menu_kode, 
        //             m.nama as nama_menu, 
        //             km.id as kategori_menu_id, 
        //             km.nama as kategori_menu_nama, 
        //             ji.jumlah as jumlah_menu, 
        //             ISNULL(ji_print.jumlah, 0) as jumlah_print, 
        //             (ji.jumlah - ISNULL(ji_print.jumlah, 0)) as jumlah,
        //             '' as request, 
        //             jp.kode as kode_jenis_pesanan, 
        //             jp.nama as nama_jenis_pesanan
        //         from jual_item_detail jid
        //         right join
        //             menu m 
        //             on
        //                 jid.menu_kode = m.kode_menu 
        //         right join
        //             branch b
        //             on
        //                 m.branch_kode = b.kode_branch
        //         right join
        //             (
        //                 select * from kategori_menu where print_cl = 1
        //             ) km
        //             on
        //                 km.id = m.kategori_menu_id 
        //         right join
        //             jual_item ji
        //             on
        //                 jid.faktur_item_kode = ji.kode_faktur_item
        //         right join
        //             jual j 
        //             on
        //                 j.kode_faktur = ji.faktur_kode 
        //         right join
        //             jenis_pesanan jp
        //             on
        //                 ji.kode_jenis_pesanan = jp.kode
        //         left join
        //             (
        //                 select
        //                     jid.menu_kode,
        //                     CASE
        //                         when jid.jumlah is null then 0
        //                         when jid.jumlah > 0 then jid.jumlah
        //                     END as jumlah 
        //                 from 
        //                 (
        //                     select
        //                         jid.menu_kode,
        //                         ji.jumlah
        //                     from jual_item_detail jid
        //                     right join
        //                         jual_item ji
        //                         on
        //                             jid.faktur_item_kode = ji.kode_faktur_item
        //                     right join
        //                         jual j
        //                         on
        //                             j.kode_faktur = ji.faktur_kode 
        //                     right join
        //                         (
        //                             select max(kode_faktur) as kode_faktur, pesanan_kode from jual where print_cl = 1 group by pesanan_kode
        //                         ) jprint
        //                         on
        //                             jprint.kode_faktur = j.kode_faktur 
        //                     where
        //                         j.pesanan_kode = '".$kode_pesanan."' and
        //                         j.mstatus = 0 and
        //                         j.print_cl = 1
        //                 ) jid
        //             ) ji_print
        //             on
        //                 jid.menu_kode = ji_print.menu_kode
        //         where
        //             (ji_print.menu_kode is null or ji.jumlah > ji_print.jumlah ) and
        //             j.pesanan_kode = '".$kode_pesanan."' and
        //             j.mstatus = 1 and
        //             jid.menu_kode is not null and
        //             km.id = '".$kategori_menu_id."' and
        //             b.kode_branch = '".$kode_branch."'
        //     ) data
        // ";
        $sql = "
            select * from 
            (
                select 
                    ji.menu_kode, 
                    m.nama as nama_menu, 
                    km.id as kategori_menu_id, 
                    km.nama as kategori_menu_nama, 
                    ji.jumlah as jumlah_menu, 
                    ISNULL(ji_print.jumlah, 0) as jumlah_print, 
                    (ji.jumlah - ISNULL(ji_print.jumlah, 0)) as jumlah, 
                    ji.request, 
                    jp.kode as kode_jenis_pesanan, 
                    jp.nama as nama_jenis_pesanan
                from jual_item ji
                right join
                    jenis_pesanan jp
                    on
                        ji.kode_jenis_pesanan = jp.kode
                right join
                    menu m 
                    on
                        ji.menu_kode = m.kode_menu 
                right join
                    branch b
                    on
                        m.branch_kode = b.kode_branch
                right join
                    (
                        select * from kategori_menu where print_cl = 1
                    ) km
                    on
                        km.id = m.kategori_menu_id 
                right join
                    jual j
                    on
                        ji.faktur_kode = j.kode_faktur 
                left outer join
                    (
                        select
                            ji.menu_kode,
                            CASE
                                when ji.jumlah is null then 0
                                when ji.jumlah > 0 then ji.jumlah
                            END as jumlah,
                            ji.request
                        from 
                        (
                            select
                                ji.menu_kode,
                                ji.jumlah,
                                ji.request
                            from jual_item ji
                            right join
                                jual j
                                on
                                    j.kode_faktur_asal = ji.faktur_kode 
                            where
                                j.kode_faktur = '".$kode_faktur."' and
                                ji.jumlah > 0
                        ) ji
                    ) ji_print
                    on
                        ji.menu_kode = ji_print.menu_kode and
                        ji.request like ji_print.request
                where
                    (ji_print.menu_kode is null or ji.jumlah > ji_print.jumlah ) and
                    j.kode_faktur = '".$kode_faktur."' and
                    j.mstatus = 1 and
                    km.id = '".$kategori_menu_id."' and
                    b.kode_branch = '".$kode_branch."'

                union all
                    
                select 
                    jid.menu_kode, 
                    m.nama as nama_menu, 
                    km.id as kategori_menu_id, 
                    km.nama as kategori_menu_nama, 
                    ji.jumlah as jumlah_menu, 
                    ISNULL(ji_print.jumlah, 0) as jumlah_print, 
                    (ji.jumlah - ISNULL(ji_print.jumlah, 0)) as jumlah,
                    '' as request, 
                    jp.kode as kode_jenis_pesanan, 
                    jp.nama as nama_jenis_pesanan
                from jual_item_detail jid
                right join
                    menu m 
                    on
                        jid.menu_kode = m.kode_menu 
                right join
                    branch b
                    on
                        m.branch_kode = b.kode_branch
                right join
                    (
                        select * from kategori_menu where print_cl = 1
                    ) km
                    on
                        km.id = m.kategori_menu_id 
                right join
                    jual_item ji
                    on
                        jid.faktur_item_kode = ji.kode_faktur_item
                right join
                    jual j 
                    on
                        j.kode_faktur = ji.faktur_kode 
                right join
                    jenis_pesanan jp
                    on
                        ji.kode_jenis_pesanan = jp.kode
                left join
                    (
                        select
                            jid.menu_kode,
                            CASE
                                when jid.jumlah is null then 0
                                when jid.jumlah > 0 then jid.jumlah
                            END as jumlah 
                        from 
                        (
                            select
                                ji.menu_kode,
                                ji.jumlah,
                                ji.request
                            from jual_item ji
                            right join
                                jual j
                                on
                                    j.kode_faktur_asal = ji.faktur_kode 
                            where
                                j.kode_faktur = '".$kode_faktur."' and
                                ji.jumlah > 0
                        ) jid
                    ) ji_print
                    on
                        jid.menu_kode = ji_print.menu_kode
                where
                    (ji_print.menu_kode is null or ji.jumlah > ji_print.jumlah ) and
                    j.kode_faktur = '".$kode_faktur."' and
                    j.mstatus = 1 and
                    jid.menu_kode is not null and
                    km.id = '".$kategori_menu_id."' and
                    b.kode_branch = '".$kode_branch."'
            ) data
        ";
        $d_data = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_data->count() > 0 ) {
            $d_data = $d_data->toArray();

            foreach ($d_data as $k_data => $v_data) {
                $data[ $v_data['kode_jenis_pesanan'] ]['kode'] = $v_data['kode_jenis_pesanan'];
                $data[ $v_data['kode_jenis_pesanan'] ]['nama'] = $v_data['nama_jenis_pesanan'];
                $data[ $v_data['kode_jenis_pesanan'] ]['detail'][] = $v_data;
            }
        }

        return $data;
    }

    public function printCheckList()
    {
        $params = $this->input->post('params');

        try {
            function buatBaris3Kolom($kolom1, $kolom2, $kolom3, $jenis) {
                // Mengatur lebar setiap kolom (dalam satuan karakter)
                if ( $jenis == 'header' ) {
                    $lebar_kolom_1 = 10;
                    $lebar_kolom_2 = 3;
                    $lebar_kolom_3 = 33;
                }
                if ( $jenis == 'center' ) {
                    $lebar_kolom_1 = 33;
                    $lebar_kolom_2 = 3;
                    $lebar_kolom_3 = 10;
                }
                if ( $jenis == 'request' ) {
                    $lebar_kolom_1 = 3;
                    $lebar_kolom_2 = 43;
                    $lebar_kolom_3 = 0;
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
                    if ( $jenis == 'header' || $jenis == 'request' ) {
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
     
                    // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                    $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                }
     
                // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                return implode($hasilBaris, "\n") . "\n";
            }

            $kode_pesanan = $params['kode_pesanan'];
            $kode_faktur = $params['kode_faktur'];

            $m_jual = new \Model\Storage\Jual_model();
            $sql = "
                select 
                    j.kode_faktur as kode_faktur, 
                    b.kode_branch as kode_branch, 
                    b.nama as nama_branch, 
                    p.nama_user as nama_kasir, 
                    m.nama_meja, 
                    p.privilege 
                from jual_item ji
                right join
                    jual j
                    on
                        j.kode_faktur = ji.faktur_kode
                right join
                    menu mn
                    on
                        ji.menu_kode = mn.kode_menu
                right join
                    branch b
                    on
                        b.kode_branch = mn.branch_kode
                right join
                    pesanan p
                    on
                        p.kode_pesanan = j.pesanan_kode
                left join
                    meja m
                    on
                        m.id = p.meja_id
                where
                    j.kode_faktur = '".$kode_faktur."' and
                    j.mstatus = 1
                group by
                    j.kode_faktur,
                    b.kode_branch, 
                    b.nama, 
                    p.nama_user, 
                    m.nama_meja, 
                    p.privilege
            ";
            $d_jual = $m_jual->hydrateRaw( $sql );

            if ( $d_jual->count() > 0 ) {
                $d_jual = $d_jual->toArray();

                foreach ($d_jual as $k_jual => $v_jual) {
                    $data_jual = $v_jual;

                    $m_ps = new \Model\Storage\PrinterStation_model();
                    $d_ps = $m_ps->where('nama', 'KITCHEN')->first();

                    $m_printer = new \Model\Storage\Printer_model();
                    $d_printer = $m_printer->where('printer_station_id', $d_ps->id)->where('branch_kode', $data_jual['kode_branch'])->where('status', 1)->get();

                    if ( $d_printer->count() > 0 ) {
                        $d_printer = $d_printer->toArray();

                        $printer_id = null;
                        foreach ($d_printer as $k_p => $v_p) {
                            $jml_print = $v_p['jml_print'];
                            $printer_name = $v_p['sharing_name'];

                            $m_conf = new \Model\Storage\Conf();
                            $now = $m_conf->getDate();
                            
                            $sql = "
                                select pkm.* from printer_kategori_menu pkm
                                where
                                    pkm.id_header = ".$v_p['id']."
                            ";
                            $d_pkm = $m_conf->hydrateRaw( $sql );

                            $kategori_menu_printer = null;
                            if ( $d_pkm->count() > 0 ) {
                                $d_pkm = $d_pkm->toArray();

                                foreach ($d_pkm as $k_pkm => $v_pkm) {
                                    // $kategori_menu_printer[] = $v_pkm['kategori_menu_id'];
                                    $m_km = new \Model\Storage\KategoriMenu_model();
                                    $d_km = $m_km->where('print_cl', 1)->where('id', $v_pkm['kategori_menu_id'])->first();

                                    if ( $d_km ) {
                                        $v_km = $d_km->toArray();

                                        $d_data = $this->mappingDataCheckList( $kode_pesanan, $v_km['id'], $data_jual['kode_branch'], $kode_faktur );

                                        if ( !empty($d_data) ) {
                                            for ($i=0; $i < $jml_print; $i++) { 
                                                if ( $printer_id != $v_p['id'] ) {
                                                    // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
                                                    $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector($printer_name);
                                                    // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                                                    // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

                                                    /* Print a receipt */
                                                    $printer = new Mike42\Escpos\Printer($connector);

                                                    $printer -> initialize();
                                                    $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                                                    $printer -> text($data_jual['nama_branch']."\n");

                                                    $printer -> initialize();
                                                    $printer -> text("\n");
                                                    $printer -> text(buatBaris3Kolom('Tanggal', ':', substr($now['waktu'], 0, 19), 'header'));
                                                    $printer -> text(buatBaris3Kolom('No. Meja', ':', $this->kodebranch.'\\'.$data_jual['nama_meja'], 'header'));
                                                    $printer -> text(buatBaris3Kolom('Waitress', ':', $this->userdata['detail_user']['nama_detuser'], 'header'));
                                                    // $printer -> text(buatBaris3Kolom('Kategori', ':', $v_km['nama'], 'header'));

                                                    $printer -> initialize();
                                                    $printer -> text('================================================'."\n");
                                                }

                                                $printer -> initialize();
                                                $printer -> text('------------------------------------------------'."\n");
                                                $printer -> text($v_km['nama']."\n");
                                                $printer -> text('------------------------------------------------'."\n");

                                                $printer -> initialize();

                                                $jml_member = 1;
                                                foreach ($d_data as $k_data => $v_data) {
                                                    $printer -> text($v_data['nama']);
                                                    $printer -> text("\n");
                                                    foreach ($v_data['detail'] as $k_det => $v_det) {
                                                        $printer -> text(buatBaris3Kolom($v_det['nama_menu'], '', angkaRibuan($v_det['jumlah']), 'center'));
                                                        if ( isset($v_det['request']) && !empty($v_det['request']) ) {
                                                            $printer -> text(buatBaris3Kolom('', $v_det['request'], '', 'request'));
                                                        }
                                                    }
                                                }

                                                if ( $printer_id != $v_p['id'] ) {
                                                    $printer -> initialize();
                                                    $printer -> text('================================================'."\n");

                                                    if ( $data_jual['privilege'] == 1 ) {
                                                        $printer -> initialize();
                                                        $printer -> selectPrintMode(32);
                                                        $printer -> setTextSize(2, 1);
                                                        $printer -> text("PRIVILEGE");
                                                    }

                                                    $printer -> feed(3);
                                                    $printer -> cut();
                                                    $printer -> close();
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $printer_id = $v_p['id'];
                        }
                    }

                    $m_jual = new \Model\Storage\Jual_model();
                    $m_jual->where('kode_faktur', $data_jual['kode_faktur'])->update(
                        array(
                            'print_cl' => 1
                        )
                    );
                }
            }

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
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
            // $this->load->library('PDFGenerator');

            // // $m_rs = new \Model\Storage\RdimSubmit_model();
            // // $res_view_html = $this->load->view('transaksi/rdim/cetak_kontrak', $content, true);
            // $content = null;
            // $res_view_html = $this->load->view($this->pathView . 'print_tes', $content, TRUE);;

            // $this->pdfgenerator->generate($res_view_html, "TES PRINT");

            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            // $printer -> setJustification(1);
            // $printer -> selectPrintMode(32);
            // $printer -> setTextSize(2, 1);
            // $printer -> text("\n\nPRINT TEST\n\n");
            $printer -> text("-----------------------------------------------");

            /* NOTE : UKURAN KARAKTER PER KERTAS */
            /*
                56mm : 35 karakter
                80mm : 47 karakter
            */

            $printer -> feed(3);
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
            $idFitur = getIdFitur( $this->current_base_uri );
            
            $m_po = new \Model\Storage\PinOtorisasi_model();
            $d_po = $m_po->where('pin', $pin)->where('id_detfitur', $idFitur)->where('status', 1)->first();

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
            $now = $m_pesanan->getDate();

            // $d_pesanan = $m_pesanan->where('kode_pesanan', $params['pesanan_kode'])->with(['pesanan_item', 'meja'])->first();
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->where('kode_faktur', $params['faktur_kode'])->with(['jual_item', 'pesanan'])->first();

            $jenis_pesanan = null;
            $nama_jenis_pesanan = null;

            $kode_member = null;
            $member = null;

            $meja_id = null;
            $meja = null;

            $data = null;
            if ( $d_jual ) {
                $d_jual = $d_jual->toArray();

                $pesanan_item = null;
                foreach ($d_jual['jual_item'] as $k_ji => $v_ji) {
                    $jenis_pesanan = $v_ji['kode_jenis_pesanan'];
                    $nama_jenis_pesanan = isset($v_ji['jenis_pesanan'][0]) ? $v_ji['jenis_pesanan'][0]['nama'] : null;

                    $m_jp = new \Model\Storage\JenisPesanan_model();
                    $d_jp = $m_jp->where('kode', $jenis_pesanan)->first();

                    $key_jp = $v_ji['kode_jenis_pesanan'];
                    $pesanan_item[$key_jp]['kode'] = $v_ji['kode_jenis_pesanan'];
                    $pesanan_item[$key_jp]['nama'] = isset($v_ji['jenis_pesanan'][0]) ? $v_ji['jenis_pesanan'][0]['nama'] : null;

                    $m_hm = new \Model\Storage\HargaMenu_model();
                    $d_hm = $m_hm->where('menu_kode', $v_ji['menu_kode'])->where('jenis_pesanan_kode', $v_ji['kode_jenis_pesanan'])->orderBy('id', 'desc')->first();

                    $m_menu = new \Model\Storage\Menu_model();
                    $d_menu = $m_menu->where('kode_menu', $v_ji['menu_kode'])->orderBy('id', 'desc')->first();

                    $m_sc = new \Model\Storage\ServiceCharge_model();
                    $d_sc = $m_sc->where('branch_kode', $this->kodebranch)->where('mstatus', 1)->where('tgl_berlaku', '<=', $now['tanggal'])->orderBy('id', 'desc')->first();

                    $m_ppn = new \Model\Storage\Ppn_model();
                    $d_ppn = $m_ppn->where('branch_kode', $this->kodebranch)->where('mstatus', 1)->where('tgl_berlaku', '<=', $now['tanggal'])->orderBy('id', 'desc')->first();

                    $total = 0;
                    $total_service_charge = 0;
                    $total_ppn = 0;
                    $total_show = 0;
                    if ( $d_jp ) {
                        if ( $d_jp->exclude == 1 ) {
                            $total = $d_hm->harga * $v_ji['jumlah'];
                            $total_show = $total;
                            $total_service_charge = ($d_menu->service_charge == 1 && $d_sc->nilai > 0) ? $total * ($d_sc->nilai / 100) : 0;
                            $total_ppn = ($d_menu->ppn == 1 && $d_ppn->nilai > 0) ? ($total + $total_service_charge) * ($d_ppn->nilai / 100) : 0;
                        } else if ( $d_jp->include == 1 ) {
                            $total_include = $d_hm->harga * $v_ji['jumlah'];
                            $total_show = $total_include;

                            $pembagi = (100 + $d_sc->nilai) + ((100 + $d_sc->nilai) * ($d_ppn->nilai/100));
                            $total = $total_include / ($pembagi / 100);

                            $total_service_charge = $total * ($d_sc->nilai/100);
                            $total_ppn = ($total + $total_service_charge) * ($d_ppn->nilai/100);
                        }
                    }

                    $key_ji = $k_ji;
                    $pesanan_item[$key_jp]['detail'][$key_ji] = array(
                        'kode_faktur_item' => $v_ji['kode_faktur_item'],
                        'pesanan_kode' => $d_jual['pesanan_kode'],
                        'kode_jenis_pesanan' => $v_ji['kode_jenis_pesanan'],
                        'menu_nama' => $v_ji['menu_nama'],
                        'menu_kode' => $v_ji['menu_kode'],
                        'jumlah' => $v_ji['jumlah'],
                        'harga_show' => ($d_hm) ? $d_hm->harga : 0,
                        'harga' => ($d_hm) ? $d_hm->harga : 0,
                        'total' => $total,
                        'service_charge' => $total_service_charge,
                        'ppn' => $total_ppn,
                        'total_show' => $total_show,
                        'request' => $v_ji['request'],
                        'pesanan_item_detail' => $v_ji['jual_item_detail'],
                        'proses' => isset($v_ji['proses']) ? $v_ji['proses'] : null
                    );
                }
                $pesanan_diskon = null;

                $kode_member = $d_jual['kode_member'];
                $member = $d_jual['member'];

                $meja_id = $d_jual['pesanan']['meja']['id'];
                $meja = $d_jual['pesanan']['meja']['lantai']['nama_lantai'].' - '.$d_jual['pesanan']['meja']['nama_meja'];

                $data = array(
                    'kode_pesanan' => $d_jual['pesanan_kode'],
                    'kode_faktur' => $d_jual['kode_faktur'],
                    'tgl_trans' => $d_jual['tgl_trans'],
                    'branch' => $d_jual['branch'],
                    'member' => $d_jual['member'],
                    'kode_member' => $d_jual['kode_member'],
                    'user_id' => $d_jual['pesanan']['user_id'],
                    'nama_user' => $d_jual['pesanan']['nama_user'],
                    'total' => $d_jual['total'],
                    'diskon' => $d_jual['diskon'],
                    'service_charge' => $d_jual['service_charge'],
                    'ppn' => $d_jual['ppn'],
                    'grand_total' => $d_jual['grand_total'],
                    'status' => $d_jual['pesanan']['status'],
                    'mstatus' => $d_jual['mstatus'],
                    'pesanan_item' => $pesanan_item,
                    'pesanan_diskon' => $pesanan_diskon
                );
            }

            $content['data'] = $data;

            $html = $this->load->view($this->pathView . 'detail_pesanan', $content, TRUE);

            $m_jp = new \Model\Storage\JenisPesanan_model();
            $d_jp = $m_jp->where('kode', $jenis_pesanan)->first();

            $content = array(
                'html' => $html,
                'pesanan_kode' => $data['kode_pesanan'],
                'faktur_kode' => $data['kode_faktur'],
                'jenis_pesanan' => $jenis_pesanan,
                'nama_jenis_pesanan' => $nama_jenis_pesanan,
                'jenis_harga_exclude' => ($d_jp) ? $d_jp->exclude : 0,
                'jenis_harga_include' => ($d_jp) ? $d_jp->include : 0,
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
            $kode_faktur = $params['faktur_kode'];

            $m_jual = new \Model\Storage\Jual_model();
            $cek_d_jual = $m_jual->where('kode_faktur', $kode_faktur)->where('mstatus', 0)->first();

            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('faktur_kode', $kode_faktur)->where('mstatus', 1)->first();

            if ( !$d_bayar && !$cek_d_jual ) {
                $new_kode_faktur = null;

                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('pesanan_kode', $kode_pesanan)->where('mstatus', 1)->get();

                if ( $d_jual->count() == 1 ) {
                    $m_pesanan = new \Model\Storage\Pesanan_model();
                    $now = $m_pesanan->getDate();

                    $d_pesanan = $m_pesanan->where('kode_pesanan', $kode_pesanan)->first();

                    $m_pesanan->where('kode_pesanan', $kode_pesanan)->update(
                        array(
                            'branch' => $this->kodebranch,
                            'member' => $params['member'],
                            'kode_member' => $params['kode_member'],
                            // 'user_id' => $this->userid,
                            // 'nama_user' => $this->userdata['detail_user']['nama_detuser'],
                            'total' => $params['sub_total'],
                            'diskon' => $params['diskon'],
                            'service_charge' => $params['service_charge'],
                            'ppn' => $params['ppn'],
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
                            $m_pesanani->service_charge = $v_lm['service_charge'];
                            $m_pesanani->ppn = $v_lm['ppn'];
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

                    $m_jual = new \Model\Storage\Jual_model();

                    $d_jual_aktif = $m_jual->where('pesanan_kode', $kode_pesanan)->where('mstatus', 1)->first();

                    $result = $this->execSavePenjualan( $params, $kode_pesanan, $d_jual_aktif->kode_faktur );
                    if ( $result['status'] == 1 ) {
                        $new_kode_faktur = $result['content']['kode_faktur'];

                        $d_jual = $m_jual->where('pesanan_kode', $kode_pesanan)->whereNotIn('kode_faktur', [$new_kode_faktur])->orderBy('kode_faktur', 'desc')->first();
                        if ( $d_jual ) {
                            $d_jual = $d_jual->toArray();

                            $this->execDeletePenjualan( $d_jual['kode_faktur'] );

                            // foreach ($d_jual as $k_jual => $v_jual) {
                            //     $this->execDeletePenjualan( $v_jual['kode_faktur'] );
                            // }
                        }

                        $m_mejal = new \Model\Storage\MejaLog_model();
                        $m_mejal->where('pesanan_kode', $kode_pesanan)->update(
                            array(
                                'meja_id' => $params['meja_id']
                            )
                        );
                    }

                    $deskripsi_log_gaktifitas = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
                    Modules::run( 'base/event/update', $d_pesanan, $deskripsi_log_gaktifitas, $kode_pesanan );
                } else {
                    $m_jual = new \Model\Storage\Jual_model();
                    $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first();

                    $m_jual->where('kode_faktur', $kode_faktur)->update(
                        array(
                            'mstatus' => 0
                        )
                    );

                    $now = $m_jual->getDate();

                    $new_kode_faktur = $m_jual->getNextKode('FAK');
                    $m_jual->kode_faktur = $new_kode_faktur;
                    $m_jual->tgl_trans = $now['waktu'];
                    $m_jual->branch = $this->kodebranch;
                    $m_jual->member = $params['member'];
                    $m_jual->kode_member = $params['kode_member'];
                    $m_jual->kasir = $this->userid;
                    $m_jual->nama_kasir = $this->userdata['detail_user']['nama_detuser'];
                    $m_jual->total = $params['sub_total'];
                    $m_jual->diskon = $params['diskon'];
                    $m_jual->service_charge = $params['service_charge'];
                    $m_jual->ppn = $params['ppn'];
                    $m_jual->grand_total = $params['grand_total'];
                    $m_jual->lunas = 0;
                    $m_jual->mstatus = 1;
                    $m_jual->pesanan_kode = $kode_pesanan;
                    $m_jual->utama = $d_jual->utama;
                    $m_jual->hutang = $d_jual->hutang;
                    $m_jual->kode_faktur_asal = $d_jual->kode_faktur;
                    $m_jual->save();

                    foreach ($params['list_pesanan'] as $k_lp => $v_lp) {
                        foreach ($v_lp['list_menu'] as $k_lm => $v_lm) {
                            $m_juali = new \Model\Storage\JualItem_model();

                            $kode_faktur_item = $m_juali->getNextKode('FKI');
                            $m_juali->kode_faktur_item = $kode_faktur_item;
                            $m_juali->faktur_kode = $new_kode_faktur;
                            $m_juali->kode_jenis_pesanan = $v_lp['kode_jp'];
                            $m_juali->menu_nama = $v_lm['nama_menu'];
                            $m_juali->menu_kode = $v_lm['kode_menu'];
                            $m_juali->jumlah = $v_lm['jumlah'];
                            $m_juali->harga = $v_lm['harga'];
                            $m_juali->total = $v_lm['total'];
                            $m_juali->service_charge = $v_lm['service_charge'];
                            $m_juali->ppn = $v_lm['ppn'];
                            $m_juali->request = $v_lm['request'];
                            $m_juali->pesanan_item_kode = isset($v_lm['kode_pesanan_item']) ? $v_lm['kode_pesanan_item'] : null;
                            $m_juali->save();

                            if ( !empty($v_lm['detail_menu']) ) {
                                foreach ($v_lm['detail_menu'] as $k_dm => $v_dm) {
                                    $m_jualid = new \Model\Storage\JualItemDetail_model();
                                    $m_jualid->faktur_item_kode = $kode_faktur_item;
                                    $m_jualid->menu_nama = $v_dm['nama_menu'];
                                    $m_jualid->menu_kode = $v_dm['kode_menu'];
                                    $m_jualid->jumlah = $v_dm['jumlah'];
                                    $m_jualid->save();
                                }
                            }
                        }
                    }

                    $m_jg = new \Model\Storage\JualGabungan_model();
                    $m_jg->where('faktur_kode', $kode_faktur)->update(
                        array('faktur_kode' => $new_kode_faktur)
                    );
                    $m_jg->where('faktur_kode_gabungan', $kode_faktur)->update(
                        array(
                            'faktur_kode_gabungan' => $new_kode_faktur,
                            'jml_tagihan' => $params['grand_total']
                        )
                    );

                    $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                    Modules::run( 'base/event/save', $m_jual, $deskripsi_log_gaktifitas, $new_kode_faktur );

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select 
                            ji.*, 
                            jid.faktur_item_kode,
                            jid.menu_nama as menu_nama_det,
                            jid.menu_kode as menu_kode_det,
                            jid.jumlah as jumlah_det
                        from jual_item ji
                        left join
                            jual_item_detail jid 
                            on
                                ji.kode_faktur_item = jid.faktur_item_kode 
                        right join
                            jual j
                            on
                                ji.faktur_kode = j.kode_faktur
                        where
                            j.pesanan_kode = '".$kode_pesanan."' and
                            j.mstatus = 1
                        order by
                            ji.kode_faktur_item asc
                    ";
                    $d_ji = $m_conf->hydrateRaw( $sql );

                    if ( $d_ji->count() > 0 ) {
                        $d_ji = $d_ji->toArray();

                        $m_pesanani = new \Model\Storage\PesananItem_model();
                        $d_pesanani = $m_pesanani->select('kode_pesanan_item')->where('pesanan_kode', $kode_pesanan)->get()->toArray();

                        $m_pesananid = new \Model\Storage\PesananItemDetail_model();
                        $m_pesananid->whereIn('pesanan_item_kode', $d_pesanani)->delete();
                        $m_pesanani->where('pesanan_kode', $kode_pesanan)->delete();

                        $kode_faktur_item = null;
                        $kode_pesanan_item = null;
                        foreach ($d_ji as $k_ji => $v_ji) {
                            if ( $kode_faktur_item != $v_ji['kode_faktur_item'] ) {
                                $m_pesanani = new \Model\Storage\PesananItem_model();

                                $kode_pesanan_item = $m_pesanani->getNextKode('FKI');
                                $m_pesanani->kode_pesanan_item = $kode_pesanan_item;
                                $m_pesanani->pesanan_kode = $kode_pesanan;
                                $m_pesanani->kode_jenis_pesanan = $v_ji['kode_jenis_pesanan'];
                                $m_pesanani->menu_nama = $v_ji['menu_nama'];
                                $m_pesanani->menu_kode = $v_ji['menu_kode'];
                                $m_pesanani->jumlah = $v_ji['jumlah'];
                                $m_pesanani->harga = $v_ji['harga'];
                                $m_pesanani->total = $v_ji['total'];
                                $m_pesanani->service_charge = $v_ji['service_charge'];
                                $m_pesanani->ppn = $v_ji['ppn'];
                                $m_pesanani->request = $v_ji['request'];
                                $m_pesanani->proses = isset($v_ji['proses']) ? $v_ji['proses'] : null;
                                $m_pesanani->save();

                                if ( $v_ji['jumlah_det'] > 0 ) {
                                    $m_pesananid = new \Model\Storage\PesananItemDetail_model();
                                    $m_pesananid->pesanan_item_kode = $kode_pesanan_item;
                                    $m_pesananid->menu_nama = $v_ji['menu_nama_det'];
                                    $m_pesananid->menu_kode = $v_ji['menu_kode_det'];
                                    $m_pesananid->jumlah = $v_ji['jumlah_det'];
                                    $m_pesananid->save();
                                }

                                $m_juali = new \Model\Storage\JualItem_model();
                                $m_juali->where('kode_faktur_item', $v_ji['kode_faktur_item'])->update(
                                    array('pesanan_item_kode' => $kode_pesanan_item)
                                );

                                $kode_faktur_item = $v_ji['kode_faktur_item'];
                            } else {
                                if ( $v_ji['jumlah_det'] > 0 ) {
                                    $m_pesananid = new \Model\Storage\PesananItemDetail_model();
                                    $m_pesananid->pesanan_item_kode = $kode_pesanan_item;
                                    $m_pesananid->menu_nama = $v_ji['menu_nama_det'];
                                    $m_pesananid->menu_kode = $v_ji['menu_kode_det'];
                                    $m_pesananid->jumlah = $v_ji['jumlah_det'];
                                    $m_pesananid->save();
                                }
                            }
                        }

                        $m_conf = new \Model\Storage\Conf();
                        $sql = "
                            select
                                p.kode_pesanan,
                                sum(pi.total) as grand_total,
                                sum(pi.service_charge) as service_charge,
                                sum(pi.ppn) as ppn,
                                ( sum(pi.total) - sum(pi.service_charge) - sum(pi.ppn) ) as total
                            from pesanan p
                            left join
                                pesanan_item pi
                                on
                                    pi.pesanan_kode = p.kode_pesanan
                            where
                                p.kode_pesanan = '".$kode_pesanan."'
                            group by
                                p.kode_pesanan
                        ";
                        $d_pesanan = $m_conf->hydrateRaw( $sql );
                        if ( $d_pesanan->count() > 0 ) {
                            $d_pesanan = $d_pesanan->toArray()[0];

                            $m_pesanan = new \Model\Storage\Pesanan_model();
                            $m_pesanan->where('kode_pesanan', $kode_pesanan)->update(
                                array(
                                    'grand_total' => $d_pesanan['grand_total'],
                                    'service_charge' => $d_pesanan['service_charge'],
                                    'ppn' => $d_pesanan['ppn'],
                                    'total' => $d_pesanan['total']
                                )
                            );

                            $deskripsi_log_gaktifitas = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
                            Modules::run( 'base/event/update', $m_pesanan, $deskripsi_log_gaktifitas, $kode_pesanan );
                        }
                    }
                }

                if ( isset($params['waste']) && !empty($params['waste']) ) {
                    foreach ($params['waste'] as $k_waste => $v_waste) {
                        $m_menu = new \Model\Storage\Menu_model();
                        $d_menu = $m_menu->where('kode_menu', $v_waste['menu_kode'])->first();

                        $m_wm = new \Model\Storage\WasteMenu_model();
                        $d_wm = $m_wm->where('tanggal', $now['tanggal'])->where('branch_kode', $d_menu->branch_kode)->first();

                        $id = null;
                        if ( !$d_wm ) {
                            $m_wm->tanggal = $now['tanggal'];
                            $m_wm->branch_kode = $d_menu->branch_kode;
                            $m_wm->save();

                            $id = $m_wm->id;
                        } else {
                            $id = $d_wm->id;
                        }

                        $m_wmi = new \Model\Storage\WasteMenuItem_model();
                        $m_wmi->id_header = $id;
                        $m_wmi->menu_kode = $v_waste['menu_kode'];
                        $m_wmi->jumlah = $v_waste['jumlah'];
                        $m_wmi->pesanan_kode = $kode_pesanan;
                        $m_wmi->user_id = $this->userid;
                        $m_wmi->nama_user = $this->userdata['detail_user']['nama_detuser'];
                        $m_wmi->keterangan = $v_waste['keterangan'];
                        $m_wmi->save();
                    }
                }

                $this->result['status'] = 1;
                $this->result['content'] = array('kode_pesanan' => $kode_pesanan, 'kode_faktur' => $new_kode_faktur);
                $this->result['message'] = 'Data berhasil di ubah.';
            } else {
                $keterangan = '';
                if ( $cek_d_jual ) {
                    $keterangan = 'Pesanan sudah di update waitress lain.<br><b>Harap lakukan pembatalan perubahan data !</b>';
                }

                if ( $d_bayar ) {
                    $keterangan = 'Pesanan ini sudah di lakukan pembayaran oleh kasir.<br><b>Harap lakukan pembatalan perubahan data !</b>';
                }

                $this->result['message'] = $keterangan;
            }
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

        // $out = '';
        // $err = '';

        // exec("cd assets\websocket\server && node index.js 2>&1", $out, $err);

        // echo "<pre>";
        // print_r($out);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($err);
        // echo "</pre>";

        $idFitur = getIdFitur( $this->current_base_uri );

        cetak_r( substr($this->current_base_uri, 1) );
        cetak_r( $idFitur );
    }
}