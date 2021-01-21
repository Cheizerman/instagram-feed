<?php
class Instagram{
    const URL_INSTAGRAM_API = 'https://graph.instagram.com/me/';
    private $access_token = 0;
    public $token_params = 0;
    public $count_post = 0;
    public $error = "";
    public $App = "";
    public function __construct($token, $count = 50){
        global $APPLICATION;
        $this->token_params = $token;
        $this->count_post = $count;
        $this->App=$APPLICATION;
    }
    public function checkApiToken(){
        if(!strlen($this->token_params)){
            $this->error="No API token instagram";
        }
        $this->access_token='/?access_token='.$this->token_params;
    }
    public function getFormatResult($method, $fields = ''){
        if(function_exists('curl_init'))
        {
            if($fields) {
                $method.$this->access_token .= '&fields='.$fields;
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::URL_INSTAGRAM_API.$method.$this->access_token."&limit=".$this->count_post);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            $out = curl_exec($curl);
            $data =  $out ? $out : curl_error($curl);            
        }
        else
        {
            $data = file_get_contents(self::URL_INSTAGRAM_API.$method.$this->access_token);            
        }
        
        $data = json_decode($data, true);
        return $data;
    }
    public function getInstagramPosts(){
        $this->checkApiToken();
        if($this->error){
            return array("ERROR" => "Y", "MESSAGE" => $this->error);
        }else{
            $data=$this->getFormatResult('media', 'id,media_url,permalink,username,timestamp,media_type');
        }
        return $data;
    }
    /* 
   public function getInstagramUser(){
        $this->checkApiToken();
        if($this->error){
            return $this->error;
        }else{
            $data=$this->getFormatResult('users/self');
        }
        return $data;
    }
    public function getInstagramTag($tag) {
        $this->checkApiToken();
        if($this->error){
            return $this->error;
        }else{
            $data=$this->getFormatResult('tag/'.$tag.'/media/recent');
        }
        return $data;
    }*/
}
?>

<?
$token ='YOUR-TOKEN-APP-INSTAGRAM-BASIC-DISPLAY';
$inst = new Instagram($token);
$instPosts = $inst->getInstagramPosts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instagram Feed</title>
</head>
<body>


<?php
foreach ($instPosts as $v  )  {
    echo $v['id'] . '<br>';
}
?>

<?php /* only data, not 'paging'
foreach ($instPosts['data'] as $v  )  {
    echo $v['id'] . '<br>';
}
*/ ?>

</body>
</html>

