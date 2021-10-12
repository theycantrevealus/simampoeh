<?php

namespace PondokCoder;

use PondokCoder\Authorization as Authorization;
use PondokCoder\Utility as Utility;
use PondokCoder\Modul as Modul;
use PondokCoder\Poli as Poli;
use PondokCoder\Unit as Unit;
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
            case 'tambah_aktaaku':
                return self::tambah_aktaaku($parameter);
                break;

            case 'tambah_aktalahir':
                return self::tambah_aktalahir($parameter);
                break;

            case '':
        }
    }

    private function tambah_aktalahir($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);



        $anak_nama = str_replace("'","''",$_POST['anak_nama']);
        $anak_tempat_lahir = str_replace("'","''",$_POST['anak_tempat_lahir']);
        $anak_berat_bayi = str_replace("'","''",$_POST['anak_berat_bayi']);
        $anak_panjang_bayi = str_replace("'","''",$_POST['anak_panjang_bayi']);

        $anak_nama = parent::anti_injection($anak_nama);
        $anak_tempat_lahir = parent::anti_injection($anak_tempat_lahir);
        $anak_berat_bayi = parent::anti_injection($anak_berat_bayi);
        $anak_berat_bayi_koma = parent::anti_injection($_POST['anak_berat_bayi_koma']);
        $anak_panjang_bayi = parent::anti_injection($anak_panjang_bayi);

        $anak_id_jenkel = parent::anti_injection($_POST['anak_id_jenkel']);
        $anak_id_tempatlahir = parent::anti_injection($_POST['anak_id_tempatlahir']);
        $anak_tanggal_lahir = parent::anti_injection($_POST['anak_tanggal_lahir']);
        $anak_jam_lahir = parent::anti_injection($_POST['anak_jam_lahir']);
        $anak_kelahiran_ke = parent::anti_injection($_POST['anak_kelahiran_ke']);
        $anak_id_tolonglahir = parent::anti_injection($_POST['anak_id_tolonglahir']);
        $anak_ayah = parent::anti_injection($_POST['anak_ayah']);
        $anak_ibu = parent::anti_injection($_POST['anak_ibu']);
        $anak_saksi1 = parent::anti_injection($_POST['anak_saksi1']);
        $anak_saksi2 = parent::anti_injection($_POST['anak_saksi2']);
        $anak_no_handphone = parent::anti_injection($_POST['anak_no_handphone']);
        $jenis_kelahiran = parent::anti_injection($_POST['jenis_kelahiran']);
        $jenis = parent::anti_injection($_POST['jenis']);
        $kirim = parent::anti_injection($_POST['kirim']);

        //SETTING BERAT BAYI DIGABUNGKAN
        $anak_berat_bayi = $anak_berat_bayi.'.'.$anak_berat_bayi_koma;
        $uid_data = parent::gen_uuid();
        $AktaLahir = self::$query->insert('aktalahir', array(
            'uid' => '',
            'waktu_input' => parent::format_date(),
            'uid_member' => $UserData['data']->uid,
            'anak_nama' => $anak_nama,
            'anak_id_jenkel' => $anak_id_jenkel,
            'anak_id_tempatlahir' => $anak_id_tempatlahir,
            'anak_tempat_lahir' => $anak_tempat_lahir,
            'anak_tanggal_lahir' => $anak_tanggal_lahir,
            'anak_jam_lahir' => $anak_jam_lahir,
            'anak_kelahiran_ke' => $anak_kelahiran_ke,
            'anak_id_tolonglahir' => $anak_id_tolonglahir,
            'anak_berat_bayi' => $anak_berat_bayi,
            'anak_panjang_bayi' => $anak_panjang_bayi,
            'anak_ayah' => $anak_ayah,
            'anak_ibu' => $anak_ibu,
            'anak_saksi1' => $anak_saksi1,
            'anak_saksi2' => $anak_saksi2,
            'anak_no_handphone' => $anak_no_handphone,
            'jenis_kelahiran' => $jenis_kelahiran
        ))
            ->execute();

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

        foreach($CheckSyarat['response_data'] as $CSK => $CSV){
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
                $vdir_upload = "../../berkas/aktalahir/";
                $vfile_upload = $vdir_upload . $nama_file_unik;
                move_uploaded_file($lokasi_file, $vfile_upload);
            }


            $Berkas = self::$query->insert('aktalahir_berkas', array(
                'uid_aktalahir' => $uid_data,
                'nama_berkas' => $CSV['nama'],
                'berkas' => $nama_file_unik
            ))
                ->execute();
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

        $id_provinsi = parent::anti_injection($_POST['id_provinsi']);
        $id_kabupaten = parent::anti_injection($_POST['id_kabupaten']);
        $id_kecamatan = parent::anti_injection($_POST['id_kecamatan']);
        $id_kelurahan = parent::anti_injection($_POST['id_kelurahan']);
        $alamat = parent::anti_injection($_POST['alamat']);
        $kode_pos = parent::anti_injection($_POST['kode_pos']);


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
                'id_pelayanan' => 2,
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
                'id_pelayanan' => 2,
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
    }




























    private function tambah_aktaaku($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $parameterBuilder = array();

        $nik = parent::anti_injection($parameter['anak_nik']);
        $ch = curl_init();


        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik($nik);

        if($hasil['response_result'] > 0) {
            $json_object = $hasil['response_data'][0];
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
                    'anak_kodepos' => $anak_kodepos,
                    'anak_telepon' => $anak_telepon,
                    'anak_kelahiran_ke' => $anak_kelahiran_ke,
                    'anak_nomor_akta' => $anak_nomor_akta,
                    'anak_tanggal_akta' => $anak_tanggal_akta,
                    'anak_dinas_akta' => '$anak_dinas_akta'

                ))
                    ->execute();

                $PelayananJenis = self::$query->select('pelayanan_jenis_syarat', array(
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

                foreach ($PelayananJenis['response_data'] as $PKey => $PValue) {
                    $berkas = "fupload_$PValue[id]";
                    $acak			 = rand(1,99);
                    $lokasi_file     = $_FILES[$berkas]['tmp_name'];
                    $tipe_file       = $_FILES[$berkas]['type'];
                    $nama_file       = $_FILES[$berkas]['name'];
                    $nama_file_unik  = $uid_data.$acak.$nama_file;

                    if ($_FILES[$berkas]["error"] > 0 || empty($lokasi_file)){
                        $nama_file_unik = "";
                    }

                    else{

                        $vdir_upload = "../berkas/aktaaku/";
                        $vfile_upload = $vdir_upload . $nama_file_unik;
                        move_uploaded_file($lokasi_file, $vfile_upload);


                    }

                    $AktaAkuBerkas = self::$query->insert('aktaaku_berkas', array(
                        'uid_aktaaku' => $uid_data,
                        'nama_berkas' => $PValue['nama'],
                        'berkas' => $nama_file_unik
                    ))
                        ->execute();
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
                        'id_pelayanan' => 2,
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
                        'id_pelayanan' => 2,
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
                $jenis = parent::encrypt($parameter['jenis']);
                header("location:tambah-aktaakuanak?jenis=$jenis");
            }
        } else {
            $parameterBuilder = $hasil;
        }

        return $parameterBuilder;
    }
}

?>