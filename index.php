<?php

//include('database/database.php');
include('function/func.php');
include('config/database.php');

function Crawl_Data(){
	GLOBAL $conn_create;
	//require gọi thư viện vào
	require "simple_html_dom.php";
	//lấy html cua trang web ve
	$tenweb="https://www.thegioididong.com/dtdd";
	$html= file_get_html($tenweb);
	//echo $html;
	$data=$html->find("ul.cate li a");
	foreach ($data as $t) {
		$link= "https://www.thegioididong.com".$t->href;
		//echo "<h2>".$t->href."</h2>";
		$linksp= file_get_html($link);
		$rate=$linksp->find("ul.ratingLst div.rc");
		foreach ($rate as $comments) {
		$comment= $comments->plaintext;
		//echo $comment."-----------------";
		$Stars=$comments->find(".iconcom-txtstar");
		$Star= count($Stars);
		if($Star!=0){
			//echo $Star."<hr/>";
			$sql_Crawl_Data = "INSERT INTO Crawl (	Comment_Crawl, Rate_Crawl)
			VALUES ('$comment', '$Star')";
			mysqli_query($conn_create, $sql_Crawl_Data);
		}
		
		}
	}
};

function Create_Database(){
	GLOBAL $conn;
	GLOBAL $servername;
	GLOBAL $username;
	GLOBAL $password;
	GLOBAL $conn_create;
	//không tồn tại, tạo database	
	$Create_Table = "CREATE DATABASE ML_Naives_Bayes  CHARACTER SET utf8 COLLATE utf8_unicode_ci";
	mysqli_query($conn, $Create_Table);

	// sql to create table	
	$Create_Crawl = "CREATE TABLE Crawl (
	ID_Crawl INT(4)  UNSIGNED AUTO_INCREMENT PRIMARY KEY,  
	Comment_Crawl Text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
	Rate_Crawl Int(1) NOT NULL
	)";
	mysqli_query($conn_create, $Create_Crawl);
	//
	
	//
	$Create_Document = "CREATE TABLE Document (
	ID_Document INT(4)  UNSIGNED AUTO_INCREMENT PRIMARY KEY,   
	ID_Crawl INT(4) UNSIGNED NOT NULL,
	Comment_Document Text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
	Rate_Document Int(1) NOT NULL,
	FOREIGN KEY (ID_Crawl) REFERENCES Crawl(ID_Crawl)
	)";
	mysqli_query($conn_create, $Create_Document);

	//
	$Create_Test = "CREATE TABLE Test (
	ID_Test INT(4)  UNSIGNED AUTO_INCREMENT PRIMARY KEY,  
	ID_Crawl INT(4) UNSIGNED NOT NULL,
	Comment_Test Text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
	Rate_Test Int(1) NOT NULL,
	FOREIGN KEY (ID_Crawl) REFERENCES Crawl(ID_Crawl)
	)";
	mysqli_query($conn_create, $Create_Test);

	//Crawl_Data
	Crawl_Data();
};


if(mysqli_select_db($conn, 'ML_Naives_Bayes')){
	// xóa table
    $Drop_Table = "DROP DATABASE ML_Naives_Bayes";
	mysqli_query($conn, $Drop_Table);
	//tạo database
	Create_Database();
}else{
    Create_Database();
}



$sql = "SELECT ID_Crawl, Comment_Crawl,Rate_Crawl FROM Crawl";
$result = mysqli_query($conn_create, $sql);
if (mysqli_num_rows($result) >0) {
	// output data of each row
	//print_r(mysqli_num_rows($result));
	//$count= "SELECT COUNT(ID_Crawl)FROM Crawl WHERE ID_Crawl>0";
	$i=0;
    while($row = mysqli_fetch_assoc($result)) {
		if($i<20){
			$id_crawl=$row['ID_Crawl'];
			$Star= $row["Rate_Crawl"];
			$comment=$row["Comment_Crawl"];
			$recomment=reg($comment);        
			$recomment_stopword=cut_stopword($recomment);
			$cut_stopword_sql = "INSERT INTO test (ID_Crawl,Comment_Test,Rate_Test)
				VALUES ('$id_crawl','$recomment_stopword','$Star')";
			
			mysqli_query($conn_create, $cut_stopword_sql);   
			$i++;
			
		}else{
			
			
			$id_crawl=$row['ID_Crawl'];
			$Star= $row["Rate_Crawl"];
			$comment=$row["Comment_Crawl"];
			$recomment=reg($comment);        
			$recomment_stopword=cut_stopword($recomment);
			$cut_stopword_sql = "INSERT INTO document (ID_Crawl,Comment_Document,Rate_Document)
				VALUES ('$id_crawl','$recomment_stopword','$Star')";
				
			
			
			mysqli_query($conn_create, $cut_stopword_sql);   
			$i++;
		}
	
		    
    }
} else {
    echo "0 results";
}

mysqli_close($conn);

