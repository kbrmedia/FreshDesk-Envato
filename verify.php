<?php
/**
* Envato verifier for Freshdesk
* @author KBRmedia (Josh)
* @link http://gempixel.com
*/
class Envato{
  /**
   * Configration
   * @var array
   */
  public $config = array(
      "key" => "9nz5016dz4dtmtotqlpj8c6ac5z3hc6k",
      "username" => "kbrmedia",
      "http" => "curl",
      "response" => "advanced",
      "theme" => "black" // black, white, green
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
    $code = strip_tags(htmlentities($_GET["code"]));
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
      curl_setopt($curl, CURLOPT_USERAGENT,'Envato-Freshdesk Purchase code verifier.');
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

        $html = $this->theme($t);
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
    echo $content["html"];
    exit;
    header("content-type: application/javascript");
    echo ($_GET["callback"]."(".json_encode($content).")");
    exit;
  }
  /**
   * Theme
   * @author KBRmedia
   * @since  1.0
   */
  private function theme($t){
    $fn = "theme_{$this->config["theme"]}";
    if(method_exists("Envato", $fn)) return $this->$fn($t);
    return $this->theme_black($t);
  }
    /**
     * Default Theme
     * @author KBRmedia
     * @since  1.0
     */
    private function theme_black($t){
        $html = "<div id='envato_purchase_verify' style='font-size:13px;background:#000;color: #fff;border-radius:2px;padding: 5px;margin-top:5px;'>";
          $html .= "<strong style='padding:0;;'>{$t->buyer}</strong> <small>(Verfied Buyer)</small>";
          $html .= "<p style='padding:0;margin:0;margin-top:5px; font-size:12px'>Purchased a <strong>{$t->licence}</strong> of <strong>{$t->item_name}</strong> on {$t->date}</p>";
        $html .= "</div>";      
      return $html;
    }
    private function theme_white($t){
        $html = "<div id='envato_purchase_verify' style='font-size:13px;background:#fff;color: #27aae1;border-radius:2px;padding: 5px;margin-top:5px;border: 2px solid #27aae1'>";
          $html .= "<strong style='padding:0;;'>{$t->buyer}</strong> <small>(Verfied Buyer)</small>";
          $html .= "<p style='padding:0;margin:0;margin-top:5px; font-size:12px'>Purchased a <strong>{$t->licence}</strong> of <strong>{$t->item_name}</strong> on {$t->date}</p>";
        $html .= "</div>";      
      return $html;
    }
    private function theme_green($t){
        $html = "<div id='envato_purchase_verify' style='font-size:13px;background:#fff;color: #71E126;border-radius:2px;padding: 5px;margin-top:5px;border: 2px solid #71E126'>";
          $html .= "<strong style='padding:0;;'>{$t->buyer}</strong> <small>(Verfied Buyer)</small>";
          $html .= "<p style='padding:0;margin:0;margin-top:5px; font-size:12px'>Purchased a <strong>{$t->licence}</strong> of <strong>{$t->item_name}</strong> on {$t->date}</p>";
        $html .= "</div>";      
      return $html;
    }           
}
  /**
   * Instantiate Class
   */
  new Envato();
