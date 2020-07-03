<?php
namespace App\Resources;

use Abstracts\Resource;
use App\Scaffolding\CreateGuesser;
use App\Scaffolding\CsvExportGuesser;
use App\Scaffolding\EditGuesser;
use App\Scaffolding\ListGuesser;
use App\Scaffolding\ShowGuesser;
use Exception;
use Services\API;
use Services\Auth;
use Services\Presenter;
use Services\Request;
use Services\Translation;

abstract class BaseBlueprint extends Resource {
    protected $url;
    protected $url_set;
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $column_scaffold = [];
    protected $data_fetched;
    protected $must_be_auth = true;
    protected $data;
    protected $api;
    protected $createable = true;
    protected $editable = true;
    protected $deleteable = true;
    protected $exportable = true;
    protected $uid = 'id';
    protected $model = null;
    protected $hidden = [];
    protected $paginate = false;
    protected $name = false;
    private $action; // request action -
    protected $callbacks = [];
    protected $widgets_positions = [];

    public function __construct()
    {

        $data = clone Request::$request;
        unset($data->php_admin_resource);
        unset($data->uid);
        unset($data->php_admin_admin_edit);
        unset($data->php_admin_list);
        unset($data->php_admin_edit);
        unset($data->php_admin_update);
        unset($data->php_admin_create);
        unset($data->php_admin_show);
        unset($data->php_admin_delete);
        unset($data->php_admin_delete_multiple);
        unset($data->php_admin_save);
        unset($data->php_admin_search);
        unset($data->php_admin_export);

        if($this->model == null)
        $this->model = Request::$request->php_admin_resource;
        $this->model = ucwords(str_replace('_', ' ', $this->model));

        $this->api = new API();

        if($this->must_be_auth and Auth::user()) {
            $this->api->header("Authorization", app('auth_type').' '.Auth::token());
        }

        if(isset(Request::$request->php_admin_update) || isset(Request::$request->php_admin_save) || isset(Request::$request->php_admin_delete) || isset(Request::$request->php_admin_export)) {
            // updating or saving or deleting or exporting
        } else {

            $this->url = app('base_url').$this->url_set["list"];


            // pagination
            if($this->paginate) {
                $this->data_fetched = $this->api->get($this->url, (array)$data)->response();

                if(isset($this->paginate['count'])) {
                    $key = $this->paginate['count'];
                    if(isset($this->data_fetched->$key)) {
                        $this->paginate['count'] = $this->data_fetched->$key;
                    }
                    else {
                        $this->paginate['count'] = null;
                    }
                } else {
                    $this->paginate['count'] = null;
                }

                if(isset($this->paginate['last_page'])) {
                    $key = $this->paginate['last_page'];
                    if(isset($this->data_fetched->$key)) {
                        $this->paginate['last_page'] = $this->data_fetched->$key;
                    } else {
                        $this->paginate['last_page'] = null;
                    }
                } else {
                    $this->paginate['last_page'] = null;
                }

                if(isset($this->paginate['data_field'])) {
                    $data_field = $this->paginate['data_field'];
                    $this->data_fetched = $this->data_fetched->$data_field;
                } else {
                    exit('error');
                }
            } 
            else {
                $this->data_fetched = $this->api->get($this->url)->response();
            }

            try {
                if(!is_array($this->data_fetched)) {
                    throw new Exception;
                }
                $keys = $this->data_fetched[0] ?? null;
            } catch (Exception $e) {
                Presenter::present('generics.global_error', [
                    'error_code'=>500,
                    'error_description'=>'The received data is not an array but of "'. get_class($this->data_fetched) .'" type. Are you getting paginated data?'
                ]);
                exit('Error');
            }


            $keys = $keys != null ? (get_object_vars($keys)) : [];

            if (empty($this->column_scaffold)) {
                // if()
                foreach ($keys as $key=>$value) {
                    if (is_object($this->data_fetched[0]->$key) or is_array($this->data_fetched[0]->$key)) {
                        continue;
                    }

                    $this->column_scaffold[$key] = [];
                }
            }

            foreach ($this->column_scaffold as $key => $value) {
                $type = null;
                $exploded = explode('.', $key);
                $new_key = $key;

                if(count($exploded)>1) {
                    $new_key = $exploded[0];
                }

                if(!empty($value['type'])) {
                    $this->column_scaffold[$key]['type'] = $value['type'];
                }
                else
                {
                    if(!isset($keys[$new_key])) {
                        $this->column_scaffold[$key]['type'] = 'text';
                        continue;
                    }

                    $column = ($keys[$new_key]);

                    if(is_object($column)) {
                        $this->column_scaffold[$key]['type'] = 'object';
                        continue;
                    }
                    if(is_array($column)) {
                        $this->column_scaffold[$key]['type'] = 'array';
                        continue;
                    }
                    if(is_numeric($column)) {
                        $this->column_scaffold[$key]['type'] = 'number';
                        continue;
                    }

                    if(preg_match("/\d\d\d\d-\d\d-\d\d\s\d\d:\d\d:\d\d/", $column)) {
                        $this->column_scaffold[$key]['type'] = 'datetime';
                        continue;
                    }

                    if(preg_match("/\d\d\d\d-\d\d-\d\d/", $column)) {
                        $this->column_scaffold[$key]['type'] = 'date';
                        continue;
                    }

                    $this->column_scaffold[$key]['type'] = 'text';
                }

            }

        }

    }

    /**
     * check if field must be hidden or not on show guesseur
     */
    public function is_hidden($key) {
        if(in_array($key, $this->hidden)) {
            return true;
        }
        return false;
    }

    public function handle(array $data = [])
    {
        $_ressourceClass = explode("\\", get_class($this));
        $_ressourceClass = $_ressourceClass[count($_ressourceClass)-1];

        $this->action = (Object) $data;

        $this->data = clone $this->action;
        $uid = $this->uid;
        if(isset($this->data->uid)) {
            $this->data->$uid = $this->data->uid;
        }
        unset($this->data->php_admin_resource);
        unset($this->data->uid);
        unset($this->data->php_admin_admin_edit);
        unset($this->data->php_admin_list);
        unset($this->data->php_admin_edit);
        unset($this->data->php_admin_update);
        unset($this->data->php_admin_create);
        unset($this->data->php_admin_show);
        unset($this->data->php_admin_delete);
        unset($this->data->php_admin_delete_multiple);
        unset($this->data->php_admin_save);
        unset($this->data->php_admin_search);
        unset($this->data->php_admin_export);

        /**
         * show the create form
         */
        // $this->model = $this->action->php_admin_resource;
        // $this->model = ucwords(str_replace('_', ' ', $this->model));

        if(isset($this->action->php_admin_create)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_singular() ."</h3>";

            $this->create();
        }

        /**
         * update
         */
        else if(isset($this->action->php_admin_update)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_singular() ."</h3>";
            // echo "<h3 class=' uk-heading-divider'>". $this->go_back() .Translation::translate('update'). " {$this->model} #". $this->action->uid ."</h3>";

            $this->validate(['uid'=>'']);

            $this->url = app('base_url').$this->url_set["update"];

            $this->update((Array) $this->data);

        }

        /**
         * shoe edit form
         */
        else if(isset($this->action->php_admin_edit)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_singular() ."</h3>";
            // echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->model #". $this->action->uid ."</h3>";

            $this->validate(['uid'=>'']);

            $this->url = app('base_url').$this->url_set["show"];

            $this->edit();
        }

        /**
         * show single object information
         */
        else if( isset($this->action->php_admin_show)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_singular() ."</h3>";
            // echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->model #". $this->action->uid ."</h3>";
            
            $this->validate(['uid'=>'']);
            
            $this->url = app('base_url').$this->url_set["show"];
        
            $this->show();
        }

        /**
         * delete single object
         */
        else if( isset($this->action->php_admin_delete)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_singular() ."</h3>";

            $this->validate(['uid'=>'']);

            $this->url = app('base_url').$this->url_set["delete"];

            $this->delete();
        }

        /**
         * delete multiple objected
         */
        else if( isset($this->action->php_admin_delete_multiple)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_plurial() ."</h3>";

            $this->validate(['selected_id'=>'No selected resources to delete']);

            foreach ($this->data->selected_id as $id) {
                $this->url = app('base_url').$this->url_set["delete"];
                $this->action->uid = $id;
                $this->delete(true);
            }

            echo "
            <div class='ui icon message success large'>
                <i onclick='window.history.back();' class='close icon'></i>
                <i class='thumbs up outline icon'></i>
                <div class='content'>
                    <div class='header'>
                    Task done !
                    </div>
                    <div class='divider'></div>
                    <p>Close this message to continue</p>
                </div>
            </div>";
        }

        /**
         * save object sent by create form
         */
        else if( isset($this->action->php_admin_save)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_singular() ."</h3>";

            $this->url = app('base_url').$this->url_set["create"];
            $this->save((Array)$this->data);
        }

        /**
         * export single or multiple object
         */
        else if( isset($this->action->php_admin_export)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_plurial() ."</h3>";

            $this->validate(['selected_id'=>'No selected resources to export']);

            $this->url = app('base_url').$this->url_set["show"];

            $this->export($this->data->selected_id);
        }

        /**
         * list fetched objects
         */
        else {
            echo "<h3 class=' uk-heading-divider'> {$this->get_name_plurial()}</h3>";
            return $this->list();
        }
    }

    public function get_columns() {
        return $this->column_scaffold;
    }

    protected function unset_guarded(string $value) {
        $del_ind = array_search($value, $this->guarded);
        if($del_ind != false)
            unset($this->guarded[$del_ind]);
    }

    protected function set_guarded(string $value) {
        $this->guarded[] = $value;
    }

    public function get_widgets_positions() {
        return $this->widgets_positions;
    }

    public function callbacks() {
        return $this->callbacks;
    }

    public function createable() {
        return $this->createable;
    }

    public function editable() {
        return $this->editable;
    }

    public function exportable() {
        return $this->exportable;
    }

    public function deleteable() {
        return $this->deleteable;
    }

    public function get_guarded() {
        return $this->guarded;
    }

    protected function list() {
        ListGuesser::render($this, $this->data_fetched);
    }

    protected function create() {
        if(!$this->createable) {
            Presenter::present('generics.unauthorised', []);
        }
        CreateGuesser::render($this);
    }

    protected function save(Array $data, $method = "POST") {

        $this->url = app('base_url').$this->url_set["create"];

        $this->api->callWith($this->url, $method, $data);

        $message = isset($this->api->response()->message) ? $this->api->response()->message : json_encode($this->api->response());


        if(isset($this->api->response()->success) && $this->api->response()->success == false) {
            Presenter::present("generics.error", [
                "error_info" => "Failed",
                "error_code" => 200,
                "error_description"=>"<code>".$message."</code>",
            ]);
        }

        Presenter::present("generics.success", [
            // "success_description"=>"<code>".$message."</code>",
        ]);
    }

    protected function edit() {
        if(!$this->editable) {
            Presenter::present('generics.unauthorised', []);
        }
        $this->url = app('base_url').$this->url_set["show"];
        EditGuesser::render($this, $this->action->uid);
    }

    protected function update(Array $data, $method = "PUT") {
        $this->url = preg_replace('/{id}/', $this->action->uid, $this->url);

        $this->api->callWith($this->url, $method, $data);

        if(isset($this->api->response()->success) && $this->api->response()->success == false) {
            Presenter::present("generics.error", [
                "error_info" => "Failed",
                "error_code" => 200,
                "error_description"=>"<code>".json_encode($this->api->response())."</code>",
            ]);
        }

        Presenter::present("generics.success", [
            // "success_description"=>"<code>".json_encode($this->api->response())."</code>",
        ]);
    }

    protected function show() {
        $this->url = app('base_url').$this->url_set["show"];
        ShowGuesser::render($this, $this->action->uid);
    }

    protected function delete(bool $verbose = false, $method = "DELETE") {

        if(!$this->deleteable) {
            Presenter::present('generics.unauthorised', []);
            exit();
        }

        $this->url = app('base_url').$this->url_set["delete"];
        $this->url = preg_replace('/{id}/', $this->action->uid, $this->url);

        $this->api->callWith($this->url, $method);


        // if(!$verbose) {
        echo "
        <div class='ui icon message success large'>
            <i class='close icon'></i>
            <i class='thumbs up outline icon'></i>
            <div class='content'>
                <div class='header'>
                ". 200 ." - Success
                </div>
                <div class='divider'></div>
                    <p>Deleted #{$this->action->uid}</p>
                </div>
            </div> ";
            //<code>".json_encode($this->api->response())."</code></p>";
    }

    protected function export(array $selected_ids) {

        if(!$this->exportable) {
            Presenter::present('generics.unauthorised', []);
        }


        $objects = [];
        $this->url = app('base_url').$this->url_set["show"];
        foreach ($selected_ids as $id) {
            $url = preg_replace('/{id}/', $id, $this->url);
            $this->api->get($url);
            $objects[] = $this->api->response();
        }

        $csv = CsvExportGuesser::render($this, $objects);

        echo "
        <div class='ui icon message ". app('primary_color') ."'>
            <i class='download icon'></i>
            <div class='content'>
                <div class='header'>
                File saved. Click the link bellow to download it before refreshing
                </div>
                <div class='divider'></div>
                <p class='footer ui label basic'>". $csv['file_link'] ."</p>
            </div>
        </div>
        ";
        // var_dump($objects);
    }

    public function url() {
        return $this->url;
    }

    public function paginate($key) {
        if(isset($this->paginate[$key])) {
            return $this->paginate[$key];
        }
        return null;
    }

    public function can_paginate() {
        return isset($this->paginate['data_field']);
    }

    /**
     * get the unique identify of the bluprint
     */
    public function uid_field() {
        return $this->uid;
    }

    private function validate(array $data) {
        foreach ($data as $param => $error_text) {
            if(empty($error_text)) {
                $error_text = "<code>$param</code> parameter is missing (or has an empty value) in the incoming request";
            }
            $req = new Request();
            if(!isset($req->$param) || empty($req->$param)) {
                Presenter::present('generics.error', [
                    'error_info'=>'Data missing',
                    'error_code'=>500,
                    'error_description'=> $error_text,
                ]);
            }
        }
    }

    public function get_model_name() {
        return $this->model;
    }

    private function get_name_singular() {
        return $this->name['one'] ?? $this->model;
    }

    private function get_name_plurial() {
        return $this->name['many'] ?? $this->model;
    }
}

?>
