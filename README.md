> # PHP API ADMIN +, Your API, Your rules

### What is "PHP API ADMIN +"

"PHP API ADMIN +" is an admin panel + CRUD generator fully made 100% in PHP. It generate the CRUD based on the responses sent by your api. That means, "PHP API ADMIN +" it is not a part of you api. So create your API using any language or framework, and scaffold your CRUD anyway.

### Base configurations

```json
{
    "base_url" : "http://chrisserver.me:8000/api",
    "entrypoint" : "dashboard",
    "primary_color" : "blue",
    "colorful" : true,
    "auth_type" : "Bearer",
    "must_auth" :true,
    "login_endpoint" : "/login",
    "login_method" : "GET",
    "app_name" : "Rent App",
    "lang": "en",
    "icon":""
}
```



### Serve your project

Basic command - serve on 127.0.0.1 on port 5000

```shell
php adminer serve
# PHP adminer development server started: http://127.0.0.1:5000
```
You can use `--host`  and `--port` options to specify the host and port on which you want to serve your project

```bash
php adminer serve --host --port 
```

Ex

```shell
php adminer serve --host='192.168.1.129'
# PHP adminer development server started: http://192.168.1.129:5000
```

```shell
php adminer serve --host='192.168.1.129' --port='8888'
# PHP adminer development server started: http://192.168.1.129:8888
```

```shell
php adminer serve --port='8888'
# PHP adminer development server started: http://127.0.0.1:8888
```



### Blueprint

```shell
php adminer create blueprint blueprint_name
```

Ex

```shell
php adminer create User
```

Will create new class located in `app/reources/User.php`

#### Complete the blueprint class

Once the blueprint created, complete the `$url_set` class method where it must be specified the endpoints for listing, showing single element, deleting, creating/saving and updating. To specified that `PHP API ADMIN +` must add the concerned `id`, just make it know by adding `{id}` at the place.

As the `base_url` is set in the `env` file, `PHP API ADMIN +` will append each endpoint to it.

```php
protected $url_set = [
    "list"=>"/users",
    "show"=>"/users/{id}",
    "delete"=>"/users/{id}",
    "create"=>"/users/store",
    "update"=>"/client/update/{id}"
];
```

Let's assume the `base_url` is defined as follow 

```json
{
    // ...
	"base_url":"http://api.mydomain.com",
    // ...
}
```

For listing, `PHP API ADMIN +` will call `http://api.mydomain.com/users`

### Column Scaffolding

```php
protected $column_scaffold =  [
    ..., 
    'column_name' => [
        'type' => '', # object | array | text | password | date | datetime
        'css_class' => '', # css class name - must be implemented to work
        'tooltip' => '', # tooltip
        'replacements' => [], # replacement of values
        'name' => '', # name to show up on table or labels
        'variable_name' => '', # form variable name
        'fetch_url' => '', # fetch url for checkboxes or dropdowns
        'fetch_method' => '', # fetch method for checkboxes or dropdowns
        'displayed_text' => '', # displayed field for checkboxes or dropdowns
        'relation_field' => '', # relationship binder for checkboxes or dropdowns
        'escape_edit' => '', # is editable or not
        'values' => [], # values if not from url
        'sub_property' => '', # value to look for in a sub element
        'visible' => '', # is visible in table or not
        'escape_create' => '',
        'labeled' => '',
        'image' => '',
    ],
    ...,
]
```





### Resource

- Basic resource

```shell
php adminer create resource resource_name
```

- Hidden resource

```shell
php adminer create resource resource_name --hidden
```



```php
<?php

namespace App\Resources;

use Abstracts\Resource;
use Services\Presenter;

class Profile extends Resource {

    public function handle(array $data = [])
    {
        // code
    }
}

?>
```



Resources and Blueprints must extend `Abstracts\Resource` interface and overwrite the `handle` method.

For any action, write the rules in the `handle` method. Use the `$request` static attribute of `Request` class and handle actions request based. 

#### The `Resource` class

Resources are single page loaded dynamically blueprinting an API resource or managing a simple custom handled resource.

You do not need to load the page manually or specify which resource class to load. `PHP API ADMIN +` uses class reflection to determine which class to load, and pass the request data to the resource, which are accessible in the `handle` method of the concerned loaded class. In the case `PHP API ADMIN +` can't find the class, an error 404 will show up.

To load resources, `PHP API ADMIN +` will lookup in the `app/resources` folder and will base on the first passed parameter. The second parameter (`Array`) can be used to pass data to the concerned data.

```php
Resource::load('user'); // will look for App\Resource\User class
```



### Presenter

`Presenter` class loads `PHP` file content in the current page. The file,to load must be in the `presenters` folder. Use commas to specified that the file to load is in a folder.

Ex:

Let's assume we want to load the login page that is located directly in the `presenters` folder

```php
Presenter::present('login');
```

Now let's say we want present the file `bile.php` located in the `sales` folder in `presenters`

```php
Presenter::present('sales.bill');
```

It can arrive that we want to pass some information to the page we are loading. To do that, just add a second parameter to the `present` static method of `Presenter` class. The second parameter must be an array;

```php
Presenter::present('profile', [
    'firstname'=>'John', 
    'lastname'=>'Doe', 
    'role'=>'Editor'
]);
```

To access these data, use the `$data` in the targeted file.

### Router

Router handle the incoming request through its `load` static method. It look for `php_admin_resource` request parameter and pass it to `load` static method of `Resource` class. It the supposed parameter is not set, then an error 400 will show up. You will likely never need to use the `load` static method.

You can use the `redirect` static method to redirect from one resource to another by passing the resource name.

```php
Router::redirect('dashboard');
```



### Request

The `Request` class create intercept the incoming request and make an object  of it. Which object is accessible by any part of the project through the it `request` attribute.

```php
Request::$request; // Object
Request::$request->email; 
```



### Auth

`Auth` class is used to authenticate the use user who want to log in. `Auth` attempt authentication by sending `email` and `password` to the `login` endpoint specified on the basic configuration in`.env` file.

If you authenticate user using different type of variable name, not `email` or `password`, your are free to change it.

Once the API respond with data, `Auth` will look for `token` key in the response through  a `deep_walk` function (`lib/deep_walk`); If you are not using `token` as key of authentication, you are again free to change it directly in `Auth` class. 

### Authenticateable

`PHP API ADMIN +` uses the `Authenticateable` class to determiner if it might authenticate the user or not before pursuing. Of course, you do not need to change anything, else in `.env` file by setting the `must_auth` `true` or `false`.

### API

```php
$api = new API();
$api->get($url, Array $data = []);
$api->post($url, Array $data = []);
$api->put($url, Array $data = []);
$api->delete($url, Array $data = []);
$api->callWith($url, $method ,Array $data = []);

$api->response(); // return the response of curl
```



```php
$api->header('Content-Type', 'application/json');
```
