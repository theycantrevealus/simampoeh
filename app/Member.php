<?php

namespace PondokCoder;

use PondokCoder\Authorization as Authorization;
use PondokCoder\Utility as Utility;
use PondokCoder\Modul as Modul;
use PondokCoder\Poli as Poli;
use PondokCoder\Unit as Unit;
use \Firebase\JWT\JWT;

class Member extends Utility {
    static $pdo, $query;

    protected static function getConn(){
        return self::$pdo;
    }

    public function __construct($connection) {
        self::$pdo = $connection;
        self::$query = new Query(self::$pdo);
    }

    public function __GET__($parameter = array())
    {
        if ($parameter[1] == 'detail') {
            //
        }
    }



    public function __POST__($parameter = array())
    {
        switch ($parameter['request']) {
            case 'login':
                return self::login($parameter);
                break;
        }
    }


    private function register($parameter) {
        $parameterBuilder = array();

        $checkNIK = self::$query->select('member', array(
            'uid'
        ))
            ->where(array(
                'member.nik' => '= ?',
                'AND',
                'member.status_hapus' => '= ?'
            ), array(
                $parameter['nik'], 'N'
            ))
            ->execute();

        if(count($checkNIK['response_data']) === 0) {
            $checkEmail = self::$query->select('member', array('uid'))
                ->where(array(
                    'member.email' => '= ?',
                    'AND',
                    'member.status_hapus' => '= ?'
                ), array(
                    $parameter['email'], 'N'
                ))
                ->execute();

            if(count($checkEmail['response_data']) === 0) {

                $DataMember = $checkEmail['response_data'][0];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"http://siakmedan3.medan.depdagri.go.id/ajax/sibisa/get-nik");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,'nik=' . $DataMember['nik']);

                $json_object = json_decode(curl_exec($ch));

                curl_close ($ch);

                $nama = parent::anti_injection($json_object->NAMA_LGKP);
                $tempat_lahir = parent::anti_injection($json_object->TMPT_LHR);
                $tanggal_lahir = parent::anti_injection($json_object->TGL_LHR);
                $agama = parent::anti_injection($json_object->AGAMA);
                $no_kk = parent::anti_injection($json_object->NO_KK);
                $jenis_kelamin = parent::anti_injection($json_object->JENIS_KLMIN);

                $id_provinsi = $json_object->KARTU_KELUARGA->NO_PROP;
                $id_kabupaten= $json_object->KARTU_KELUARGA->NO_KAB;
                $id_kecamatan= $json_object->KARTU_KELUARGA->NO_KEC;
                $id_kelurahan= $json_object->KARTU_KELUARGA->NO_KEL;

                $alamat = $json_object->KARTU_KELUARGA->ALAMAT;
                $kode_pos = $json_object->KARTU_KELUARGA->KODE_POS;

                $nama_provinsi = $json_object->KARTU_KELUARGA->WILAYAH->KECAMATAN->KABUPATEN->PROPINSI->NAMA_PROP;
                $nama_kabupaten = $json_object->KARTU_KELUARGA->WILAYAH->KECAMATAN->KABUPATEN->NAMA_KAB;
                $nama_kecamatan = $json_object->KARTU_KELUARGA->WILAYAH->KECAMATAN->NAMA_KEC;
                $nama_kelurahan = $json_object->KARTU_KELUARGA->WILAYAH->NAMA_KEL;

                if($parameter['nokk'] == $no_kk) {
                    $uid = parent::gen_uuid();
                    $nama=str_replace("'","''", $nama);
                    $proceed = self::$query->insert('member', array(
                        'uid' => $uid,
                        'email' => $parameter['email'],
                        'nama' => $nama,
                        'nik' => $parameter['nik'],
                        'no_kk' => $no_kk,
                        'id_status' => 1,
                        'waktu_input' => parent::format_date(),
                        'tanggal_lahir' => $tanggal_lahir,
                        'tempat_lahar' => $tempat_lahir,
                        'jenis_kelamin' => $jenis_kelamin,
                        'agama' => $agama,
                        'no_handphone' => $parameter['no_handphone'],
                        'id_provinsi' => $id_provinsi,
                        'nama_provinsi' => $nama_provinsi,
                        'id_kabupaten' => $id_kabupaten,
                        'nama_kabupaten' => $nama_kabupaten,
                        'id_kecamatan' => $id_kecamatan,
                        'nama_kecamatan' => $nama_kecamatan,
                        'id_kelurahan' => $id_kelurahan,
                        'nama_kelurahan' => $nama_kelurahan,
                        'alamat' => $alamat,
                        'kode_pos' => $kode_pos
                    ))
                        ->execute();

                    if($proceed['response_result'] > 0) {
                        $LOG = self::$query->insert('member_log_status', array(
                            'uid_member' => '= ?',
                            'waktu_input' => parent::format_date(),
                            'id_status' => '1'
                        ))
                            ->execute();

                        $parameterBuilder = array(
                            'response_result' => 1,
                            'response_message' => 'Terima kasih sudah mendaftar di Aplikasi Sibisa, Dinas Kependudukan dan Catatan Sipil Kota Medan.Silahkan cek email Anda untuk mengaktivasi akun Anda.'
                        );
                    }
                } else {
                    $parameterBuilder = array(
                        'response_result' => 0,
                        'response_message' => 'Mohon maaf data NIK dan Nomor Kartu Keluarga tidak sesuai. Silahkan diperiksa kembali. Terima kasih'
                    );
                }

            } else {
                $parameterBuilder = array(
                    'response_result' => 0,
                    'response_message' => 'Email Anda sudah terdaftar sebagai member dalam Aplikasi Sibisa. Silahkan login atau menghubungi Administrator Aplikasi Sibisa jika Anda merasa belum pernah mendaftar di Aplikasi'
                );
            }
        } else {
            $parameterBuilder = array(
                'response_result' => 0,
                'response_message' => 'NIK Anda sudah terdaftar sebagai member dalam Aplikasi Sibisa. Silahkan login atau menghubungi Administrator Aplikasi Sibisa jika Anda merasa belum pernah mendaftar di Aplikasi'
            );
        }
    }


    private function login($parameter) {
        $data = self::$query->select('member', array(
            'uid' , 'email', 'password', 'nama', 'gambar', 'nik', 'no_kk',
            'status_hapus', 'status_login', 'id_status', 'waktu_input',
            'tanggal_lahir', 'tempat_lahir', 'jenis_kelamin', 'agama', 'no_handphone',
            'id_provinsi', 'nama_provinsi',
            'id_kabupaten', 'nama_kabupaten',
            'id_kecamatan', 'nama_kecamatan',
            'id_kelurahan', 'nama_kelurahan',
            'alamat', 'kode_pos'
        ))
            ->where(array(
                'member.status_hapus' => '= ?',
                'AND',
                'member.nik' => '= ?',
                'AND',
                'member.password' => '= ?',
            ), array(
                'N', $parameter['nik'], parent::encrypt($parameter['password'])
            ))
            ->execute();

        $parameterBuilder = array();

        if(count($data['response_data']) > 0) {


            $LOG = self::$query->insert('member_log_status', array(
                'uid_member' => $data['response_data'][0]['uid'],
                'waktu_input' => parent::format_date(),
                'id_status' => 10
            ))
                ->execute();

            $secret_key = file_get_contents('taknakal.pub');

            //Register JWT
            $iss = __HOSTNAME__;
            $iat = time();
            $nbf = $iat + 10;
            $exp = $iat + 30;
            $aud = 'users_library';

            $user_arr_data = array(
                'uid' => $data['response_data'][0]['uid'],
                'foto' => $data['response_data'][0]['foto'],
                'nik' => $data['response_data'][0]['nik'],
                'password' => $data['response_data'][0]['password'],
                'email' => $data['response_data'][0]['email'],
                'nama' => $data['response_data'][0]['nama'],
                'no_handphone' => $data['response_data'][0]['no_handphone'],
                'kecamatan' => $data['response_data'][0]['id_kecamatan'],
                'kelurahan' => $data['response_data'][0]['id_kelurahan'],
                'log_id' => 1
            );

            $payload_info = array(
                'iss' => $iss,
                'iat' => $iat,
                'nbf' => $nbf,
                'exp' => $exp,
                'aud' => $aud,
                'data' => $user_arr_data
            );
            $jwt = JWT::encode($payload_info, $secret_key);

            unset($data['response_data'][0]['password']);
            $parameterBuilder['response_package'] = array();
            $parameterBuilder['token'] = $jwt;
            $parameterBuilder['response_package']['response_result'] = $data['response_result'];
            $parameterBuilder['response_package']['response_message'] = 'Login berhasil';
            //$parameterBuilder['response_package']['response_token'] = $jwt;
            $parameterBuilder['response_package']['response_data'] = array($data['response_data'][0]);
        } else {
            $parameterBuilder = array(
                'data' => $data,
                'response_result' => 0,
                'response_message' => 'Email / Password salah',
                'response_data' => $data['response_data']
            );
        }

        return $parameterBuilder;
    }
}

?>