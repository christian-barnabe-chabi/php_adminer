<?php

namespace Services;

use App\Resources\Resource;
use Serializable;

class API {
    private $url;
    private $ch;
    private $header;
    private $response;
    private $method;
    
    public function __construct()
    {

        $this->reinit();

    }

    private function reinit() {
        $this->ch = curl_init();
        if(app('content-type')) {
            $this->header [] =  'Content-Type: '.app('content-type');
        }
        $this->header [] =  'Accept: application/json';
        $this->response = null;

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);
    }

    public function header($key, $value) {

        $this->header[] = "$key: $value";
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);

    }

    private function call() {
        $url = $this->url;
        
        if(empty($this->url)) {
            Presenter::present("generics.error", [
                "error_info" => "API",
                "error_code" => -1,
                "url" => $this->method .' '. $url,
                "error_description"=>"Can't make request on null endpoint",
            ]);
        }

        $response = curl_exec($this->ch);
        $http_code = curl_getinfo($this->ch)['http_code'];

        if ($http_code == 401) { // unauthorized
            $this->response = null;
        }
        else if ($http_code == 412) { // unauthorized
            Presenter::present('generics.unauthorised');
        }

        
        else if (!in_array($http_code, [200, 201])) {

            if($http_code == 0) {
                $response = "<code>{$this->url}</code><br>".Translation::translate('is_api_working');
            }

            $responseObject = is_string($response) ? json_decode($response) : $response;
            $messageInResponse = isset($responseObject->message) ? "<div class='ui message'><span class='ui tiny label orange'>Message:</span> $responseObject->message</div>" : null;

            if($http_code >= 400 && $http_code <= 499) {


                switch($http_code) {
                    case 404:
                        $messageInResponse = Translation::translate('not_found_api') . $messageInResponse . '<div><code>' . (app('debug') ? $response : "") . '</code></div>';
                    break;

                    case 422:
                        $messageInResponse = Translation::translate('422') . $messageInResponse . '<div><code>' . (app('debug') ? $response : "") . '</code></div>';
                    break;

                    default:
                        $messageInResponse = Translation::translate('error_occurs') . $messageInResponse . '<div><code>' . (app('debug') ? $response : "") . '</code></div>';

                }

                // return Presenter::present("generics.error", [
                //     "error_info" => "API",
                //     "error_code" => $http_code,
                //     "error_description"=>$response ?? Translation::translate('failure'),
                //     "url" => $this->method .' '. $url,
                // ]);
            }

            // if(($res = Translation::translate($http_code)) != $http_code) {
            //     $response = $res;
            // }

            // begin update
            $this->response = (object)[];

            
            // if(is_string($response)){
            //     $response = json_decode($response);
                
            // }
            
            // $this->response = $response;
            
            // if(!isset($response->message) || $response->message == NULL) {
            //     $this->response->message = $res;
            // } else {
            //     $this->response->message = $response->message;
            // }
            
            $this->response->message = $messageInResponse;
            $this->response->success = false;

            // var_dump($this->response());
            // exit();

            curl_close($this->ch);
        
            return $this;
            // end update

            //begin comment -- old code
            
            /*
                Presenter::present("generics.error", [
                    "error_info" => "API",
                    "error_code" => $http_code,
                    "error_description"=>$response ?? "Error",
                    "url" => $this->method .' '. $url,
                ]);

                exit();
            */

            // end comment
        }

        // else {
            $this->response = $response;
        // }

        curl_close($this->ch);
        
        return $this;
    }

    public function response() {
        if($this->response) {
            if(is_string($this->response)) {
                return json_decode($this->response);
            }
            return $this->response;
        }
        return null;
    }

    public function get($url, array $data = []) {
        $this->reinit();

        $this->method = 'GET';

        $this->url = $url;
        $this->url = sprintf("%s?%s", $this->url, http_build_query($data));

        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        return $this->call();
    }

    public function post($url, array $data = null) {
        $this->reinit();

        $this->method = 'POST';

        $this->url = $url;
        curl_setopt($this->ch, CURLOPT_URL, $this->url);

        curl_setopt($this->ch, CURLOPT_POST, 1);
        if ($data){
            if(app('content-type') == 'application/json') {
                $data = json_encode($data);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        }

        return $this->call();
    }

    public function put($url, array $data = null) {
        $this->reinit();

        $this->method = 'PUT';

        $this->url = $url;

        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data) {
            if(app('content-type') == 'application/json') {
                $data = json_encode($data);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        return $this->call();
    }

    public function delete($url, array $data = null) {
        $this->reinit();

        $this->url = $url;

        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        if ($data) {
            if(app('content-type') == 'application/json') {
                $data = json_encode($data);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        }
        return $this->call();
    }

    public function callWith($url, string $method, array $data = []) {
        
        $this->reinit();
        
        $this->method = strtoupper($method);
        
        $query_ext = $this->transform_data($data);

        $this->url = $url; #.$query_ext;

        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        switch ($method){
            case "POST":
                return $this->post($this->url, $data);
                break;
            case "PUT":
                return $this->put($this->url, $data);
                break;
            case "DELETE":
                return $this->delete($this->url, $data);
                break;
            default:
                return $this->get($this->url, $data);
        }
    }

    private function transform_data(array &$data) {
        $query_ext = [];
        foreach ($data as $key => $value) {
            if(is_array($value)) {
                $query_ext[$key] = $value;
                $data[$key] = json_encode(array_values($value)); 
                # json_encode($value);
                // unset($data[$key]);
            }
        }
        $query_ext = sprintf("%s?%s", '', http_build_query($query_ext));
        return $query_ext;
    }
}
?>
