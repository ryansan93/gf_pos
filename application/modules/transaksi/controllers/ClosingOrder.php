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
            $content['sales_recapitulation'] = $this->dataSalesRecapitulation( $this->kodebranch );
            $content['shift_detail'] = $this->dataShiftDetail( $this->kodebranch );

            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function mappingDataSales($user_id = null, $branch_kode = null)
    {
        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        $start_date = $now['tanggal'].' 00:00:01';
        $end_date = $now['tanggal'].' 23:59:59';
        // $start_date = '2023-01-23 00:00:01';
        // $end_date = '2023-01-23 23:59:59';

        $sql_user = null;
        $sql_user_select = null;
        $sql_user_group_by = null;
        if ( !empty($user_id) ) {
            $sql_user = "and p.user_id = '".$user_id."'";
            $sql_user_select = "p.user_id, p.nama_user,";
            $sql_user_group_by = "p.user_id, p.nama_user,";
        }
        $sql = "
            select
                ".$sql_user_select."
                km.id as id_kategori_menu,
                km.nama as nama_kategori_menu,
                ji.menu_nama,
                ji.menu_kode,
                sum(ji.jumlah) as jumlah,
                sum(ji.total) as total
            from jual_item ji
            right join
                menu m 
                on
                    ji.menu_kode = m.kode_menu 
            right join
                (
                    select * from kategori_menu where print_cl = 1
                ) km
                on
                    km.id = m.kategori_menu_id 
            right join
                jual j
                on
                    j.kode_faktur = ji.faktur_kode
            right join
                pesanan p
                on
                    p.kode_pesanan = j.pesanan_kode
            where
                j.branch = '".$branch_kode."' and
                j.tgl_trans between '".$start_date."' and '".$end_date."' and
                j.mstatus = 1 ".$sql_user."
            group by
                ".$sql_user_group_by."
                km.id,
                km.nama,
                ji.menu_nama,
                ji.menu_kode
        ";
        $d_data = $m_conf->hydrateRaw( $sql );

        $data_sales = null;
        if ( $d_data->count() > 0 ) {
            $d_data = $d_data->toArray();

            foreach ($d_data as $k_data => $v_data) {
                if ( !empty($user_id) ) {
                    $data_sales[ $v_data['user_id'] ]['user_id'] = $v_data['user_id'];
                    $data_sales[ $v_data['user_id'] ]['nama'] = $v_data['nama_user'];
                    $data_sales[ $v_data['user_id'] ]['kategori_menu'][ $v_data['id_kategori_menu'] ]['id'] = $v_data['id_kategori_menu'];
                    $data_sales[ $v_data['user_id'] ]['kategori_menu'][ $v_data['id_kategori_menu'] ]['nama'] = $v_data['nama_kategori_menu'];
                    $data_sales[ $v_data['user_id'] ]['kategori_menu'][ $v_data['id_kategori_menu'] ]['detail'][] = $v_data;
                } else {
                    $data_sales[ $v_data['id_kategori_menu'] ]['id'] = $v_data['id_kategori_menu'];
                    $data_sales[ $v_data['id_kategori_menu'] ]['nama'] = $v_data['nama_kategori_menu'];
                    $data_sales[ $v_data['id_kategori_menu'] ]['detail'][] = $v_data;
                }
            }
        }

        $sql_user = null;
        $sql_user_select = null;
        $sql_user_group_by = null;
        if ( !empty($user_id) ) {
            $sql_user = "and j.kasir = '".$user_id."'";
            $sql_user_select = "j.kasir, j.nama_kasir,";
            $sql_user_group_by = "j.kasir, j.nama_kasir,";
        }
        $sql = "
            select
                j.branch as kode_branch,
                brc.nama as nama_branch,
                ".$sql_user_select."
                j.kode_faktur,
                bd.kode_jenis_kartu,
                bd.jenis_bayar,
                sum(bd.nominal) as total
            from bayar_det bd
            right join
                bayar b
                on
                    bd.id_header = b.id
            right join
                jual j
                on
                    b.faktur_kode = j.kode_faktur
            right join
                branch brc
                on
                    brc.kode_branch = j.branch
            where
                j.branch = '".$branch_kode."' and
                j.tgl_trans between '".$start_date."' and '".$end_date."' and
                j.mstatus = 1 ".$sql_user." and
                b.mstatus = 1 and
                b.id is not null
            group by
                j.branch,
                brc.nama,
                ".$sql_user_group_by."
                j.kode_faktur,
                bd.kode_jenis_kartu,
                bd.jenis_bayar
        ";
        $d_data = $m_conf->hydrateRaw( $sql );

        $data_cashier = null;
        if ( $d_data->count() > 0 ) {
            $d_data = $d_data->toArray();

            foreach ($d_data as $k_data => $v_data) {
                if ( !empty($user_id) ) {
                    $data_cashier[ $v_data['kasir'] ]['user_id'] = $v_data['kasir'];
                    $data_cashier[ $v_data['kasir'] ]['nama'] = $v_data['nama_kasir'];
                    $data_cashier[ $v_data['kasir'] ]['nama_branch'] = $v_data['nama_branch'];
                    $data_cashier[ $v_data['kasir'] ]['jenis_kartu'][ $v_data['kode_jenis_kartu'] ]['id'] = $v_data['kode_jenis_kartu'];
                    $data_cashier[ $v_data['kasir'] ]['jenis_kartu'][ $v_data['kode_jenis_kartu'] ]['nama'] = $v_data['jenis_bayar'];
                    $data_cashier[ $v_data['kasir'] ]['jenis_kartu'][ $v_data['kode_jenis_kartu'] ]['detail'][] = $v_data;
                } else {
                    $data_cashier[ $v_data['kode_jenis_kartu'] ]['id'] = $v_data['kode_jenis_kartu'];
                    $data_cashier[ $v_data['kode_jenis_kartu'] ]['nama'] = $v_data['jenis_bayar'];
                    $data_cashier[ $v_data['kode_jenis_kartu'] ]['detail'][] = $v_data;
                }
            }
        }

        $data = null;
        $data = array(
            'data_sales' => $data_sales,
            'data_cashier' => $data_cashier
        );

        return $data;
    }

    public function dataShiftDetail( $branch_kode )
    {
        $data = $this->mappingDataSales( null, $branch_kode );

        return $data;
    }

    public function dataSalesRecapitulation( $branch_kode )
    {
        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        $start_date = $now['tanggal'].' 00:00:00';
        $end_date = $now['tanggal'].' 23:59:59';

        $sql_sales_total = "
            select
                sum(j.grand_total) as total
            from jual j
            where
                j.branch = '".$branch_kode."' and
                j.mstatus = 1 and
                j.tgl_trans between '".$start_date."' and '".$end_date."'
        ";
        $nilai_sales_total = 0;

        $d_sales_total = $m_conf->hydrateRaw( $sql_sales_total );
        if ( $d_sales_total->count() > 0 ) {
            $d_sales_total = $d_sales_total->toArray();

            $nilai_sales_total = $d_sales_total[0]['total'];
        }

        $sql_pending = "
            select
                sum(j.grand_total) as total
            from jual j
            where
                j.branch = '".$branch_kode."' and
                j.mstatus = 1 and
                j.lunas = 0 and
                j.hutang = 0 and
                j.tgl_trans between '".$start_date."' and '".$end_date."'
        ";
        $nilai_pending = 0;

        $d_pending = $m_conf->hydrateRaw( $sql_pending );
        if ( $d_pending->count() > 0 ) {
            $d_pending = $d_pending->toArray();

            $nilai_pending = $d_pending[0]['total'];
        }

        $sql_cl = "
            select
                sum(j.grand_total) as total
            from jual j
            where
                j.branch = '".$branch_kode."' and
                j.mstatus = 1 and
                j.lunas = 0 and
                j.hutang = 1 and
                j.tgl_trans between '".$start_date."' and '".$end_date."'
        ";
        $nilai_cl = 0;

        $d_cl = $m_conf->hydrateRaw( $sql_cl );
        if ( $d_cl->count() > 0 ) {
            $d_cl = $d_cl->toArray();

            $nilai_cl = $d_cl[0]['total'];
        }

        $sql_discount = "
            select
                sum(b.diskon) as total
            from jual j
            right join
                bayar b
                on
                    j.kode_faktur = b.faktur_kode
            where
                j.branch = '".$branch_kode."' and
                j.mstatus = 1 and
                j.tgl_trans between '".$start_date."' and '".$end_date."'
        ";
        $nilai_discount = 0;

        $d_discount = $m_conf->hydrateRaw( $sql_discount );
        if ( $d_discount->count() > 0 ) {
            $d_discount = $d_discount->toArray();

            $nilai_discount = $d_discount[0]['total'];
        }

        $nilai_net_sales_total = $nilai_sales_total - ($nilai_cl + $nilai_pending + $nilai_discount);

        $data = array(
            'sales_total' => $nilai_sales_total,
            'pending' => $nilai_pending,
            'cl' => $nilai_cl,
            'discount' => $nilai_discount,
            'net_sales_total' => $nilai_net_sales_total
        );

        return $data;
    }

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
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function printEndShift()
    {
        try {
            $m_conf = new \Model\Storage\Conf();
            $now = $m_conf->getDate();

            $data = $this->mappingDataSales($this->userid, $this->kodebranch);

            if ( !empty($data['data_sales']) ) {
                $data_sales = $data['data_sales'];

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
         
                        // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                        $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                    }
         
                    // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                    return implode($hasilBaris, "\n") . "\n";
                }

                // Enter the share name for your USB printer here
                $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('GTR_KASIR');
                // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

                /* Print a receipt */
                $printer = new Mike42\Escpos\Printer($connector);

                $printer -> initialize();
                $printer -> text('******************** START *********************'."\n");
                $printer -> text('------------------------------------------------'."\n");
                $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer -> text('Sales By Menu Qty & Value | '.$now['tanggal']."\n");
                $printer -> text('------------------------------------------------'."\n");

                $printer -> initialize();
                foreach ($data_sales as $k_ds => $v_ds) {
                    $idx = 0;
                    foreach ($v_ds['kategori_menu'] as $k_km => $v_km) {
                        if ( $idx > 0 ) {
                            $printer -> text("\n");
                        }
                        $printer -> text($v_ds['nama'].' - '.$v_km['nama']."\n");

                        $tot_jumlah = 0;
                        $tot_value = 0;
                        foreach ($v_km['detail'] as $k_det => $v_det) {
                            $printer -> text(buatBaris3Kolom('   - '.$v_det['menu_nama'], '', '', 'center'));
                            $printer -> text(buatBaris3Kolom('      - Qty', ':', angkaRibuan($v_det['jumlah']), 'center'));
                            $printer -> text(buatBaris3Kolom('      - Value', ':', angkaRibuan($v_det['total']), 'center'));

                            $tot_jumlah += $v_det['jumlah'];
                            $tot_value += $v_det['total'];
                        }

                        $printer -> text($v_ds['nama'].' - '.$v_km['nama'].' Summary Total'."\n");
                        $printer -> text(buatBaris3Kolom('   - Qty', ':', angkaRibuan($tot_jumlah), 'center'));
                        $printer -> text(buatBaris3Kolom('   - Value', ':', angkaRibuan($tot_value), 'center'));

                        $idx++;
                    }
                }
                $printer -> text('================================================'."\n");
                $printer -> text('********************** END *********************'."\n");

                $printer -> initialize();
                $printer -> text("\n");

                $printer -> initialize();
                $printer -> text('******************** START *********************'."\n");
                $printer -> text('------------------------------------------------'."\n");
                $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer -> text('Sales By Menu Qty | '.$now['tanggal']."\n");
                $printer -> text('------------------------------------------------'."\n");

                $printer -> initialize();
                foreach ($data_sales as $k_ds => $v_ds) {
                    $idx = 0;
                    foreach ($v_ds['kategori_menu'] as $k_km => $v_km) {
                        if ( $idx > 0 ) {
                            $printer -> text("\n");
                        }
                        $printer -> text($v_ds['nama'].' - '.$v_km['nama']."\n");

                        $tot_jumlah = 0;
                        foreach ($v_km['detail'] as $k_det => $v_det) {
                            $printer -> text(buatBaris3Kolom('   - '.$v_det['menu_nama'], ':', angkaRibuan($v_det['jumlah']), 'center'));

                            $tot_jumlah += $v_det['jumlah'];
                        }

                        $printer -> text(buatBaris3Kolom($v_ds['nama'].' - '.$v_km['nama'].' Summary Total', ':', angkaRibuan($tot_jumlah), 'center'));

                        $idx++;
                    }
                }
                $printer -> text('================================================'."\n");
                $printer -> text('********************** END *********************'."\n");

                $printer -> feed(1);
                $printer -> cut();
                $printer -> close();
            }

            if ( !empty($data['data_cashier']) ) {
                $data_cashier = $data['data_cashier'];

                function buatBaris3Kolom($kolom1, $kolom2, $kolom3, $jenis) {
                    // Mengatur lebar setiap kolom (dalam satuan karakter)
                    if ( $jenis == 'header' ) {
                        $lebar_kolom_1 = 15;
                        $lebar_kolom_2 = 3;
                        $lebar_kolom_3 = 27;
                    }
                    if ( $jenis == 'center' ) {
                        $lebar_kolom_1 = 26;
                        $lebar_kolom_2 = 3;
                        $lebar_kolom_3 = 17;
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
         
                        // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                        $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                    }
         
                    // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                    return implode($hasilBaris, "\n") . "\n";
                }

                // Enter the share name for your USB printer here
                $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('GTR_KASIR');
                // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

                /* Print a receipt */
                $printer = new Mike42\Escpos\Printer($connector);

                $printer -> initialize();
                $printer -> text('******************** START *********************'."\n");
                $printer -> text('REPORT SHIFT KASIR'."\n\n");

                $tot_value_all = 0;

                $printer -> initialize();
                foreach ($data_cashier as $k_dc => $v_dc) {
                    $printer -> text(buatBaris3Kolom('Branch', ':', $v_dc['nama_branch'], 'header'));
                    $printer -> text(buatBaris3Kolom('Kasir', ':', $v_dc['nama'], 'header'));
                    $printer -> text(buatBaris3Kolom('Tanggal Print', ':', substr($now['waktu'], 0, 19), 'header'));
                    $printer -> text('================================================'."\n");

                    $idx = 0;
                    foreach ($v_dc['jenis_kartu'] as $k_jk => $v_jk) {
                        $printer -> text('------------------------------------------------'."\n");
                        $printer -> text($v_jk['nama']."\n");
                        $printer -> text('------------------------------------------------'."\n");

                        $tot_value = 0;
                        foreach ($v_jk['detail'] as $k_det => $v_det) {
                            $printer -> text(buatBaris3Kolom($v_det['kode_faktur'], ':', angkaRibuan($v_det['total']), 'center'));

                            $tot_value += $v_det['total'];
                            $tot_value_all += $v_det['total'];
                        }

                        $printer -> text('------------------------------------------------'."\n");
                        $printer -> text(buatBaris3Kolom('Total '.$v_jk['nama'], ':', angkaRibuan($tot_value), 'center'));

                        $idx++;
                    }
                }
                $printer -> text('================================================'."\n");
                $printer -> text('************************************************'."\n");
                $printer -> text(buatBaris3Kolom('Total All', ':', angkaRibuan($tot_value_all), 'center'));
                $printer -> text('************************************************'."\n");
                $printer -> text("\n");
                $printer -> text('********************** END *********************'."\n");

                $printer -> feed(1);
                $printer -> cut();
                $printer -> close();
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Shift anda berhasil di akhiri.';
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
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

    public function printClosingOrder()
    {
        try {
            $m_conf = new \Model\Storage\Conf();
            $now = $m_conf->getDate();

            $data = $this->mappingDataSales(null, $this->kodebranch);
            $dataSalesRecapitulation = $this->dataSalesRecapitulation($this->kodebranch);

            function buatBaris3Kolom($kolom1, $kolom2, $kolom3, $jenis) {
                // Mengatur lebar setiap kolom (dalam satuan karakter)
                $lebar_kolom_1 = 15;
                $lebar_kolom_2 = 3;
                $lebar_kolom_3 = 27;
     
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
                    // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                    $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                    $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
                    if ( $jenis == 'center' ) {
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
                    } else {
                        $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ");
                    }
     
                    // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                    $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                }
     
                // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                return implode($hasilBaris, "\n") . "\n";
            }

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('GTR_KASIR');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);

            $printer -> initialize();
            $printer -> text(buatBaris3Kolom('Branch', ':', $this->session->userdata()['namaBranch'], 'header'));
            $printer -> text(buatBaris3Kolom('User', ':', $this->userdata['detail_user']['nama_detuser'], 'header'));
            $printer -> text(buatBaris3Kolom('Tanggal Print', ':', substr($now['waktu'], 0, 19), 'header'));
            $printer -> text('================================================'."\n");

            $printer -> initialize();
            $printer -> text('******************** START *********************'."\n");
            $printer -> text('SALES RECAPITULATION'."\n");
            $printer -> text('------------------------------------------------'."\n");
            $printer -> text(buatBaris3Kolom('Sales Total', ':', angkaRibuan($dataSalesRecapitulation['sales_total']), 'center'));
            $printer -> text(buatBaris3Kolom('Pending Sales', ':', angkaRibuan($dataSalesRecapitulation['pending']), 'center'));
            $printer -> text(buatBaris3Kolom('CL', ':', angkaRibuan($dataSalesRecapitulation['cl']), 'center'));
            $printer -> text(buatBaris3Kolom('Discount Total', ':', angkaRibuan($dataSalesRecapitulation['discount']), 'center'));
            $printer -> text(buatBaris3Kolom('Net Sales Total', ':', angkaRibuan($dataSalesRecapitulation['net_sales_total']), 'center'));
            $printer -> text('================================================'."\n");
            $printer -> text('********************* END **********************'."\n\n");

            $data_sales = $data['data_sales'];
            $data_cashier = $data['data_cashier'];
            if ( !empty($data_sales) ) {
                function buatBaris3KolomSales($kolom1, $kolom2, $kolom3, $jenis) {
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
         
                        // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                        $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                    }
         
                    // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                    return implode($hasilBaris, "\n") . "\n";
                }

                $printer -> initialize();
                $printer -> text('******************** START *********************'."\n");
                $printer -> text('REPORT WAITRESS'."\n");
                $printer -> text('------------------------------------------------'."\n");
                $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer -> text('Sales By Menu Qty & Value | '.$now['tanggal']."\n");
                $printer -> text('------------------------------------------------'."\n");

                $printer -> initialize();
                $idx = 0;
                foreach ($data_sales as $k_km => $v_km) {
                    if ( $idx > 0 ) {
                        $printer -> text("\n");
                    }
                    $printer -> text($v_km['nama']."\n");

                    $tot_jumlah = 0;
                    $tot_value = 0;
                    foreach ($v_km['detail'] as $k_det => $v_det) {
                        $printer -> text(buatBaris3KolomSales('   - '.$v_det['menu_nama'], '', '', 'center'));
                        $printer -> text(buatBaris3KolomSales('      - Qty', ':', angkaRibuan($v_det['jumlah']), 'center'));
                        $printer -> text(buatBaris3KolomSales('      - Value', ':', angkaRibuan($v_det['total']), 'center'));

                        $tot_jumlah += $v_det['jumlah'];
                        $tot_value += $v_det['total'];
                    }

                    $printer -> text($v_km['nama'].' Summary Total'."\n");
                    $printer -> text(buatBaris3KolomSales('   - Qty', ':', angkaRibuan($tot_jumlah), 'center'));
                    $printer -> text(buatBaris3KolomSales('   - Value', ':', angkaRibuan($tot_value), 'center'));

                    $idx++;
                }
                $printer -> text('================================================'."\n");

                $printer -> initialize();
                $printer -> text("\n");

                $printer -> initialize();
                $printer -> text('------------------------------------------------'."\n");
                $printer -> setJustification(Mike42\Escpos\Printer::JUSTIFY_CENTER);
                $printer -> text('Sales By Menu Qty | '.$now['tanggal']."\n");
                $printer -> text('------------------------------------------------'."\n");

                $printer -> initialize();
                $idx = 0;
                foreach ($data_sales as $k_km => $v_km) {
                    if ( $idx > 0 ) {
                        $printer -> text("\n");
                    }
                    $printer -> text($v_km['nama']."\n");

                    $tot_jumlah = 0;
                    foreach ($v_km['detail'] as $k_det => $v_det) {
                        $printer -> text(buatBaris3KolomSales('   - '.$v_det['menu_nama'], ':', angkaRibuan($v_det['jumlah']), 'center'));

                        $tot_jumlah += $v_det['jumlah'];
                    }

                    $printer -> text(buatBaris3KolomSales($v_km['nama'].' Summary Total', ':', angkaRibuan($tot_jumlah), 'center'));

                    $idx++;
                }
                $printer -> text('================================================'."\n");
                $printer -> text('********************* END **********************'."\n");
            }

            if ( !empty($data_cashier) ) {
                function buatBaris3KolomCashier($kolom1, $kolom2, $kolom3, $jenis) {
                    // Mengatur lebar setiap kolom (dalam satuan karakter)
                    if ( $jenis == 'header' ) {
                        $lebar_kolom_1 = 15;
                        $lebar_kolom_2 = 3;
                        $lebar_kolom_3 = 27;
                    }
                    if ( $jenis == 'center' ) {
                        $lebar_kolom_1 = 26;
                        $lebar_kolom_2 = 3;
                        $lebar_kolom_3 = 17;
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
         
                        // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                        $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
                    }
         
                    // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
                    return implode($hasilBaris, "\n") . "\n";
                }

                $printer -> initialize();
                $printer -> text("\n".'******************** START *********************'."\n");
                $printer -> text('REPORT KASIR'."\n");

                $tot_value_all = 0;

                $printer -> initialize();
                foreach ($data_cashier as $k_jk => $v_jk) {
                    $printer -> text('------------------------------------------------'."\n");
                    $printer -> text($v_jk['nama']."\n");
                    $printer -> text('------------------------------------------------'."\n");

                    $tot_value = 0;
                    foreach ($v_jk['detail'] as $k_det => $v_det) {
                        $printer -> text(buatBaris3KolomCashier($v_det['kode_faktur'], ':', angkaRibuan($v_det['total']), 'center'));

                        $tot_value += $v_det['total'];
                        $tot_value_all += $v_det['total'];
                    }

                    $printer -> text('------------------------------------------------'."\n");
                    $printer -> text(buatBaris3KolomCashier('Total '.$v_jk['nama'], ':', angkaRibuan($tot_value), 'center'));
                }
                $printer -> text('================================================'."\n");
                $printer -> text('************************************************'."\n");
                $printer -> text(buatBaris3KolomCashier('Total All', ':', angkaRibuan($tot_value_all), 'center'));
                $printer -> text('********************* END **********************'."\n");
            }

            $printer -> feed(1);
            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
            $this->result['message'] = 'Print Berhasil.';
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }
}