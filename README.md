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
   --form id_kecamatan2=1 \
   --form 'alasan=Pengen Aja' \
   --form no_kk_lama=1271062210200001 \
   --form shdk=12 \
   --form jenis=47 \
   --form nama=JOSIN \
   --form id_provinsi=23 \
   --form id_kabupaten=34 \
   --form 'alamat=Jln Kapodang' \
   --form kodepos=123 \
   --form id_kelurahan2=1 \
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
   --form id_provinsi=2 \
   --form id_kabupaten=3 \
   --form id_kecamatan=1 \
   --form id_kelurahan=4 \
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
   --form id_provinsi=1 \
   --form id_kabupaten=1 \
   --form id_kecamatan=1 \
   --form id_kelurahan=1 \
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