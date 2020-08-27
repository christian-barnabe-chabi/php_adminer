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

            if($http_code == 404) {
                $response = Translation::translate('not_found_api');
            }

            Presenter::present("generics.error", [
                "error_info" => "API",
                "error_code" => $http_code,
                "error_description"=>$response ?? "Error",
                "url" => $this->method .' '. $url,
            ]);

            exit();
        }

        else {
            $this->response = $response;
        }

        curl_close($this->ch);
        
        return $this;
    }

    public function response() {
        if($this->response) {
            return json_decode($this->response);
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

        $this->url = $url.$query_ext;

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
                unset($data[$key]);
            }
        }
        $query_ext = sprintf("%s?%s", '', http_build_query($query_ext));
        return $query_ext;
    }
}
?>
