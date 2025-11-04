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
        # Setup browser
        options = webdriver.ChromeOptions()
        options.add_argument("--start-maximized")
        cls.driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
        cls.wait = WebDriverWait(cls.driver, 10)
        cls.base_url = "http://localhost/test-aplikasi-php-main/index.php"

    # 1️⃣ Buka halaman utama
    def test_1_buka_index(self):
        driver = self.driver
        wait = self.wait

        driver.get(self.base_url)
        wait.until(EC.presence_of_element_located((By.XPATH, "//h2[contains(text(),'SISTEM PENGGAJIAN KARYAWAN')]")))
        print("✅ Halaman utama (index.php) terbuka")

    # 2️⃣ CRUD Karyawan
    def test_2_crud_karyawan(self):
        driver = self.driver
        wait = self.wait

        # Klik tombol Data Karyawan dari index
        driver.find_element(By.LINK_TEXT, "Data Karyawan").click()
        wait.until(EC.url_contains("data-karyawan/karyawan.php"))
        print("✅ Berhasil masuk ke halaman Data Karyawan")

        # Tambah karyawan
        driver.find_element(By.XPATH, "//button[contains(text(),'Add Data')]").click()
        wait.until(EC.presence_of_element_located((By.NAME, "nama")))
        driver.find_element(By.NAME, "nama").send_keys("Test Karyawan")
        driver.find_element(By.NAME, "jabatan").send_keys("Staff IT")
        driver.find_element(By.NAME, "alamat").send_keys("Jl. Testing No. 1")
        driver.find_element(By.NAME, "no_telp").send_keys("0800123456")
        driver.find_element(By.XPATH, "//button[text()='Simpan']").click()
        time.sleep(2)

        try:
            alert = driver.switch_to.alert
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data karyawan berhasil ditambah")

        # Edit karyawan
        # Ganti baris ini:
# edit_buttons = driver.find_elements(By.LINK_TEXT, "Edit")

# Dengan ini:
        edit_buttons = driver.find_elements(By.XPATH, "//i[contains(@class,'ri-edit-2-fill')]")
        self.assertGreater(len(edit_buttons), 0, "❌ Tidak ada tombol edit ditemukan")
        edit_buttons[0].click()
        time.sleep(1)
        wait.until(EC.presence_of_element_located((By.ID, "edit_nama")))
        nama_edit = driver.find_element(By.ID, "edit_nama")
        nama_edit.clear()
        nama_edit.send_keys("Test Karyawan Update")
        driver.find_element(By.XPATH, "//button[text()='Update']").click()
        time.sleep(2)

        try:
            alert = driver.switch_to.alert
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data karyawan berhasil diupdate")

        # Hapus karyawan
        delete_buttons = driver.find_elements(By.XPATH, "//button[contains(@onclick,'hapusData')]")
        self.assertGreater(len(delete_buttons), 0, "❌ Tidak ada tombol hapus ditemukan")
        delete_buttons[0].click()

        # Tunggu dan tangani alert konfirmasi hapus
        alert = WebDriverWait(driver, 5).until(EC.alert_is_present())
        print(f"📢 Alert: {alert.text}")
        alert.accept()  # klik OK
        print("✅ Konfirmasi hapus diterima")

        # Tunggu alert sukses muncul setelah data dihapus
        alert = WebDriverWait(driver, 5).until(EC.alert_is_present())
        print(f"📢 Alert: {alert.text}")
        alert.accept()
        print("✅ Data karyawan berhasil dihapus")
        

    # 3️⃣ CRUD Gaji
    def test_3_crud_gaji(self):
        driver = self.driver
        wait = self.wait

        # Balik ke index
        driver.get(self.base_url)
        wait.until(EC.presence_of_element_located((By.XPATH, "//h2[contains(text(),'SISTEM PENGGAJIAN KARYAWAN')]")))

        # Klik tombol Gaji Karyawan
        driver.find_element(By.LINK_TEXT, "Gaji Karyawan").click()
        wait.until(EC.url_contains("gaji-karyawan/gaji.php"))
        print("✅ Berhasil masuk ke halaman Gaji Karyawan")

        # Tambah data gaji
        driver.find_element(By.XPATH, "//button[contains(text(),'Add Gaji')]").click()
        wait.until(EC.presence_of_element_located((By.NAME, "nama_karyawan")))

        Select(driver.find_element(By.NAME, "nama_karyawan")).select_by_index(1)
        driver.find_element(By.NAME, "bulan").send_keys("November")
        driver.find_element(By.NAME, "gaji_pokok").send_keys("4000000")
        driver.find_element(By.NAME, "tunjangan").send_keys("500000")
        driver.find_element(By.NAME, "potongan").send_keys("200000")

        driver.find_element(By.XPATH, "//button[text()='Simpan']").click()
        time.sleep(2)
        try:
            alert = driver.switch_to.alert
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data gaji berhasil ditambah")

        # Edit gaji
        edit_buttons = driver.find_elements(By.XPATH, "//button[contains(@onclick,'openModalEdit')]")
        self.assertGreater(len(edit_buttons), 0, "❌ Tidak ada tombol edit ditemukan")
        edit_buttons[0].click()
        wait.until(EC.presence_of_element_located((By.ID, "edit_bulan")))
        bulan_edit = driver.find_element(By.ID, "edit_bulan")
        bulan_edit.clear()
        bulan_edit.send_keys("Desember")
        driver.find_element(By.XPATH, "//button[text()='Update']").click()
        time.sleep(2)

        try:
            alert = driver.switch_to.alert
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data gaji berhasil diupdate")

        # Hapus gaji
        delete_buttons = driver.find_elements(By.XPATH, "//button[contains(@onclick,'hapusData')]")
        self.assertGreater(len(delete_buttons), 0, "❌ Tidak ada tombol hapus ditemukan")
        delete_buttons[0].click()
        wait.until(EC.presence_of_element_located((By.ID, "btnConfirmHapus"))).click()
        time.sleep(2)

        try:
            alert = driver.switch_to.alert
            print("📢 Alert:", alert.text)
            alert.accept()
        except:
            pass
        print("✅ Data gaji berhasil dihapus")

    @classmethod
    def tearDownClass(cls):
        time.sleep(2)
        cls.driver.quit()


if __name__ == "__main__":
    unittest.main()
