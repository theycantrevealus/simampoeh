<?php

namespace PondokCoder;

use PondokCoder\Authorization as Authorization;
use PondokCoder\Utility as Utility;
use PondokCoder\Modul as Modul;
use PondokCoder\Poli as Poli;
use PondokCoder\Unit as Unit;
use \Firebase\JWT\JWT;

class Master extends Utility
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
                return $parameter;
        }
    }


    public function __POST__($parameter = array())
    {
        switch ($parameter['request']) {
            case 'nik':
                return self::get_nik($parameter);
                break;
            case 'kk':
                return self::get_kk($parameter);
                break;
            case 'provinsi':
                return self::get_provinsi($parameter);
                break;
            case 'kabupaten':
                return self::get_kabupaten($parameter);
                break;
            case 'kecamatan':
                return self::get_kecamatan($parameter);
                break;
            case 'kelurahan':
                return self::get_kelurahan($parameter);
                break;
            case 'tempat_dilahirkan':
                return self::term_data(6);
                break;
            case 'penolong_kelahiran':
                return self::term_data(8);
                break;
            default:
                return array();

        }
    }


    public function get_nik($parameter) {
        $nik = parent::anti_injection($parameter['nik']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,__TARGET_SYNC__ . '/get-nik');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"nik=$nik");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_object = json_decode(curl_exec($ch));

        curl_close ($ch);

        return array(
            'token' => null,
            'response_package' => array(
                'response_result' => (isset($json_object->ERROR)) ? 0 : 1,
                'response_message' => (isset($json_object->ERROR)) ? $json_object->ERROR : '',
                'response_data' => (isset($json_object->ERROR)) ? array() : array($json_object)
            )
        );
    }

    public function term_data($term) {
        $data = self::$query->select('term', array(
            'id', 'nama'
        ))
            ->where(array(
                'term.status_hapus' => '= ?',
                'AND',
                'term.id_termusage' => '= ?'
            ), array(
                'N', $term
            ))
            ->execute();
        return array(
            'token' => null,
            'response_package' => array(
                'response_result' => (count($data['response_data']) > 0) ? 1 : 0,
                'response_message' => (count($data['response_data']) > 0) ? 'Nah datanya' : 'G ada data',
                'response_data' => (count($data['response_data']) > 0) ? $data['response_data'] : array()
            )
        );
    }

    public function get_kk($parameter) {
        $no_kk = parent::anti_injection($parameter['kk']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,__TARGET_SYNC__ . '/get-kk');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"no_kk=$no_kk");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_object = json_decode(curl_exec($ch));

        curl_close ($ch);

        return array(
            'token' => null,
            'response_package' => array(
                'response_result' => (isset($json_object->ERROR)) ? 0 : 1,
                'response_message' => (isset($json_object->ERROR)) ? $json_object->ERROR : '',
                'response_data' => (isset($json_object->ERROR)) ? array() : array($json_object)
            )
        );
    }

    public function get_provinsi($parameter) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,__TARGET_SYNC__ . '/lok-provinsi');
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_object = json_decode(curl_exec($ch));

        curl_close ($ch);

        $parsed = array();

        if(!isset($json_object->ERROR)) {
            foreach ($json_object as $key => $value) {
                array_push($parsed, array(
                    'id' => $value->NO_PROP,
                    'name' => $value->NAMA_PROP
                ));
            }
        }

        return array(
            'token' => null,
            'response_package' => array(
                'response_result' => (isset($json_object->ERROR)) ? 0 : 1,
                'response_message' => (isset($json_object->ERROR)) ? $json_object->ERROR : '',
                'response_data' => (isset($json_object->ERROR)) ? array() : $parsed
            )
        );
    }

    public function get_kabupaten($parameter) {
        $provinsi = parent::anti_injection($parameter['id_provinsi']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,__TARGET_SYNC__ . '/lok-kabupaten');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"id_provinsi=12");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_object = json_decode(curl_exec($ch));

        curl_close ($ch);

        $parsed = array();
        if(!isset($json_object->ERROR)) {
            foreach ($json_object as $key => $value) {
                array_push($parsed, array(
                    'id' => $value->NO_KAB,
                    'name' => $value->NAMA_KAB
                ));
            }
        }

        return array(
            'token' => null,
            'response_package' => array(
                'response_result' => (isset($json_object->ERROR)) ? 0 : 1,
                'response_message' => (isset($json_object->ERROR)) ? $json_object->ERROR : '',
                'response_data' => (isset($json_object->ERROR)) ? array() : $parsed
            )
        );
    }

    public function get_kecamatan($parameter) {
        $provinsi = parent::anti_injection($parameter['id_provinsi']);
        $kabupaten = parent::anti_injection($parameter['id_kabupaten']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,__TARGET_SYNC__ . '/lok-kecamatan');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'id_provinsi' => 12,
            'id_kabupaten' => 71
        )));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_object = json_decode(curl_exec($ch));

        curl_close ($ch);

        $parsed = array();
        if(!isset($json_object->ERROR)) {
            foreach ($json_object as $key => $value) {
                array_push($parsed, array(
                    'id' => $value->NO_KEC,
                    'name' => $value->NAMA_KEC
                ));
            }
        }

        return array(
            'token' => null,
            'response_package' => array(
                'response_result' => (isset($json_object->ERROR)) ? 0 : 1,
                'response_message' => (isset($json_object->ERROR)) ? $json_object->ERROR : '',
                'response_data' => (isset($json_object->ERROR)) ? array() : $parsed
            )
        );
    }

    public function get_kelurahan($parameter) {
        $provinsi = parent::anti_injection($parameter['id_provinsi']);
        $kabupaten = parent::anti_injection($parameter['id_kabupaten']);
        $kecamatan = parent::anti_injection($parameter['parent']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,__TARGET_SYNC__ . '/lok-kelurahan');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'id_provinsi' => 12,
            'id_kabupaten' => 71,
            'id_kecamatan' => $kecamatan
        )));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $json_object = json_decode(curl_exec($ch));

        $parsed = array();
        if(!isset($json_object->ERROR)) {
            foreach ($json_object as $key => $value) {
                array_push($parsed, array(
                    'id' => $value->NO_KEL,
                    'name' => $value->NAMA_KEL
                ));
            }
        }

        curl_close ($ch);

        return array(
            'token' => null,
            'response_package' => array(
                'response_result' => (isset($json_object->ERROR)) ? 0 : 1,
                'response_message' => (isset($json_object->ERROR)) ? $json_object->ERROR : '',
                'response_data' => (isset($json_object->ERROR)) ? array() : ((isset($json_object)) ? $parsed : array())
            )
        );
    }
}
?>