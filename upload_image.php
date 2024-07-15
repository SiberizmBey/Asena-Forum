<?php
if ($_FILES['upload']) {
    $file = $_FILES['upload'];
    $filename = $file['name'];
    $location = 'uploads/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $location)) {
        $funcNum = $_GET['CKEditorFuncNum'];
        $url = $location;
        $message = 'Dosya başarıyla yüklendi';
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
    } else {
        echo 'Dosya yükleme başarısız oldu.';
    }
}
?>
