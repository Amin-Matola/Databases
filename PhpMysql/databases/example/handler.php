<!DOCTYPE HTML>
<html>
    <head>
        <title>Data Transfer</title>
        <style>
            html, body{
                margin:0px;
                padding:0px;
            }
            
            div.main{
                width:60%;
                margin:auto;
                text-align:center;
            }
            
        </style>
    </head>
    <body>
    <div class="main">
        <?php
                require_once("../connection.php");
                
                
                $userdata   = $_POST;
                
                
                /* *
                 * Check if the data is ready to be saved
                 * */
                if(empty($userdata) || !isset($userdata["nounce"]) || $userdata["nounce"] !== "^ahqbhbhew09*721"){
                    issue_error();
                    exit;
                }
        ?>
        <h1>Hello <?= addslashes($userdata["name"]); ?></h1>
        <?php
                
                /* *
                 * Create the table in case it doesnt exist
                 * */
                if(defined("TABLE") && !empty(TABLE)){
                    $tb = TABLE;
                }else{
                    $tb = "test";
                }
                $table = table($tb, 
                                ["id" => "int primary key auto_increment",
                                "website"=>"text", 
                                "email"=>"text", 
                                "phone"=>"text", 
                                "name" => "varchar(50)"]);
                            
                            
                /* *
                 * First insert the data to the database
                 * Removing the nounce field
                 * */
               $table->insert($tb, remove_nounce($userdata));
                
                
                /* *
                 * Help to remove the nounce field, used for verification
                 * */
                function remove_nounce($data){
                    $result = array_filter($data, function($x){return $x != "^ahqbhbhew09*721";});
                    
                    return $result;
                }
                
                
                /* *
                 * If all checks go well, then process this query
                 * */
                function process_query($data = []){
                    $userdata       = $data;
                    if(!empty($userdata)){
                        
                        $serv       = $_SERVER["SERVER_NAME"];
                    
                        $table      = "<table style='width:40%;background:#fafafa;margin:50px auto;padding:10px 2% 40px'>
                        <caption>
                        Message From <a href='http://{$serv}' style='text-decoration:none'>
                        {$serv}
                        </a>
                        </caption>";
                        $header     = "<tr>";
                        $body       = "<tr>";
                        
                        foreach($userdata as $k => $v){
                            if($k == "nounce"){
                                continue;
                            }
                            $header .= "<th style='text-align:left'>$k</th>";
                            $body   .= "<td>$v</td>";
                        }
                        $header     .= "</tr>";
                        $body       .= "</tr></table>";
                        
                        $whole = $table.$header.$body;
                        
                        //send_mail($whole, $userdata["email"]);
                    }
                    
                    
                }
                
                
                /* *
                 * If the check wasnt successful, isue the error
                 * */
                function issue_error(){
                    echo "<div style='width:60%; margin:auto; text-align:center'>
                        <h2>Hohoho! My friend --</h2>
                        <p>The data you are submiting is invalid</p>
                        </div>";
                }
                
                
                /* *
                 * Now send the email after all goes well.
                 * */
                function send_mail($data, $reciever = ''){
                     if(empty($reciever)){
                            return false;
                     }

                     
                     $sender   =  table()->get_admin_email();
                     $cc       =  "";
                     
                     if(defined("CC_EMAIL") && !empty(CC_EMAIL)){
                         $cc   = "Cc:".CC_EMAIL." \r\n";
                     }
        
                     $subject  = "Message from <a href='http://".$_SERVER["HTTP_HOST"]."'>".$_SERVER["SERVER_NAME"]."</a>";
                     
                     $mail     = file_get_contents("emails/mail.php");
                    
                     $body     = $data;
                     
                     if(!empty(trim($mail, " "))){
                         $body = $mail;
                     }
                     
                     $header   = "From:$sender \r\n{$cc}MIME-Version: 1.0\r\nContent-type: text/html\r\n";
                     
                     new Mail_Master("smtp.gmail.com", 465, ["admin@example.com", "some-pass"]);
                     $response = mail ($reciever, $subject, $body,$header);

                     
                     if( $response == true ) {
                        echo "<p>Check your inbox for our message...</p>";
                     }else {
                        echo "<p>Message could not be sent...</p>";
                     }
                }
                
                /* *
                 * Now fire it out
                 * */
                process_query($userdata);
                
        ?>
        
       
</div>
</body>
</html>
