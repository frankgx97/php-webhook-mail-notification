<?php
echo '<html>';
$json = file_get_contents('php://input');
$post_content = json_decode($json);
//var_dump($post_content);
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
        //ifttt_send_mail($i->url,$post_content->pusher->name);
    }
    else
    {
        echo '<p>sending mail</p>';
        ifttt_send_mail($i->url,$post_content->pusher->name);
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
