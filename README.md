# STS Routing Library

![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)
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
    ```bach
    "sts/routing-library": "^v1.0",
    ```

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

### 2. Utilizarea Middleware-ului

Middleware-urile pot fi aplicate la rute individuale sau la grupuri de rute.

```php
$router->middleware('AuthMiddleware')
       ->get('/profile', 'ProfileController@show');
```

### 3. Grupuri de Rute cu Prefix

Organizează rutele sub un prefix comun, de exemplu pentru un panou de administrare.

```php
$router->group('/admin', function ($router) {
    $router->middleware('AuthMiddleware')
           ->get('/dashboard', 'AdminController@dashboard')
           ->name('admin.dashboard');
});
```

### 4. Generare Automată de URL-uri

Puteți genera URL-uri dinamice pe baza numelui rutei.

```php
$url = $router->routeUrl('admin.dashboard');
echo $url; // Output: /admin/dashboard
```

### 5. Activarea Debugging-ului

Activează modul de debugging pentru a loga mesajele de eroare.

```php
$router->enableDebug();
```

## Structura Directorului

```lua
/sts-routing-library
|-- src/
|   |-- TrieNode.php
|   |-- Router.php
|-- cache/
|-- composer.json
|-- autoload.php
|-- test.php
|-- README.md
```

## Contribuții

Contribuțiile sunt binevenite! Te rog să deschizi un issue sau un pull request pentru orice îmbunătățiri sau probleme.

## Licență

Acest proiect este licențiat sub licența MIT. Vezi fișierul LICENSE pentru mai multe detalii.

```

### Explicații

- **Insigne (badges)**: Acestea indică versiunea minimă de PHP necesară și licența proiectului.
- **Caracteristici**: O listă cu principalele funcționalități oferite de librărie.
- **Instalare**: Instrucțiuni pentru configurarea proiectului folosind Composer.
- **Utilizare**: Exemple de cod pentru definirea rutelor, aplicarea middleware-urilor, gestionarea grupurilor de rute, generarea URL-urilor și activarea debugging-ului.
- **Structura Directorului**: O scurtă prezentare a structurii fișierelor din proiect.
- **Contribuții**: Informații despre cum se poate contribui la dezvoltarea proiectului.
- **Licență**: Detalii despre licența proiectului.

Acest `README.md` oferă o imagine clară și concisă a modului în care poate fi utilizată librăria de rutare, fiind prietenos pentru utilizatori și contribuitori.

```