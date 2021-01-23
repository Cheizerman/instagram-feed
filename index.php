<?php
class Instagram{
    const URL_INSTAGRAM_API = 'https://graph.instagram.com/me/';
    private $access_token = 0;
    public $token_params = 0;
    public $count_post = 0;
    public $error = "";
    public $App = "";
    public function __construct($token, $count = 3){
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
$token ='YOUR-TOKEN-INSTAGRAMM';
$inst = new Instagram($token);
$usersMedia = $inst->getInstagramPosts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>
<style>
			body {
				font-family: 'Helvetica';
			}

			.pages-list {
				display: block;
			}

			.pages-list-item {
				display: inline-block;
				vertical-align: top;
				margin-top: 10px;
				width: 250px;
				border: 1px solid #333;
				margin-left: 10px;
				padding: 10px;
			}

			.pages-media {
				width: 100%;
				max-width:100px;
			}

			.raw-response {
				width: 100%;
				height: 600px;
			}

			.nav-container {
				margin-top: 20px;
				font-size: 20px;
				display: inline-block;
				width: 100%;
			}

			.nav-next {
				float: right;
			}
		</style>

		<div class="nav-container">
			<?php if ( isset( $usersMedia['paging']['previous'] ) ) : ?>
				<a href="<?php $_SERVER['PHP_SELF']; ?>?cursor_type=before&cursor=<?php echo $usersMedia['paging']['cursors']['before']; ?>&limit=3">
					< PREVIOUS
				</a>
			<?php endif; ?>
			<?php if ( isset( $usersMedia['paging']['next'] ) ) : ?>
				<a class="nav-next" href="<?php $_SERVER['PHP_SELF']; ?>?cursor_type=after&cursor=<?php echo $usersMedia['paging']['cursors']['after']; ?>&limit=3">
					NEXT >
				</a>
			<?php endif; ?>
		</div>
		<ul class="pages-list">
			<?php foreach ( $usersMedia['data'] as $media ) : // loop over posts returned for the page ?>
				<li class="pages-list-item">
					<?php if ( 'IMAGE' == $media['media_type'] || 'CAROUSEL_ALBUM' == $media['media_type'] ) : // media is an image ?>
						<img class="pages-media" src="<?php echo $media['media_url']; ?>" />
					<?php else : // media is a video ?>
						<video class="pages-media" controls>
							<source src="<?php echo $media['media_url']; ?>" >
						</video>
					<?php endif; ?>
					<h4>
						<?php echo nl2br( $media['caption'] ); // display the caption preserving spaces ?>
					</h4>
					<div>
						Link to Post:
						<br />
						<a target="_blank" href="<?php echo $media['permalink']; ?>">
							<?php echo $media['permalink']; // link to media on instagram ?>
						</a>
					</div>
					<br />
					<div>
						Post at: <?php echo $media['timestamp']; // time the media was posted ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>



</body>
</html>

