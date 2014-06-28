<?php
/**
* Envato verifier for Freshdesk
* @author KBRmedia (Josh)
* @link http://gempixel.com
*/
class Envato{
  /**
   * Configuration
   * @var array
   */
  public $config = array(
      "key" => "", // Envato API Key
      "username" => "", // Envato Username
      "http" => "curl", // HTTP Request method: "curl" for cURL or empty for file_get_contents
      "response" => "advanced" // Response method: "simple" will return "Verifed or not verified while "advanced" will return more info
    );  
  /**
   * Envato API URL
   * @var string
   */
  protected $api_url = "http://marketplace.envato.com/api/edge/";  
  /**
   * Run script
   */
  public function __construct(){
    // Is set code
    if(!isset($_GET["code"]) || empty($_GET["code"])) return $this->response();
    // Clean Code
    $code = htmlentities(strip_tags($_GET["code"]));
    // Format URL
    $this->api_url = $this->api_url."/{$this->config["username"]}/{$this->config["key"]}/verify-purchase:{$code}.json";
    $response = $this->http(TRUE);
    // var_dump($response);
    // Return Response
    return $this->response($response);
  }

  /**
   * HTTP Request to envato
   * @param  bool $decode  Decode JSON
   * @return string         response
   */
  private function http($decode = FALSE) {
    // Switch Method
    if($this->config["http"]=="curl"){

      $curl = curl_init();

      curl_setopt($curl, CURLOPT_URL, $this->api_url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

      $response = curl_exec($curl);
      curl_close($curl);
    }else{

      $response = file_get_contents($this->api_url);      
    }

    // Format Response
    if($decode){
      return json_decode($response);
    }else{
      return $response;
    }
  }  
  /**
   * Generate Response
   * @param  string $content the content to be returned
   */
  private function response($content = NULL){
    // Check if valid request
    if(!isset($_GET["callback"]) || is_null($content) || empty($content)){
      header('HTTP/1.1 400 Bad Request', true, 400);
      $this->e(array("type"=>"text", "text"=>"Customer Not Verified"));
    }
    if(isset($content->{'verify-purchase'}) && isset($content->{'verify-purchase'}->created_at)){
      if($this->config["response"] == "advanced"){
        $t = $content->{'verify-purchase'}; 
        $t->date = date("d F, Y", strtotime($t->created_at));
        $t->date .= " at ".date("H:i", strtotime($t->created_at));

        $html = "<div id='envato_purchase_verify' style='font-size:13px;background:#000;color: #fff;border-radius:2px;padding: 5px;margin-top:5px;'>";
          $html .= "<strong style='padding:0;;'><a href='http://themeforest.com/{{$t->buyer}}' style='color:#fff' target='_blank'>{$t->buyer}</a></strong> <small>(Verfied Buyer)</small>";
          $html .= "<p style='padding:0;margin:0;margin-top:5px; font-size:12px'>Purchased a <strong>{$t->licence}</strong> of <strong>{$t->item_name}</strong> on {$t->date}</p>";
        $html .= "</div>";
        $this->e(array("type"=>"html","html"=>$html));
      }else{
        $this->e(array("type"=>"text", "text"=>"Verified Customer"));
      }
    }
    // If script reaches here then code cannot be verified.
    $this->e(array("type"=>"text", "text"=>"Customer Not Verified"));
  }
  /**
   * Echo and exist
   * @param  mixed $content Content to echo
   */
  private function e($content){
    header("content-type: application/javascript");
    echo ($_GET["callback"]."(".json_encode($content).")");
    exit;
  }
}
  /**
   * Instantiate Class
   */
  new Envato();
