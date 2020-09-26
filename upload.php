<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
    <title>图片上传</title>
</head>
<body>

</br></br><h2 align="center">Upload files to apache</h3></br>
<form align="center" enctype="multipart/form-data" action="index.php" method="post" name="file">
    上传文件:
    <input name="file" type="file"></br>
    输入你想存储的文件名：<input name='name' type="text">
    <input type="submit" value="upload"><br>
</form>
<?php
if(isset($_FILES['file'])) {
    $name = basename($_POST['name']);
    $ext = pathinfo($name,PATHINFO_EXTENSION);
    if(in_array($ext, ["php", "php5", "php4", "php3", "phtml", "pht", "jsp", "jspa", "jspx","<",">","jsw", "jsv", "jspf","jtml", "asp", "aspx", "asa", "asax", "ascx", "ashx", "asmx", "cer","{","}", "swf", "ini"])) {
        exit('bad file');
    }
    if(move_uploaded_file($_FILES['file']['tmp_name'], "upload/".$name)){
        echo "Success!The file is in upload/$name";
    }else{
        echo "Something wrong";
    };
}
