<?php
namespace PondokCoder;

use DateTime;

abstract class Utility {
	protected abstract static function getConn();
	const appRoot = './';

    public function encrypt($parameter) {
        return base64_encode(base64_encode($parameter));
    }

    public function isi_email($content) {
        $pesan="
        <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
            <head>
                <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
                <title>Pendaftaran Sibisa</title>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
                <style>
                    body{
                        font-family:Calibri;
                    }
                </style>
            </head>
            
            <body style='margin: 0; padding: 0;'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                        <td align='center' bgcolor='#F2F2F2' style='padding: 20px 0 20px 0;'>
                            <img src='https://sibisa.pemkomedan.go.id/images/logo.png' style='max-width:100%; max-height:80px; text-align:center;'>
                        </td>
                    </tr>
                    <tr>
                        <td align='center' style='padding: 20px 0 30px 0;'>
                            $content
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor='#F2F2F2' style='padding: 10px 30px 10px 30px;'>
                            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                    <td>
                                        Copyright &copy; 2020 Sibisa Disdukcapil Pemerintah Kota Medan
                                    </td>
                                    <td align='right'>
                                        <table border='0' cellpadding='0' cellspacing='0'>
                                            <tr>
                                                <td>
                                                    <a href='https://www.lapor.go.id/instansi/dinas-kependudukan-dan-pencatatan-sipil-kota-medan' target='_blank'>
                                                    <img src='https://sibisa.pemkomedan.go.id/images/lapor.png' alt='Lapor' width='38' height='38' style='display: block;' border='0' />
                                                    </a>
                                                </td>
                                                <td style='font-size: 0; line-height: 0;' width='5'>&nbsp;</td>
                                                <td>
                                                    <a href='https://www.facebook.com/disduk.capil.14' target='_blank'>
                                                    <img src='https://sibisa.pemkomedan.go.id/images/icon-fb.png' alt='Facebook' width='38' height='38' style='display: block;' border='0' />
                                                    </a>
                                                </td>
                                                <td style='font-size: 0; line-height: 0;' width='5'>&nbsp;</td>
                                                <td>
                                                    <a href='https://www.instagram.com/disdukcapilmedan/' target='_blank'>
                                                    <img src='https://sibisa.pemkomedan.go.id/images/icon-ig.png' alt='Instagram' width='38' height='38' style='display: block;' border='0' />
                                                    </a>
                                                </td>
                                                <td style='font-size: 0; line-height: 0;' width='5'>&nbsp;</td>
                                                <td>
                                                    <a href='https://api.whatsapp.com/send?phone=6281362387372' target='_blank'>
                                                    <img src='https://sibisa.pemkomedan.go.id/images/icon-wa.png' alt='Whatsapp' width='38' height='38' style='display: block;' border='0' />
                                                    </a>
                                                </td>
                                                <td style='font-size: 0; line-height: 0;' width='5'>&nbsp;</td>
                                                <td>
                                                    <a href='mailto:disdukcapil.medan@gmail.com' target='_blank'>
                                                    <img src='https://sibisa.pemkomedan.go.id/images/icon-email.png' alt='Email' width='38' height='38' style='display: block;' border='0' />
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>";

        return $pesan;
    }

    public function decrypt($parameter) {
        return base64_decode(base64_decode($parameter));
    }

    public function acak($panjang) {
        $karakter= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $panjang; $i++) {
            $pos = rand(0, strlen($karakter)-1);
            $string .= $karakter{$pos};
        }
        return $string;
    }

    public function kirim_wa($no_handphone, $message){
        $response = file_get_contents('http://103.159.77.25/wa_api/index.php/send_message_static_get?%20kd_id=003&auth_key=aUth_0Key_Waa_(ApI)&no_wa='.$no_handphone.'&text='.urlencode($message));
        return $response;
    }

    public function anti_injection($sql){
        $sql = pg_escape_string(addslashes(trim($sql)));
        return $sql;
    }

	public function gen_uuid() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	public static function getClassesInNamespace($namespace) {
		if(self::getNamespaceDirectory($namespace) == 203) {
			return 'lock file not found : ' . self::appRoot;
		} else {
			$files = scandir(self::getNamespaceDirectory($namespace));
			$classes = array_map(function($file) use ($namespace) {
				return $namespace . '\\' . str_replace('.php', '', $file);
			}, $files);
			
			return array_filter($classes, function($possibleClass){
				return class_exists($possibleClass);
			});
		}
	}

    public function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    public function humanTiming ($time) {

        $time = time() - $time; // to get the time since that moment
        $time = ($time < 1) ? 1 : $time;
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text.(($numberOfUnits>1) ? 's' : '');
        }
    }

    public function numberToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public function saveBase64ImagePng($data, $target, $name) {
        //Check
        if(is_writable('../images')) {
            if(!file_exists('../images/documentation') || !is_dir('../images/documentation')) {
                mkdir('../images/documentation');
            }

            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            return file_put_contents($target . '/' . $name, $data);
        } else {
            return 'Cant write directory';
        }
    }

    public function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = self::penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = self::penyebut($nilai/10)." puluh". self::penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . self::penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = self::penyebut($nilai/100) . " ratus" . self::penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . self::penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = self::penyebut($nilai/1000) . " ribu" . self::penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = self::penyebut($nilai/1000000) . " juta" . self::penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = self::penyebut($nilai/1000000000) . " milyar" . self::penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = self::penyebut($nilai/1000000000000) . " trilyun" . self::penyebut(fmod($nilai,1000000000000));
        }
        return $temp;
    }

    public function terbilang($nilai) {
        $x = stristr($nilai, '.');

        if($nilai < 0) {
            $hasil = "minus ". trim(self::penyebut($nilai));
        } else {
            $hasil = trim(self::penyebut($nilai));
        }
        $sen = explode('.', $x);
        $sen = str_pad($sen[count($sen) - 1], 2, "0", STR_PAD_RIGHT);

        if(floatval($sen) > 0) {
            return $hasil . ' '. trim(self::penyebut(floatval($sen))) . ' sen';
        } else {
            return $hasil;
        }
    }

	private static function getDefinedNamespaces() {
		$composerJsonPath = self::appRoot . 'composer.json';
		if(file_exists($composerJsonPath)) {
			$composerConfig = json_decode(file_get_contents($composerJsonPath));

			$psr4 = "psr-4";
			return (array) $composerConfig->autoload->$psr4;
		} else {
			return 203;
		}
	}

	private static function getNamespaceDirectory($namespace) {
		$composerNamespaces = self::getDefinedNamespaces();
		if($composerNamespaces == 203) {
			return $composerNamespaces;
		} else {
			$namespaceFragments = explode('\\', $namespace);
			$undefinedNamespaceFragments = [];

			while($namespaceFragments) {
				$possibleNamespace = implode('\\', $namespaceFragments) . '\\';

				if(array_key_exists($possibleNamespace, $composerNamespaces)){
					return realpath(self::appRoot . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments));
				}
				array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));            
			}
			return false;
		}
	}

	public function date_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }

    public function license_manager() {
        if(!isset($_SESSION['license']) || $_SESSION['license'] !== true) {
            //Get Client IP
            $ip = '';

            //Get Client Harddisk number
            $harddisk = '';

            //Package Information
            $package = '';

            //Check to License Server
            //


        } else {
            return $_SESSION['license'];
        }
    }

	public function format_date() {
		$micro_date = microtime();
		$date_array = explode(" ",$micro_date);
		$date = date("Y-m-d H:i:s",$date_array[1]);

		return $date;
	}

	public static function log($parameter = array()) {
		/*
			type,
			column,
			value,
			class
		*/
		if(count($parameter['column']) != count($parameter['value'])) {
			return 0;
		} else {
			$columnBuilder = array();
			foreach ($parameter['column'] as $key => $value) {
				array_push($columnBuilder, "?");
			}

			$query = static::getConn()->prepare('INSERT INTO log_' . $parameter['type'] . '(' . implode(",", $parameter['column']) . ') VALUES (' . implode(",", $columnBuilder) . ')');
			$query->execute($parameter['value']);
			if($query->rowCount() > 0) {
				return static::getConn()->lastInsertId();
			} else {
				return 0;
				$error_log = static::getConn()->prepare('INSERT INTO log_error (type, class, logged_at) VALUES (?, ?, NOW())');
				$error_log->execute(array(
					$parameter['type'],
					$parameter['class']
				));
			}
		}
	}

	public static function hitungUsia($date) {
		$biday = new DateTime($date);		
		$today = new DateTime();
	
		$diff = $today->diff($biday);
		
		$result=$diff->y." thn ".$diff->m." bln ".$diff->d." hari";
		return $result;
	}

	public static function dateToIndo($date) {
	    $HariIndo = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu');
		$BulanIndo = array("Januari", "Februari", "Maret",
						   "April", "Mei", "Juni",
						   "Juli", "Agustus", "September",
						   "Oktober", "November", "Desember");
	
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $HariIndo[intval(date('w', strtotime($date)))] . ', ' .$tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;
		return $result;
	}

	public static function dateToIndoSlash($date) {
		// fungsi atau method untuk mengubah tanggal ke format indonesia
		// variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
			 
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tgl . "/" .$bulan. "/". $tahun;
		return $result;
	}

	public function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
?>