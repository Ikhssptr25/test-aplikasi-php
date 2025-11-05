import time
import unittest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

class SistemPenggajianTest(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        options = webdriver.ChromeOptions()
        options.add_argument("--start-maximized")
        cls.driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
        cls.wait = WebDriverWait(cls.driver, 10)
        cls.base_url = "http://localhost/test-aplikasi-php-main/index.php"

    # 1️⃣ Buka halaman utama
    def test_1_buka_index(self):
        self.driver.get(self.base_url)
        self.wait.until(EC.presence_of_element_located((By.XPATH, "//h2[contains(text(),'SISTEM PENGGAJIAN KARYAWAN')]")))
        print("✅ Halaman utama (index.php) terbuka")

    # 2️⃣ CRUD Karyawan
    def test_2_crud_karyawan(self):
        driver = self.driver
        wait = self.wait

        driver.find_element(By.LINK_TEXT, "Data Karyawan").click()
        wait.until(EC.url_contains("data-karyawan/karyawan.php"))
        print("✅ Halaman Data Karyawan terbuka")

        # Tambah karyawan
        driver.find_element(By.XPATH, "//button[contains(text(),'Add Data')]").click()
        wait.until(EC.presence_of_element_located((By.NAME, "nama")))
        driver.find_element(By.NAME, "nama").send_keys("Test Karyawan")
        driver.find_element(By.NAME, "jabatan").send_keys("Staff IT")
        driver.find_element(By.NAME, "alamat").send_keys("Jl. Testing No. 1")
        driver.find_element(By.NAME, "no_telp").send_keys("0800123456")
        driver.find_element(By.XPATH, "//button[text()='Simpan']").click()

        try:
            alert = wait.until(EC.alert_is_present())
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data karyawan berhasil ditambah")

        # Edit karyawan
        edit_buttons = driver.find_elements(By.XPATH, "//i[contains(@class,'ri-edit-2-fill')]")
        edit_buttons[0].click()
        wait.until(EC.presence_of_element_located((By.ID, "edit_nama"))).clear()
        driver.find_element(By.ID, "edit_nama").send_keys("Test Karyawan Update")
        driver.find_element(By.XPATH, "//button[text()='Update']").click()
        try:
            alert = wait.until(EC.alert_is_present())
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data karyawan berhasil diupdate")

        # Hapus karyawan
        # Hapus karyawan
        delete_buttons = driver.find_elements(By.XPATH, "//button[contains(@onclick,'hapusData')]")
        self.assertGreater(len(delete_buttons), 0, "❌ Tidak ada tombol hapus ditemukan")

        # Gunakan JS click untuk menghindari intercept
        driver.execute_script("arguments[0].click();", delete_buttons[0])

        # Tunggu alert muncul dan terima
        alert = WebDriverWait(driver, 5).until(EC.alert_is_present())
        print(f"📢 Alert: {alert.text}")
        alert.accept()

        # Tunggu alert kedua jika ada
        try:
            alert = WebDriverWait(driver, 3).until(EC.alert_is_present())
            print(f"📢 Alert: {alert.text}")
            alert.accept()
        except:
            pass

        print("✅ Data karyawan berhasil dihapus")

    # 3️⃣ CRUD Gaji
    def test_3_crud_gaji(self):
        driver = self.driver
        wait = self.wait

        driver.get(self.base_url)
        driver.find_element(By.LINK_TEXT, "Gaji Karyawan").click()
        wait.until(EC.url_contains("gaji-karyawan/gaji.php"))
        print("✅ Halaman Gaji Karyawan terbuka")

        # Tambah gaji
        driver.find_element(By.XPATH, "//button[contains(text(),'Add Gaji')]").click()
        wait.until(EC.presence_of_element_located((By.ID, "id_karyawan")))

        Select(driver.find_element(By.ID, "id_karyawan")).select_by_index(1)
        driver.find_element(By.NAME, "bulan").send_keys("November")
        driver.find_element(By.NAME, "gaji_pokok").send_keys("4000000")
        driver.find_element(By.NAME, "tunjangan").send_keys("500000")
        driver.find_element(By.NAME, "potongan").send_keys("200000")
        driver.find_element(By.XPATH, "//button[text()='Simpan']").click()

        try:
            alert = wait.until(EC.alert_is_present())
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data gaji berhasil ditambah")

        # Edit gaji
        edit_buttons = driver.find_elements(By.XPATH, "//button[contains(@onclick,'openModalEdit')]")
        edit_buttons[0].click()
        wait.until(EC.presence_of_element_located((By.ID, "edit_bulan"))).clear()
        driver.find_element(By.ID, "edit_bulan").send_keys("Desember")
        driver.find_element(By.XPATH, "//button[text()='Update']").click()

        try:
            alert = wait.until(EC.alert_is_present())
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data gaji berhasil diupdate")

        # Hapus gaji
        delete_buttons = driver.find_elements(By.XPATH, "//button[contains(@onclick,'hapusData')]")
        delete_buttons[0].click()
        wait.until(EC.presence_of_element_located((By.ID, "btnConfirmHapus"))).click()
        try:
            alert = wait.until(EC.alert_is_present())
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data gaji berhasil dihapus")

# 5️⃣ Negative test: input gaji dengan angka negatif 
    def test_4_negative_input_gaji_tidak_valid(self): 
        driver = self.driver 
        wait = self.wait 
        driver.get(self.base_url) 
        driver.find_element(By.LINK_TEXT, "Gaji Karyawan").click() 
        wait.until(EC.url_contains("gaji-karyawan/gaji.php")) 

        driver.find_element(By.XPATH, "//button[contains(text(),'Add Gaji')]").click() 
        wait.until(EC.presence_of_element_located((By.ID, "id_karyawan"))) 
        
        Select(driver.find_element(By.ID, "id_karyawan")).select_by_index(1) 
        driver.find_element(By.NAME, "bulan").send_keys("November") 
        
        # Temukan field gaji pokok dan simpan
        gaji_pokok_field = driver.find_element(By.NAME, "gaji_pokok")
        gaji_pokok_field.send_keys("-5000000") # Isi dengan nilai negatif
        
        driver.find_element(By.NAME, "tunjangan").send_keys("500000") 
        driver.find_element(By.NAME, "potongan").send_keys("200000") 
        
        # Klik tombol Simpan
        driver.find_element(By.XPATH, "//button[text()='Simpan']").click() 
        time.sleep(1) # Beri waktu browser untuk menjalankan validasi
        
        is_valid = driver.execute_script("return arguments[0].checkValidity();", gaji_pokok_field)

        is_modal_open = False
        try:
            # Kita cek salah satu elemen di dalam modal, misal tombol 'Simpan'
            is_modal_open = driver.find_element(By.XPATH, "//button[text()='Simpan']").is_displayed()
        except:
            is_modal_open = False # Jika elemen tidak ada, modal tertutup

        # Gunakan assertion untuk validasi
        self.assertFalse(is_valid, "Form valid, seharusnya tidak valid karena angka negatif")
        self.assertTrue(is_modal_open, "Modal tertutup, seharusnya tetap terbuka karena submit gagal")
        
        print("✅ Negative Test: Form gagal submit karena input tidak valid (angka negatif)")

        driver.refresh()
        time.sleep(1)

    def test_5_negative_tambah_karyawan_kosong(self):
        driver = self.driver 
        wait = self.wait 
        driver.get(self.base_url) 
        driver.find_element(By.LINK_TEXT, "Data Karyawan").click() 
        wait.until(EC.url_contains("data-karyawan/karyawan.php")) 
        driver.find_element(By.XPATH, "//button[contains(text(),'Add Data')]").click() 
        wait.until(EC.presence_of_element_located((By.NAME, "nama")))
        
         # Kirim form kosong 
        driver.find_element(By.XPATH, "//button[text()='Simpan']").click() 
        time.sleep(1) 

        fields = ["nama", "jabatan", "alamat", "no_telp"]
        all_invalid = False
        for field_name in fields:
            field = driver.find_element(By.NAME, field_name)
            if not driver.execute_script("return arguments[0].checkValidity();", field):
                all_invalid = True
                print(f"⚠️ Negative Test: Field '{field_name}' tidak valid (kosong)")
        
        if all_invalid:
            print("✅ Form tidak bisa submit karena ada field kosong")
        else:
            print("❌ Form bisa submit, seharusnya gagal")

    @classmethod
    def tearDownClass(cls):
        time.sleep(1)
        cls.driver.quit()

if __name__ == "__main__":
    unittest.main()
