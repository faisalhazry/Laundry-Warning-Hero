# Laundry-Warning-Hero
Monitoring real time weather using MySQL

## 1. Problem Statement
Housewives often have  to dry clothes outside after the laundry. This can be difficult when the weather is unpredictable or when they don't have a reliable way to check the current conditions. 

To address this problem, we want to build an IoT system that can measure the humidity levels in the air and predict when it is likely to rain, so that housewives can plan their laundry schedules accordingly.

The system should be easy to use, reliable, and accessible from anywhere, and it should provide real-time updates and alerts when rain is predicted. Additionally, the system should be cost-effective and environmentally friendly, using minimal resources and energy to operate.

## 2. System Architecture, sensor & cloud platform

![Gambar flow chart](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/789ec6ca-a8db-4849-8b76-4681c5c9ec12)


## 3. Hardware Setup:
Connect the DHT11 sensor to the ESP32 microcontroller, ensuring proper wiring and compatibility.
ESP32 with DHT11 coding through Arduino IDE. Configure the ESP32 microcontroller to read data from the DHT11 sensor at regular intervals. The NodeMCU ESP32, together with the DHT11 sensor forms a smart laundry warning system. The NodeMCU ESP32 is a powerful microcontroller with built-in Wi-Fi. It collects real-time temperature and humidity data from the DHT11 sensor. 

![image](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/b75a3dd9-3ad9-493b-b2d9-1b3c18ff9a10)
```
#include <WiFi.h>
#include <HTTPClient.h>

#include <DHT.h> 
#define DHTPIN 19 //D19 
#define DHTTYPE DHT11 
DHT dht11(DHTPIN, DHTTYPE); 

String URL = "http://192.168.175.233/LWHero_project/test_data.php";

const char* ssid = "NodeMCU"; 
const char* password = "12345678"; 

int temperature = 0;
int humidity = 0;

void setup() {
  Serial.begin(115200);

  dht11.begin(); 
  
  connectWiFi();
}

void loop() {
  if(WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }

  Load_DHT11_Data();
  String postData = "temperature=" + String(temperature) + "&humidity=" + String(humidity);
  
  HTTPClient http;
  http.begin(URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
  int httpCode = http.POST(postData);
  String payload = http.getString();

  Serial.print("URL : "); Serial.println(URL); 
  Serial.print("Data: "); Serial.println(postData);
  Serial.print("httpCode: "); Serial.println(httpCode);
  Serial.print("payload : "); Serial.println(payload);
  Serial.println("--------------------------------------------------");
  delay(5000);
}


void Load_DHT11_Data() {
  //-----------------------------------------------------------
  temperature = dht11.readTemperature(); //Celsius
  humidity = dht11.readHumidity();
  //-----------------------------------------------------------
  // Check if any reads failed.
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Failed to read from DHT sensor!");
    temperature = 0;
    humidity = 0;
  }
  //-----------------------------------------------------------
  Serial.printf("Temperature: %d °C\n", temperature);
  Serial.printf("Humidity: %d %%\n", humidity);
}

void connectWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  //This line hides the viewing of ESP as wifi hotspot
  WiFi.mode(WIFI_STA);
  
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
    
  Serial.print("connected to : "); Serial.println(ssid);
  Serial.print("IP address: "); Serial.println(WiFi.localIP());
}
```

## 4. Software Setup:

### 4.1 Setup HTTP protocal 
The given PHP code establishes a connection to a MySQL database using the mysqli_connect() function and stores the connection object in the $conn variable. The code then checks if the variables "temperature" and "humidity" are set in the $_POST superglobal array using the isset() function. If they are set, the code retrieves their values and stores them in the $t and $h variables.

The code then constructs an SQL INSERT statement using the retrieved temperature and humidity values and inserts them into the "dht11" table of the database using the mysqli_query() function. If the query is successful, the code outputs the message "New record created successfully". Otherwise, it outputs an error message along with the SQL query and the error message returned by mysqli_error().

To use this PHP file to receive data from an ESP32 via HTTP protocol, the ESP32 needs to send an HTTP POST request to this PHP file URL with the temperature and humidity values in the request body as key-value pairs. The ESP32 can use the HTTPClient library to make the HTTP request and include the temperature and humidity values in the request body. Once the PHP file receives the data, it inserts it into the MySQL database. This way, the ESP32 can send the temperature and humidity data to a server running the PHP file, which can store the data in a database for further analysis and visualization.

```
<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "lwhero";

$conn = mysqli_connect($hostname, $username, $password, $database);
if(!$conn) {
	die ("connection failed: " . mysqli_connect_error());
}

echo "Database connection is OK <br>";


if(isset($_POST["temperature"]) && isset($_POST["humidity"])) {
	$t = $_POST["temperature"];
	$h = $_POST["humidity"];


$sql = "INSERT INTO `dht11`( `temperature`, `humidity`) VALUES (".$t.",".$h.")";

if (mysqli_query($conn, $sql)) {
	echo "New record created successfully";
} else {
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

}
?>
```
### 4.1 XAMPP
![image](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/17436045-fd60-4728-9d7c-96cbee89f240)

### 4.2 MySql 
![image](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/ec5ee668-86d3-4bc8-b8f2-af3a582ff1bb)

The data received from the hardware is being kept in the mysql database. Then, it will be shown to the user every time the data is updated 

### 4.3 Django 
To install Django on the computer hosting the Ngrok server, you first need to ensure that Python is installed on the machine. Django is a Python web framework, so it requires Python to run. Once Python is installed, you can use the pip package manager to install Django. Open a command prompt or terminal window and enter the command "pip install Django" to begin the installation process. After Django is installed, you can configure it to connect to the MySQL database within Ngrok. This involves specifying the database connection details in the Django settings file, such as the database name, host, port, username, and password.

Once Django is connected to the MySQL database, you can start creating dashboards and panels to visualize weather data. This involves creating HTML templates that display temperature and humidity trends in a user-friendly way. Django provides a powerful templating engine that allows you to create reusable HTML templates with dynamic content. You can use Python code within the templates to generate the weather data and pass it to the HTML for display. With Django's built-in support for forms and models, you can create interactive dashboards and panels that allow users to filter and manipulate the weather data. Overall, Django provides a robust and flexible platform for building web applications that visualize weather data and other types of data.

Django file code can be download above repo

![image](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/ef418585-6905-437c-bd34-c1085c594eac)

### 4.4 Ngrok
Ngrok, you can establish a secure tunnel, making it easier for your app to access the internet and communicate with external services. This convenient solution simplifies the process of making your locally hosted app accessible to the wider world.
![image](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/d6156cec-884f-4d1f-9025-de6565602622)

The sign Up the service can go thru this link 
https://dashboard.ngrok.com/login?state=U3_a7VDkgSGweS2GY0b8bAAL2aPm3qYu7BR7Dbcs8QteldjwBSKO6Y26FSGiwTyyuWxHU2ORNmZDKqRiDfYh_UKmqCnOD_TNC3SglR6JFrsIObb4HeaXFjggndWZMnDPMWTBtOgigHOGa8sICe9PMYDFxJk2TU7dUHn-nC60y9EBO9FqNP56ExYp

### 4.5 Grafana

![image](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/a8b50a14-c219-4b1f-aab5-65ef719deacd)

Grafana is a powerful and user-friendly data visualization tool that allows you to create insightful and visually appealing dashboards and graphs. It simplifies the process of analyzing and monitoring data by providing customizable and interactive visual representations. With Grafana, you can effortlessly visualize various metrics, such as temperature and humidity trends, enabling you to gain valuable insights and make informed decisions. Its flexible and intuitive interface makes it easy to configure and customize visualizations according to your specific needs. Grafana is a valuable tool for both individuals and organizations, empowering them to effectively analyze and present data in a visually appealing and easily understandable format.



## 5.0 Result and discussion 

LWHero or Laundry Warning Hero is the integration of DHT11 and ESP32, along with XAMPP, Grafana, and Ngrok, offers a comprehensive solution for monitoring and analyzing weather data. The DHT11 sensor is a cost-effective and easy-to-use sensor capable of measuring temperature and humidity accurately. When connected to the ESP32 microcontroller, the DHT11 provides real-time weather data that can be utilized for analysis and decision-making. To store and manage the collected data, XAMPP, a local server solution, is employed. XAMPP includes Apache as the web server and MySQL as the database management system. By configuring XAMPP on a computer connected to the ESP32, the system can receive and store the transmitted weather data.

Grafana is integrated into the system to provide insightful and visually appealing representations of the weather data. With Grafana's flexible dashboards and customizable graphs, users can easily monitor and receive notifications upon temperature and humidity trends to ensure that their laundry is safe from being poured under the rain.  To make the weather data accessible from anywhere, Ngrok is utilized for cloud publishing. Ngrok creates secure tunnels to expose the local XAMPP server to the internet. This enables remote access to the weather data and provides the ability to monitor weather conditions from any device with an internet connection.

![image](https://github.com/faisalhazry/Laundry-Warning-Hero/assets/121289405/8d29902e-46ed-48b9-a76e-566c226a7e38)

One of the major advantages of LWHero is its scalability and flexibility. The ESP32 microcontroller can be easily expanded to include additional sensors for measuring other weather parameters such as pressure, wind speed, or rainfall. The data can be seamlessly integrated into the existing system architecture, stored in the XAMPP server and visualized using Grafana. Moreover, the system allows for real-time monitoring, analysis and warning of weather conditions. By collecting and visualizing data over extended periods, users can identify patterns, make informed decisions, and take necessary actions based on weather trends. The integration of DHT11 and ESP32 with XAMPP, Grafana, and Ngrok provides a comprehensive laundry forecast warning system. This system offers real-time data collection, storage, visualization, and cloud accessibility, making it an effective solution for various weather monitoring applications.

## 6.0 Result and discussion 

In summary, the Laundry Warning Hero (LWHero) system, which combines the DHT11 sensor and ESP32 microcontroller with XAMPP, Grafana, and Ngrok offers a comprehensive solution for monitoring and analyzing weather data. By seamlessly integrating these components, LWHero enables real-time data collection, storage, visualization, and cloud accessibility. The DHT11 sensor proves invaluable in accurately measuring temperature and humidity, providing up-to-the-minute weather data for analysis and decision-making. When connected to the ESP32 microcontroller, it becomes a reliable source of information. LWHero leverages XAMPP, a local server solution comprising Apache and MySQL to effectively store and manage the collected weather data. This ensures a seamless flow of transmitted data from the ESP32 microcontroller to the server.

Grafana is harnessed in the system to visualize and analyze weather data in an insightful and visually appealing manner. With its customizable dashboards and graphs, users can effortlessly monitor and receive notifications about temperature and humidity trends. This feature proves particularly useful in protecting laundry from unexpected rain or unfavorable weather conditions. Through the integration of Ngrok, LWHero enables cloud publishing, granting access to weather data from any device with an internet connection. By creating secure tunnels, the local XAMPP server is made accessible remotely, allowing users to monitor and stay updated on weather conditions. Scalability and flexibility are key strengths of LWHero. The ESP32 microcontroller can easily accommodate additional sensors for measuring various weather parameters such as pressure, wind speed, or rainfall. This expandability seamlessly integrates the additional data into the existing system architecture, ensuring it is stored in the XAMPP server and visualized through Grafana.

In conclusion, the LWHero system, with its integration of DHT11, ESP32, XAMPP, Grafana, and Ngrok, provides a comprehensive solution for monitoring and analyzing weather data. This system offers real-time data collection, storage, visualization, and cloud accessibility. By effectively leveraging these technologies, LWHero facilitates proactive monitoring, informed decision-making, and timely alerts, ensuring the safety of laundry and providing valuable insights into weather patterns for various applications.







