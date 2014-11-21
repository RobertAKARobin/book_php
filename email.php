<?php

if(empty($_POST)) header("Location: index.php");

require "r/scripts.php";

try{
	email(
		$_POST["email"],
		"Thanks for your message! :)",
		sanitize($_POST["message"])
	);	
	$success = "Thanks for your message!";
	if(!empty($_POST["email"])) $success .= " I'm sending a copy to $_POST[email] right now.";
	throw new success($success);
	
}catch(error $e){
	echo json_encode(array("error",$e->getMessage()));
}catch(success $s){
	echo json_encode(array("success",$s->getMessage()));	
}


?>