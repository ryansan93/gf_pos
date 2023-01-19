<?php defined('BASEPATH') or exit('No direct script access allowed');

class ClosingOrder extends Public_Controller
{
    private $pathView = 'transaksi/closing_order/';
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
                    "assets/transaksi/closing_order/js/closing-order.js"
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    "assets/transaksi/closing_order/css/closing-order.css"
                )
            );
            $data = $this->includes;

            $m_clo = new \Model\Storage\ClosingOrder_model();
            $now = $m_clo->getDate();

            $start_date = $now['tanggal'].' 00:00:01';
            $end_date = $now['tanggal'].' 23:59:59';

            $d_clo = $m_clo->whereBetween('tanggal', [$start_date, $end_date])->first();

            $content['akses'] = $this->hakAkses;
            $content['persen_ppn'] = $this->persen_ppn;
            $content['kode_branch'] = $this->kodebranch;
            $content['closing_order'] = !empty($d_clo) ? 1 : 0;

            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    // public function salesRecapitulation()
    // {
    //     $m_conf = new \Model\Storage\Conf();
    //     $sql = "
    //         select 
    //             dm.menu_kode,
    //             case
    //                 when ji.total > 0 and dm.diskon > 0 then
    //                     case
    //                         when dm.diskon_jenis = 'persen' then
    //                             ji.total * (dm.diskon / 100)
    //                         else
    //                             ji.total - dm.diskon
    //                     end
    //                 else
    //                     0
    //             end as diskon
    //         from diskon_menu dm
    //         right join
    //             (
    //                 select 
    //                     ji.menu_kode, 
    //                     ji.menu_nama, 
    //                     ji.kode_jenis_pesanan,
    //                     jp.exclude,
    //                     jp.include,
    //                     sum(ji.jumlah) as jumlah, 
    //                     case 
    //                         when jp.exclude = 1 then
    //                             sum(ji.total)
    //                         when jp.include = 1 then
    //                             sum(ji.total) + sum(ji.ppn) + sum(ji.service_charge)
    //                     end as total
    //                 from jual_item ji
    //                 right join
    //                     (
    //                         select j.kode_faktur as kode_faktur from jual j where j.kode_faktur = '".$kode_faktur."'
    //                         UNION ALL
    //                         select jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode = '".$kode_faktur."'
    //                     ) jual
    //                     on
    //                         jual.kode_faktur = ji.faktur_kode 
    //                 right join
    //                     menu m
    //                     on
    //                         m.kode_menu = ji.menu_kode
    //                 right join
    //                     jenis_pesanan jp
    //                     on
    //                         jp.kode = ji.kode_jenis_pesanan
    //                 where
    //                     ji.jumlah > 0
    //                 group by
    //                     ji.kode_jenis_pesanan,
    //                     jp.exclude,
    //                     jp.include,
    //                     ji.menu_kode, 
    //                     ji.menu_nama
    //             ) ji
    //             on
    //                 dm.menu_kode = ji.menu_kode
    //         where
    //             dm.diskon_kode = '".$v_dd."'
    //     ";

    //     $pending_sales = null;

    //     $d_sql = $m_conf->hydrateRaw( $sql );
    //     if ( $d_sql->count() > 0 ) {
    //         $data = $d_sql->toArray();
    //     }
    // }

    public function getDataPenjualan()
    {
        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        $start = $now['tanggal'].' 00:00:01';
        $end = $now['tanggal'].' 23:59:59';
        $branch_kode = $this->kodebranch;

        $sql = "
            select ji.* from (
                select
                    ji1.faktur_kode,
                    ji1.menu_kode,
                    ji1.jumlah,
                    b.id as bom_id
                from jual_item ji1
                right join
                    (
                        select max(id) as id, menu_kode from bom where tgl_berlaku <= cast(GETDATE() as date) group by menu_kode
                    ) b 
                    on
                        ji1.menu_kode = b.menu_kode 
                union all
                select
                    ji2.faktur_kode,
                    jid.menu_kode,
                    ji2.jumlah,
                    b.id as bom_id
                from jual_item_detail jid 
                right join
                    jual_item ji2
                    on
                        jid.faktur_item_kode = ji2.kode_faktur_item 
                right join
                    (
                        select max(id) as id, menu_kode from bom where tgl_berlaku <= cast(GETDATE() as date) group by menu_kode
                    ) b 
                    on
                        ji2.menu_kode = b.menu_kode 
            ) ji
            right join
                (
                    select j.kode_faktur as kode_faktur_utama, j.kode_faktur as kode_faktur from jual j where j.kode_faktur in (
                        select j_params.kode_faktur from jual j_params
                        where
                            j_params.tgl_trans BETWEEN '".$start."' and '".$end."' and
                            j_params.mstatus = 1 and
                            j_params.branch = '".$branch_kode."'
                    )
                    UNION ALL
                    select jg.faktur_kode as kode_faktur_utama, jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode in (
                        select j_params.kode_faktur from jual j_params
                        where
                            j_params.tgl_trans BETWEEN '".$start."' and '".$end."' and
                            j_params.mstatus = 1 and
                            j_params.branch = '".$branch_kode."'
                    )
                ) jual
                on
                    ji.faktur_kode = jual.kode_faktur
            where
                ji.menu_kode is not null
        ";

        $data = null;

        $d_sql = $m_conf->hydrateRaw( $sql );
        if ( $d_sql->count() > 0 ) {
            $data = $d_sql->toArray();
        }

        return $data;
    }

    public function getDataVoidMenu()
    {
        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        $start = $now['tanggal'].' 00:00:01';
        $end = $now['tanggal'].' 23:59:59';
        $branch_kode = $this->kodebranch;

        $sql = "
            select
                wm.id as kode_trans,
                wmi.menu_kode,
                wmi.jumlah,
                b.id as bom_id
            from waste_menu_item wmi 
            right join
                waste_menu wm 
                on
                    wmi.id_header = wm.id
            right join
                (
                    select max(id) as id, menu_kode from bom where tgl_berlaku <= cast(GETDATE() as date) group by menu_kode
                ) b 
                on
                    wmi.menu_kode = b.menu_kode
            where
                wm.tanggal between '".$start."' and '".$end."' and
                wm.branch_kode = '".$branch_kode."'
        ";

        $data = null;

        $d_sql = $m_conf->hydrateRaw( $sql );
        if ( $d_sql->count() > 0 ) {
            $data = $d_sql->toArray();
        }

        return $data;
    }

    public function saveEndShift()
    {
        try {
            $m_sak = new \Model\Storage\SaldoAkhirKasir_model();
            $now = $m_sak->getDate();

            $m_sak->tanggal = $now['waktu'];
            $m_sak->user_id = $this->userid;
            $m_sak->nominal = 0;
            $m_sak->branch_kode = $this->kodebranch;
            $m_sak->save();

            $deskripsi = 'di-simpan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_sak, $deskripsi );

            $this->result['status'] = 1;
            $this->result['message'] = 'Shift anda berhasil di akhiri.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function saveClosingOrder()
    {
        try {
            $data_penjualan =    $this->getDataPenjualan();
            $data_void_menu = $this->getDataVoidMenu();

            $m_clo = new \Model\Storage\ClosingOrder_model();
            $now = $m_clo->getDate();

            $kode = $m_clo->getNextId();

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_hitung_stok_awal @tanggal = '".$now['waktu']."'";

            $m_clo->kode = $kode;
            $m_clo->tanggal = $now['waktu'];
            $m_clo->user_id = $this->userid;
            $m_clo->branch_kode = $this->kodebranch;
            $m_clo->save();

            if ( !empty($data_penjualan) ) {
                foreach ($data_penjualan as $k_data => $v_data) {
                    $m_clom = new \Model\Storage\ClosingOrderMenu_model();

                    $m_clom->closing_order_kode = $kode;
                    $m_clom->menu_kode = $v_data['menu_kode'];
                    $m_clom->jumlah = $v_data['jumlah'];
                    $m_clom->bom_id = $v_data['bom_id'];
                    $m_clom->tbl_name = 'jual';
                    $m_clom->kode_trans = $v_data['faktur_kode'];
                    $m_clom->save();
                }
            }

            if ( !empty($data_void_menu) ) {
                foreach ($data_void_menu as $k_data => $v_data) {
                    $m_clom = new \Model\Storage\ClosingOrderMenu_model();

                    $m_clom->closing_order_kode = $kode;
                    $m_clom->menu_kode = $v_data['menu_kode'];
                    $m_clom->jumlah = $v_data['jumlah'];
                    $m_clom->bom_id = $v_data['bom_id'];
                    $m_clom->tbl_name = 'waste_menu';
                    $m_clom->kode_trans = $v_data['kode_trans'];
                    $m_clom->save();
                }
            }

            $deskripsi = 'di-simpan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_clo, $deskripsi );

            $this->result['status'] = 1;
            $this->result['content'] = array('kode' => $kode);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitungStok()
    {
        $params = $this->input->post('params');

        try {
            $kode = $params['kode'];

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_kurang_stok @kode = '".$kode."', @table = 'closing_order'";

            $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}