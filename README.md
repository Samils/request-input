# RequestInput

 HTTP Request input/body parser for php rest API.

 This should be used as a body parser for an http request in a rest API;

 Assuming that an http request can be sent with some information in different formats like: XML, JSON, YAML, and others; Request Input accepts all of the above, and can be complemented by creating a custom parser.

# Install

- From the prompt command line [cmd]

```bash
composer require php_modules/request-input
```

- composer.json

```json
{
  "require": {
    "php_modules/request-input": "^1.0.*"
  }
}
```

# USE 

## Without php-module

```php
use Sammy\Packs\RequestInput;

$request = new RequestInput;

$body = $request->getRequestInput();
```

## With php-module

```php
$request = requires ('request-input');

$body = $request->getRequestInput();
```

The '$body' variable should be evaluated acording to the request body.

Below is a list of data type matches supported by requestInput.

### JSON

```json
{
  "user": {
    "name": "Foo",
    "email": "foo@bar",
    "site": "foobar.baz"
  }
}
```

### YAML

```yaml
user:
  name: Foo
  email: foo@bar
  size: foobar.baz
```

### XML

```xml
<user>
  <name>Foo</name>
  <email>foo@bar</email>
  <site>foobar.baz</site>
</user>
```

Any of the request body below should be send with a header acording to the used type; so RequestInput may parse the sent request body to be used in php.

Frameworks like @Samils should parse it automatically and provide the request body as a property of a '$request' object.

Using it inside a @Samils Controller should be done as bellow:

```php
namespace Application\Controller;

class SomeController extends SamiController {
  public function someAction () {
    # $requestBody = params;
    $user = params['user'];  
  }
}
```

So, the basical way for using RequestInput should aid you parsing any request body sent to a rest API.

...
