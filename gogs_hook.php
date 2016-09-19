<?php
require 'PHPMailer/PHPMailerAutoload.php';

echo '<html>';
$json = file_get_contents('php://input');
$post_content = json_decode($json);
if ($post_content->before == $post_content->after)
{
    echo '<p>invalid request</p>';
    return;
}
foreach ($post_content->commits as $i)
{
    if (strpos($i->message , "notification") == null)
    {
        echo '<p>not for notification, skipped.</p>';
    }else{
        echo '<p>sending mail</p>';
        send_mail($i->url,$post_content->pusher->name);
    }
}

function send_mail($mail_commit_url,$mail_pusher)
{
    $mail = new PHPMailer;
    $mail -> setLanguage('zh', '/optional/path/to/language/directory/');
    $mail -> CharSet = "utf-8";
    $mail -> isSMTP();
    $mail -> SMTPDubug = 2;
    $mail -> Debugoutput = 'html';
    $mail -> SMTPSecure = 'tls';
    $mail -> Host = 'smtp.exmail.qq.com';
    $mail -> Port = 587;
    $mail -> SMTPAuth = True;
    require 'smtp_key.php';//include file which contents smtp username and passcode.
    /*include file which contents smtp username and passcode,write:
    $mail -> Username = '';
    $mail -> Password = '';
    in smtp_key.php*/
    $mail -> setFrom('i@nyan.im', 'Frank');
    require 'mail_list.php';//include mail reciver list.
    $mail -> Subject = '你有一条来自IBM俱乐部的通知';
    $mail -> Body = date(DATE_RFC2822).'<br />'.$mail_pusher.'通过git push发布了一条通知：<br /><a href="'.$mail_commit_url.'">'.$mail_commit_url.'</a>';
    $mail->IsHTML(true);
 
    if (!$mail -> send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }else{
        echo "mail sent";
    }
}


function ifttt_send_mail($mail_commit_url,$mail_pusher)
{
    $data = array('value1' => $mail_commit_url, 'value2' => $mail_pusher);
    $uri = 'https://maker.ifttt.com/trigger/gogs_hook/with/key/cfY4fGDsxx4OnH3h5Gy5tT';
    $json = json_encode($data);
                                                                                                                     
    $ch = curl_init($uri);                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($json))                                                                       
    );                                                                                                                   
                                                                                                                     
    $result = curl_exec($ch);
    echo '<p>mail sent</p>';
}
?>
<p>This is a payload page for gogs webhook</p>
</html>
