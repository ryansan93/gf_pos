<?php defined('BASEPATH') or exit('No direct script access allowed');

class MemberGroup extends Public_Controller
{
    private $pathView = 'master/member_group/';
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

    public function modalMemberGroup()
    {
        $m_member_group = new \Model\Storage\MemberGroup_model();
        $now = $m_member_group->getDate();

        $d_member_group = $m_member_group->where('status', 1)->orderBy('nama', 'desc')->get();

        $data = null;
        if ( $d_member_group->count() > 0 ) {
            $data = $d_member_group->toArray();
        }

        $content['akses'] = $this->hasAkses;
        $content['tanggal'] = $now['tanggal'];
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_member_group', $content, TRUE);

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
        $id = $this->input->get('kode');

        $m_mg = new \Model\Storage\MemberGroup_model();
        $now = $m_mg->getDate();

        $d_mg = $m_mg->where('id', $id)->first();

        $data = null;
        if ( $d_mg ) {
            $data = $d_mg->toArray();
        }

        $content['akses'] = $this->hasAkses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'view_form', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');
        try {
            $m_mg = new \Model\Storage\MemberGroup_model();
            $now = $m_mg->getDate();

            $m_mg->nama = $params['nama'];
            $m_mg->status = 1;
            $m_mg->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_mg, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member group berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');
        try {
            $m_mg = new \Model\Storage\MemberGroup_model();

            $id = $params['kode'];

            $m_mg->where('id', $id)->update(
                array(
                    'nama' => $params['nama']
                )
            );

            $d_member = $m_mg->where('id', $id)->first()->toArray();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_mg, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member group berhasil di ubah.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');
        try {
            $m_mg = new \Model\Storage\MemberGroup_model();

            $id = $params['kode'];

            $m_mg->where('id', $id)->update(
                array(
                    'status' => 0
                )
            );

            $d_member = $m_mg->where('id', $id)->first()->toArray();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $m_mg, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member group berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}