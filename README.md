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
composer require mdantas/express-request
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
    // The finder need to be a method what return a Cake\ORM\Query object.
    return $this
        ->responseJson(
            $this->ExpressParams->search(
                $this->request,
                $this->Domains,
                'findDomainsByCompany', // Finder model method
                $companyId // Optional
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
### Details
I've talked about this help me a lot, right ? now, see how stuff work's.

Every requests is treat on ``ExpressRequest.ExpressParams`` this component
try understand what request need and with help of model he reproduce a response,
some operations or conditions by users can be dangerous or simple introduces requests
errors on application because request is wrong, because this reason, model express to
component what he can do, if can't did, nothing occur, and think, what we try search 'A'
on boolean typed data ? because this question some typed filters is implemented.



### Select only fields.
````php
// localhost/domains?props=name,price,created_at - Select only this three fields.
````
### Sorting data
for each field, have two values: asc, desc.
````php
// localhost/domains?sort[name]=asc
// localhost/domains?sort[name]=asc&sort[price]=desc
````

### Contained data
Sometimes we need to retrieve data with relationship, of course, it's can be easily.
````php
// http://localhost:8765/domains?nested=users,comments //Get data with relationship
````
Need to add complex or conditions to your related data, the simple awnser is: you can't, 
and you shouldn't try, if you need some complex related data see:
[CakePHP docs](https://book.cakephp.org/4/en/orm/associations.html#using-association-finders)

### Filters
Powerfully and secure filter's:

##### BooleanFilter
Filter by boolean values ? like `'true', 'false', '1', '0'`

##### NumberFilter
can filter data by numbers with some helps.
````php
// localhost/domains?price=100 - Exact by 100
// localhost/domains?price=100..200 - Between 100 and 200
// localhost/domains?price[lt|gt|lte|gte]=100 - filter less, great, less than or great than.
````

##### SearchFilter
This filter have four methods of work:
````php
class SearchFilter implements FilterTypeInterface
{
    use ProcessableFilterTrait;

    const PARTIAL_STRATEGY = 'partial';
    const START_STRATEGY = 'start';
    const END_STRATEGY = 'end';
    const EXACT_STRATEGY = 'exact';
    
    ...code
}
````
````php
new SearchFilter('name') - Exact is default
// localhost/domains?name=marcos - Exact by marcos
new SearchFilter('name', SearchFilter::PARTIAL_STRATEGY)
// localhost/domains?name=marcos - add a %value% by like method.
new SearchFilter('name', SearchFilter::START_STRATEGY) 
// localhost/domains?name=marcos - add a value% by like method.
new SearchFilter('name', SearchFilter::END_STRATEGY) 
// localhost/domains?name=marcos - add a %value by like method.
````

##### SearchInFilter
filter by value or group values.
````php
// localhost/domains?name=marcos - name in('marcos')
// localhost/domains?name=marcos,github - name in('marcos', 'github')
// localhost/domains?name[not]=marcos,github - name not in('marcos', 'github') 
````

##### SearchDateFilter
````php
// localhost/domains?created_at=2019 - by init of 2019 year.
// localhost/domains?created_at=2019-01 - by init of Jan/2019 year.
// localhost/domains?created_at=201903 - by init of Mar/2019 year.
// localhost/domains?created_at=2019-01-12 - by day 12 of Jan/2019 year.
// localhost/domains?created_at=20190315 - by day 15 of Mar/2019 year.
````

##### Custom Filter ?
By implementing ``FilterTypeInterface`` and if you want ``ProcessableFilterTrait``
you can create a filter for what you need.

#### About Component
The component have a list of configurations values for work above
query params, without ``reserved`` keys he try to filter content, if model
accept it.
````php
[
    'pagination' => true,
    'maxSize' => 100, // Max number of per page.
    'size' => 20, // default numbers of items per page
    'ssl' => true, //generate routes with ssl by default
    'reserved' => [  // If you need to use one o more of this keywords, change to alias.
        'size' => 'size',
        'page' => 'page',
        'props' => 'props',
        'nested' => 'nested',
        'sort' => 'sort'
    ]
];
````
Change some configs...
````php
//Controller/AppController.php
public function initialize()
{
    // ...code
    $this->loadComponent('ExpressRequest.ExpressParams', [
        'ssl' => false,
        'maxSize' => 30,
        'reserved' => [
            'props' => 'fields'
        ]
    ]);
}
````
