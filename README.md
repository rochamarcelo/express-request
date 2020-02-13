# ExpressRequest plugin for CakePHP

## Something about package
This package help me a lot with query url, maybe he help you, many time
when i try to make api's for long time, i tried to make complex url, complex query
parameters and i felt frustrated, because something not make sense before i did.
after poc's and poc's and good companion's help, i've come to this conclusion.

### How this package can help you
Instead of make more and more complex url query params or many endpoints,
let's try produce what really model can be to customers, goto code.

### Installation
You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require mdantas/ExpressRequest
```
Add plugin to our ``Application.php``

````php
public function bootstrap(): void
{
    //... other codes.
    $this->addPlugin('ExpressRequest');
}
````

### Express your filter's on model
Our table object needed implement ``ExpressRepositoryInterface``
make new methods.
````php
//Model/Table/DomainsTable.php
class ModelTable extends Table implements ExpressRepositoryInterface {
    //...code...
    public function getQuery(): Query
    {
        // Implement custom query by your.
        return $this->find('all');
    }
    
    public function getFilterable(): FiltersCollection
    {
        return new FiltersCollection([
            new BooleanFilter('active'),
            new SearchFilter('name', SearchFilter::START_STRATEGY),
            new SearchDateFilter('created_at'),
            new NumberFilter('price'),
            new SearchInFilter('type')
        ]);
    }

    public function getSelectables(): array
    {
        return [
            'name',
            'created_at',
            'price',
            'type',
            'active'
        ];
    }
}
````
### Controller
In ``Controller/AppController.php`` load component called: ``ExpressRequest.ExpressParams``
and now, add some code to our DomainsController.
````php
//Controller/DomainsController.php

public function index()
{
    return $this
        ->responseJson(
            $this->ExpressParams->search(
                $this->request,
                $this->Domains
            )
        );
}
````
``*Of course, can't remember add route to this controller.``
````php
//routes.php
$routes->scope('/', function (RouteBuilder $builder) {
    $builder->get('/domains', ['controller' => 'Domains', 'action' => 'index']);
}
````
### Request
Let's see how you ``/domains`` endpoint now is more friendly for the requests/resources.
Open browser: ``http://localhost:8765/domains?price=140..3000&sort[price]=asc&type[not]=profit&size=1``

````php
// http://localhost:8765/domains?price=140..3000&sort[price]=asc&type[not]=profit&size=1

{
  "data": [
    {
      "id": "dee7b40b-df70-33b7-90e0-1d13a4b13693",
      "name": "Azevedo e Pacheco",
      "company_id": "2130a414-828a-4ae3-a3a6-5f153fe25ad8",
      "city_id": 2507705,
      "created_at": "2010-02-12T21:18:44+00:00",
      "modified_at": null,
      "price": "291.600",
      "type": "purchase"
    }
  ],
  "meta": {
    "total": 103,
    "per_page": 1,
    "current_page": 1,
    "last_page": 103,
    "first_page_url": "http://localhost:8765/domains?price=140..3000&sort%5Bprice%5D=asc&type%5Bnot%5D=profit&size=1&page=1",
    "next_page_url": "http://localhost:8765/domains?price=140..3000&sort%5Bprice%5D=asc&type%5Bnot%5D=profit&size=1&page=2",
    "last_page_url": "http://localhost:8765/domains?price=140..3000&sort%5Bprice%5D=asc&type%5Bnot%5D=profit&size=1&page=103",
    "prev_page_url": null,
    "path": "http://localhost:8765/domains",
    "from": 1,
    "to": 1
  }
}
````
