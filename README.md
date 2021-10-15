# SIMAMPOEH
Simampoeh API Only


# API Documentation
1. Login

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/CapeeL/Pegawai \
   --header 'Content-Type: application/x-www-form-urlencoded' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --data request=login \
   --data nik=12700***** \
   --data password=123
   ```

   **Response**
   ```
   {
      "response_result": 1,
      "response_message": "Login berhasil",
      "response_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjFcL3NpbXJzdjJcL2NsaWVudCIsImlhdCI6MTYzMjgxNTQwOCwibmJmIjoxNjMyODE1NDE4LCJleHAiOjE2MzI4MTU0MzgsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjgxMTM2NTJkLTRjYjctZTg1MC1kNDg3LTI4MWExNzYyMDQyYSIsImZvdG8iOiIiLCJwYXNzd29yZCI6IiQyeSQxMCRGYTlvWU95Yi53dElaMXBMbXN6NGNlRnJtMHowT3d2ZlI2elM3Tzg3bGVVakhuem1VQm94QyIsImVtYWlsIjoidGhleWNhbnRyZXZlYWx1c0BnbWFpbC5jb20iLCJuYW1hIjoiSGVuZHJ5IFRhbmFrYSIsIm5vX2hhbmRwaG9uZSI6IjA4NTI2MTUxMDIwMiIsImxvZ19pZCI6IjMwIn19.jWjDIaXEzYDvZBQbAb10AgzMk_e5JiRmIx67PRQtvxs",
      "response_data": {
         "uid": "8113652d-4cb7-e850-d487-281a1762042a",
         "username": "tanaka",
         "nama": "Hendry Tanaka",
         "email": "theycantrevealus@gmail.com",
         "no_handphone": "085261510202",
         "foto": "",
         "is_login": "N",
         "last_login": "2021-09-16 15:27:58.224389",
         "id_level": null,
         "created_at": "2021-09-16 15:27:58.224389",
         "updated_at": "2021-09-16 15:27:58.224389",
         "deleted_at": null,
         "id": 1
      }
   }
   ```

2. Kartu Keluarga

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer token' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_kartukeluarga \
   --form nik=1271062205920002 \
   --form 'alamat_pemohon=Jln. Kapodang II' \
   --form id_kecamatan2=1|Medan Deli \
   --form 'alasan=Pengen Aja' \
   --form no_kk_lama=1271062210200001 \
   --form shdk=12 \
   --form jenis=47 \
   --form nama=JOSIN \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form 'alamat=Jln Kapodang' \
   --form kodepos=123 \
   --form id_kelurahan2=1|Kota Bangun \
   --form id_pelayanan=4
   ```

   **Response**
   ```
   {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjFcL3NpbXJzdjJcL2NsaWVudCIsImlhdCI6MTYzNDI4MDk2NywibmJmIjoxNjM0MTIzMTY2LCJleHAiOjE2MzQyODEwMjcsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.EKlEcTJ5Xwae37TY7W9wzfK6JmSS0aoUaKoAfI10C0c",
      "response_package": {
         "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Kartu Keluarga. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
         "response_result": null,
         "response_data": [
            "TkRjPQ=="
         ]
      },
      "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

3. Pengesahan Anak

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_sahanak \
   --form anak_nik=1271062205920002 \
   --form anak_jenkel=LAKI-LAKI \
   --form anak_kelahiran_ke=66 \
   --form anak_nomor_akta=AL.538.666666 \
   --form anak_tanggal_akta=2013-08-28 \
   --form 'anak_dinas_akta=Kota Medan' \
   --form jenis=31 \
   --form kirim=N \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form id_kecamatan=1|Medan Deli \
   --form id_kelurahan=1|Kota Bangun \
   --form 'alamat=Jln Kapodang II' \
   --form kode_pos=123 \
   --form id_pelayanan=4
   ```

   **Response**
   ```
   {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4MTE0NiwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODEyMDYsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.3par4RFgPRJIMQWsvHWky95XmIai338AWJ19wunVxcg",
      "response_package": {
         "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Pengesahan Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
         "response_result": null,
         "response_data": [
            "TXpFPQ=="
         ]
      },
      "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```
   
4. KTP

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_ktp \
   --form nik=1271062205920002 \
   --form alasan=rusak \
   --form jenis=84 \
   --form kirim=N \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form id_kecamatan=1|Medan Deli \
   --form id_kelurahan=1|Kota Bangun \
   --form 'alamat=Jln. Kapodang II' \
   --form kode_pos=123 \
   --form jenkel=LAKI-LAKI \
   --form id_pelayanan=4
   ```

   **Response**
   ```
   {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4MTIyNiwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODEyODYsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.-q9pwOOmlbfyACbFm8NUx2BM1rP6DD6OJqnJS1fQLjw",
      "response_package": {
         "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Kartu Tanda Penduduk. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
         "response_result": null,
         "response_data": [
            "T0RRPQ=="
         ]
      },
      "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

5. Angkat Anak

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_angkatanak \
   --form nik=1271062205920002 \
   --form anak_jenkel=LAKI-LAKI \
   --form jenis=34 \
   --form kirim=N \
   --form anak_kelahiran_ke=1 \
   --form anak_nomor_akta=22AD8989 \
   --form anak_tanggal_akta=2020-01-01 \
   --form 'anak_dinas_akta=Kota Medan' \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form id_kecamatan=1|Medan Deli \
   --form id_kelurahan=1|Kota Bangun \
   --form 'alamat=Jln. Kapodang II' \
   --form kode_pos=123 \
   --form id_pelayanan=11
   ```

   **Response**
   ```
   {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4MTkyOSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODE5ODksImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.b_gJWBnJOeLBpIpAAWnp3c8kCpKmivqLjnLPDi7V1VM",
      "response_package": {
         "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Pengangkatan Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
         "response_result": null,
         "response_data": [
            "TXpRPQ=="
         ]
      },
      "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```


6. Akta Lahir

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_aktalahir \
   --form nik=1271062205920002 \
   --form anak_nama=Susan \
   --form jenis=34 \
   --form kirim=N \
   --form anak_tempat_lahir=Medan \
   --form anak_berat_bayi=12 \
   --form anak_panjang_bayi=12 \
   --form anak_berat_bayi_koma=2 \
   --form anak_id_jenkel=1 \
   --form anak_id_tempatlahir=1 \
   --form anak_tanggal_lahir=2020-01-01 \
   --form anak_jam_lahir=10:00 \
   --form anak_kelahiran_ke=1 \
   --form anak_id_tolonglahir=1 \
   --form anak_ayah=Sutotok \
   --form anak_ibu=Septiani \
   --form anak_saksi1=Hapis \
   --form anak_saksi2=NurAhini \
   --form anak_no_handphone=085261510202 \
   --form jenis_kelahiran=1 \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form id_kecamatan=1|Medan Deli \
   --form id_kelurahan=1|Kota Bangun \
   --form 'alamat=Jln. Kapodang II' \
   --form kode_pos=666 \
   --form id_pelayanan=4
   ```

   **Response**
   ```
   {
     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4Mjk0OSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODMwMDksImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.MrRwCbOXQomtwWm9Fggiudw_KDj3Erte-C4uWmL-jPM",
     "response_package": {
       "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Kelahiran Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
       "response_result": null,
       "response_data": [
         "TXpRPQ=="
       ]
     },
     "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

7. Aku Anak

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_aktaaku \
   --form nik=1271062205920002 \
   --form anak_jenkel=LAKI-LAKI \
   --form jenis=34 \
   --form kirim=N \
   --form anak_kelahiran_ke=1 \
   --form anak_nomor_akta=RF23124 \
   --form anak_tanggal_akta=2020-01-01 \
   --form anak_dinas_akta=DW \
   --form anak_id_jenkel=1 \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form id_kecamatan=1|Medan Deli \
   --form id_kelurahan=1|Kota Bangun \
   --form 'alamat=Jln. Kapodang II' \
   --form kode_pos=666 \
   --form id_pelayanan=4
   ```

   **Response**
   ```
   {
     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4MzMxNywibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODMzNzcsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.DYUHnDPDH0yNw5jsrQQAai3dMuknSCld0qfqoRy-DDs",
     "response_package": {
       "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Pengakuan Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
       "response_result": null,
       "response_data": [
         "TXpRPQ=="
       ]
     },
     "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

8. Akta Kawin

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_aktakawin \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form id_kecamatan=1|Medan Deli \
   --form id_kelurahan=1|Kota Bangun \
   --form 'alamat=Jln. Kapodang II' \
   --form kode_pos=666 \
   --form id_pelayanan=4 \
   --form organisasi_penghayat=Gereja \
   --form badan_peradilan=Kanonik \
   --form nomor_pengadilan=IJ239872 \
   --form 'nama_pemuka=Pt. Peter' \
   --form izin_perwakilan=BOLEH123 \
   --form tanggal_kawin=2020-01-01 \
   --form id_agama=1 \
   --form tanggal_pengadilan=2020-02-01 \
   --form jumlah_anak=2 \
   --form jenis=47 \
   --form kirim=N \
   --form nik_suami=1271062205920002 \
   --form nik_istri=1271062205920001 \
   --form nama_suami=Suryo \
   --form nama_istri=Surti \
   --form nik_suami_ayah=1271062205920005 \
   --form nama_suami_ayah=Sarno \
   --form nik_suami__ibu=1271062205920006 \
   --form nama_suami_ibu=Sarni \
   --form nik_istri_ayah=1271062205920007 \
   --form nama_istri_ayah=Satimin \
   --form nik_istri_ibu=1271062205920008 \
   --form nama_istri_ibu=Susi \
   --form nik_saksi_1=1271062205920009 \
   --form nama_saksi_1=Soni \
   --form nik_saksi_2=1271062205920010 \
   --form nama_saksi_2=Sungkup \
   --form tanggal_pengadilan=2020-01-01
   ```

   **Response**
   ```
   {
     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4NDQ3OCwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODQ1MzgsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.WvzSB6aOeLxq65K6TY2_pQ-aUwoFSaXEbExZT0bv6MU",
     "response_package": {
       "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Perkawinan. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
       "response_result": null,
       "response_data": [
         "TkRjPQ=="
       ]
     },
     "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

9. Akta Mati

   **Request**
   ```
   curl --request POST \
   --url http://127.0.0.1/simampoeh/Pelaporan2/ \
   --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
   --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
   --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
   --form request=tambah_aktamati \
   --form nik=1271062205920002 \
   --form jenis=34 \
   --form kirim=N \
   --form id_provinsi=1|Sumatera Utara \
   --form id_kabupaten=1|Kota Medan \
   --form id_kecamatan=1|Medan Deli \
   --form id_kelurahan=1|Kota Bangun \
   --form 'alamat=Jln. Kapodang II' \
   --form kode_pos=666 \
   --form id_pelayanan=4 \
   --form hubungan=PAMAN \
   --form nama=Sarno \
   --form id_jenkel=1 \
   --form tempat_lahir=Medan \
   --form tanggal_lahir=1990-01-01 \
   --form id_agama=1 \
   --form id_kewarganegaraan=1 \
   --form 'alamat=Jln. Kapodang II' \
   --form rt=01 \
   --form rw=02 \
   --form telepon=085261510202 \
   --form tanggal_meninggal=2020-01-01 \
   --form jam_meninggal=10:00 \
   --form tempat_meninggal=WC \
   --form 'penyebab_meninggal=Berak Semen' \
   --form no_handphone=085261510202 \
   --form id_provinsi2=1|Sumatera Utara \
   --form id_kabupaten2=1|Kota Medan \
   --form id_kecamatan2=1|Medan Deli \
   --form id_kelurahan2=1|Kota Bangun
   ```

   **Response**
   ```
   {
     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4NDk2MiwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODUwMjIsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.WqKdhZGOG31QJBkWZCRaUDjnPfTZzyF_-eBahpRJWLs",
     "response_package": {
       "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Akta Kematian. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
       "response_result": null,
       "response_data": [
         "TXpRPQ=="
       ]
     },
     "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

10. KIA

   **Request**
   ```
      curl --request POST \
      --url http://127.0.0.1/simampoeh/Pelaporan2/ \
      --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
      --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
      --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
      --form request=tambah_identitasanak \
      --form nik=1271062205920002 \
      --form jenis=34 \
      --form kirim=N \
      --form id_provinsi=1|Sumatera Utara \
      --form id_kabupaten=1|Kota Medan \
      --form id_kecamatan=1|Medan Deli \
      --form id_kelurahan=1|Kota Bangun \
      --form 'alamat=Jln. Kapodang II' \
      --form kode_pos=666 \
      --form id_pelayanan=4 \
      --form anak_noakta=12376 \
      --form anak_ayah=Sarno \
      --form anak_ibu=Susan
   ```

   **Response**
   ```
   {
     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4NTM0OCwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODU0MDgsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.CBSAIeTURNR-ibsFphshEJiQ2_fZQkEcesB19o5wrmQ",
     "response_package": {
       "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Kartu Identitas Anak. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
       "response_result": null,
       "response_data": [
         "TXpRPQ=="
       ]
     },
     "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

11. Surat Pindah

   **Request**
   ```
   curl --request POST \
     --url http://127.0.0.1/simampoeh/Pelaporan2/ \
     --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDIxNjM1NSwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyMTYzODUsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.nzUJAIDD-1YV4uGt43X3BDn5TRBt7FN7HZfzhg9nlBg' \
     --header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
     --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
     --form request=tambah_suratpindah \
     --form nik=1271062205920002 \
     --form jenis=34 \
     --form kirim=N \
     --form 'id_provinsi=1|Sumatera Utara' \
     --form 'id_kabupaten=1|Kota Medan' \
     --form 'id_kecamatan=1|Medan Deli' \
     --form 'id_kelurahan=1|Kota Bangun' \
     --form 'id_provinsi2=1|Sumatera Utara' \
     --form 'id_kabupaten2=1|Kota Medan' \
     --form 'id_kecamatan2=1|Medan Deli' \
     --form 'id_kelurahan2=1|Kota Bangun' \
     --form id_pelayanan=4 \
     --form alasan=1 \
     --form 'alasan_lainnya=G ada' \
     --form nama=Suhardi \
     --form 'alamat_tujuan=Jln. Kapoding III' \
     --form no_kk=12312314 \
     --form rt_tujuan=01 \
     --form rw_tujuan=02 \
     --form kode_pos_tujuan=3333 \
     --form telepon=1231241 \
     --form jenis_pindahan=1 \
     --form kk_bagi_tidak_pindah=123 \
     --form kk_bagi_pindah=4124 \
     --form 'id_anggota=1234-Santi,3324-Siti'
   ```

   **Response**
   ```
   {
     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80NS4xMjcuMTM0LjU0XC9zaW1hbXBvZWhcL2NsaWVudCIsImlhdCI6MTYzNDI4NTk0OCwibmJmIjoxNjM0MjE2MzY1LCJleHAiOjE2MzQyODYwMDgsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6IjZiZTIwYjQ4LWQzODItNjIyMi0xNmRmLWU0OTNlNDE4ZTkxYyIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJqeWVyLmRydW1tZXJAZ21haWwuY29tIiwibmFtYSI6IkpPTkFUQU4gU0lBTlRVUkkiLCJub19oYW5kcGhvbmUiOiIwODIzNzA3OTg3MjciLCJrZWNhbWF0YW4iOiI2Iiwia2VsdXJhaGFuIjoiMTAwMSIsImxvZ19pZCI6MX19.8brK9zlpm9LEaaDUuetNdLjB3mykP6RERMZ97MvP6kQ",
     "response_package": {
       "response_message": "Selamat Anda sudah melakukan pengajuan data untuk layanan Surat Pindah. Pengajuan Anda akan segera diproses. Silakan cek email atau whatsapp Anda untuk informasi selanjutnya. Terima kasih",
       "response_result": null,
       "response_data": [
         "TXpRPQ=="
       ]
     },
     "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
   }
   ```

12. Register

   **Request**
   ```
   curl --request POST \
     --url http://127.0.0.1/simampoeh/Member \
     --header 'Content-Type: application/x-www-form-urlencoded' \
     --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
     --data request=register \
     --data nik=1271062205920002 \
     --data nokk=1271062210200001 \
     --data email=theycantrevealus@gmail.com \
     --data no_handphone=085261510202
   ```
   
   **Response**
   ```
   {
     "response_result": 1,
     "response_message": "Terima kasih sudah mendaftar di Aplikasi Sibisa, Dinas Kependudukan dan Catatan Sipil Kota Medan.Silahkan cek email Anda untuk mengaktivasi akun Anda."
   }
   ```

13. Update Profile

**Request**
```
curl --request POST \
  --url http://127.0.0.1/simampoeh/Member \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjFcL3NpbXJzdjJcL2NsaWVudCIsImlhdCI6MTYzNDI5MDkwNCwibmJmIjoxNjM0MjkwOTE0LCJleHAiOjE2MzQyOTA5MzQsImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6ImEzNmM1OTE5LWI3NzktNGQ4ZC04Zjk5LThhOTZhODlhMWE0NSIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJ0aGV5Y2FudHJldmVhbHVzQGdtYWlsLmNvbSIsIm5hbWEiOiJKT05BVEFOIFNJQU5UVVJJIiwibm9faGFuZHBob25lIjoiMDg1MjYxNTEwMjAyIiwia2VjYW1hdGFuIjoiNiIsImtlbHVyYWhhbiI6IjEwMDEiLCJsb2dfaWQiOjF9fQ.4ReCEpSgqh3eTwKGZCffwEWgo5do5pMkqFaeFjcRWws' \
  --header 'Content-Type: application/x-www-form-urlencoded' \
  --cookie PHPSESSID=63gclihstfp1bgum9pctmg3udm \
  --data request=update_profile \
  --data email=theycantrevealus@gmail.com \
  --data no_handphone=0852615102022
```

**Response**
```
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjFcL3NpbXJzdjJcL2NsaWVudCIsImlhdCI6MTYzNDI5MDk5OSwibmJmIjoxNjM0MjkwOTE0LCJleHAiOjE2MzQyOTEwNTksImF1ZCI6InVzZXJzX2xpYnJhcnkiLCJkYXRhIjp7InVpZCI6ImEzNmM1OTE5LWI3NzktNGQ4ZC04Zjk5LThhOTZhODlhMWE0NSIsImZvdG8iOm51bGwsIm5payI6IjEyNzEwNjIyMDU5MjAwMDIiLCJwYXNzd29yZCI6IlRWUkplZz09IiwiZW1haWwiOiJ0aGV5Y2FudHJldmVhbHVzQGdtYWlsLmNvbSIsIm5hbWEiOiJKT05BVEFOIFNJQU5UVVJJIiwibm9faGFuZHBob25lIjoiMDg1MjYxNTEwMjAyIiwia2VjYW1hdGFuIjoiNiIsImtlbHVyYWhhbiI6IjEwMDEiLCJsb2dfaWQiOjF9fQ.SFHafqqw4dX3vbCCWNmLitjZOqG_YwRj1hXAado_-2Y",
  "response_package": {
    "response_package": {
      "response_message": "Profile berhasil diubah",
      "response_result": 1
    }
  },
  "license": "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXVQmq3+UqbhC3rLCXSEu\/\/miV\nFXhkr+zoK17NTfA9VbdVT95Ag+CLi8hEAnkffpPEacLAIoVjOgtzT4wlWTpkUHCR\nLlVqw6mjJsqF4EWH4b4N\/eJ+7S0O+vAJi7cxscOaU6zs9Dm+lPNvN4AmRi05xOHW\nDhZ8i8+VWEP\/azAO1wIDAQAB\n-----END PUBLIC KEY-----\n"
}
```