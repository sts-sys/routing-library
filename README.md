# STS Routing Library

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

STS Routing Library este o librărie PHP simplă și flexibilă pentru gestionarea rutelor HTTP într-o aplicație web. Aceasta oferă funcționalități avansate precum suport pentru middleware, grupuri de rute, rute denumite, generare automată de URL-uri și parametri opționali.

## Caracteristici

- **Routing flexibil**: Suportă toate metodele HTTP comune (GET, POST, PUT, DELETE).
- **Middleware în lanț**: Aplică middleware-uri pentru rute individuale sau grupuri de rute.
- **Grupuri de rute cu prefix**: Organizează rutele sub prefixuri comune pentru o mai bună structură.
- **Rute denumite**: Definire de rute cu nume pentru generarea URL-urilor și verificarea existenței.
- **Generare automată de URL-uri**: Crează URL-uri dinamic pe baza numelui rutei și a parametrilor.
- **Parametri opționali**: Definire și utilizare de parametri opționali în rute.
- **Cachare rute**: Cache pentru rute pentru performanță optimizată.
- **Debugging și logging**: Mod de debugging și logare a erorilor pentru dezvoltare.

## Instalare

1. Clonează acest repository sau descarcă arhiva ZIP.
2. Instalează dependențele folosind Composer:

    ```bash
    composer install
    ```

3. Generează autoload-ul:

    ```bash
    composer dump-autoload
    ```

## Utilizare

### 1. Definirea rutelor

Puteți defini rute simple și complexe folosind metodele `get`, `post`, `put`, și `delete`.

```php
require_once 'autoload.php';

use sts\routes\Router;

$router = new Router();

// Definirea unei rute GET
$router->get('/home', function () {
    echo 'Welcome to the home page!';
});

// Definirea unei rute POST
$router->post('/login', 'AuthController@login');

// Dispatcher automat
$router->dispatch();
```
