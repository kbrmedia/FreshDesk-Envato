FreshDesk-Envato
================

Verify Envato purchase codes via FreshDesk using FreshPlug. Setting this up is very easy. All you have to do is to follow the instruction below and that is it.

Version
----

1.1

Installation
-----------
1. Download and Upload verify.php to your own server
2. Open verify.php and configure it 

```sh
public $config = array(
      "key" => "", // Envato API Key
      "username" => "", // Envato Username
      "http" => "curl", // HTTP Request method: "curl" for cURL or empty for file_get_contents
      "response" => "advanced", // Response method: "simple" will return "Verifed or not verified" while "advanced" will return more info
      "theme" => "black" // Choose the theme you want
    ); 
```
3. Create a custom ticket field on Freshdesk and name it "Purchase Code"
4. Create a custom "FreshPlug" using the code below (make sure to change YOURSITE)

FreshPlug Code
--------------

```sh
 <script>
  jQuery(document).ready(function(){
    var code = '{{ticket.purchase_code}}';
  	jQuery.getJSON("http://YOURSITE/verify.php?code="+code+"&callback=?",
      function(r) {
        if(r.type == "text"){
         jQuery(".user_name a").append("&nbsp;&nbsp;<strong><small>("+r.text+")</small></strong>");
        }else if(r.type == "html"){
        	jQuery("#contact_info_default").after(r.html);
        }
      });
  });
 </script>
```

Response Type
--------------

You have the option to use two different response types. 

**Simple**

![](http://gempixel.com/i/simple.jpeg)

**Advanced**

![](http://gempixel.com/i/advanced.jpeg)

License
----

MIT (i.e. do whatever you want ;))
