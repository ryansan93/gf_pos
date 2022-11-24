<?php defined('BASEPATH') or exit('No direct script access allowed');

class Member extends Public_Controller
{
    private $pathView = 'master/member/';
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

    public function modalMember()
    {
        $m_member = new \Model\Storage\Member_model();
        $now = $m_member->getDate();

        $d_member = $m_member->where('status', 1)->orderBy('kode_member', 'desc')->get();

        $data = null;
        if ( $d_member->count() > 0 ) {
            $data = $d_member->toArray();
        }

        $content['akses'] = $this->hasAkses;
        $content['tanggal'] = $now['tanggal'];
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_member', $content, TRUE);

        echo $html;
    }

    public function addForm()
    {
        $content = null;

        $html = $this->load->view($this->pathView . 'add_form', $content, TRUE);

        echo $html;
    }

    public function viewForm()
    {
        $kode = $this->input->get('kode');

        $m_member = new \Model\Storage\Member_model();
        $now = $m_member->getDate();

        $d_member = $m_member->where('kode_member', $kode)->first();

        $data = null;
        if ( $d_member ) {
            $data = $d_member->toArray();
        }

        $content['tanggal'] = $now['tanggal'];
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'view_form', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();
            $now = $m_member->getDate();

            $kode_member = $m_member->getNextId();

            $m_member->kode_member = $kode_member;
            $m_member->nama = $params['nama'];
            $m_member->no_telp = $params['no_telp'];
            $m_member->alamat = $params['alamat'];
            $m_member->privilege = 0;
            $m_member->status = 1;
            $m_member->tgl_berakhir = prev_date(date('Y-m-d', strtotime($now['tanggal']. ' + 1 years')));
            $m_member->mstatus = 1;
            $m_member->save();

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'nama' => $params['nama'],
                    'no_telp' => $params['no_telp'],
                    'alamat' => $params['alamat'],
                    'privilege' => $params['privilege']
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di ubah.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'status' => 0
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function aktif()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();
            $now = $m_member->getDate();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'mstatus' => 1,
                    'tgl_berakhir' => prev_date(date('Y-m-d', strtotime($now['tanggal']. ' + 1 years')))
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-aktifkan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di aktifkan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function nonAktif()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();
            $now = $m_member->getDate();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-nonaktifkan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di nonaktifkan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function getMember()
    {
        $m_member = new \Model\Storage\Member_model();
        $d_member = $m_member->where('status', 1)->get();

        $data = null;
        if ( $d_member->count() > 0 ) {
            $data = $d_member->toArray();
        }

        return $data;
    }

    public function modalSaldoMember()
    {
        $m_sm = new \Model\Storage\SaldoMember_model();
        $d_sm = $m_sm->where('status', 1)->with(['member'])->get();

        $data = null;
        if ( $d_sm->count() > 0 ) {
            $data = $d_sm->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_saldo_member', $content, TRUE);

        echo $html;
    }

    public function addSaldoForm()
    {
        $content['member'] = $this->getMember();

        $html = $this->load->view($this->pathView . 'add_saldo_form', $content, TRUE);

        echo $html;
    }

    public function viewSaldoForm()
    {
        $kode = $this->input->get('kode');

        $m_sm = new \Model\Storage\SaldoMember_model();
        $d_sm = $m_sm->where('id', $kode)->first();

        $data = null;
        if ( $d_sm ) {
            $data = $d_sm->toArray();
        }

        $content['data'] = $data;
        $content['member'] = $this->getMember();

        $html = $this->load->view($this->pathView . 'view_saldo_form', $content, TRUE);

        echo $html;
    }

    public function saveSm()
    {
        $params = $this->input->post('params');
        try {
            $m_sm = new \Model\Storage\SaldoMember_model();

            $m_sm->member_kode = $params['kode_member'];
            $m_sm->saldo = $params['saldo'];
            $m_sm->status = 1;
            $m_sm->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_sm, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function editSm()
    {
        $params = $this->input->post('params');
        try {
            $m_sm = new \Model\Storage\SaldoMember_model();

            $m_sm->where('id', $params['kode'])->update(
                array(
                    'member_kode' => $params['kode_member'],
                    'saldo' => $params['saldo']
                )
            );

            $d_sm = $m_sm->where('id', $params['kode'])->first();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_sm, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di ubah.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deleteSm()
    {
        $params = $this->input->post('params');
        try {
            $m_sm = new \Model\Storage\SaldoMember_model();

            $id = $params['kode'];

            $m_sm->where('id', $id)->update(
                array(
                    'status' => 0
                )
            );

            $d_sm = $m_sm->where('id', $id)->first()->toArray();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $m_sm, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}