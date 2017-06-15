<?php 
$response['code'] = "404";
$response['message'] = "Please fill in all the form.";
        // print_r($_POST);
if(isset($_POST['name']) && !empty($_POST['name']) 
&& isset($_POST['email']) && !empty($_POST['email']) 
&& isset($_POST['message']) && !empty($_POST['message'])
&& isset($_POST['submit']) && !empty($_POST['submit']) ){
   if($_POST['submit'] == "submit"){
        
        // print_r($_POST);
        //Email information
        $admin_email = "rickyicezz@gmail.com";
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = date("d m Y").", Request for Ricky Kennedy from ".$name;
        $message = $_POST['message'];
        
        //send email
        if(mail($admin_email, "$subject", $message, "From:" . $email)){
            // print_r("successful");
            $response['code'] = "200";
            $response['message'] = "Your message has been successfully sent, I will get back to you soon.";

        }else{
            // print_r("there is an error");
        }
   }
}
echo json_encode($response);