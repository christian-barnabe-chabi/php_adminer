<?php
namespace Abstracts;

use Abstracts\Resource;
use Services\Scaffolders\CreateGuesser;
use Services\Scaffolders\CsvExportGuesser;
use Services\Scaffolders\EditGuesser;
use Services\Scaffolders\ListGuesser;
use Services\Scaffolders\ShowGuesser;
use Config\PublicResource;
use Exception;
use Services\API;
use Services\Auth;
use Services\Presenter;
use Services\Request;
use Services\Resource as ServicesResource;
use Services\Router;
use Services\Session;
use Services\Translation;

abstract class BaseBlueprint extends Resource {
    protected $url;
    protected $endpoints;
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $columns = [];
    protected $data_fetched;
    protected $must_be_auth = true;
    protected $api;
    protected $createable = true;
    protected $editable = true;
    protected $deleteable = true;
    protected $exportable = true;
    protected $alwaysExportAll = false;
    protected $uid = 'id';
    protected $model = null;
    protected $hidden = [];
    protected $title = [];
    protected $callbacks = [];
    protected $widgets_positions = [];
    protected $data;
    protected $action; // request action -
    protected $online_paginate = false;
    protected $local_paginate = true;
    protected $embeded = false;
    protected $endpoints_methods = [
        "create" => "POST",
        "read" => "GET",
        "update" => "PUT",
        "delete" => "DELETE",
    ];

    public function __construct()
    {

        Translation::define(422, [
            "fr" => "Assurez-vous de remplir correctement tous les champs",
            "en" => "Make sure you fill in all the fields correctly",
        ]);

        $data = clone Request::$request;
        $this->action = clone (Object) $data;
        
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
            $this->api->header("Authorization", app('authType').' '.Auth::token());
        }

        if($this->embeded || isset(Request::$request->php_admin_update) || isset(Request::$request->php_admin_save) || isset(Request::$request->php_admin_delete) || (isset(Request::$request->php_admin_export) && !$this->alwaysExportAll) ) {
            // updating or saving or deleting or exporting
        } else {

            $this->url = app('baseUrl').$this->endpoints["list"];

            // pagination
            if($this->online_paginate) {

                $this->data_fetched = $this->api->callWith($this->url, $this->endpoints_methods()->read, (array)$data)->response();
                // $this->data_fetched = $this->api->get($this->url, (array)$data)->response();

                if(isset($this->online_paginate['count'])) {
                    $key = $this->online_paginate['count'];
                    if(isset($this->data_fetched->$key)) {
                        $this->online_paginate['count'] = $this->data_fetched->$key;
                    }
                    else {
                        $this->online_paginate['count'] = null;
                    }
                } else {
                    $this->online_paginate['count'] = null;
                }

                if(isset($this->online_paginate['last_page'])) {
                    $key = $this->online_paginate['last_page'];
                    if(isset($this->data_fetched->$key)) {
                        $this->online_paginate['last_page'] = $this->data_fetched->$key;
                    } else {
                        $this->online_paginate['last_page'] = null;
                    }
                } else {
                    $this->online_paginate['last_page'] = null;
                }

                if(isset($this->online_paginate['data_field'])) {
                    $data_field = $this->online_paginate['data_field'];
                    $this->data_fetched = $this->data_fetched->$data_field;
                } else {
                    exit('error');
                }
            } else {
                $this->data_fetched = $this->api->get($this->url)->response();
            }
            

            try {
                // if(PublicResource::isPublic(Router::route())) {
                    
                // } else {
                    // if(!is_array($this->data_fetched)) {
                    //     throw new Exception("Exception: An array is required for listing");
                    // }
                    // var_dump($this->data_fetched);
                    if(is_array($this->data_fetched) && isset($this->data_fetched[0])) {
                        $keys = $this->data_fetched[0] ?? null;
                    } else {
                        $keys = null;
                    }
                // }


                // exit();
                if(isset($keys)) {
                    $keys = $keys != null ? (get_object_vars($keys)) : [];
                }

            } catch (Exception $e) {
                Presenter::present('generics.error', [
                    'url' => $this->url,
                    'error_code'=>500,
                    'error_description' => $e->getMessage().'<br>Make sure the data is not null and is an array?'
                ]);
                exit('Error');
            }

            if (empty($this->columns)) {
                if($keys) {
                    foreach ($keys as $key=>$value) {
                        if (is_object($this->data_fetched[0]->$key) or is_array($this->data_fetched[0]->$key)) {
                            continue;
                        }
    
                        $this->columns[$key] = [];
                    }
                } else{
                    // echo "<script> alert('Can\'t autodetect resource schema'); </script>";
                }
            }

            // auto define type for columns
            foreach ($this->columns as $key => $value) {
                $type = null;
                $exploded = explode('.', $key);
                $new_key = $key;

                if(count($exploded)>1) {
                    $new_key = $exploded[0];
                }

                if(!empty($value['type'])) {
                    $this->columns[$key]['type'] = $value['type'];
                }
                else
                {
                    if(!isset($keys[$new_key])) {
                        $this->columns[$key]['type'] = 'text';
                        continue;
                    }

                    $column = ($keys[$new_key]);

                    if(is_object($column)) {
                        $this->columns[$key]['type'] = 'object';
                        continue;
                    }
                    if(is_array($column)) {
                        $this->columns[$key]['type'] = 'array';
                        continue;
                    }
                    if(is_numeric($column)) {
                        $this->columns[$key]['type'] = 'number';
                        continue;
                    }

                    if(preg_match("/\d\d\d\d-\d\d-\d\d\s\d\d:\d\d:\d\d/", $column)) {
                        $this->columns[$key]['type'] = 'datetime';
                        continue;
                    }

                    if(preg_match("/\d\d\d\d-\d\d-\d\d/", $column)) {
                        $this->columns[$key]['type'] = 'date';
                        continue;
                    }

                    $this->columns[$key]['type'] = 'text';
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
        unset($this->data->php_admin_action);

        /**
         * update
         */
        if(isset($this->action->php_admin_update)) {
            $this->validate(['uid'=>'']);

            $this->url = app('baseUrl').$this->endpoints["update"];

            $this->update((Array) $this->data);

        }

        /**
         * show single object information
         */
        else if( isset($this->action->php_admin_show)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() ."<i class='ui folder open outline icon'></i> | ". $this->get_name_singular() ." | ". Translation::translate('profile') ."</h3>";
            
            $this->validate(['uid'=>'']);
            
            $this->url = app('baseUrl').$this->endpoints["show"];
        
            echo $this->show();
        }

        /**
         * delete single object
         */
        else if( isset($this->action->php_admin_delete)) {

            $this->validate(['uid'=>'']);

            $this->url = app('baseUrl').$this->endpoints["delete"];

            $this->delete();
        }

        /**
         * delete multiple objected
         */
        else if( isset($this->action->php_admin_delete_multiple)) {
            // echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_plurial() ."</h3>";

            $this->validate(['selected_id'=>Translation::translate('no_selected_ressources')]);

            foreach ($this->data->selected_id as $id) {
                $this->url = app('baseUrl').$this->endpoints["delete"];
                $this->action->uid = $id;
                $this->delete(true);
            }

            Presenter::present('generics.success');
            unset(Request::$request->php_admin_delete_multiple);
            echo ServicesResource::call(get_class($this), (array)Request::$request);
            exit();
        }

        /**
         * save object sent by create form
         */
        else if( isset($this->action->php_admin_save)) {
            // echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_singular() ."</h3>";

            $this->url = app('baseUrl').$this->endpoints["create"];
            $this->save((Array)$this->data);
        }

        /**
         * export single or multiple object
         */
        else if( isset($this->action->php_admin_export)) {
            echo "<h3 class=' uk-heading-divider'>". $this->go_back() . $this->get_name_plurial() ."</h3>";

            $this->validate(['selected_id'=>Translation::translate('no_selected_ressources')]);

            $this->url = app('baseUrl').$this->endpoints["show"];

            $this->export($this->data->selected_id);
        }

        /**
         * list fetched objects
         */
        else {
            echo "<h3 class=' uk-heading-divider uk-margin-remove-top'><i class='ui folder open outline icon'></i> | {$this->get_name_plurial()} | ". Translation::translate('list') ."</h3>";
            echo $this->list();
        }
    }

    public function get_columns() {
        return $this->columns;
    }
    
    public function set_columns(array $columns) {
        return $this->columns = $columns;
    }

    public function is_embeded() {
        return $this->embeded;
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

    public function editable($data = NULL) {
        return $this->editable;
    }

    public function exportable() {
        return $this->exportable;
    }

    public function deleteable($data = NULL) {
        return $this->deleteable;
    }

    public function editPreRenderConfig($data) {
        // code
    }
    
    public function createPreRenderConfig() {
        // code
    }

    public function get_guarded() {
        return $this->guarded;
    }

    public function list() {
        Session::set('create_old_data', null);
        return ListGuesser::render($this, $this->data_fetched);
    }

    public function create() {
        if(!$this->createable) {
            Presenter::present('generics.unauthorised');
            exit();
        }
        return CreateGuesser::render($this);
    }

    // create
    protected function save(Array $data, $external_call = false) {

        $method = $this->endpoints_methods()->create;

        $this->url = app('baseUrl').$this->endpoints["create"];

        $this->api->callWith($this->url, $method, $data);

        
        
        $message = isset($this->api->response()->message) ? $this->api->response()->message : json_encode($this->api->response());
        
        if(isset($this->api->response()->success) && $this->api->response()->success == false) {
            
            Session::set('create_old_data', $data);
            
            
            Presenter::present("generics.error_saving", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 200,
                "error_description"=>"<span>".$message."</span>",
                "php_admin_resource_class" => get_class($this),
                "php_admin_resource_class_name_singular" => $this->get_name_singular(),
            ]);
            exit('HERE');

            Session::set('create_old_data', null);
        }

        unset(Request::$request->php_admin_save);
        if($external_call == false) {
            echo ServicesResource::call(get_class($this), (array)Request::$request);
        } else {
            if($external_call === 'redirect') {
                return Presenter::present("generics.success_back", [
                    // "success_info" => Translation::translate('success'),
                    // "success_code" => 200,
                    // "success_description"=>"<span>".$message."</span>",
                ]);
            }
            return Presenter::present("generics.success", [
                // "success_info" => Translation::translate('success'),
                // "success_code" => 200,
                // "success_description"=>"<span>".$message."</span>",
            ]);
        }

        Presenter::present("generics.success", []);
    }

    // update
    protected function update(Array $data, $external_call = false) {

        $method = $this->endpoints_methods()->update;

        $this->url = app('baseUrl').$this->endpoints["update"];

        $this->url = preg_replace('/{id}/', $this->action->uid, $this->url);

        $this->api->callWith($this->url, $method, $data);

        $message = isset($this->api->response()->message) ? $this->api->response()->message : json_encode($this->api->response());

        if(isset($this->api->response()->success) && $this->api->response()->success == false) {
            $data[$this->uid] = $data['uid'];
            return Presenter::present("generics.error_updating", [
                "error_info" => Translation::translate('failure'),
                "error_code" => 200,
                "error_description"=>"<span>$message</span>",
                "php_admin_resource_class" => get_class($this),
                "php_admin_resource_class_name_singular" => $this->get_name_singular(),
                "data_edited" => $data
            ]);
        }
        
        $routeDetail = ServicesResource::routeDetail($_SERVER['HTTP_REFERER']);

        if($routeDetail->action != 'show') {

            unset(Request::$request->php_admin_update);
            if($external_call == false) {
                echo ServicesResource::call(get_class($this), (array)Request::$request);
                return Presenter::present("generics.success", [
                    // "success_info" => Translation::translate('success'),
                    // "success_code" => 200,
                    // "success_description"=>"<span>".$message."</span>",
                ]);
            } 
            else {
                return Presenter::present("generics.success", [
                    // "success_info" => Translation::translate('success'),
                    // "success_code" => 200,
                    // "success_description"=>"<span>".$message."</span>",
                ]);
            }

        } 
        else if($routeDetail->action == 'show') {
            echo ServicesResource::call(get_class($this), (array)Request::$request, 'show');
            return Presenter::present("generics.success", [
                // "success_info" => Translation::translate('success'),
                // "success_code" => 200,
                // "success_description"=>"<span>".$message."</span>",
            ]);
        }

        // changed
        return Presenter::present("generics.update_success", [
            "success_info" => Translation::translate('success'),
                "success_code" => 200,
                "success_description"=>"<span>".$message."</span>",
        ]);

    }

    // read
    public function show() {
        $this->url = app('baseUrl').$this->endpoints["show"];
        return ShowGuesser::render($this, $this->action->uid);
    }

    // delete
    protected function delete(bool $verbose = false) {

        $method = $this->endpoints_methods()->delete;

        if(!$this->deleteable) {
            return Presenter::present('generics.unauthorised');
        }

        $this->url = app('baseUrl').$this->endpoints["delete"];
        $this->url = preg_replace('/{id}/', $this->action->uid, $this->url);

        $this->api->callWith($this->url, $method);
        
        $message = isset($this->api->response()->message) ? $this->api->response()->message : json_encode($this->api->response());

        if(!$verbose) {
            unset(Request::$request->php_admin_delete);
            echo ServicesResource::call(get_class($this), (array)Request::$request);
            return Presenter::present('generics.success', [
                // "success_info" => Translation::translate('success'),
                // "success_code" => 200,
                // "success_description"=>"<span>".$message."</span>",
            ]);
        } else {
            return ;
        }

    }

    protected function export(array $selected_ids) {

        if(!$this->exportable) {
            Presenter::present('generics.unauthorised');
        }


        $objects = [];
        $this->url = app('baseUrl').$this->endpoints["show"];
        foreach ($selected_ids as $id) {
            $url = preg_replace('/{id}/', $id, $this->url);
            $this->api->get($url);
            $objects[] = $this->api->response();
        }
        
        if($this->api->response()->success == false) {
            $objects = (array)$this->data_fetched;
        }

        $csv = CsvExportGuesser::render($this, $objects);

        if(isset($csv['file_link'])) {
            echo "
            <div class='ui icon message ". app('primaryColor') ."'>
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
        } else {
            echo "
            <div class='ui icon message red'>
                <div class='header'>
                Error exporting data
                </div>
            </div>
            ";
        }

    }

    public function url() {
        return $this->url;
    }

    public function edit($data) {
        if(!$this->editable) {
            return "";
            // Presenter::present('generics.unauthorised');
        } else {
            return EditGuesser::render($this, (Object) $data);
        }
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
                    'error_info'=>Translation::translate('data_missing'),
                    'error_code'=>1782,
                    'error_description'=> $error_text,
                ]);
            }
        }
    }

    public function get_model_name() {
        return $this->model;
    }

    public function get_name_singular() {
        return ucfirst($this->title['one'] ?? $this->model);
    }

    public function get_name_plurial() {
        return ucfirst($this->title['many'] ?? $this->model);
    }

    public function local_paginate() {
        return $this->local_paginate;
    }

    public function endpoints() {
        return (object)$this->endpoints;
    }
    
    public function endpoints_methods() {
        return (object)$this->endpoints_methods;
    }


    // server paginate
    public function paginate($key) {
        if(isset($this->online_paginate[$key])) {
            return $this->online_paginate[$key];
        }
        return null;
    }

    public function can_paginate() {
        return isset($this->online_paginate['data_field']);
    }

}

?>
