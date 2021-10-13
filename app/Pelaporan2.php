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

            case 'tambah_aktacerai':
                return self::tambah_aktacerai($parameter);
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

        $nik = parent::anti_injection($parameter['anak_nik']);
        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik($nik);

        if($hasil['response_result'] > 0) {
            $json_object = $hasil['response_data'][0];

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

            //Todo : Lanjutan

            $sql="INSERT INTO aktaesah (waktu_input, uid_member, anak_nik, anak_nama, anak_tempat_lahir, anak_tanggal_lahir, anak_jenkel, anak_agama, anak_alamat, anak_rt, anak_rw, anak_provinsi, anak_kabupaten, anak_kecamatan, anak_kelurahan, anak_kodepos, anak_telepon, anak_kelahiran_ke, anak_nomor_akta, anak_tanggal_akta, anak_dinas_akta) VALUES ('$waktu_sekarang', '$_SESSION[login_user]', '$nik', '$anak_nama', '$anak_tempat_lahir', '$anak_tanggal_lahir', '$anak_jenkel', '$anak_agama', '$anak_alamat', '$anak_rt', '$anak_rw', '$anak_provinsi', '$anak_kabupaten', '$anak_kecamatan', '$anak_kelurahan', '$anak_kodepos', '$anak_telepon', '$anak_kelahiran_ke', '$anak_nomor_akta', '$anak_tanggal_akta', '$anak_dinas_akta') RETURNING uid";

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
                    UploadBerkasAktaEsah($nama_file_unik, $lokasi_file);
                }

                $sql="INSERT INTO aktaesah_berkas (uid_aktaesah, nama_berkas, berkas) VALUES  ('$uid_data', '$r[nama]', '$nama_file_unik')";
                pg_query($conn,$sql);
            }

            $kode = acak(6);

            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM pengajuan WHERE kode='$kode' AND status_hapus='N'"));
            if($c['ada']!=''){
                $kode = acak(6);
                $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM pengajuan WHERE kode='$kode' AND status_hapus='N'"));
                if($c['ada']!=''){
                    $kode = acak(6);
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
                $sql="INSERT INTO pengajuan (id_pelayanan, uid_member, waktu_input, id_status, uid_pengajuan_data, jenis, kode, id_provinsi, nama_provinsi, id_kabupaten,  nama_kabupaten, id_kecamatan, nama_kecamatan, id_kelurahan, nama_kelurahan, alamat_kirim, kode_pos, dikirim) VALUES ('1', '$_SESSION[login_user]', '$waktu_sekarang', '1', '$uid_data', '$jenis', '$kode', '$id_provinsi', '$nama_provinsi', '$id_kabupaten', '$nama_kabupaten', '$id_kecamatan', '$nama_kecamatan', '$id_kelurahan', '$nama_kelurahan', '$alamat', '$kode_pos', '$kirim') RETURNING id";
            }
            else{
                $sql="INSERT INTO pengajuan (id_pelayanan, uid_member, waktu_input, id_status,  uid_pengajuan_data, jenis, kode, dikirim) VALUES ('1', '$_SESSION[login_user]', '$waktu_sekarang', '1', '$uid_data', '$jenis', '$kode', '$kirim') RETURNING id";
            }

            //echo $sql;
            $result=pg_query($conn,$sql);
            $d=pg_fetch_array($result);
            $id_pengajuan=$d['id'];

            $sql="INSERT INTO pengajuan_log(id_pengajuan, waktu, id_status) VALUES ('$id_pengajuan', '$waktu_sekarang', '1')";
            pg_query($conn,$sql);

            $jenis = parent::encrypt($jenis);

            $message="Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Pengesahan Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih";
            parent::kirim_wa($_SESSION['no_handphone'], $message);

            header("location:aktaesahanak?jenis=$jenis");
            return array();

        }
    }

    private function tambah_angkatanak($parameter) {
        return array();
    }

    private function tambah_aktakawin($parameter) {
        return array();
    }

    private function tambah_aktacerai($parameter) {
        return array();
    }

    private function tambah_aktamati($parameter) {
        return array();
    }

























    private function tambah_kartukeluarga($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

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

            $id_kecamatan = parent::anti_injection($parameter['id_kecamatan2']);
            $id_kelurahan = parent::anti_injection($parameter['id_kelurahan2']);

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

                //Todo : Sini perlu di pindahkan upload temp nya

                if(count($AndrTemp['response_data']) > 0) {
                    $berkas = "fupload_$CSV[id]";

                    $acak			 = rand(1,99);
                    $lokasi_file     = $AndrTemp['response_data'][0]['dir_from'];
                    $tipe_file       = $_FILES[$berkas]['type'];
                    $nama_file       = $AndrTemp['response_data'][0]['file_name'];
                    $nama_file_unik  = $uid_data.$acak.$nama_file;

                    $vdir_upload = 'berkas/kk/';
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






            if (isset($parameter['id_anggota'])){
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
            }

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
                'response_message' => $hasil['response_package']['response_message'],
                'response_result' => $hasil['response_package']['response_result'],
                'response_data' => $hasil['response_package']['response_data']
            );
        }

        return $parameterBuilder;

    }













    private function tambah_ktp($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $nik = parent::anti_injection($parameter['anak_nik']);


        $Master = new Master(self::$pdo);
        $hasil = $Master->get_nik($nik);

        if($hasil['response_result'] > 0) {
            $json_object = $hasil['response_data'][0];

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
            $KartuKTP = self::$query->select('kartuktp', array(
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
                    $vdir_upload = "../../berkas/ktp/";
                    $vfile_upload = $vdir_upload . $nama_file_unik;

                    //Simpan gambar dalam ukuran sebenarnya
                    move_uploaded_file($lokasi_file, $vfile_upload);
                }

                $KartuKTPBerkas = self::$query->insert('kartuktp_berkas', array(
                    'uid_kartuktp' => $uid_data,
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

            return $parameterBuilder;
        }
    }















    private function tambah_identitasanak($parameter) {
        return array();
    }

    private function tambah_suratpindah($parameter) {
        return array();
    }













    private function tambah_aktalahir($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);



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

        return $parameterBuilder;
    }




























    private function tambah_aktaaku($parameter) {
        $Authorization = new Authorization();
        $UserData = $Authorization->readBearerToken($parameter['access_token']);

        $parameterBuilder = array();

        $nik = parent::anti_injection($parameter['anak_nik']);


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