> # PHP ADMINER, Your API, Your rules

### About "PHP ADMINER"

"PHP ADMINER" is an admin panel generator fully made in PHP. It generate the views based on the responses sent by your api. That means, "PHP ADMINER" is not a part of you api. So create your API using any language or framework, and scaffold your CRUD view in no time.

### Installation

1. Clone the project

   ```sh
   git clone https://github.com/Chris-Nilson/php_adminer.git
   ```

2. Move to php_adminer directory

   ```sh
   cd php_adminer
   ```

3. Make a copy of `.env_example.json` to  `.env.json`

   ```sh
   cp .env_example .env.json
   ```

4. Edit your `.env.json` file to fit your needs

   ```sh
   vim .env.json
   ```

5. Serve your project

   ```sh
   php adminer serve
   ```

   

### Base configurations

```json
{
    "base_url" : "http://chrisserver.me:8000/api",
    "entrypoint" : "dashboard",
    "primary_color" : "green",
    "colorful" : true,
    "auth_type" : "Bearer",
    "must_auth" :false,
    "login_endpoint" : "/login",
    "login_method" : "GET",
    "app_name" : "PHP Adminer v2.0",
    "lang": "en",
    "icon":"",
    "date_format" : "d/m/Y",
    "debug":true
}
```



### Serve an adminer project

The basic command `php adminer serve` will serve the project in `localhost` on port `5000`

```sh
php adminer serve
# PHP adminer development server started: http://127.0.0.1:5000
```
You can use `--host`  and `--port` options to specify the host and port on which you want to serve your project

```bash
php adminer serve --host=the_host_ip --port=the_port_to_serve_on
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

Will create new class located in `app/resources/User.php`

When creating a new blueprint, a new line will be added to the menu definition using the class `MenuScaffold` in  `MenuScaffold.php` 

```php
ResourceScaffold::define('Users', 'user', 'angle right');
```



#### Complete the blueprint class

Once the blueprint created, complete the `$endpoints` class attribute where it must be specified the endpoints for listing, showing single element, deleting, creating/saving and updating. To specified that `PHP ADMINER` must add the concerned `id`, just make it know by adding `{id}` at the place.

As the `base_url` is set in the `env` file, `PHP ADMINER` will append each endpoint to it.

```php
protected endpoints = [
    "list"=>"/users",
    "show"=>"/users/{id}",
    "delete"=>"/users/{id}",
    "create"=>"/users/store",
    "update"=>"/client/update/{id}"
];
```

Let's assume the `base_url` is defined as follow in `.env.json`

```json
{
    // ...
	"base_url":"http://api.mydomain.com",
    // ...
}
```

For listing, `PHP Adminer` will call `http://api.mydomain.com/users`

### Column Scaffolding

```php
protected $column_scaffold =  [
    ..., 
    'column_name' => [
        'type' => '', # object | array | text | password | date | datetime
        'tooltip' => '', # tooltip
        'replacements' => [], # replacement of values
        'name' => '', # name to show up on table or labels
        'variable' => '', # form variable name
        'endpoint' => '', # fetch url for checkboxes or dropdowns
        'method' => '', # fetch method for checkboxes or dropdowns
        'relation' => '', # relationship binder for checkboxes or dropdowns
        'editable' => '', # is editable or not
        'values' => [], # values if not from url
        'visible' => '', # is visible in table or not
        'createable' => '',
        'labeled' => '', # when true, will put the cell value in a labeled tag
        'image' => '', # specify that image type [rounded, circular, avatar]
        'required' => '', # specify if the field is required when creating or updating 
        'disabled' => '',	# specify if the field is disabled 
        'option_image' => '', # add an image in the dropdown (when object) 
        'callback' => '', # a closure to call on each value (method must exists)
    ],
    ...,
]
```



Not all of these specification you will need. Just use what you need

### Resource

- Basic resource

```shell
php adminer create resource resource_name
```

- Visible resource (will be added at the left side menu)

```shell
php adminer create resource resource_name --visible
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

For any action, write the rules in the `handle` method. Use the `$request` static attribute of `Request` class to handle actions on request based. 

#### The `Resource` class

Resources are single page loaded dynamically blueprinting an API resource or managing a simple custom handled resource.

You do not need to load the page manually or specify which resource class to load. `PHP ADMINER` uses class reflection to determine which class to load, and pass the request data to the resource, which are accessible in the `handle` method of the concerned loaded class. In the case `PHP ADMINER` can't find the class, a 404 error  will show up.

To load resources, `PHP ADMINER` will look up in the `app/resources` folder and will base on the first passed parameter. The second parameter (`Array`) can be used to pass data to the concerned data.

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

### Public Routes

By default, all routes will be private when `must_auth` is true. That means, user must be authenticated before accessing the main screen otherwise the login page will be shown up. Sometime you will need to access certain pages without being authenticated. To do so, edit the `routes`  attribute of `PublicResource` class. Add all the routes you want to make public

```php
private static $routes = [
    '/password_reset', '/logout',
];
```

### Request

The `Request` class create intercept the incoming request and make an object  of it. Which object is accessible by any part of the project through the it `request` attribute.

```php
Request::$request; // Object
Request::$request->email; 
```

### Auth

`Auth` class is used to authenticate the use user who want to log in. `Auth` attempt authentication by sending `email` and `password` to the `login` endpoint specified on the basic configuration in`.env.json` file.

If you authenticate user using different type of variable name, not `email` or `password`, your are free to change it.

Once the API respond with data, `Auth` will look for `token` key in the response through  a `deep_walk` function (`lib/deep_walk`); If you are not using `token` as key of authentication, you are again free to change it directly in `Auth` class. 

### Authenticateable

`PHP ADMINER` uses the `Authenticateable` class to determiner if it might authenticate the user or not before pursuing. Of course, you do not need to change anything, else in `.env.json` file by setting the `must_auth` `true` or `false`.

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

### Translation

Adminer provide a multi language support dictionary based. By default, adminer will put  errors text, buttons text, ... in English.  To change this, change the `lang` value in `.env.json` to what you want.

You can change or add new translation using the `.translations.json` file. It is structured as follow

```json
{
    "word" {
    	"lang1" : "value for lang1",
    	"lang2" : "value for lang2"
	}
}
```



Example:

 ```json
"create": {
    "fr": "Nouveau",
    "en": "Create"
},
"delete": {
    "fr": "Supprimer",
    "en": "Remove"
},
"show": {
    "fr": "Afficher",
    "en": "Show"
},
"edit": {
    "fr": "Modifier",
    "en": "Edit"
},
 ```

To get a translation use `translate` static method of `Translation` class

```php
Translation::translate("create")
```

If the definition on the set language doesn't exist in the `.translations.json` file, the values passed to `translate` method will return without any change.



PHP adminer comes with some words in `.translations.json` file in English and French. You can change them by adding others languages or updating the values of already existing languages. To keep adminer working properly, it is not recommended to changes these words.

