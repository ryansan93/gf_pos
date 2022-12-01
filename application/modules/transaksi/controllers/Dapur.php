<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dapur extends Public_Controller
{
    private $pathView = 'transaksi/dapur/';
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
                    "assets/transaksi/dapur/js/dapur.js"
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    "assets/transaksi/dapur/css/dapur.css"
                )
            );
            $data = $this->includes;

            // exec("cd websocket\server && forever stopall 2>&1");
            // exec("cd websocket\server && forever start index.js 2>&1");

            $content['akses'] = $this->hakAkses;
            $content['persen_ppn'] = $this->persen_ppn;
            $content['kode_branch'] = $this->kodebranch;

            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function listPesanan()
    {
        $content['data_outstanding'] = $this->getDataPesananOutstanding( $this->kodebranch );
        $content['data_done'] = $this->getDataPesananDone( $this->kodebranch );

        $html = $this->load->view($this->pathView . 'list_pesanan', $content, TRUE);

        echo $html;
    }

    public function getDataPesananOutstanding($kodebranch)
    {
        $m_pesanan = new \Model\Storage\Pesanan_model();
        // $d_pesanan = $m_pesanan->where('branch', $kodebranch)->where('status', 1)->with(['pesanan_item', 'meja'])->get();
        $sql = "
            select 
                p.kode_pesanan,
                p.member,
                p.privilege,
                m.id,
                m.nama_meja,
                l.id,
                l.nama_lantai,
                pi.* 
            from pesanan_item pi
            right join
                (
                    select 
                        pesanan_kode 
                    from 
                        pesanan_item pi
                    right join 
                        menu mn 
                        on 
                            pi.menu_kode = mn.kode_menu 
                    where 
                        pi.proses is null or
                        pi.proses <> 2 and
                        mn.branch_kode = '".$kodebranch."'
                    group by 
                        pi.pesanan_kode
                ) pi2
                on
                    pi.pesanan_kode = pi2.pesanan_kode
            right join
                pesanan p 
                on
                    pi.pesanan_kode = p.kode_pesanan
            right join
                meja m
                on
                    p.meja_id = m.id 
            right join
                lantai l 
                on
                    m.lantai_id = l.id 
            right join
                menu mn
                on
                    pi.menu_kode = mn.kode_menu 
            where 
                pi.pesanan_kode is not null and
                mn.branch_kode = '".$kodebranch."' and
                p.mstatus = 1 and
                p.tgl_pesan between SUBSTRING(convert(varchar(20), GETDATE(), 120), 1, 10)+' 00:00:00' and SUBSTRING(convert(varchar(20), GETDATE(), 120), 1, 10)+' 23:59:59'
        ";

        $d_pesanan = $m_pesanan->hydrateRaw($sql);

        $data = null;
        if ( $d_pesanan->count() > 0 ) {
            $d_pesanan = $d_pesanan->toArray();

            foreach ($d_pesanan as $k => $val) {
                $m_pid = new \Model\Storage\PesananItemDetail_model();
                $d_pid = $m_pid->where('pesanan_item_kode', $val['kode_pesanan_item'])->get();

                $data[ $val['kode_pesanan'] ]['privilege'] = $val['privilege'];
                $data[ $val['kode_pesanan'] ]['meja']['nama_meja'] = $val['nama_meja'];
                $data[ $val['kode_pesanan'] ]['meja']['lantai']['nama_lantai'] = $val['nama_lantai'];
                $data[ $val['kode_pesanan'] ]['pesanan_item'][] = array(
                    'jumlah' => $val['jumlah'],
                    'menu_nama' => $val['menu_nama'],
                    'proses' => $val['proses'],
                    'kode_pesanan_item' => $val['kode_pesanan_item'],
                    'request' => $val['request'],
                    'pesanan_item_detail' => ($d_pid->count() > 0) ? $d_pid->toArray() : null
                );
            }
        }

        return $data;
    }

    public function getDataPesananDone($kodebranch)
    {
        $m_pesanan = new \Model\Storage\Pesanan_model();
        // $d_pesanan = $m_pesanan->where('branch', $kodebranch)->where('status', 1)->with(['pesanan_item', 'meja'])->get();
        $sql = "
            select 
                p.kode_pesanan,
                p.member,
                p.privilege,
                m.id,
                m.nama_meja,
                l.id,
                l.nama_lantai,
                pi.* 
            from pesanan_item pi
            right join
                (
                    select 
                        p.kode_pesanan as pesanan_kode 
                    from 
                        pesanan p 
                    where
                        (
                            select 
                                count(*) 
                            from 
                                pesanan_item pi 
                            right join 
                                menu mn 
                                on 
                                    pi.menu_kode = mn.kode_menu
                            where 
                                pi.pesanan_kode = p.kode_pesanan and
                                mn.branch_kode = '".$kodebranch."'
                            group by pi.pesanan_kode
                        ) = (
                            select 
                                count(*) 
                            from 
                                pesanan_item pi 
                            right join 
                                menu mn 
                                on 
                                    pi.menu_kode = mn.kode_menu
                            where 
                                pi.pesanan_kode = p.kode_pesanan and 
                                pi.proses = 2 and
                                mn.branch_kode = '".$kodebranch."'
                            group by 
                                pi.pesanan_kode
                        )
                    group by
                        p.kode_pesanan 
                ) pi2
                on
                    pi.pesanan_kode = pi2.pesanan_kode
            right join
                pesanan p 
                on
                    pi.pesanan_kode = p.kode_pesanan
            right join
                meja m
                on
                    p.meja_id = m.id 
            right join
                lantai l 
                on
                    m.lantai_id = l.id 
            right join
                menu mn
                on
                    pi.menu_kode = mn.kode_menu 
            where 
                pi.pesanan_kode is not null and
                mn.branch_kode = '".$kodebranch."' and
                p.mstatus = 1 and
                p.tgl_pesan between SUBSTRING(convert(varchar(20), GETDATE(), 120), 1, 10)+' 00:00:00' and SUBSTRING(convert(varchar(20), GETDATE(), 120), 1, 10)+' 23:59:59'
        ";

        $d_pesanan = $m_pesanan->hydrateRaw($sql);

        $data = null;
        if ( $d_pesanan->count() > 0 ) {
            $d_pesanan = $d_pesanan->toArray();

            foreach ($d_pesanan as $k => $val) {
                $m_pid = new \Model\Storage\PesananItemDetail_model();
                $d_pid = $m_pid->where('pesanan_item_kode', $val['kode_pesanan_item'])->get();

                $data[ $val['kode_pesanan'] ]['privilege'] = $val['privilege'];
                $data[ $val['kode_pesanan'] ]['meja']['nama_meja'] = $val['nama_meja'];
                $data[ $val['kode_pesanan'] ]['meja']['lantai']['nama_lantai'] = $val['nama_lantai'];
                $data[ $val['kode_pesanan'] ]['pesanan_item'][] = array(
                    'jumlah' => $val['jumlah'],
                    'menu_nama' => $val['menu_nama'],
                    'proses' => $val['proses'],
                    'kode_pesanan_item' => $val['kode_pesanan_item'],
                    'request' => $val['request'],
                    'pesanan_item_detail' => ($d_pid->count() > 0) ? $d_pid->toArray() : null
                );
            }
        }

        return $data;
    }

    public function ubahStatusPesanan()
    {
        $params = $this->input->post('params');

        try {
            $m_pi = new \Model\Storage\PesananItem_model();
            $m_pi->where('kode_pesanan_item', $params['kode_pesanan_item'])->update(
                array(
                    'proses' => $params['status_tujuan']
                )
            );

            $d_pi = $m_pi->where('kode_pesanan_item', $params['kode_pesanan_item'])->first();

            $deskripsi_log = 'di-siapkan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_pi, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['content'] = array('status' => $d_pi->proses);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}