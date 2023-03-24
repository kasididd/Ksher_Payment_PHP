<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true); // แปลงข้อมูล JSON เป็น array

  // รับข้อมูลจาก HTTP POST request
  $file_name = "creaete.json"; // ชื่อไฟล์ที่ต้องการสร้าง
  $file_content = json_encode($data) ; // เนื้อหาที่ต้องการเขียนลงในไฟล์
  $file_content = '';
  foreach ($data as $key => $value) {
    $file_content .= $key . ': ' . $value . "\n"; // เพิ่มเครื่องหมาย newline (\n) หลังจบแต่ละค่า
  }
  // สร้างไฟล์และเขียนข้อมูลลงในไฟล์
  if (file_put_contents($file_name, $json_data)) {
    echo 'ไฟล์ถูกสร้างและเขียนข้อมูลเรียบร้อยแล้ว'; // แสดงข้อความถ้าสามารถสร้างไฟล์และเขียนข้อมูลได้
  } else {
    print_r($_POST['data']);
    print_r($data);
    echo 'ไม่สามารถสร้างไฟล์ได้'; // แสดงข้อความถ้าไม่สามารถสร้างไฟล์ได้
  }
}else{
    echo "ไม่ได้ Post";
}
?>
