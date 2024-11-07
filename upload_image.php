<?php
header('Content-Type: application/json');
$n = random_int(5,11);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // "/edit"
        $uploadDir = 'image/uploads/';
        $uploadFile = $uploadDir . $n.basename($_FILES['file']['name']);

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $fileUrl = $scriptDir.'/'.$uploadFile;
            echo json_encode(['success' => true, 'fileUrl' => $fileUrl]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ファイルのアップロードに失敗しました。']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ファイルがアップロードされませんでした。']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '無効なリクエストです。']);
}
?>
