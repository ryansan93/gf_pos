<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller
{
	private $permission;
	private $userid;
	private $branch_kode;
	private $username;
	private $password;
	private $isLogin = FALSE;
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->add_external_js(
			array(
				"assets/select2/js/select2.min.js",
				"assets/login/js/login.js"
			)
		);
		$this->add_external_css(
			array(
				"assets/select2/css/select2.min.css",
				"assets/login/css/login.css"
			)
		);

		$data = $this->includes;

        $data['username'] = $this->getUsername();
		$this->load->view('user/login', $data);
	}

	public function getUsername()
	{
		$m_user = new \Model\Storage\User_model();
		$d_user = $m_user->where('status_user', 1)->get()->toArray();

		return $d_user;
	}

	public function checkLogin()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$pin_branch = $this->input->post('pin_branch');
		$jml_uang = $this->input->post('jml_uang');

		$m_branch = new \Model\Storage\Branch_model();
		$branch = $m_branch->where('pin_branch', $pin_branch)->first();

		if ( $branch ) {
			$m_user = new \Model\Storage\User_model();
			$user = $m_user->where('username_user', trim($username))->with(['detail_user'])->get()->first();

			if($username == $user['username_user']) {
				if ( $user['status_user'] == 1 ) {
					$m_group = new \Model\Storage\Group_model();
					$group = $m_group->where('id_group', $user['detail_user']['id_group'])->with(['detail_group'])->get()->first();

					$listUrl = \Model\Storage\DetGroup_model::whereIdGroup($user['detail_user']['id_group'])->with('detail_fitur')->get()->lists('detail_fitur.path_detfitur');

					$fitur = array();
					if ( $group ) {
						$group = $group->toArray();
						foreach ($group['detail_group'] as $key => $v_group) {
							$detail = $v_group['detail_fitur'];
							if ( !empty($detail) ) {
								if ( $v_group['detail_fitur']['fitur']['status'] == 1 ) {
									$fitur[$v_group['detail_fitur']['fitur']['id_fitur']]['header_fitur'] = $v_group['detail_fitur']['fitur']['nama_fitur'];
									$fitur[$v_group['detail_fitur']['fitur']['id_fitur']]['id_header_fitur'] = $v_group['detail_fitur']['fitur']['id_fitur'];
									$fitur[$v_group['detail_fitur']['fitur']['id_fitur']]['urut'] = $v_group['detail_fitur']['fitur']['urut'];
									$fitur[$v_group['detail_fitur']['fitur']['id_fitur']]['detail'][] = $v_group['detail_fitur'];
								}
							}
						}
					}


					// MAPPING HAK AKSES PER FITUR PER GROUP
					$ak = null;
					$akses_khusus = null;
					foreach ($fitur as $k_fitur => $v_fitur) {
						foreach ($v_fitur['detail'] as $k_dfitur => $v_dfitur) {
							foreach ($group['detail_group'] as $key => $v_group) {
								if ( $v_group['id_detfitur'] == $v_dfitur['id_detfitur'] ) {
									// cetak_r( $user['detail_user']['id_group'].' | '.$v_dfitur['id_detfitur'] );
									if ( empty($ak) ) {
										$m_ak = new \Model\Storage\AksesKhusus_model();
										$ak = $m_ak->where('id_group', $user['detail_user']['id_group'])->where('id_detfitur', $v_dfitur['id_detfitur'])->get();

										if ( $ak->count() > 0 ) {
											$ak = $ak->toArray();
											foreach ($ak as $k_ak => $v_ak) {
												$akses_khusus[] = $v_ak['akses_khusus'];
											}
										} else {
											$ak = null;
										}
									}

									$fitur[$k_fitur]['detail'][$k_dfitur]['akses']['a_view'] = $v_group['a_view'];
									$fitur[$k_fitur]['detail'][$k_dfitur]['akses']['a_submit'] = $v_group['a_submit'];
									$fitur[$k_fitur]['detail'][$k_dfitur]['akses']['a_edit'] = $v_group['a_edit'];
									$fitur[$k_fitur]['detail'][$k_dfitur]['akses']['a_delete'] = $v_group['a_delete'];
									$fitur[$k_fitur]['detail'][$k_dfitur]['akses']['a_ack'] = $v_group['a_ack'];
									$fitur[$k_fitur]['detail'][$k_dfitur]['akses']['a_approve'] = $v_group['a_approve'];
									$fitur[$k_fitur]['detail'][$k_dfitur]['akses']['a_khusus'] = $akses_khusus;
								}
							}
						}
					}

					foreach ($fitur as $key => $val) {
						$sort_fitur = $this->msort($val['detail'], 'nama_detfitur');
						$fitur[$val['id_header_fitur']]['detail'] = $sort_fitur;
					}

					$data_fitur = $this->msort($fitur, 'urut');

					// ENCRYPT PASSWORD
					$this->load->helper('phppass');
					$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

					$hash_password = $user['pass_user'];
					$success = $hasher->CheckPassword($password,$hash_password);

					if ( $success ) {
						$dataUser = $user->toArray();
						$dataUser['isLogin'] = 1;
						$dataUser['kodeBranch'] = $branch->kode_branch;
						$dataUser['namaBranch'] = $branch->nama;
						$dataUser['alamatBranch'] = $branch->alamat;
						$dataUser['telpBranch'] = $branch->telp;
						$dataUser['Fitur'] = $data_fitur;

						$this->userid = $user['id_user'];
						$this->branch_kode = $branch->kode_branch;

						$this->session->set_userdata($dataUser);
						$this->session->set_userdata('url',json_encode($listUrl));

						$this->result['status'] = 1;
					} else {
						/* password tidak sesuai */
						$this->result['message'] = 'Password tidak sesuai';
					}
				} else {
					$this->result['message'] = 'User yang anda masukkan sudah tidak aktif.<br>Hubungi administrator untuk mengaktifkan kembali.<br>Terima Kasih';
				}
			} else {
				/* user tidak ditemukan */
				$this->result['message'] = 'User tidak ditemukan';
			}
		} else {
			/* branch tidak ditemukan */
			$this->result['message'] = 'Branch tidak ditemukan';
		}

		echo display_json($this->result);
	}

	public function copyStok()
	{
		$m_conf = new \Model\Storage\Conf();
		$now = $m_conf->getDate();

		$tanggal = $now['tanggal'];

		$m_cs = new \Model\Storage\CopyStok_model();
		$d_cs = $m_cs->where('tanggal', $tanggal)->first();

		if ( !$d_cs ) {
			$sql = "EXEC sp_copy_stok '".$tanggal."'";
			$d_conf = $m_conf->hydrateRaw($sql);

			if ( $d_conf->count() > 0 ) {
				$m_cs = new \Model\Storage\CopyStok_model();
	
				$m_cs->tanggal = $tanggal;
				$m_cs->save();
	
				$deskripsi_log = 'di-copy';
				Modules::run( 'base/event/save', $m_cs, $deskripsi_log );
	
				$this->result['status'] = 1;
			} else {
				$this->result['message'] = 'Data stok gagal di copy.';
			}
		} else {
			$this->result['status'] = 1;
		}

		echo display_json($this->result);
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('user/Login');
	}

	public function isLogin()
	{
		return $this->session->userdata('isLogin');
	}

	public function reset($iduser){
		$this->load->helper('phppass');
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$password = $iduser;
		$hash_password = $hasher->HashPassword($password);

		$m_usr = new \Model\Storage\User_model();
		$d_usr = $m_usr->where('id_user', $iduser)->with(['detail_user'])->first();

		if( isset($d_usr['id_user']) ){
			$m_usr->where('id_user', $iduser)->update(array('pass_user'=>$hash_password));

			$m_dusr = new \Model\Storage\DetUser_model();
			$m_dusr->where('id_detuser', $d_usr['detail_user']['id_detuser'])->update(array(
				'pass_detuser' => $hash_password
			));

			echo 'reset password berhasil';
			echo 'password baru adalah <strong>'.$password.'</strong>';
		}else{
			echo 'reset password gagal';
		}
	}

	public function msort($array, $key, $sort_flags = SORT_REGULAR) {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        // @TODO This should be fixed, now it will be sorted as string
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                asort($mapping, $sort_flags);
                $sorted = array();
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }

    public function tes()
    {
    	$password = 'argopurosa612';

    	$this->load->helper('phppass');
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

		$hash_password = $user['pass_user'];
		$success = $hasher->CheckPassword($password,$hash_password);
    }
}