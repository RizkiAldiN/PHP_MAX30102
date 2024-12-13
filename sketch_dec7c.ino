#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include "MAX30105.h"

#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64

Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);
MAX30105 particleSensor;

// Wi-Fi Credentials
const char* ssid = "POCO M6"; 
const char* password = "Aldi181818";

// Server Endpoint
String URL = "http://192.168.205.229/max30102_project/max30102_data.php"; // Ganti dengan URL server Anda

void setup() {
  Serial.begin(115200);

  // OLED Display Initialization
  if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) { 
    Serial.println("OLED gagal diinisialisasi!");
    while (true);
  }
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 0);
  display.println("Inisialisasi...");
  display.display();

  // MAX30102 Initialization
  if (!particleSensor.begin(Wire, I2C_SPEED_STANDARD)) {
    Serial.println("Sensor MAX30102 tidak ditemukan!");
    display.clearDisplay();
    display.println("Sensor tidak terdeteksi!");
    display.display();
    while (true);
  }

  particleSensor.setup(); 
  particleSensor.setPulseAmplitudeRed(0x0A);   
  particleSensor.setPulseAmplitudeGreen(0);   

  // Wi-Fi Connection
  connectWiFi();
}

void loop() {
  long irValue = particleSensor.getIR();

  if (irValue < 50000) { 
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("Silakan letakkan jari");
    display.display();
    delay(1000);
    return;
  }

  // Simulasi Data
  float heartRate = calculateHeartRate();
  float spo2 = calculateSpO2();

  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Deteksi Jantung");
  display.println("----------------");
  display.print("Detak Jantung: ");
  display.print(heartRate);
  display.println(" BPM");
  display.print("Kadar Oksigen: ");
  display.print(spo2);
  display.println(" %");
  display.display();

  // Kirim Data ke Server
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Format Data untuk POST
    String postData = "detak_jantung=" + String(heartRate) + "&kadar_oksigen=" + String(spo2);


    http.begin(URL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // Menampilkan data yang dikirim ke serial monitor untuk debugging
    Serial.println("Mengirim data ke server...");
    Serial.println("URL: " + URL);
    Serial.println("Data: " + postData);

    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.print("Response Server: ");
      Serial.println(http.getString());
    } else {
      Serial.print("Error Pengiriman Data: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("WiFi tidak tersambung!");
  }

  delay(5000); // Interval pengiriman data
}

void connectWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  
  WiFi.begin(ssid, password);
  Serial.println("Menghubungkan ke WiFi...");
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Menghubungkan ke WiFi...");
  display.display();
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.print("Terhubung ke: "); 
  Serial.println(ssid);
  Serial.print("IP address: "); 
  Serial.println(WiFi.localIP());
  display.clearDisplay();
  display.println("WiFi Tersambung!");
  display.println(WiFi.localIP());
  display.display();
}

float calculateHeartRate() {
  return random(60, 100); // Simulasi detak jantung
}

float calculateSpO2() {
  return random(95, 99); // Simulasi kadar oksigen
}
