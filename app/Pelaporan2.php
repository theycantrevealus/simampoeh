<?php

namespace PondokCoder;

use PondokCoder\Authorization as Authorization;
use PondokCoder\Utility as Utility;
use \Firebase\JWT\JWT;

class Pelaporan2 extends Utility
{
    static $pdo, $query;

    protected static function getConn()
    {
        return self::$pdo;
    }

    public function __construct($connection)
    {
        self::$pdo = $connection;
        self::$query = new Query(self::$pdo);
    }

    public function __GET__($parameter = array())
    {
        switch ($parameter[1]) {
            default:
                return array();
        }
    }

    public function __POST__($parameter = array())
    {
        switch ($parameter['request']) {

            case 'biaya':
                return self::biaya($parameter);
                break;

            case 'history_saya':
                return self::history_saya($parameter);
                break;
            case 'tambah_aktalahir':
                return self::tambah_aktalahir($parameter);
                break;

            case 'tambah_aktaaku':
                return self::tambah_aktaaku($parameter);
                break;

            case 'tambah_sahanak':
                return self::tambah_sahanak($parameter);
                break;

            case 'tambah_angkatanak':
                return self::tambah_angkatanak($parameter);
                break;

            case 'tambah_aktakawin':
                return self::tambah_aktakawin($parameter);
                break;

            case 'tambah_aktamati':
                return self::tambah_aktamati($parameter);
                break;

            case 'tambah_kartukeluarga':
                return self::tambah_kartukeluarga($parameter);
                break;

            case 'tambah_ktp':
                return self::tambah_ktp($parameter);
                break;

            case 'tambah_identitasanak':
                return self::tambah_identitasanak($parameter);
                break;

            case 'tambah_suratpindah':
                return self::tambah_suratpindah($parameter);
                break;

            case 'partial_upload':
                return self::partial_upload($parameter);
                break;



            default:
                return array();
        }
    }

    private function biaya($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);
        $data = self::$query->select('trx_pembayaran_bank_sumut', array(
            'no_sts',
            'jatuh_tempo'
        ))
            ->join('pengajuan', array(
                'uid'
            ))
            ->join('pelayanan_jenis', array(
                'id', 'nama as nama_jenis'
            ))
            ->join('member', array(
                'uid'
            ))
            ->on(array(
                array('trx_pembayaran_bank_sumut.uid_pengajuan', '=', 'pengajuan.uid'),
                array('pengajuan.jenis', '=', 'pelayanan_jenis.id'),
                array('pengajuan.uid_member', '=', 'member.uid')
            ))
            ->where(array(
                'trx_pembayaran_bank_sumut.deleted_at' => 'IS NULL',
                'AND',
                'trx_pembayaran_bank_sumut.tgl_bayar' => 'IS NULL',
                'AND',
                'member.uid' => '= ?'
            ), array(
                $UserData['data']->uid
            ))
            ->execute();
        return array(
            'query' => $data['response_query'],
            'response_data' => isset($data['response_data']) ? $data['response_data'] : array(),
            'response_result' => count($data['response_data']),
            'response_message' => 'Nah'
        );
    }


    private function history_saya($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $data = self::$query->select('pengajuan', array(
            'uid', 'id_status', 'jenis', 'kode', 'waktu_input'
        ))
            ->join('pelayanan_jenis', array(
                'nama as nama_pelayanan'
            ))
            ->join('pengajuan_status', array(
                'nama as nama_status'
            ))
            ->on(array(
                array('pengajuan.jenis', '=', 'pelayanan_jenis.id'),
                array('pengajuan.id_status', '=', 'pengajuan_status.id')
            ))
            ->where(array(
                'pengajuan.uid_member' => '= ?'
            ), array(
                $UserData['data']->uid
            ))
            ->execute();

        foreach ($data['response_data'] as $key => $value) {
            $data['response_data'][$key]['waktu_input'] = date('d F Y', strtotime($value['waktu_input']));
        }

        return array(
            'response_result' => count($data['response_data']),
            'response_data' => (isset($data['response_data']) ? $data['response_data'] : array())
        );
    }




    private function partial_upload($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);


        $parameterBuilder = array();

        //Upload File
        if(!is_dir('android_temp/' . $UserData['data']->uid) && !file_exists('android_temp/' . $UserData['data']->uid)) {
            mkdir('android_temp/' . $UserData['data']->uid);
        }

        if(move_uploaded_file($_FILES['file']['tmp_name'], 'android_temp/' . $UserData['data']->uid . '/'. $parameter['file_name'])) {

            $CheckDup = self::$query->hard_delete('android_file_coordinator')
                ->where(array(
                    'android_file_coordinator.type' => '= ?',
                    'AND',
                    'android_file_coordinator.member' => '= ?'
                ), array(
                    $parameter['type'], $UserData['data']->uid
                ))
                ->execute();

            $AndFile = self::$query->insert('android_file_coordinator', array(
                'file_name' => $parameter['file_name'],
                'dir_from' => $UserData['data']->uid . '/' . $parameter['file_name'],
                'dir_to' => '',
                'type' => $parameter['type'],
                'member' => $UserData['data']->uid,
                'status' => 'N',
                'created_at' => parent::format_date()
            ))
                ->execute();

            $parameterBuilder = array(
                'response_message' => 'File berhasil di upload',
                'response_data' => array(
                    'url' => __HOST__ . 'android_temp/' . $UserData['data']->uid . '/' . $parameter['file_name']
                ),
                'response_result' => 1
            );
        } else {
            $parameterBuilder = array(
                'temp_image' => $_FILES['file']['tmp_name'],
                'response_message' => 'File gagal upload',
                'response_result' => 0
            );
        }

        return $parameterBuilder;

    }

    private function tambah_sahanak($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $nik = parent::anti_injection($parameter['nik']);
        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik(array(
            'nik' => $nik
        ));

        if($hasil['response_package']['response_result'] > 0) {
            $json_object = $hasil['response_package']['response_data'][0];

            $anak_nama = $json_object->NAMA_LGKP;
            $anak_nama = str_replace("'","''",$anak_nama);
            $anak_tempat_lahir = $json_object->TMPT_LHR;
            $anak_tanggal_lahir = $json_object->TGL_LHR;
            $anak_jenkel=$parameter['anak_jenkel'];
            $anak_agama = $json_object->AGAMA;
            $kk = $json_object->KARTU_KELUARGA;
            $anak_alamat = $kk->ALAMAT;
            $anak_rt = $kk->NO_RT;
            $anak_rw = $kk->NO_RW;
            $anak_provinsi = $json_object->NAMA_PROP;
            $anak_kabupaten = $json_object->NAMA_KAB;
            $anak_kecamatan = $json_object->NAMA_KEC;
            $anak_kelurahan = $json_object->NAMA_KEL;
            $anak_kodepos = $kk->KODE_POS;
            $anak_telepon = $json_object->TELEPON;

            $anak_kelahiran_ke = parent::anti_injection($parameter['anak_kelahiran_ke']);
            $anak_nomor_akta = parent::anti_injection($parameter['anak_nomor_akta']);
            $anak_tanggal_akta = parent::anti_injection($parameter['anak_tanggal_akta']);
            $anak_dinas_akta = parent::anti_injection($parameter['anak_dinas_akta']);

            $jenis = parent::anti_injection($parameter['jenis']);
            $kirim = parent::anti_injection($parameter['kirim']);

            $uid_data = parent::gen_uuid();
            $AktaSah = self::$query->insert('aktaesah', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => $UserData['data']->uid,
                'anak_nik' => $nik,
                'anak_nama' => $anak_nama,
                'anak_tempat_lahir' => $anak_tempat_lahir,
                'anak_tanggal_lahir' => $anak_tanggal_lahir,
                'anak_jenkel' => $anak_jenkel,
                'anak_agama' => $anak_agama,
                'anak_alamat' => $anak_alamat,
                'anak_rt' => $anak_rt,
                'anak_rw' => $anak_rw,
                'anak_provinsi' => $anak_provinsi,
                'anak_kabupaten' => $anak_kabupaten,
                'anak_kecamatan' => $anak_kecamatan,
                'anak_kelurahan' => $anak_kelurahan,
                'anak_kodepos' => (isset($anak_kodepos) ? $anak_kodepos : ''),
                'anak_telepon' => (isset($anak_telepon) ? $anak_telepon : ''),
                'anak_kelahiran_ke' => $anak_kelahiran_ke,
                'anak_nomor_akta' => $anak_nomor_akta,
                'anak_tanggal_akta' => $anak_tanggal_akta,
                'anak_dinas_akta' => $anak_dinas_akta
            ))
                ->execute();


            if($AktaSah['response_result'] > 0) {
                $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                    'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                ))
                    ->where(array(
                        'pelayanan_jenis_syarat.status_hapus' => '= ?',
                        'AND',
                        'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                    ), array(
                        'N', $jenis
                    ))
                    ->execute();

                foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                    //Check Android Upload Partial
                    $AndrTemp = self::$query->select('android_file_coordinator', array(
                        'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                    ))
                        ->where(array(
                            'android_file_coordinator.uid_foreign' => 'IS NULL',
                            'AND',
                            'android_file_coordinator.type' => '= ?',
                            'AND',
                            'android_file_coordinator.member' => '= ?',
                            'AND',
                            'android_file_coordinator.status' => '= ?'
                        ), array(
                            $CSV['id'], $UserData['data']->uid, 'N'
                        ))
                        ->order(array(
                            'created_at' => 'DESC'
                        ))
                        ->limit(1)
                        ->execute();

                    if(count($AndrTemp['response_data']) > 0) {
                        $berkas = "fupload_$CSV[id]";

                        $acak			 = rand(1,99);
                        $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                        $tipe_file       = $_FILES[$berkas]['type'];
                        $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                        $nama_file_unik  = $uid_data.$acak.$nama_file;

                        $vdir_upload = '../berkas/aktaesah/';
                        if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                            mkdir($vdir_upload);
                        }

                        $vfile_upload = $vdir_upload . $nama_file_unik;
                        if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                            //Reset Temp Data
                            $AndrTempStat = self::$query->update('android_file_coordinator', array(
                                'uid_foreign' => $uid_data,
                                'dir_to' => $vfile_upload,
                                'status' => 'D'
                            ))
                                ->where(array(
                                    'android_file_coordinator.id' => '= ?'
                                ), array(
                                    $AndrTemp['response_data'][0]['id']
                                ))
                                ->execute();
                        }

                        $AktaEsahBerkas = self::$query->insert('aktaesah_berkas', array(
                            'uid_aktaesah' => $uid_data,
                            'nama_berkas' => $CSV['nama'],
                            'berkas' => $nama_file_unik
                        ))
                            ->execute();
                    }
                }








                $kode = parent::acak(6);

                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                    $Pengajuan = self::$query->select('pengajuan', array(
                        'uid'
                    ))
                        ->where(array(
                            'pengajuan.kode' => '= ?',
                            'AND',
                            'pengajuan.status_hapus' => '= ?'
                        ), array(
                            $kode, 'N'
                        ))
                        ->execute();

                    if(count($Pengajuan['response_data']) > 0) {
                        $kode = parent::acak(6);
                    }
                }

                $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
                $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
                $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
                $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
                $alamat = parent::anti_injection($parameter['alamat']);
                $kode_pos = parent::anti_injection($parameter['kode_pos']);


                $a=explode("|",$id_provinsi);
                $id_provinsi=$a[0];
                $nama_provinsi=$a[1];

                $a=explode("|",$id_kabupaten);
                $id_kabupaten=$a[0];
                $nama_kabupaten=$a[1];

                $a=explode("|",$id_kecamatan);
                $id_kecamatan=$a[0];
                $nama_kecamatan=$a[1];

                $a=explode("|",$id_kelurahan);
                $id_kelurahan=$a[0];
                $nama_kelurahan=$a[1];

                if($kirim=='Y'){
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'id_provinsi' => $id_provinsi,
                        'nama_provinsi' => $nama_provinsi,
                        'id_kabupaten' => $id_kabupaten,
                        'nama_kabupaten' => $nama_kabupaten,
                        'id_kecamatan' => $id_kecamatan,
                        'nama_kecamatan' => $nama_kecamatan,
                        'id_kelurahan' => $id_kelurahan,
                        'nama_kelurahan' => $nama_kelurahan,
                        'alamat_kirim' => $alamat,
                        'kode_pos' => $kode_pos,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }
                else{
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }

                $id_pengajuan= $TambahPengajuan['response_unique'];

                $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                    'id_pengajuan' => $id_pengajuan,
                    'waktu' => parent::format_date(),
                    'id_status' => 1
                ))
                    ->execute();

                $jenis = parent::encrypt($jenis);

                $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Pengesahan Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';

                parent::kirim_wa($_SESSION['no_handphone'], $message);

                $parameterBuilder = array(
                    'response_message' => $message,
                    'response_result' => $TambahPengajuan['response_result'],
                    'response_data' => array($jenis)
                );
            } else {
                $parameterBuilder = array(
                    'response_message' => 'Gagal tambah permohonan',
                    'response_result' => 0,
                    'response_data' => $AktaSah
                );
            }
        } else {
            $parameterBuilder = array(
                'response_message' => $hasil['response_package']['response_message'],
                'response_result' => $hasil['response_package']['response_result'],
                'response_data' => $hasil['response_package']['response_data']
            );
        }

        return $parameterBuilder;
    }

    private function tambah_angkatanak($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $nik = parent::anti_injection($parameter['nik']);
        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik(array(
            'nik' => $nik
        ));

        if($hasil['response_package']['response_result'] > 0) {
            $json_object = $hasil['response_package']['response_data'][0];

            $anak_nama = $json_object->NAMA_LGKP;
            $anak_nama = str_replace("'","''",$anak_nama);
            $anak_tempat_lahir = $json_object->TMPT_LHR;
            $anak_tanggal_lahir = $json_object->TGL_LHR;
            $anak_jenkel=$parameter['anak_jenkel'];
            $anak_agama = $json_object->AGAMA;
            $kk = $json_object->KARTU_KELUARGA;
            $anak_alamat = $kk->ALAMAT;
            $anak_rt = $kk->NO_RT;
            $anak_rw = $kk->NO_RW;
            $anak_provinsi = $json_object->NAMA_PROP;
            $anak_kabupaten = $json_object->NAMA_KAB;
            $anak_kecamatan = $json_object->NAMA_KEC;
            $anak_kelurahan = $json_object->NAMA_KEL;
            $anak_kodepos = $kk->KODE_POS;
            $anak_telepon = $json_object->TELEPON;

            $jenis = parent::anti_injection($parameter['jenis']);
            $kirim = parent::anti_injection($parameter['kirim']);

            $anak_kelahiran_ke = parent::anti_injection($parameter['anak_kelahiran_ke']);
            $anak_nomor_akta = parent::anti_injection($parameter['anak_nomor_akta']);
            $anak_tanggal_akta = parent::anti_injection($parameter['anak_tanggal_akta']);
            $anak_dinas_akta = parent::anti_injection($parameter['anak_dinas_akta']);

            $uid_data = parent::gen_uuid();
            $AngkatAnak = self::$query->insert('aktaangkat', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => $UserData['data']->uid,
                'anak_nik' => $nik,
                'anak_nama' => $anak_nama,
                'anak_tempat_lahir' => $anak_tempat_lahir,
                'anak_tanggal_lahir' => $anak_tanggal_lahir,
                'anak_jenkel' => $anak_jenkel,
                'anak_agama' => $anak_agama,
                'anak_alamat' => $anak_alamat,
                'anak_rt' => $anak_rt,
                'anak_rw' => $anak_rw,
                'anak_provinsi' => $anak_provinsi,
                'anak_kabupaten' => $anak_kabupaten,
                'anak_kecamatan' => $anak_kecamatan,
                'anak_kelurahan' => $anak_kelurahan,
                'anak_kodepos' => (isset($anak_kodepos) ? $anak_kodepos : ''),
                'anak_telepon' => (isset($anak_telepon) ? $anak_telepon : ''),
                'anak_kelahiran_ke' => $anak_kelahiran_ke,
                'anak_nomor_akta' => $anak_nomor_akta,
                'anak_tanggal_akta' => $anak_tanggal_akta,
                'anak_dinas_akta' => $anak_dinas_akta
            ))
                ->execute();

            if($AngkatAnak['response_result'] > 0) {
                $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                    'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                ))
                    ->where(array(
                        'pelayanan_jenis_syarat.status_hapus' => '= ?',
                        'AND',
                        'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                    ), array(
                        'N', $jenis
                    ))
                    ->execute();

                foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                    //Check Android Upload Partial
                    $AndrTemp = self::$query->select('android_file_coordinator', array(
                        'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                    ))
                        ->where(array(
                            'android_file_coordinator.uid_foreign' => 'IS NULL',
                            'AND',
                            'android_file_coordinator.type' => '= ?',
                            'AND',
                            'android_file_coordinator.member' => '= ?',
                            'AND',
                            'android_file_coordinator.status' => '= ?'
                        ), array(
                            $CSV['id'], $UserData['data']->uid, 'N'
                        ))
                        ->order(array(
                            'created_at' => 'DESC'
                        ))
                        ->limit(1)
                        ->execute();

                    if(count($AndrTemp['response_data']) > 0) {
                        $berkas = "fupload_$CSV[id]";

                        $acak			 = rand(1,99);
                        $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                        $tipe_file       = $_FILES[$berkas]['type'];
                        $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                        $nama_file_unik  = $uid_data.$acak.$nama_file;

                        $vdir_upload = '../berkas/aktaangkat/';
                        if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                            mkdir($vdir_upload);
                        }

                        $vfile_upload = $vdir_upload . $nama_file_unik;
                        if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                            //Reset Temp Data
                            $AndrTempStat = self::$query->update('android_file_coordinator', array(
                                'uid_foreign' => $uid_data,
                                'dir_to' => $vfile_upload,
                                'status' => 'D'
                            ))
                                ->where(array(
                                    'android_file_coordinator.id' => '= ?'
                                ), array(
                                    $AndrTemp['response_data'][0]['id']
                                ))
                                ->execute();
                        }

                        $AktaAngkatBerkas = self::$query->insert('aktaangkat_berkas', array(
                            'uid_aktaangkat' => $uid_data,
                            'nama_berkas' => $CSV['nama'],
                            'berkas' => $nama_file_unik
                        ))
                            ->execute();
                    }
                }


                $kode = parent::acak(6);

                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                    $Pengajuan = self::$query->select('pengajuan', array(
                        'uid'
                    ))
                        ->where(array(
                            'pengajuan.kode' => '= ?',
                            'AND',
                            'pengajuan.status_hapus' => '= ?'
                        ), array(
                            $kode, 'N'
                        ))
                        ->execute();

                    if(count($Pengajuan['response_data']) > 0) {
                        $kode = parent::acak(6);
                    }
                }

                $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
                $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
                $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
                $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
                $alamat = parent::anti_injection($parameter['alamat']);
                $kode_pos = parent::anti_injection($parameter['kode_pos']);


                $a=explode("|",$id_provinsi);
                $id_provinsi=$a[0];
                $nama_provinsi=$a[1];

                $a=explode("|",$id_kabupaten);
                $id_kabupaten=$a[0];
                $nama_kabupaten=$a[1];

                $a=explode("|",$id_kecamatan);
                $id_kecamatan=$a[0];
                $nama_kecamatan=$a[1];

                $a=explode("|",$id_kelurahan);
                $id_kelurahan=$a[0];
                $nama_kelurahan=$a[1];

                if($kirim=='Y'){
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'id_provinsi' => $id_provinsi,
                        'nama_provinsi' => $nama_provinsi,
                        'id_kabupaten' => $id_kabupaten,
                        'nama_kabupaten' => $nama_kabupaten,
                        'id_kecamatan' => $id_kecamatan,
                        'nama_kecamatan' => $nama_kecamatan,
                        'id_kelurahan' => $id_kelurahan,
                        'nama_kelurahan' => $nama_kelurahan,
                        'alamat_kirim' => $alamat,
                        'kode_pos' => $kode_pos,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }
                else{
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }

                $id_pengajuan = $TambahPengajuan['response_unique'];

                $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                    'id_pengajuan' => $id_pengajuan,
                    'waktu' => parent::format_date(),
                    'id_status' => 1
                ))
                    ->execute();

                $jenis = parent::encrypt($jenis);
                $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Pengangkatan Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';
                parent::kirim_wa($UserData['data']->no_handphone, $message);
                $parameterBuilder = array(
                    'response_message' => $message,
                    'response_result' => $TambahPengajuan['response_result'],
                    'response_data' => array($jenis)
                );
            } else {
                $parameterBuilder = array(
                    'response_message' => 'Gagal tambah permohonan',
                    'response_result' => 0,
                    'response_data' => $AngkatAnak
                );
            }
        } else {
            $parameterBuilder = array(
                'response_message' => $hasil['response_package']['response_message'],
                'response_result' => $hasil['response_package']['response_result'],
                'response_data' => $hasil['response_package']['response_data']
            );
        }

        return $parameterBuilder;
    } //Todo : Samakan status query tambah permohonan dengan sahanak

    private function tambah_kartukeluarga($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $nik = parent::anti_injection($parameter['nik']);


        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik(array(
            'nik' => $nik
        ));

        if($hasil['response_package']['response_result'] > 0) {
            $json_object = $hasil['response_package']['response_data'][0];
            $nama = $json_object->NAMA_LGKP;
            $nama = str_replace("'","''",$nama);
            $alamat = strtoupper($parameter['alamat_pemohon']);

            $alasan_pemohon = $parameter['alasan'];
            if ($parameter['alasan'] == "3"){
                $alasan_pemohon = $parameter['alasan_lainnya'];
            }

            if ($json_object == ""){
                $nama = $parameter['nama'];
            }

            $id_kecamatan = intval(parent::anti_injection($parameter['id_kecamatan2']));
            $id_kelurahan = intval(parent::anti_injection($parameter['id_kelurahan2']));

            $a=explode("|",$id_kecamatan);
            $id_kecamatan=$a[0];

            $a=explode("|",$id_kelurahan);
            $id_kelurahan=$a[0];


            $no_kk_lama = parent::anti_injection($parameter['no_kk_lama']);
            $nama = parent::anti_injection($nama);
            $shdk = parent::anti_injection($parameter['shdk']);
            $jenis = parent::anti_injection($parameter['jenis']);
            $alasan_pemohon = parent::anti_injection($alasan_pemohon);

            $kirim = parent::anti_injection($parameter['kirim']);

            $uid_data = parent::gen_uuid();

            $KartuKK = self::$query->insert('kartukk', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => $UserData['data']->uid,
                'nik' => $nik,
                'alasan_pemohon' => $alasan_pemohon,
                'alamat_baru' => $alamat,
                'id_kecamatan' => $id_kecamatan,
                'id_kelurahan' => $id_kelurahan,
                'no_kk_lama' => $no_kk_lama,
                'nama' => $nama
            ))
                ->execute();

            if($KartuKK['response_result'] > 0) {
                $KKAnak = self::$query->insert('kartukk_anak',  array(
                    'nik' => $nik,
                    'nama' => $parameter['nama'],
                    'uid_kartukk' => $uid_data,
                    'shdk' => $shdk
                ))
                    ->execute();


                /*$CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                    'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                ))
                    ->where(array(
                        'pelayanan_jenis_syarat.status_hapus' => '= ?',
                        'AND',
                        'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                    ), array(
                        'N', $jenis
                    ))
                    ->execute();

                foreach($CheckSyarat['response_data'] as $CSK => $CSV) {
                    $berkas = "fupload_$CSV[id]";

                    $acak			 = rand(1,99);
                    $lokasi_file     = $_FILES[$berkas]['tmp_name'];
                    $tipe_file       = $_FILES[$berkas]['type'];
                    $nama_file       = $_FILES[$berkas]['name'];
                    $nama_file_unik  = $uid_data.$acak.$nama_file;

                    if ($_FILES[$berkas]["error"] > 0 OR empty($lokasi_file)){
                        $nama_file_unik = "";
                    }

                    else{
                        $vdir_upload = "../../berkas/kk/";
                        $vfile_upload = $vdir_upload . $nama_file_unik;

                        //Simpan gambar dalam ukuran sebenarnya
                        move_uploaded_file($lokasi_file, $vfile_upload);
                    }

                    $KartuKKBerkas = self::$query->insert('kartukk_berkas', array(
                        'uid_kartukk' => $uid_data,
                        'nama_berkas' => $CSV['nama'],
                        'berkas' => $nama_file_unik
                    ))
                        ->execute();
                }*/




                //Revamp
                $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                    'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                ))
                    ->where(array(
                        'pelayanan_jenis_syarat.status_hapus' => '= ?',
                        'AND',
                        'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                    ), array(
                        'N', $jenis
                    ))
                    ->execute();

                foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                    //Check Android Upload Partial
                    $AndrTemp = self::$query->select('android_file_coordinator', array(
                        'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                    ))
                        ->where(array(
                            'android_file_coordinator.uid_foreign' => 'IS NULL',
                            'AND',
                            'android_file_coordinator.type' => '= ?',
                            'AND',
                            'android_file_coordinator.member' => '= ?',
                            'AND',
                            'android_file_coordinator.status' => '= ?'
                        ), array(
                            $CSV['id'], $UserData['data']->uid, 'N'
                        ))
                        ->order(array(
                            'created_at' => 'DESC'
                        ))
                        ->limit(1)
                        ->execute();

                    if(count($AndrTemp['response_data']) > 0) {
                        $berkas = "fupload_$CSV[id]";

                        $acak			 = rand(1,99);
                        $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                        $tipe_file       = $_FILES[$berkas]['type'];
                        $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                        $nama_file_unik  = $uid_data.$acak.$nama_file;

                        $vdir_upload = '../berkas/kk/';
                        if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                            mkdir($vdir_upload);
                        }

                        $vfile_upload = $vdir_upload . $nama_file_unik;
                        if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                            //Reset Temp Data
                            $AndrTempStat = self::$query->update('android_file_coordinator', array(
                                'uid_foreign' => $uid_data,
                                'dir_to' => $vfile_upload,
                                'status' => 'D'
                            ))
                                ->where(array(
                                    'android_file_coordinator.id' => '= ?'
                                ), array(
                                    $AndrTemp['response_data'][0]['id']
                                ))
                                ->execute();
                        }

                        $KartuKKBerkas = self::$query->insert('kartukk_berkas', array(
                            'uid_kartukk' => $uid_data,
                            'nama_berkas' => $CSV['nama'],
                            'berkas' => $nama_file_unik
                        ))
                            ->execute();
                    }
                }





                if(isset($parameter['anggota'])) {
                    foreach ($parameter['anggota'] as $AAKey => $AAValue) {
                        $nama_anggota = strtoupper(parent::anti_injection($AAValue['nama']));
                        $shdk_anggota = parent::anti_injection($AAValue['shdk']);

                        $KKAnakS = self::$query->insert('kartukk_anak', array(
                            'nik' => $AAValue['nik'],
                            'nama' => $nama_anggota,
                            'uid_kartukk' => $uid_data,
                            'shdk' => $shdk_anggota
                        ))
                            ->execute();
                    }
                }

                /*if (isset($parameter['id_anggota'])){
                    $id_anggota = $parameter['id_anggota'];

                    $tambahan = explode(",",$id_anggota);

                    //print_r($tambahan);

                    foreach ($tambahan as $key => $value) {
                        $nik_anggota = "";
                        if (isset($parameter['nik-anggota-'.$value])){
                            $nik_anggota = parent::anti_injection($parameter['nik-anggota-'.$value]);
                        }

                        $nama_anggota = strtoupper(parent::anti_injection($parameter['nama-anggota-'.$value]));
                        $shdk_anggota = parent::anti_injection($parameter['shdk-anggota-'.$value]);

                        $KKAnakS = self::$query->insert('kartukk_anak', array(
                            'nik' => $nik_anggota,
                            'nama' => $nama_anggota,
                            'uid_kartukk' => $uid_data,
                            'shdk' => $shdk_anggota
                        ))
                            ->execute();
                    }
                }*/

                /*----------------------------------------------------------*/

                $kode = parent::acak(6);

                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                    $Pengajuan = self::$query->select('pengajuan', array(
                        'uid'
                    ))
                        ->where(array(
                            'pengajuan.kode' => '= ?',
                            'AND',
                            'pengajuan.status_hapus' => '= ?'
                        ), array(
                            $kode, 'N'
                        ))
                        ->execute();

                    if(count($Pengajuan['response_data']) > 0) {
                        $kode = parent::acak(6);
                    }
                }

                $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
                $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
                $id_kecamatan = parent::anti_injection($parameter['id_kecamatan2']);
                $id_kelurahan = parent::anti_injection($parameter['id_kelurahan2']);
                $alamat = parent::anti_injection($parameter['alamat']);
                $kode_pos = parent::anti_injection($parameter['kode_pos']);


                $a=explode("|",$id_provinsi);
                $id_provinsi=$a[0];
                $nama_provinsi=$a[1];

                $a=explode("|",$id_kabupaten);
                $id_kabupaten=$a[0];
                $nama_kabupaten=$a[1];

                $a=explode("|",$id_kecamatan);
                $id_kecamatan=$a[0];
                $nama_kecamatan=$a[1];

                $a=explode("|",$id_kelurahan);
                $id_kelurahan=$a[0];
                $nama_kelurahan=$a[1];

                if($kirim=='Y'){
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'id_provinsi' => $id_provinsi,
                        'nama_provinsi' => $nama_provinsi,
                        'id_kabupaten' => $id_kabupaten,
                        'nama_kabupaten' => $nama_kabupaten,
                        'id_kecamatan' => $id_kecamatan,
                        'nama_kecamatan' => $nama_kecamatan,
                        'id_kelurahan' => $id_kelurahan,
                        'nama_kelurahan' => $nama_kelurahan,
                        'alamat_kirim' => $alamat,
                        'kode_pos' => $kode_pos,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }
                else{
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }

                $id_pengajuan = $TambahPengajuan['response_unique'];

                $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                    'id_pengajuan' => $id_pengajuan,
                    'waktu' => parent::format_date(),
                    'id_status' => 1
                ))
                    ->execute();

                $jenis = parent::encrypt($jenis);
                $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Kartu Keluarga. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';
                parent::kirim_wa($UserData['data']->no_handphone, $message);
                $parameterBuilder = array(
                    'response_message' => $message,
                    'response_result' => $TambahPengajuan['response_result'],
                    'response_data' => array($jenis)
                );
            } else {
                $parameterBuilder = array(
                    'response_message' => 'Gagal tambah permohonan',
                    'response_result' => 0,
                    'response_data' => $KartuKK
                );
            }

        } else {
            $parameterBuilder = array(
                'response_message' => $hasil['response_package']['response_message'],
                'response_result' => $hasil['response_package']['response_result'],
                'response_data' => $hasil['response_package']['response_data']
            );
        }

        return $parameterBuilder;

    } //Todo : Samakan status query tambah permohonan dengan sahanak

    private function tambah_ktp($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $nik = parent::anti_injection($parameter['nik']);


        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik(array(
            'nik' => $nik
        ));

        if($hasil['response_package']['response_result'] > 0) {
            $json_object = $hasil['response_package']['response_data'][0];

            $nama = $json_object->NAMA_LGKP;
            $nama = str_replace("'","''",$nama);
            $tempat_lahir = $json_object->TMPT_LHR;
            $tanggal_lahir = $json_object->TGL_LHR;
            $jenkel=$parameter['jenkel'];
            $agama = $json_object->AGAMA;

            $alasan = parent::anti_injection($parameter['alasan']);
            $jenis = parent::anti_injection($parameter['jenis']);

            $kirim = parent::anti_injection($parameter['kirim']);

            $uid_data = parent::gen_uuid();
            $KartuKTP = self::$query->insert('kartuktp', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => $UserData['data']->uid,
                'nik' => $nik,
                'nama' => $nama,
                'tempat_lahir' => $tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'jenkel' => $jenkel,
                'agama' => $agama,
                'alasan' => $alasan
            ))
                ->execute();

            if($KartuKTP['response_result'] > 0) {
                $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                    'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                ))
                    ->where(array(
                        'pelayanan_jenis_syarat.status_hapus' => '= ?',
                        'AND',
                        'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                    ), array(
                        'N', $jenis
                    ))
                    ->execute();

                foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                    //Check Android Upload Partial
                    $AndrTemp = self::$query->select('android_file_coordinator', array(
                        'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                    ))
                        ->where(array(
                            'android_file_coordinator.uid_foreign' => 'IS NULL',
                            'AND',
                            'android_file_coordinator.type' => '= ?',
                            'AND',
                            'android_file_coordinator.member' => '= ?',
                            'AND',
                            'android_file_coordinator.status' => '= ?'
                        ), array(
                            $CSV['id'], $UserData['data']->uid, 'N'
                        ))
                        ->order(array(
                            'created_at' => 'DESC'
                        ))
                        ->limit(1)
                        ->execute();

                    if(count($AndrTemp['response_data']) > 0) {
                        $berkas = "fupload_$CSV[id]";

                        $acak			 = rand(1,99);
                        $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                        $tipe_file       = $_FILES[$berkas]['type'];
                        $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                        $nama_file_unik  = $uid_data.$acak.$nama_file;

                        $vdir_upload = '../berkas/ktp/';
                        if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                            mkdir($vdir_upload);
                        }

                        $vfile_upload = $vdir_upload . $nama_file_unik;
                        if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                            //Reset Temp Data
                            $AndrTempStat = self::$query->update('android_file_coordinator', array(
                                'uid_foreign' => $uid_data,
                                'dir_to' => $vfile_upload,
                                'status' => 'D'
                            ))
                                ->where(array(
                                    'android_file_coordinator.id' => '= ?'
                                ), array(
                                    $AndrTemp['response_data'][0]['id']
                                ))
                                ->execute();
                        }

                        $KartuKTPBerkas = self::$query->insert('kartuktp_berkas', array(
                            'uid_kartuktp' => $uid_data,
                            'nama_berkas' => $CSV['nama'],
                            'berkas' => $nama_file_unik
                        ))
                            ->execute();
                    }
                }










                $kode = parent::acak(6);

                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                    $Pengajuan = self::$query->select('pengajuan', array(
                        'uid'
                    ))
                        ->where(array(
                            'pengajuan.kode' => '= ?',
                            'AND',
                            'pengajuan.status_hapus' => '= ?'
                        ), array(
                            $kode, 'N'
                        ))
                        ->execute();

                    if(count($Pengajuan['response_data']) > 0) {
                        $kode = parent::acak(6);
                    }
                }

                $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
                $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
                $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
                $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
                $alamat = parent::anti_injection($parameter['alamat']);
                $kode_pos = parent::anti_injection($parameter['kode_pos']);


                $a=explode("|",$id_provinsi);
                $id_provinsi=$a[0];
                $nama_provinsi=$a[1];

                $a=explode("|",$id_kabupaten);
                $id_kabupaten=$a[0];
                $nama_kabupaten=$a[1];

                $a=explode("|",$id_kecamatan);
                $id_kecamatan=$a[0];
                $nama_kecamatan=$a[1];

                $a=explode("|",$id_kelurahan);
                $id_kelurahan=$a[0];
                $nama_kelurahan=$a[1];

                if($kirim=='Y') {
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'id_provinsi' => $id_provinsi,
                        'nama_provinsi' => $nama_provinsi,
                        'id_kabupaten' => $id_kabupaten,
                        'nama_kabupaten' => $nama_kabupaten,
                        'id_kecamatan' => $id_kecamatan,
                        'nama_kecamatan' => $nama_kecamatan,
                        'id_kelurahan' => $id_kelurahan,
                        'nama_kelurahan' => $nama_kelurahan,
                        'alamat_kirim' => $alamat,
                        'kode_pos' => $kode_pos,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }
                else{
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }

                $id_pengajuan = $TambahPengajuan['response_unique'];

                $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                    'id_pengajuan' => $id_pengajuan,
                    'waktu' => parent::format_date(),
                    'id_status' => 1
                ))
                    ->execute();

                $jenis = parent::encrypt($jenis);
                $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Kartu Tanda Penduduk. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';
                parent::kirim_wa($_SESSION['no_handphone'], $message);
                $parameterBuilder = array(
                    'response_message' => $message,
                    'response_result' => $TambahPengajuan['response_result'],
                    'response_data' => array($jenis)
                );
            } else {
                $parameterBuilder = array(
                    'response_message' => 'Gagal tambah permohonan',
                    'response_result' => 0,
                    'response_data' => $KartuKTP
                );
            }
        } else {
            $parameterBuilder = array(
                'response_message' => $hasil['response_package']['response_message'],
                'response_result' => $hasil['response_package']['response_result'],
                'response_data' => $hasil['response_package']['response_data']
            );
        }

        return $parameterBuilder;
    } //Todo : Samakan status query tambah permohonan dengan sahanak

    private function tambah_aktalahir($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;
        $parameter = json_decode($parameter['data'], true);


        $nik = parent::anti_injection($parameter['nik']);


        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik(array(
            'nik' => $nik
        ));

        //if($hasil['response_package']['response_result'] > 0) {
            $json_object = $hasil['response_package']['response_data'][0];

            $anak_nama = str_replace("'","''",$parameter['anak_nama']);
            $anak_tempat_lahir = str_replace("'","''",$parameter['anak_tempat_lahir']);
            $anak_berat_bayi = str_replace("'","''",$parameter['anak_berat_bayi']);
            $anak_panjang_bayi = str_replace("'","''",$parameter['anak_panjang_bayi']);

            $anak_nama = parent::anti_injection($anak_nama);
            $anak_tempat_lahir = parent::anti_injection($anak_tempat_lahir);
            $anak_berat_bayi = parent::anti_injection($anak_berat_bayi);
            $anak_berat_bayi_koma = parent::anti_injection($parameter['anak_berat_bayi_koma']);
            $anak_panjang_bayi = parent::anti_injection($anak_panjang_bayi);

            $anak_id_jenkel = parent::anti_injection($parameter['anak_id_jenkel']);
            $anak_id_tempatlahir = parent::anti_injection($parameter['anak_id_tempatlahir']);
            $anak_tanggal_lahir = parent::anti_injection($parameter['anak_tanggal_lahir']);
            $anak_jam_lahir = parent::anti_injection($parameter['anak_jam_lahir']);
            $anak_kelahiran_ke = parent::anti_injection($parameter['anak_kelahiran_ke']);
            $anak_id_tolonglahir = parent::anti_injection($parameter['anak_id_tolonglahir']);
            $anak_ayah = parent::anti_injection($parameter['anak_ayah']);
            $anak_ibu = parent::anti_injection($parameter['anak_ibu']);
            $anak_saksi1 = parent::anti_injection($parameter['anak_saksi1']);
            $anak_saksi2 = parent::anti_injection($parameter['anak_saksi2']);
            $anak_no_handphone = parent::anti_injection($parameter['anak_no_handphone']);
            $jenis_kelahiran = parent::anti_injection($parameter['jenis_kelahiran']);
            $jenis = parent::anti_injection($parameter['jenis']);
            $kirim = parent::anti_injection($parameter['kirim']);

            //SETTING BERAT BAYI DIGABUNGKAN
            //$anak_berat_bayi = $anak_berat_bayi.'.'.$anak_berat_bayi_koma;
            $uid_data = parent::gen_uuid();
            $AktaLahir = self::$query->insert('aktalahir', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => $UserData['data']->uid,
                'anak_nama' => parent::anti_null($anak_nama),
                'anak_id_jenkel' => parent::anti_null($anak_id_jenkel),
                'anak_id_tempatlahir' => parent::anti_null($anak_id_tempatlahir),
                'anak_tempat_lahir' => parent::anti_null($anak_tempat_lahir),
                'anak_tanggal_lahir' => parent::anti_null($anak_tanggal_lahir),
                'anak_jam_lahir' => parent::anti_null($anak_jam_lahir),
                'anak_kelahiran_ke' => parent::anti_null($anak_kelahiran_ke),
                'anak_id_tolonglahir' => parent::anti_null($anak_id_tolonglahir),
                'anak_berat_bayi' => parent::anti_null($anak_berat_bayi),
                'anak_panjang_bayi' => parent::anti_null($anak_panjang_bayi),
                'anak_ayah' => parent::anti_null($anak_ayah),
                'anak_ibu' => parent::anti_null($anak_ibu),
                'anak_saksi1' => parent::anti_null($anak_saksi1),
                'anak_saksi2' => parent::anti_null($anak_saksi2),
                'anak_no_handphone' => parent::anti_null($anak_no_handphone),
                'jenis_kelahiran' => parent::anti_null($jenis_kelahiran)
            ))
                ->execute();
            if($AktaLahir['response_result'] > 0) {
                $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                    'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                ))
                    ->where(array(
                        'pelayanan_jenis_syarat.status_hapus' => '= ?',
                        'AND',
                        'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                    ), array(
                        'N', $jenis
                    ))
                    ->execute();

                foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                    //Check Android Upload Partial
                    $AndrTemp = self::$query->select('android_file_coordinator', array(
                        'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                    ))
                        ->where(array(
                            'android_file_coordinator.uid_foreign' => 'IS NULL',
                            'AND',
                            'android_file_coordinator.type' => '= ?',
                            'AND',
                            'android_file_coordinator.member' => '= ?',
                            'AND',
                            'android_file_coordinator.status' => '= ?'
                        ), array(
                            $CSV['id'], $UserData['data']->uid, 'N'
                        ))
                        ->order(array(
                            'created_at' => 'DESC'
                        ))
                        ->limit(1)
                        ->execute();

                    if(count($AndrTemp['response_data']) > 0) {
                        $berkas = "fupload_$CSV[id]";

                        $acak			 = rand(1,99);
                        $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                        $tipe_file       = $_FILES[$berkas]['type'];
                        $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                        $nama_file_unik  = $uid_data.$acak.$nama_file;

                        $vdir_upload = '../berkas/aktalahir/';
                        if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                            mkdir($vdir_upload);
                        }

                        $vfile_upload = $vdir_upload . $nama_file_unik;
                        if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                            //Reset Temp Data
                            $AndrTempStat = self::$query->update('android_file_coordinator', array(
                                'uid_foreign' => $uid_data,
                                'dir_to' => $vfile_upload,
                                'status' => 'D'
                            ))
                                ->where(array(
                                    'android_file_coordinator.id' => '= ?'
                                ), array(
                                    $AndrTemp['response_data'][0]['id']
                                ))
                                ->execute();
                        }

                        $AktaLahirBerkas = self::$query->insert('aktalahir_berkas', array(
                            'uid_aktalahir' => $uid_data,
                            'nama_berkas' => $CSV['nama'],
                            'berkas' => $nama_file_unik
                        ))
                            ->execute();
                    }
                }

                $kode = parent::acak(6);

                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                    $Pengajuan = self::$query->select('pengajuan', array(
                        'uid'
                    ))
                        ->where(array(
                            'pengajuan.kode' => '= ?',
                            'AND',
                            'pengajuan.status_hapus' => '= ?'
                        ), array(
                            $kode, 'N'
                        ))
                        ->execute();

                    if(count($Pengajuan['response_data']) > 0) {
                        $kode = parent::acak(6);
                    }
                }

                $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
                $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
                $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
                $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
                $alamat = parent::anti_injection($parameter['alamat']);
                $kode_pos = parent::anti_injection($parameter['kode_pos']);


                $a=explode("|",$id_provinsi);
                $id_provinsi=$a[0];
                $nama_provinsi=$a[1];

                $a=explode("|",$id_kabupaten);
                $id_kabupaten=$a[0];
                $nama_kabupaten=$a[1];

                $a=explode("|",$id_kecamatan);
                $id_kecamatan=$a[0];
                $nama_kecamatan=$a[1];

                $a=explode("|",$id_kelurahan);
                $id_kelurahan=$a[0];
                $nama_kelurahan=$a[1];


                if($kirim=='Y') {
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'id_provinsi' => $id_provinsi,
                        'nama_provinsi' => $nama_provinsi,
                        'id_kabupaten' => $id_kabupaten,
                        'nama_kabupaten' => $nama_kabupaten,
                        'id_kecamatan' => $id_kecamatan,
                        'nama_kecamatan' => $nama_kecamatan,
                        'id_kelurahan' => $id_kelurahan,
                        'nama_kelurahan' => $nama_kelurahan,
                        'alamat_kirim' => $alamat,
                        'kode_pos' => $kode_pos,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                } else {
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }

                $id_pengajuan = $TambahPengajuan['response_unique'];

                $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                    'id_pengajuan' => $id_pengajuan,
                    'waktu' => parent::format_date(),
                    'id_status' => 1
                ))
                    ->execute();

                $jenis = parent::encrypt($jenis);
                $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Kelahiran Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';
                parent::kirim_wa($_SESSION['no_handphone'], $message);
                $parameterBuilder = array(
                    'response_message' => $message,
                    'response_result' => $TambahPengajuan['response_result'],
                    'response_data' => array($jenis)
                );
            } else {
                $parameterBuilder = array(
                    'response_message' => 'Gagal tambah permohonan',
                    'response_result' => 0,
                    'response_data' => $AktaLahir
                );
            }
        /*} else {
            $parameterBuilder = array(
                'response_message' => $hasil['response_package']['response_message'],
                'response_result' => $hasil['response_package']['response_result'],
                'response_data' => $hasil['response_package']['response_data']
            );
        }*/




        return $parameterBuilder;
    }  //Todo : Samakan status query tambah permohonan dengan sahanak

    private function tambah_aktaaku($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $parameterBuilder = array();

        $nik = parent::anti_injection($parameter['nik']);


        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik(array(
            'nik' => $nik
        ));

        if($hasil['response_package']['response_result'] > 0) {
            $json_object = $hasil['response_package']['response_data'][0];
            $anak_nama = $json_object->NAMA_LGKP;
            if($anak_nama!=''){
                $anak_nama = str_replace("'","''",$anak_nama);
                $anak_tempat_lahir = $json_object->TMPT_LHR;
                $anak_tanggal_lahir = $json_object->TGL_LHR;
                $anak_jenkel=$parameter['anak_jenkel'];
                $anak_agama = $json_object->AGAMA;
                $kk = $json_object->KARTU_KELUARGA;
                $anak_alamat = $kk->ALAMAT;
                $anak_rt = $kk->NO_RT;
                $anak_rw = $kk->NO_RW;
                $anak_provinsi = $json_object->NAMA_PROP;
                $anak_kabupaten = $json_object->NAMA_KAB;
                $anak_kecamatan = $json_object->NAMA_KEC;
                $anak_kelurahan = $json_object->NAMA_KEL;
                $anak_kodepos = $kk->KODE_POS;
                $anak_telepon = $json_object->TELEPON;

                $anak_kelahiran_ke = parent::anti_injection($parameter['anak_kelahiran_ke']);
                $anak_nomor_akta = parent::anti_injection($parameter['anak_nomor_akta']);
                $anak_tanggal_akta = parent::anti_injection($parameter['anak_tanggal_akta']);
                $anak_dinas_akta = parent::anti_injection($parameter['anak_dinas_akta']);

                $jenis = parent::anti_injection($parameter['jenis']);
                $kirim = parent::anti_injection($parameter['kirim']);

                $uid_data = parent::gen_uuid();
                $proceedAktaAku = self::$query->insert('aktaaku', array(
                    'uid' => $uid_data,
                    'waktu_input' => parent::format_date(),
                    'uid_member' => $UserData['data']->uid,
                    'anak_nik' => $nik,
                    'anak_nama' => $anak_nama,
                    'anak_tempat_lahir' => $anak_tempat_lahir,
                    'anak_tanggal_lahir' => $anak_tanggal_lahir,
                    'anak_jenkel' => $anak_jenkel,
                    'anak_agama' => $anak_agama,
                    'anak_alamat' => $anak_alamat,
                    'anak_rt' => $anak_rt,
                    'anak_rw' => $anak_rw,
                    'anak_provinsi' => $anak_provinsi,
                    'anak_kabupaten' => $anak_kabupaten,
                    'anak_kecamatan' => $anak_kecamatan,
                    'anak_kelurahan' => '$anak_kelurahan',
                    'anak_kodepos' => (isset($anak_kodepos) ? $anak_kodepos : ''),
                    'anak_telepon' => (isset($anak_telepon) ? $anak_telepon : ''),
                    'anak_kelahiran_ke' => $anak_kelahiran_ke,
                    'anak_nomor_akta' => $anak_nomor_akta,
                    'anak_tanggal_akta' => $anak_tanggal_akta,
                    'anak_dinas_akta' => $anak_dinas_akta

                ))
                    ->execute();

                if($proceedAktaAku['response_result'] > 0) {
                    $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                        'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                    ))
                        ->where(array(
                            'pelayanan_jenis_syarat.status_hapus' => '= ?',
                            'AND',
                            'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                        ), array(
                            'N', $jenis
                        ))
                        ->execute();

                    foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                        //Check Android Upload Partial
                        $AndrTemp = self::$query->select('android_file_coordinator', array(
                            'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                        ))
                            ->where(array(
                                'android_file_coordinator.uid_foreign' => 'IS NULL',
                                'AND',
                                'android_file_coordinator.type' => '= ?',
                                'AND',
                                'android_file_coordinator.member' => '= ?',
                                'AND',
                                'android_file_coordinator.status' => '= ?'
                            ), array(
                                $CSV['id'], $UserData['data']->uid, 'N'
                            ))
                            ->order(array(
                                'created_at' => 'DESC'
                            ))
                            ->limit(1)
                            ->execute();

                        if(count($AndrTemp['response_data']) > 0) {
                            $berkas = "fupload_$CSV[id]";

                            $acak			 = rand(1,99);
                            $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                            $tipe_file       = $_FILES[$berkas]['type'];
                            $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                            $nama_file_unik  = $uid_data.$acak.$nama_file;

                            $vdir_upload = '../berkas/aktaaku/';
                            if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                                mkdir($vdir_upload);
                            }

                            $vfile_upload = $vdir_upload . $nama_file_unik;
                            if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                                //Reset Temp Data
                                $AndrTempStat = self::$query->update('android_file_coordinator', array(
                                    'uid_foreign' => $uid_data,
                                    'dir_to' => $vfile_upload,
                                    'status' => 'D'
                                ))
                                    ->where(array(
                                        'android_file_coordinator.id' => '= ?'
                                    ), array(
                                        $AndrTemp['response_data'][0]['id']
                                    ))
                                    ->execute();
                            }

                            $AktaLahirBerkas = self::$query->insert('aktaaku_berkas', array(
                                'uid_aktaaku' => $uid_data,
                                'nama_berkas' => $CSV['nama'],
                                'berkas' => $nama_file_unik
                            ))
                                ->execute();
                        }
                    }

                    $kode = parent::acak(6);

                    $Pengajuan = self::$query->select('pengajuan', array(
                        'uid'
                    ))
                        ->where(array(
                            'pengajuan.kode' => '= ?',
                            'AND',
                            'pengajuan.status_hapus' => '= ?'
                        ), array(
                            $kode, 'N'
                        ))
                        ->execute();

                    if(count($Pengajuan['response_data']) > 0) {
                        $kode = parent::acak(6);
                        $Pengajuan = self::$query->select('pengajuan', array(
                            'uid'
                        ))
                            ->where(array(
                                'pengajuan.kode' => '= ?',
                                'AND',
                                'pengajuan.status_hapus' => '= ?'
                            ), array(
                                $kode, 'N'
                            ))
                            ->execute();

                        if(count($Pengajuan['response_data']) > 0) {
                            $kode = parent::acak(6);
                        }
                    }


                    $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
                    $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
                    $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
                    $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
                    $alamat = parent::anti_injection($parameter['alamat']);
                    $kode_pos = parent::anti_injection($parameter['kode_pos']);


                    $a=explode("|",$id_provinsi);
                    $id_provinsi=$a[0];
                    $nama_provinsi=$a[1];

                    $a=explode("|",$id_kabupaten);
                    $id_kabupaten=$a[0];
                    $nama_kabupaten=$a[1];

                    $a=explode("|",$id_kecamatan);
                    $id_kecamatan=$a[0];
                    $nama_kecamatan=$a[1];

                    $a=explode("|",$id_kelurahan);
                    $id_kelurahan=$a[0];
                    $nama_kelurahan=$a[1];

                    if($kirim=='Y'){
                        $TambahPengajuan = self::$query->insert('pengajuan', array(
                            'id_pelayanan' => $parameter['id_pelayanan'],
                            'uid_member' => $UserData['data']->uid,
                            'waktu_input' => parent::format_date(),
                            'id_status' => 1,
                            'uid_pengajuan_data' => $uid_data,
                            'jenis' => $jenis,
                            'kode' => $kode,
                            'id_provinsi' => $id_provinsi,
                            'nama_provinsi' => $nama_provinsi,
                            'id_kabupaten' => $id_kabupaten,
                            'nama_kabupaten' => $nama_kabupaten,
                            'id_kecamatan' => $id_kecamatan,
                            'nama_kecamatan' => $nama_kecamatan,
                            'id_kelurahan' => $id_kelurahan,
                            'nama_kelurahan' => $nama_kelurahan,
                            'alamat_kirim' => $alamat,
                            'kode_pos' => $kode_pos,
                            'dikirim' => $kirim,
                        ))
                            ->returning('id')
                            ->execute();
                    }
                    else{
                        $TambahPengajuan = self::$query->insert('pengajuan', array(
                            'id_pelayanan' => $parameter['id_pelayanan'],
                            'uid_member' => $UserData['data']->uid,
                            'waktu_input' => parent::format_date(),
                            'id_status' => 1,
                            'uid_pengajuan_data' => $uid_data,
                            'jenis' => $jenis,
                            'kode' => $kode,
                            'dikirim' => $kirim,
                        ))
                            ->returning('id')
                            ->execute();
                    }

                    $id_pengajuan= $TambahPengajuan['response_unique'];

                    $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                        'id_pengajuan' => $id_pengajuan,
                        'waktu' => parent::format_date(),
                        'id_status' => 1
                    ))
                        ->execute();

                    $jenis = parent::encrypt($jenis);

                    $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Pengakuan Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';

                    parent::kirim_wa($_SESSION['no_handphone'], $message);

                    $parameterBuilder = array(
                        'response_message' => $message,
                        'response_result' => $TambahPengajuan['response_result'],
                        'response_data' => array($jenis)
                    );
                } else {
                    $parameterBuilder = array(
                        'response_message' => 'Gagal tambah permohonan',
                        'response_result' => 0,
                        'response_data' => $proceedAktaAku
                    );
                }
            } else {
                $jenis = parent::encrypt($parameter['jenis']);
                header("location:tambah-aktaakuanak?jenis=$jenis");
            }
        } else {
            $parameterBuilder = $hasil;
        }

        return $parameterBuilder;
    }  //Todo : Samakan status query tambah permohonan dengan sahanak

    private function tambah_aktakawin($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $organisasi_penghayat = parent::anti_injection(str_replace("'","''",$parameter['organisasi_penghayat']));
        $badan_peradilan = parent::anti_injection(str_replace("'","''",$parameter['badan_peradilan']));
        $nomor_pengadilan = parent::anti_injection(str_replace("'","''",$parameter['nomor_pengadilan']));
        $nama_pemuka = parent::anti_injection(str_replace("'","''",$parameter['nama_pemuka']));
        $izin_perwakilan = parent::anti_injection(str_replace("'","''",$parameter['izin_perwakilan']));

        $tanggal_kawin = parent::anti_injection($parameter['tanggal_kawin']);
        $id_agama = parent::anti_injection($parameter['id_agama']);
        $tanggal_pengadilan = parent::anti_injection($parameter['tanggal_pengadilan']);
        $jumlah_anak = intval(parent::anti_injection($parameter['jumlah_anak']));

        $jenis = parent::anti_injection($parameter['jenis']);

        $kirim = parent::anti_injection($parameter['kirim']);


        $nik_suami = parent::anti_injection(str_replace("'","''",$parameter['nik_suami']));
        $nama_suami = parent::anti_injection(str_replace("'","''",$parameter['nama_suami']));
        $nik_istri = parent::anti_injection(str_replace("'","''",$parameter['nik_istri']));
        $nama_istri = parent::anti_injection(str_replace("'","''",$parameter['nama_istri']));
        $nik_suami_ayah = parent::anti_injection(str_replace("'","''",$parameter['nik_suami_ayah']));
        $nama_suami_ayah = parent::anti_injection(str_replace("'","''",$parameter['nama_suami_ayah']));
        $nik_suami_ibu = parent::anti_injection(str_replace("'","''",$parameter['nik_suami_ibu']));
        $nama_suami_ibu = parent::anti_injection(str_replace("'","''",$parameter['nama_suami_ibu']));
        $nik_istri_ayah = parent::anti_injection(str_replace("'","''",$parameter['nik_istri_ayah']));
        $nama_istri_ayah = parent::anti_injection(str_replace("'","''",$parameter['nama_istri_ayah']));
        $nik_istri_ibu = parent::anti_injection(str_replace("'","''",$parameter['nik_istri_ibu']));
        $nama_istri_ibu = parent::anti_injection(str_replace("'","''",$parameter['nama_istri_ibu']));
        $nik_saksi_1 = parent::anti_injection(str_replace("'","''",$parameter['nik_saksi_1']));
        $nama_saksi_1 = parent::anti_injection(str_replace("'","''",$parameter['nama_saksi_1']));
        $nik_saksi_2 = parent::anti_injection(str_replace("'","''",$parameter['nik_saksi_2']));
        $nama_saksi_2 = parent::anti_injection(str_replace("'","''",$parameter['nama_saksi_2']));


        $uid_data = parent::gen_uuid();
        if($parameter['tanggal_pengadilan']!='') {
            $AktaKawin = self::$query->insert('aktakawin', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => $UserData['data']->uid,
                'tanggal_kawin' => $tanggal_kawin,
                'id_agama' => $id_agama,
                'organisasi_penghayat' => $organisasi_penghayat,
                'badan_peradilan' => $badan_peradilan,
                'nomor_pengadilan' => $nomor_pengadilan,
                'tanggal_pengadilan' => $tanggal_pengadilan,
                'nama_pemuka' => $nama_pemuka,
                'izin_perwakilan' => $izin_perwakilan,
                'jumlah_anak' => $jumlah_anak,
                'nik_suami' => $nik_suami,
                'nama_suami' => $nama_suami,
                'nik_istri' => $nik_istri,
                'nama_istri' => $nama_istri,
                'nik_suami_ayah' => $nik_suami_ayah,
                'nama_suami_ayah' => $nama_suami_ayah,
                'nik_suami_ibu' => $nik_suami_ibu,
                'nama_suami_ibu' => $nama_suami_ibu,
                'nik_istri_ayah' => $nik_istri_ayah,
                'nama_istri_ayah' => $nama_istri_ayah,
                'nik_istri_ibu' => $nik_istri_ibu,
                'nama_istri_ibu' => $nama_istri_ibu,
                'nik_saksi_1' => $nik_saksi_1,
                'nama_saksi_1' => $nama_saksi_1,
                'nik_saksi_2' => $nik_saksi_2,
                'nama_saksi_2' => $nama_saksi_2
            ))
                ->execute();
            
        }
        else{
            $AktaKawin = self::$query->insert('aktakawin', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => '',
                'tanggal_kawin' => $tanggal_kawin,
                'id_agama' => $id_agama,
                'organisasi_penghayat' => $organisasi_penghayat,
                'badan_peradilan' => $badan_peradilan,
                'nomor_pengadilan' => $nomor_pengadilan,
                'tanggal_pengadilan' => $tanggal_pengadilan,
                'nama_pemuka' => $nama_pemuka,
                'izin_perwakilan' => $izin_perwakilan,
                'jumlah_anak' => $jumlah_anak,
                'nik_suami' => $nik_suami,
                'nama_suami' => $nama_suami,
                'nik_istri' => $nik_istri,
                'nama_istri' => $nama_istri,
                'nik_suami_ayah' => $nik_suami_ayah,
                'nama_suami_ayah' => $nama_suami_ayah,
                'nik_suami_ibu' => $nik_suami_ibu,
                'nama_suami_ibu' => $nama_suami_ibu,
                'nik_istri_ayah' => $nik_istri_ayah,
                'nama_istri_ayah' => $nama_istri_ayah,
                'nik_istri_ibu' => $nik_istri_ibu,
                'nama_istri_ibu' => $nama_istri_ibu,
                'nik_saksi_1' => $nik_saksi_1,
                'nama_saksi_1' => $nama_saksi_1,
                'nik_saksi_2' => $nik_saksi_2,
                'nama_saksi_2' => $nama_saksi_2
            ))
                ->execute();
        }

        if($AktaKawin['response_result'] > 0) {
            $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
            ))
                ->where(array(
                    'pelayanan_jenis_syarat.status_hapus' => '= ?',
                    'AND',
                    'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                ), array(
                    'N', $jenis
                ))
                ->execute();

            foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                //Check Android Upload Partial
                $AndrTemp = self::$query->select('android_file_coordinator', array(
                    'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                ))
                    ->where(array(
                        'android_file_coordinator.uid_foreign' => 'IS NULL',
                        'AND',
                        'android_file_coordinator.type' => '= ?',
                        'AND',
                        'android_file_coordinator.member' => '= ?',
                        'AND',
                        'android_file_coordinator.status' => '= ?'
                    ), array(
                        $CSV['id'], $UserData['data']->uid, 'N'
                    ))
                    ->order(array(
                        'created_at' => 'DESC'
                    ))
                    ->limit(1)
                    ->execute();

                if(count($AndrTemp['response_data']) > 0) {
                    $berkas = "fupload_$CSV[id]";

                    $acak			 = rand(1,99);
                    $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                    $tipe_file       = $_FILES[$berkas]['type'];
                    $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                    $nama_file_unik  = $uid_data.$acak.$nama_file;

                    $vdir_upload = '../berkas/aktakawin/';
                    if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                        mkdir($vdir_upload);
                    }

                    $vfile_upload = $vdir_upload . $nama_file_unik;
                    if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                        //Reset Temp Data
                        $AndrTempStat = self::$query->update('android_file_coordinator', array(
                            'uid_foreign' => $uid_data,
                            'dir_to' => $vfile_upload,
                            'status' => 'D'
                        ))
                            ->where(array(
                                'android_file_coordinator.id' => '= ?'
                            ), array(
                                $AndrTemp['response_data'][0]['id']
                            ))
                            ->execute();
                    }

                    $AktaKawinBerkas = self::$query->insert('aktakawin_berkas', array(
                        'uid_aktakawin' => $uid_data,
                        'nama_berkas' => $CSV['nama'],
                        'berkas' => $nama_file_unik
                    ))
                        ->execute();
                }
            }

            $kode = parent::acak(6);

            $Pengajuan = self::$query->select('pengajuan', array(
                'uid'
            ))
                ->where(array(
                    'pengajuan.kode' => '= ?',
                    'AND',
                    'pengajuan.status_hapus' => '= ?'
                ), array(
                    $kode, 'N'
                ))
                ->execute();

            if(count($Pengajuan['response_data']) > 0) {
                $kode = parent::acak(6);
                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                }
            }

            $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
            $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
            $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
            $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
            $alamat = parent::anti_injection($parameter['alamat']);
            $kode_pos = parent::anti_injection($parameter['kode_pos']);


            $a=explode("|",$id_provinsi);
            $id_provinsi=$a[0];
            $nama_provinsi=$a[1];

            $a=explode("|",$id_kabupaten);
            $id_kabupaten=$a[0];
            $nama_kabupaten=$a[1];

            $a=explode("|",$id_kecamatan);
            $id_kecamatan=$a[0];
            $nama_kecamatan=$a[1];

            $a=explode("|",$id_kelurahan);
            $id_kelurahan=$a[0];
            $nama_kelurahan=$a[1];

            if($kirim=='Y'){
                $TambahPengajuan = self::$query->insert('pengajuan', array(
                    'id_pelayanan' => $parameter['id_pelayanan'],
                    'uid_member' => $UserData['data']->uid,
                    'waktu_input' => parent::format_date(),
                    'id_status' => 1,
                    'uid_pengajuan_data' => $uid_data,
                    'jenis' => $jenis,
                    'kode' => $kode,
                    'id_provinsi' => $id_provinsi,
                    'nama_provinsi' => $nama_provinsi,
                    'id_kabupaten' => $id_kabupaten,
                    'nama_kabupaten' => $nama_kabupaten,
                    'id_kecamatan' => $id_kecamatan,
                    'nama_kecamatan' => $nama_kecamatan,
                    'id_kelurahan' => $id_kelurahan,
                    'nama_kelurahan' => $nama_kelurahan,
                    'alamat_kirim' => $alamat,
                    'kode_pos' => $kode_pos,
                    'dikirim' => $kirim,
                ))
                    ->returning('id')
                    ->execute();
            }
            else{
                $TambahPengajuan = self::$query->insert('pengajuan', array(
                    'id_pelayanan' => $parameter['id_pelayanan'],
                    'uid_member' => $UserData['data']->uid,
                    'waktu_input' => parent::format_date(),
                    'id_status' => 1,
                    'uid_pengajuan_data' => $uid_data,
                    'jenis' => $jenis,
                    'kode' => $kode,
                    'dikirim' => $kirim,
                ))
                    ->returning('id')
                    ->execute();
            }

            $id_pengajuan= $TambahPengajuan['response_unique'];

            $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                'id_pengajuan' => $id_pengajuan,
                'waktu' => parent::format_date(),
                'id_status' => 1
            ))
                ->execute();

            $jenis = parent::encrypt($jenis);

            $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Perkawinan. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';

            parent::kirim_wa($_SESSION['no_handphone'], $message);

            $parameterBuilder = array(
                'response_message' => $message,
                'response_result' => $TambahPengajuan['response_result'],
                'response_data' => array($jenis)
            );
        } else {
            $parameterBuilder = array(
                'response_message' => 'Gagal tambah permohonan',
                'response_result' => 0,
                'response_data' => $AktaKawin
            );
        }

        return $parameterBuilder;
    }  //Todo : Samakan status query tambah permohonan dengan sahanak











    /*private function tambah_aktacerai($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $jenis = parent::anti_injection($parameter['jenis']);

        $tanggal_panitera = 'NULL';
        if ($parameter['tanggal_panitera_pengadilan'] != ""){
            $tanggal_panitera = "'" . $parameter['tanggal_panitera_pengadilan'] . "'";
        }

        $id_pengaju_perceraian = parent::anti_injection($parameter['id_pengaju_perceraian']);
        $nomor_akta_kawin = parent::anti_injection($parameter['nomor_akta_kawin']);
        $tanggal_akta_kawin = parent::anti_injection($parameter['tanggal_akta_kawin']);
        $tempat_pencatatan_perkawinan = parent::anti_injection($parameter['tempat_pencatatan_perkawinan']);
        $nomor_putusan_pengadilan = parent::anti_injection($parameter['nomor_putusan_pengadilan']);
        $tanggal_putusan_pengadilan = parent::anti_injection($parameter['tanggal_putusan_pengadilan']);
        $nama_dan_tingkat_peradilan = parent::anti_injection($parameter['nama_dan_tingkat_peradilan']);
        $tempat_kedudukan_peradilan = parent::anti_injection($parameter['tempat_kedudukan_peradilan']);
        $sebab = parent::anti_injection($parameter['sebab']);
        $nama_lembaga_peradilan_penerbit = parent::anti_injection($parameter['nama_lembaga_peradilan_penerbit']);
        $panitera_pengadilan_negri = parent::anti_injection($parameter['panitera_pengadilan_negri']);
        $nomor_panitera_pengadilan = parent::anti_injection($parameter['nomor_panitera_pengadilan']);
        $tanggal_panitera_pengadilan = parent::anti_injection($parameter['tanggal_panitera_pengadilan']);

        $uid_data = parent::gen_uuid();
        $AktaCerai = self::$query->insert('aktacerai', array(
            'uid' => $uid_data,
            'waktu_input' => parent::format_date(),
            'uid_member' => $UserData['data']->uid,
            'id_pengaju_perceraian' => $id_pengaju_perceraian,
            'nomor_akta_kawin' => $nomor_akta_kawin,
            'tanggal_akta_kawin' => $tanggal_akta_kawin,
            'tempat_kawin' => $tempat_pencatatan_perkawinan,
            'nomor_putusan_pengadilan' => $nomor_putusan_pengadilan,
            'tanggal_putusan_pengadilan' => $tanggal_putusan_pengadilan,
            'nama_peradilan' => $nama_tingkat_peradilan,
            'tempat_peradilan' => $tempat_peradilan,
            'id_alasan_cerai' => $id_alasan_cerai,
            'nama_lembaga_peradilan_penerbit' => $sebab,
            'panitera_pengadilan_negri' => $nama_lembaga_peradilan_penerbit,
            'nomor_panitera' => $panitera_pengadilan_negri,
            'tanggal_panitera' => $nomor_panitera_pengadilan,
            'tanggal_melapor' => $tanggal_panitera,
        ))
            ->execute();

        $sql="INSERT INTO aktacerai (, , , , , , , , , , , , , , , ) VALUES ('', '$]', '', '', '', '', '', '', '', '',  '', '','','','', ,'$waktu_sekarang') RETURNING uid";

        //echo $sql;
        $result=pg_query($conn,$sql);
        $d=pg_fetch_array($result);
        $uid_data=$d['uid'];

        $tampil=pg_query($conn,"SELECT * FROM pelayanan_jenis_syarat WHERE status_hapus='N' AND id_pelayanan_jenis='$jenis'");
        while($r=pg_fetch_array($tampil)){
            $berkas = "fupload_$r[id]";

            $acak			 = rand(1,99);
            $lokasi_file     = $_FILES[$berkas]['tmp_name'];
            $tipe_file       = $_FILES[$berkas]['type'];
            $nama_file       = $_FILES[$berkas]['name'];
            $nama_file_unik  = $uid_data.$acak.$nama_file;

            if ($_FILES[$berkas]["error"] > 0 OR empty($lokasi_file)){
                $nama_file_unik = "";
            }

            else{
                UploadBerkasAktaCerai($nama_file_unik, $lokasi_file);
            }

            $sql="INSERT INTO aktacerai_berkas (uid_aktacerai, nama_berkas, berkas) VALUES  ('$uid_data', '$r[nama]', '$nama_file_unik')";
            pg_query($conn,$sql);
        }

        $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM pengajuan WHERE kode='$kode' AND status_hapus='N'"));
        if($c['ada']!=''){
            $kode = acak(6);
            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM pengajuan WHERE kode='$kode' AND status_hapus='N'"));
            if($c['ada']!=''){
                $kode = acak(6);
            }
        }

        $sql="INSERT INTO pengajuan (id_pelayanan, uid_member, waktu_input, id_status, uid_pengajuan_data, jenis, kode) VALUES ('6', '$_SESSION[login_user]', '$waktu_sekarang', '1',  '$uid_data', '$jenis', '$kode') RETURNING id";
        $result=pg_query($conn,$sql);
        $d=pg_fetch_array($result);
        $id_pengajuan=$d['id'];

        $sql="INSERT INTO pengajuan_log(id_pengajuan, waktu, id_status) VALUES ('$id_pengajuan', '$waktu_sekarang', '1')";
        pg_query($conn,$sql);
    }*/

    private function tambah_aktamati($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $hubungan = parent::anti_injection($parameter['hubungan']);
        $nik = parent::anti_injection($parameter['nik']);
        $nama = parent::anti_injection($parameter['nama']);
        $id_jenkel = parent::anti_injection($parameter['id_jenkel']);
        $tempat_lahir = parent::anti_injection($parameter['tempat_lahir']);
        $tanggal_lahir = parent::anti_injection($parameter['tanggal_lahir']);
        $id_agama = parent::anti_injection($parameter['id_agama']);
        $id_kewarganegaraan = parent::anti_injection($parameter['id_kewarganegaraan']);
        $alamat = parent::anti_injection($parameter['alamat']);
        $rt = parent::anti_injection($parameter['rt']);
        $rw = parent::anti_injection($parameter['rw']);
        $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
        $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
        $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
        $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);

        $a=explode("|",$id_provinsi);
        $id_provinsi=$a[0];

        $a=explode("|",$id_kabupaten);
        $id_kabupaten=$a[0];

        $a=explode("|",$id_kecamatan);
        $id_kecamatan=$a[0];

        $a=explode("|",$id_kelurahan);
        $id_kelurahan=$a[0];

        $kodepos = parent::anti_injection($parameter['kodepos']);
        $telepon = parent::anti_injection($parameter['telepon']);
        $tanggal_meninggal = parent::anti_injection($parameter['tanggal_meninggal']);
        $jam_meninggal = parent::anti_injection($parameter['jam_meninggal']);
        $tempat_meninggal = parent::anti_injection($parameter['tempat_meninggal']);
        $penyebab_meninggal = parent::anti_injection($parameter['penyebab_meninggal']);
        $no_handphone = parent::anti_injection($parameter['no_handphone']);

        $jenis = parent::anti_injection($parameter['jenis']);
        $kirim = parent::anti_injection($parameter['kirim']);
        $uid_data = parent::gen_uuid();
        $AktaMati = self::$query->insert('aktamati', array(
            'uid' => $uid_data,
            'waktu_input' => parent::format_date(),
            'uid_member' => $UserData['data']->uid,
            'hubungan' => $hubungan,
            'nik' => $nik,
            'nama' => $nama,
            'id_jenkel' => $id_jenkel,
            'tempat_lahir' => $tempat_lahir,
            'tanggal_lahir' => $tanggal_lahir,
            'id_agama' => $id_agama,
            'id_kewarganegaraan' => $id_kewarganegaraan,
            'alamat' => $alamat,
            'rt' => $rt,
            'rw' => $rw,
            'id_provinsi' => $id_provinsi,
            'id_kabupaten' => $id_kabupaten,
            'id_kecamatan' => $id_kecamatan,
            'id_kelurahan' => $id_kelurahan,
            'kodepos' => $kodepos,
            'telepon' => $telepon,
            'tanggal_meninggal' => $tanggal_meninggal,
            'jam_meninggal' => $jam_meninggal,
            'tempat_meninggal' => $tempat_meninggal,
            'penyebab_meninggal' => $penyebab_meninggal,
            'no_handphone' => $no_handphone
        ))
            ->execute();

        if($AktaMati['response_result'] > 0) {
            $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
            ))
                ->where(array(
                    'pelayanan_jenis_syarat.status_hapus' => '= ?',
                    'AND',
                    'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                ), array(
                    'N', $jenis
                ))
                ->execute();

            foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                //Check Android Upload Partial
                $AndrTemp = self::$query->select('android_file_coordinator', array(
                    'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                ))
                    ->where(array(
                        'android_file_coordinator.uid_foreign' => 'IS NULL',
                        'AND',
                        'android_file_coordinator.type' => '= ?',
                        'AND',
                        'android_file_coordinator.member' => '= ?',
                        'AND',
                        'android_file_coordinator.status' => '= ?'
                    ), array(
                        $CSV['id'], $UserData['data']->uid, 'N'
                    ))
                    ->order(array(
                        'created_at' => 'DESC'
                    ))
                    ->limit(1)
                    ->execute();

                if(count($AndrTemp['response_data']) > 0) {
                    $berkas = "fupload_$CSV[id]";

                    $acak			 = rand(1,99);
                    $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                    $tipe_file       = $_FILES[$berkas]['type'];
                    $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                    $nama_file_unik  = $uid_data.$acak.$nama_file;

                    $vdir_upload = '../berkas/aktamati/';
                    if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                        mkdir($vdir_upload);
                    }

                    $vfile_upload = $vdir_upload . $nama_file_unik;
                    if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                        //Reset Temp Data
                        $AndrTempStat = self::$query->update('android_file_coordinator', array(
                            'uid_foreign' => $uid_data,
                            'dir_to' => $vfile_upload,
                            'status' => 'D'
                        ))
                            ->where(array(
                                'android_file_coordinator.id' => '= ?'
                            ), array(
                                $AndrTemp['response_data'][0]['id']
                            ))
                            ->execute();
                    }

                    $AktaMatiBerkas = self::$query->insert('aktamati_berkas', array(
                        'uid_aktamati' => $uid_data,
                        'nama_berkas' => $CSV['nama'],
                        'berkas' => $nama_file_unik
                    ))
                        ->execute();
                }
            }

            $kode = parent::acak(6);

            $Pengajuan = self::$query->select('pengajuan', array(
                'uid'
            ))
                ->where(array(
                    'pengajuan.kode' => '= ?',
                    'AND',
                    'pengajuan.status_hapus' => '= ?'
                ), array(
                    $kode, 'N'
                ))
                ->execute();

            if(count($Pengajuan['response_data']) > 0) {
                $kode = parent::acak(6);
                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                }
            }

            $id_provinsi = parent::anti_injection($parameter['id_provinsi2']);
            $id_kabupaten = parent::anti_injection($parameter['id_kabupaten2']);
            $id_kecamatan = parent::anti_injection($parameter['id_kecamatan2']);
            $id_kelurahan = parent::anti_injection($parameter['id_kelurahan2']);
            $alamat = parent::anti_injection($parameter['alamat']);
            $kode_pos = parent::anti_injection($parameter['kode_pos']);


            $a=explode("|",$id_provinsi);
            $id_provinsi=$a[0];
            $nama_provinsi=$a[1];

            $a=explode("|",$id_kabupaten);
            $id_kabupaten=$a[0];
            $nama_kabupaten=$a[1];

            $a=explode("|",$id_kecamatan);
            $id_kecamatan=$a[0];
            $nama_kecamatan=$a[1];

            $a=explode("|",$id_kelurahan);
            $id_kelurahan=$a[0];
            $nama_kelurahan=$a[1];

            if($kirim=='Y') {
                $TambahPengajuan = self::$query->insert('pengajuan', array(
                    'id_pelayanan' => $parameter['id_pelayanan'],
                    'uid_member' => $UserData['data']->uid,
                    'waktu_input' => parent::format_date(),
                    'id_status' => 1,
                    'uid_pengajuan_data' => $uid_data,
                    'jenis' => $jenis,
                    'kode' => $kode,
                    'id_provinsi' => $id_provinsi,
                    'nama_provinsi' => $nama_provinsi,
                    'id_kabupaten' => $id_kabupaten,
                    'nama_kabupaten' => $nama_kabupaten,
                    'id_kecamatan' => $id_kecamatan,
                    'nama_kecamatan' => $nama_kecamatan,
                    'id_kelurahan' => $id_kelurahan,
                    'nama_kelurahan' => $nama_kelurahan,
                    'alamat_kirim' => $alamat,
                    'kode_pos' => $kode_pos,
                    'dikirim' => $kirim,
                ))
                    ->returning('id')
                    ->execute();
            }
            else{
                $TambahPengajuan = self::$query->insert('pengajuan', array(
                    'id_pelayanan' => $parameter['id_pelayanan'],
                    'uid_member' => $UserData['data']->uid,
                    'waktu_input' => parent::format_date(),
                    'id_status' => 1,
                    'uid_pengajuan_data' => $uid_data,
                    'jenis' => $jenis,
                    'kode' => $kode,
                    'dikirim' => $kirim,
                ))
                    ->returning('id')
                    ->execute();
            }

            $id_pengajuan= $TambahPengajuan['response_unique'];

            $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                'id_pengajuan' => $id_pengajuan,
                'waktu' => parent::format_date(),
                'id_status' => 1
            ))
                ->execute();

            $jenis = parent::encrypt($jenis);

            $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Kematian. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';

            parent::kirim_wa($_SESSION['no_handphone'], $message);

            $parameterBuilder = array(
                'response_message' => $message,
                'response_result' => $TambahPengajuan['response_result'],
                'response_data' => array($jenis)
            );
        } else {
            $parameterBuilder = array(
                'response_message' => 'Gagal tambah permohonan',
                'response_result' => 0,
                'response_data' => $AktaMati
            );
        }

        return $parameterBuilder;
    }

    private function tambah_identitasanak($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $nik = parent::anti_injection($parameter['nik']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://siakmedan3.medan.depdagri.go.id/ajax/sibisa/get-nik");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"nik=$nik");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_object = json_decode(curl_exec($ch));

        //print("<pre>".print_r($json_object,true)."</pre>");
        curl_close ($ch);

        $anak_nama = $json_object->NAMA_LGKP;
        $anak_nama = str_replace("'","''",$anak_nama);
        $anak_tempat_lahir = $json_object->TMPT_LHR;
        $anak_tanggal_lahir = $json_object->TGL_LHR;
        $anak_jenkel=$json_object->JENIS_KLMIN;
        $anak_agama = $json_object->AGAMA;
        $kk = $json_object->KARTU_KELUARGA;
        $anak_alamat = $kk->ALAMAT;

        $anak_noakta = parent::anti_injection($parameter['anak_noakta']);
        $anak_ayah = parent::anti_injection($parameter['anak_ayah']);
        $anak_ibu = parent::anti_injection($parameter['anak_ibu']);
        $jenis = parent::anti_injection($parameter['jenis']);

        $kirim = parent::anti_injection($parameter['kirim']);
        $uid_data = parent::gen_uuid();
        $KartuKIA = self::$query->insert('kartukia', array(
            'uid' => $uid_data,
            'waktu_input' => parent::format_date(),
            'uid_member' => $UserData['data']->uid,
            'anak_nik' => $nik,
            'anak_noakta' => $anak_noakta,
            'anak_nama' => (isset($anak_nama) ? $anak_nama : ''),
            'anak_tempatlahir' => (isset($anak_tempat_lahir) ? $anak_tempat_lahir : ''),
            'anak_tanggallahir' => (isset($anak_tanggal_lahir) ? $anak_tanggal_lahir : ''),
            'anak_ayah' => (isset($anak_ayah) ? $anak_ayah : ''),
            'anak_ibu' => (isset($anak_ibu) ? $anak_ibu : ''),
            'anak_alamat' => (isset($anak_alamat) ? $anak_alamat : '')
        ))
            ->execute();

        if($KartuKIA['response_result'] > 0) {
            $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
            ))
                ->where(array(
                    'pelayanan_jenis_syarat.status_hapus' => '= ?',
                    'AND',
                    'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                ), array(
                    'N', $jenis
                ))
                ->execute();

            foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                //Check Android Upload Partial
                $AndrTemp = self::$query->select('android_file_coordinator', array(
                    'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                ))
                    ->where(array(
                        'android_file_coordinator.uid_foreign' => 'IS NULL',
                        'AND',
                        'android_file_coordinator.type' => '= ?',
                        'AND',
                        'android_file_coordinator.member' => '= ?',
                        'AND',
                        'android_file_coordinator.status' => '= ?'
                    ), array(
                        $CSV['id'], $UserData['data']->uid, 'N'
                    ))
                    ->order(array(
                        'created_at' => 'DESC'
                    ))
                    ->limit(1)
                    ->execute();

                if(count($AndrTemp['response_data']) > 0) {
                    $berkas = "fupload_$CSV[id]";

                    $acak			 = rand(1,99);
                    $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                    $tipe_file       = $_FILES[$berkas]['type'];
                    $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                    $nama_file_unik  = $uid_data.$acak.$nama_file;

                    $vdir_upload = '../berkas/kia/';
                    if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                        mkdir($vdir_upload);
                    }

                    $vfile_upload = $vdir_upload . $nama_file_unik;
                    if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                        //Reset Temp Data
                        $AndrTempStat = self::$query->update('android_file_coordinator', array(
                            'uid_foreign' => $uid_data,
                            'dir_to' => $vfile_upload,
                            'status' => 'D'
                        ))
                            ->where(array(
                                'android_file_coordinator.id' => '= ?'
                            ), array(
                                $AndrTemp['response_data'][0]['id']
                            ))
                            ->execute();
                    }

                    $AktaEsahBerkas = self::$query->insert('kartukia_berkas', array(
                        'uid_kartukia' => $uid_data,
                        'nama_berkas' => $CSV['nama'],
                        'berkas' => $nama_file_unik
                    ))
                        ->execute();
                }
            }

            $kode = parent::acak(6);

            $Pengajuan = self::$query->select('pengajuan', array(
                'uid'
            ))
                ->where(array(
                    'pengajuan.kode' => '= ?',
                    'AND',
                    'pengajuan.status_hapus' => '= ?'
                ), array(
                    $kode, 'N'
                ))
                ->execute();

            if(count($Pengajuan['response_data']) > 0) {
                $kode = parent::acak(6);
                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                }
            }

            $id_provinsi = parent::anti_injection($parameter['id_provinsi']);
            $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
            $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
            $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
            $alamat = parent::anti_injection($parameter['alamat']);
            $kode_pos = parent::anti_injection($parameter['kode_pos']);


            $a=explode("|",$id_provinsi);
            $id_provinsi=$a[0];
            $nama_provinsi=$a[1];

            $a=explode("|",$id_kabupaten);
            $id_kabupaten=$a[0];
            $nama_kabupaten=$a[1];

            $a=explode("|",$id_kecamatan);
            $id_kecamatan=$a[0];
            $nama_kecamatan=$a[1];

            $a=explode("|",$id_kelurahan);
            $id_kelurahan=$a[0];
            $nama_kelurahan=$a[1];

            if($kirim=='Y') {
                $TambahPengajuan = self::$query->insert('pengajuan', array(
                    'id_pelayanan' => $parameter['id_pelayanan'],
                    'uid_member' => $UserData['data']->uid,
                    'waktu_input' => parent::format_date(),
                    'id_status' => 1,
                    'uid_pengajuan_data' => $uid_data,
                    'jenis' => $jenis,
                    'kode' => $kode,
                    'id_provinsi' => $id_provinsi,
                    'nama_provinsi' => $nama_provinsi,
                    'id_kabupaten' => $id_kabupaten,
                    'nama_kabupaten' => $nama_kabupaten,
                    'id_kecamatan' => $id_kecamatan,
                    'nama_kecamatan' => $nama_kecamatan,
                    'id_kelurahan' => $id_kelurahan,
                    'nama_kelurahan' => $nama_kelurahan,
                    'alamat_kirim' => $alamat,
                    'kode_pos' => $kode_pos,
                    'dikirim' => $kirim,
                ))
                    ->returning('id')
                    ->execute();
            }
            else{
                $TambahPengajuan = self::$query->insert('pengajuan', array(
                    'id_pelayanan' => $parameter['id_pelayanan'],
                    'uid_member' => $UserData['data']->uid,
                    'waktu_input' => parent::format_date(),
                    'id_status' => 1,
                    'uid_pengajuan_data' => $uid_data,
                    'jenis' => $jenis,
                    'kode' => $kode,
                    'dikirim' => $kirim,
                ))
                    ->returning('id')
                    ->execute();
            }

            $id_pengajuan= $TambahPengajuan['response_unique'];

            $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                'id_pengajuan' => $id_pengajuan,
                'waktu' => parent::format_date(),
                'id_status' => 1
            ))
                ->execute();

            $jenis = parent::encrypt($jenis);

            $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Kartu Identitas Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';

            parent::kirim_wa($_SESSION['no_handphone'], $message);

            $parameterBuilder = array(
                'response_message' => $message,
                'response_result' => $TambahPengajuan['response_result'],
                'response_data' => array($jenis)
            );
        } else {
            $parameterBuilder = array(
                'response_message' => 'Gagal tambah permohonan',
                'response_result' => 0,
                'response_data' => $KartuKIA
            );
        }

        return $parameterBuilder;
    }

    private function tambah_suratpindah($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $temp_parameter = $parameter;   //for backup request
        $parameter = json_decode($parameter['data'], true);

        $nik = parent::anti_injection($parameter['nik']);
        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik(array(
            'nik' => $nik
        ));

        if($hasil['response_package']['response_result'] > 0) {
            $json_object = $hasil['response_package']['response_data'][0];


            $nama = $json_object->NAMA_LGKP;
            $nama = str_replace("'","''",$nama);
            $alamat = strtoupper($parameter['alamat_tujuan']);

            $alasan_lainnya = "";
            if ($parameter['alasan'] == "75"){
                $alasan_lainnya = $parameter['alasan_pindah_lainnya'];
            }

            if ($json_object == ""){
                $nama = $parameter['nama'];
            }

            $jenis = parent::anti_injection($parameter['jenis']);
            $alasan_lainnya = parent::anti_injection($parameter['alasan_lainnya']);
            $nama = parent::anti_injection($parameter['nama']);
            $no_kk = parent::anti_injection($parameter['no_kk']);
            $alasan = parent::anti_injection($parameter['alasan']);
            $rt_tujuan = parent::anti_injection($parameter['rt_tujuan']);
            $rw_tujuan = parent::anti_injection($parameter['rw_tujuan']);
            $id_kelurahan = parent::anti_injection($parameter['id_kelurahan']);
            $id_kecamatan = parent::anti_injection($parameter['id_kecamatan']);
            $id_kabupaten = parent::anti_injection($parameter['id_kabupaten']);
            $id_provinsi = parent::anti_injection($parameter['id_provinsi']);

            $a=explode("|",$id_provinsi);
            $id_provinsi=$a[0];
            $nama_provinsi_tujuan=$a[1];

            $a=explode("|",$id_kabupaten);
            $id_kabupaten=$a[0];
            $nama_kabupaten_tujuan=$a[1];

            $a=explode("|",$id_kecamatan);
            $id_kecamatan=$a[0];
            $nama_kecamatan_tujuan=$a[1];

            $a=explode("|",$id_kelurahan);
            $id_kelurahan=$a[0];
            $nama_kelurahan_tujuan=$a[1];

            $kode_pos = parent::anti_injection($parameter['kode_pos_tujuan']);
            $telepon = parent::anti_injection($parameter['telepon']);
            $jenis_pindahan = parent::anti_injection($parameter['jenis_pindahan']);
            $kk_bagi_tidak_pindah = parent::anti_injection($parameter['kk_bagi_tidak_pindah']);
            $kk_bagi_pindah = parent::anti_injection($parameter['kk_bagi_pindah']);


            $kirim = parent::anti_injection($parameter['kirim']);
            $uid_data = parent::gen_uuid();

            $SuratPindah = self::$query->insert('suratpindah', array(
                'uid' => $uid_data,
                'waktu_input' => parent::format_date(),
                'uid_member' => $UserData['data']->uid,
                'no_kk' => $no_kk,
                'nik_pemohon' => $nik,
                'nama_pemohon' => $nama,
                'alasan_pindah' => $alasan,
                'alasan_pindah_lainnya' => $alasan_lainnya,
                'alamat_tujuan' => $alamat,
                'rt_tujuan' => $rt_tujuan,
                'rw_tujuan' => $rw_tujuan,
                'id_kelurahan_tujuan' => intval($id_kelurahan),
                'id_kecamatan_tujuan' => intval($id_kecamatan),
                'id_kabupaten_tujuan' => intval($id_kabupaten),
                'id_provinsi_tujuan' => intval($id_provinsi),
                'kode_pos_tujuan' => $kode_pos,
                'telepon' => $telepon,
                'jenis_kepindahan' => intval($jenis_pindahan),
                'status_kk_tidak_pindah' => intval($kk_bagi_tidak_pindah),
                'status_kk_pindah' => intval($kk_bagi_pindah),
                'nama_provinsi_tujuan' => $nama_provinsi_tujuan,
                'nama_kabupaten_tujuan' => $nama_kabupaten_tujuan,
                'nama_kecamatan_tujuan' => $nama_kecamatan_tujuan,
                'nama_kelurahan_tujuan' => $nama_kelurahan_tujuan
            ))
                ->execute();
            if($SuratPindah['response_result'] > 0) {
                $CheckSyarat = self::$query->select('pelayanan_jenis_syarat', array(
                    'id', 'nama', 'id_pelayanan_jenis', 'status_hapus', 'is_required', 'urutan'
                ))
                    ->where(array(
                        'pelayanan_jenis_syarat.status_hapus' => '= ?',
                        'AND',
                        'pelayanan_jenis_syarat.id_pelayanan_jenis' => '= ?'
                    ), array(
                        'N', $jenis
                    ))
                    ->execute();

                foreach($CheckSyarat['response_data'] as $CSK => $CSV) {

                    //Check Android Upload Partial
                    $AndrTemp = self::$query->select('android_file_coordinator', array(
                        'id', 'file_name', 'dir_from', 'dir_to', 'type', 'status'

                    ))
                        ->where(array(
                            'android_file_coordinator.uid_foreign' => 'IS NULL',
                            'AND',
                            'android_file_coordinator.type' => '= ?',
                            'AND',
                            'android_file_coordinator.member' => '= ?',
                            'AND',
                            'android_file_coordinator.status' => '= ?'
                        ), array(
                            $CSV['id'], $UserData['data']->uid, 'N'
                        ))
                        ->order(array(
                            'created_at' => 'DESC'
                        ))
                        ->limit(1)
                        ->execute();

                    if(count($AndrTemp['response_data']) > 0) {
                        $berkas = "fupload_$CSV[id]";

                        $acak			 = rand(1,99);
                        $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                        $tipe_file       = $_FILES[$berkas]['type'];
                        $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                        $nama_file_unik  = $uid_data.$acak.$nama_file;

                        $vdir_upload = '../berkas/suratpindah/';
                        if(!is_dir($vdir_upload) && !file_exists($vdir_upload)) {
                            mkdir($vdir_upload);
                        }

                        $vfile_upload = $vdir_upload . $nama_file_unik;
                        if(rename('android_temp/' . $AndrTemp['response_data'][0]['dir_from'], $vfile_upload)) {
                            //Reset Temp Data
                            $AndrTempStat = self::$query->update('android_file_coordinator', array(
                                'uid_foreign' => $uid_data,
                                'dir_to' => $vfile_upload,
                                'status' => 'D'
                            ))
                                ->where(array(
                                    'android_file_coordinator.id' => '= ?'
                                ), array(
                                    $AndrTemp['response_data'][0]['id']
                                ))
                                ->execute();
                        }

                        $AktaEsahBerkas = self::$query->insert('suratpindah_berkas', array(
                            'uid_suratpindah' => $uid_data,
                            'nama_berkas' => $CSV['nama'],
                            'berkas' => $nama_file_unik
                        ))
                            ->execute();
                    }
                }

                if (isset($parameter['id_anggota'])){
                    /*$id_anggota = $parameter['id_anggota'];
                    $data_anggota = array();

                    //$anggota = explode(",",$id_anggota);
                    //print_r($anggota);
                    foreach ($parameter['id_anggota'] as $key => $value) {
                        //$temp_arr = explode("-", $value);

                        //print_r($temp_arr[0]);
                        array_push($data_anggota,array(
                                "nik" => $value['nik'],
                                "nama" => $value['nama']
                            )
                        );
                    }*/

                    foreach ($parameter['id_anggota'] as $key => $value) {
                        $SuratPindahAnak = self::$query->insert('suratpindah_anak', array(
                            'nik' => $value['nik'],
                            'nama' => $value['nama'],
                            'uid_suratpindah' => $uid_data
                        ))
                            ->execute();
                    }
                }

                $kode = parent::acak(6);

                $Pengajuan = self::$query->select('pengajuan', array(
                    'uid'
                ))
                    ->where(array(
                        'pengajuan.kode' => '= ?',
                        'AND',
                        'pengajuan.status_hapus' => '= ?'
                    ), array(
                        $kode, 'N'
                    ))
                    ->execute();

                if(count($Pengajuan['response_data']) > 0) {
                    $kode = parent::acak(6);
                    $Pengajuan = self::$query->select('pengajuan', array(
                        'uid'
                    ))
                        ->where(array(
                            'pengajuan.kode' => '= ?',
                            'AND',
                            'pengajuan.status_hapus' => '= ?'
                        ), array(
                            $kode, 'N'
                        ))
                        ->execute();

                    if(count($Pengajuan['response_data']) > 0) {
                        $kode = parent::acak(6);
                    }
                }

                $id_provinsi = parent::anti_injection($parameter['id_provinsi2']);
                $id_kabupaten = parent::anti_injection($parameter['id_kabupaten2']);
                $id_kecamatan = parent::anti_injection($parameter['id_kecamatan2']);
                $id_kelurahan = parent::anti_injection($parameter['id_kelurahan2']);
                $alamat = parent::anti_injection($parameter['alamat']);
                $kode_pos = parent::anti_injection($parameter['kode_pos']);


                $a=explode("|",$id_provinsi);
                $id_provinsi=$a[0];
                $nama_provinsi=$a[1];

                $a=explode("|",$id_kabupaten);
                $id_kabupaten=$a[0];
                $nama_kabupaten=$a[1];

                $a=explode("|",$id_kecamatan);
                $id_kecamatan=$a[0];
                $nama_kecamatan=$a[1];

                $a=explode("|",$id_kelurahan);
                $id_kelurahan=$a[0];
                $nama_kelurahan=$a[1];

                if($kirim=='Y') {
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'id_provinsi' => $id_provinsi,
                        'nama_provinsi' => $nama_provinsi,
                        'id_kabupaten' => $id_kabupaten,
                        'nama_kabupaten' => $nama_kabupaten,
                        'id_kecamatan' => $id_kecamatan,
                        'nama_kecamatan' => $nama_kecamatan,
                        'id_kelurahan' => $id_kelurahan,
                        'nama_kelurahan' => $nama_kelurahan,
                        'alamat_kirim' => $alamat,
                        'kode_pos' => $kode_pos,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }
                else{
                    $TambahPengajuan = self::$query->insert('pengajuan', array(
                        'id_pelayanan' => $parameter['id_pelayanan'],
                        'uid_member' => $UserData['data']->uid,
                        'waktu_input' => parent::format_date(),
                        'id_status' => 1,
                        'uid_pengajuan_data' => $uid_data,
                        'jenis' => $jenis,
                        'kode' => $kode,
                        'dikirim' => $kirim,
                    ))
                        ->returning('id')
                        ->execute();
                }

                $id_pengajuan= $TambahPengajuan['response_unique'];

                $TambahPengajuanLog = self::$query->insert('pengajuan_log', array(
                    'id_pengajuan' => $id_pengajuan,
                    'waktu' => parent::format_date(),
                    'id_status' => 1
                ))
                    ->execute();

                $jenis = parent::encrypt($jenis);

                $message = 'Selamat Anda sudah melakukan pengajuan data untuk layanan Surat Pindah. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih';

                parent::kirim_wa($_SESSION['no_handphone'], $message);

                $parameterBuilder = array(
                    'response_message' => $message,
                    'response_result' => $SuratPindah['response_result'],
                    'response_data' => array($jenis)
                );
            } else {
                $parameterBuilder = array(
                    'response_message' => 'Gagal tambah permohonan',
                    'response_result' => 0,
                    'response_data' => $SuratPindah
                );
            }
        } else {
            $parameterBuilder = array(
                'response_message' => $hasil['response_package']['response_message'],
                'response_result' => $hasil['response_package']['response_result'],
                'response_data' => $hasil['response_package']['response_data']
            );
        }

        return $parameterBuilder;
    }
}

?>