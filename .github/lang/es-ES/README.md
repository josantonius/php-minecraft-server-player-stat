# PHP Minecraft Server Player Stat library

[![Latest Stable Version](https://poser.pugx.org/josantonius/minecraft-server-player-stat/v/stable)](https://packagist.org/packages/josantonius/minecraft-server-player-stat)
[![License](https://poser.pugx.org/josantonius/minecraft-server-player-stat/license)](LICENSE)
[![Total Downloads](https://poser.pugx.org/josantonius/minecraft-server-player-stat/downloads)](https://packagist.org/packages/josantonius/minecraft-server-player-stat)
[![CI](https://github.com/josantonius/php-minecraft-server-player-stat/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/josantonius/php-minecraft-server-player-stat/actions/workflows/ci.yml)
[![CodeCov](https://codecov.io/gh/josantonius/php-minecraft-server-player-stat/branch/main/graph/badge.svg)](https://codecov.io/gh/josantonius/php-minecraft-server-player-stat)
[![PSR1](https://img.shields.io/badge/PSR-1-f57046.svg)](https://www.php-fig.org/psr/psr-1/)
[![PSR4](https://img.shields.io/badge/PSR-4-9b59b6.svg)](https://www.php-fig.org/psr/psr-4/)
[![PSR12](https://img.shields.io/badge/PSR-12-1abc9c.svg)](https://www.php-fig.org/psr/psr-12/)

**Traducciones**: [English](/README.md)

Biblioteca PHP para obtener estadísticas en vivo sobre jugadores en servidores de Minecraft.

---

- [Requisitos](#requisitos)
- [Detalles relevantes](#detalles-relevantes)
- [Instalación](#instalación)
- [Clases disponibles](#clases-disponibles)
  - [Clase MinecraftServer](#clase-minecraftserver)
  - [Instancia MinecraftPlayerStat](#instancia-minecraftplayerstat)
- [Excepciones utilizadas](#excepciones-utilizadas)
- [Uso](#uso)
- [Tests](#tests)
- [Tareas pendientes](#tareas-pendientes)
- [Registro de Cambios](#registro-de-cambios)
- [Contribuir](#contribuir)
- [Patrocinar](#patrocinar)
- [Licencia](#licencia)

---

## Requisitos

- Esta biblioteca es compatible con las versiones de PHP: 8.1.

- Servidor de Minecraft: [Spigot](https://www.spigotmc.org/).

- Versión de Minecraft: 1.17.

- Sistema operativo: Linux | Windows.

## Detalles relevantes

- Esta biblioteca fue desarrollada para una aplicación que se ejecutaba desde la línea de comandos
(CLI) de PHP, específicamente para alimentar un bot de Twitch que mostraba las estadísticas de los
jugadores en vivo. No recomiendo su uso para un entorno web, aunque funcionaría.

  ![alt](/.resources/example-minecraft-twitch-bot-es.png)

- Es probable que funcione para las versiones recientes del servidor Spigot, pero no puedo asegurarlo
ya que sólo se ha sido probado con los requisitos detallados anteriormente. Siéntete libre de agregar
nuevas pruebas para otros servidores y/o versiones de Minecraft.

- Los términos disponibles se establecen desde el repositorio
[minecraft-assets repository](https://github.com/InventivetalentDev/minecraft-assets/branches/all)
de [InventivetalentDev](https://github.com/InventivetalentDev).

## Instalación

La mejor forma de instalar esta extensión es a través de [Composer](http://getcomposer.org/download/).

Para instalar **PHP Minecraft Server Player Stat library**, simplemente escribe:

```console
composer require josantonius/minecraft-server-player-stat
```

El comando anterior sólo instalará los archivos necesarios,
si prefieres **descargar todo el código fuente** puedes utilizar:

```console
composer require josantonius/minecraft-server-player-stat --prefer-source
```

También puedes **clonar el repositorio** completo con Git:

```console
git clone https://github.com/josantonius/php-minecraft-server-player-stat.git
```

## Clases disponibles

### Clase MinecraftServer

`Josantonius\MinecraftServerPlayerStat\MinecraftServer`

Crea una nueva instancia para el servidor de Minecraft:

```php
/**
 * @param string $version     Versión del servidor.
 * @param string $language    Idioma del servidor.
 * @param string $logsPath    Ruta del directorio logs del servidor.
 * @param string $statsPath   Ruta del directorio stats del servidor.
 * @param string $storagePath Ruta del directorio donde almacenar los 
 *                            términos disponibles y lista de jugadores.
 *
 * @throws MinecraftServerException      si la versión o el idioma de Minecraft no son válidos.
 * @throws UnreadableDirectoryException  si la ruta de los registros o de las estadísticas no es válida.
 * @throws UnwriteableDirectoryException si la ruta de almacenamiento no es válida.
 *
 * @see https://mcasset.cloud/1.19.2/assets/minecraft/lang para ver idiomas disponibles.
 */
public function __construct(
    private string $version,
    private string $language,
    private string $logsPath,
    private string $statsPath,
    private string $storagePath,
);
```

Obtener detalles sobre determinadas estadísticas de los jugadores:

```php
/**
 * @param string $username El nombre de usuario sin distinguir entre mayúsculas y minúsculas.
 * @param string $term     Término literal de Minecraft sin distinguir entre mayúsculas y minúsculas.
 *                         En español, un término con tildes puede escribirse sin ellas. 
 *                         Para otras lenguas se requieren las tildes.
 *
 * @throws WrongTermException       si el término no es válido.
 * @throws StatsNotFoundException   si no se encuentra el archivo de estadísticas.
 * @throws UnknownUsernameException si el nombre de usuario no es válido.
 */
public function getPlayerStat(string $username, string $term): MinecraftPlayerStat;
```

Obtener estadísticas disponibles:

```php
public function getAvailableStats(): array;
```

Obtener jugadores del servidor:

```php
public function getPlayerList(): array;
```

### Instancia MinecraftPlayerStat

`Josantonius\MinecraftServerPlayerStat\MinecraftPlayerStat`

Número de artículos rotos o nulo si no hay estadísticas:

```php
public readonly int|null $broken;
```

Número de artículos elaborados o nulo si no hay estadísticas:

```php
public readonly int|null $crafted;
```

Número de elementos soltados o nulo si no hay estadísticas:

```php
public readonly int|null $dropped;
```

Número de mobs muertos o nulo si no hay estadísticas:

```php
public readonly int|null $killed;
```

Número de muertes por mobs o nulo si no hay estadísticas:

```php
public readonly int|null $killedBy;
```

Número de elementos minados o nulo si no hay estadísticas:

```php
public readonly int|null $mined;
```

Número de elementos recogidos o nulo si no hay estadísticas:

```php
public readonly int|null $pickedUp;
```

Número de elementos utilizados o nulo si no hay estadísticas:

```php
public readonly int|null $used;
```

Valor de las estadísticas personalizadas o nulo si no hay estadísticas:

```php
/**
 * Si el tipo de unidad es distancia, este valor se da en centímetros.
 * Si el tipo de unidad es el tiempo, este valor se da en ticks.
 * 
 * @see https://minecraft.fandom.com/wiki/Tutorials/Units_of_measure para ver las conversiones.
 */
public readonly int|null $custom;
```

Clave saneada para el término:

```php
public readonly string $key;
```

Término expresado de forma agradable:

```php
public readonly string $prettyTerm;
```

Término de la consulta:

```php
public readonly string $term;
```

Tipo de elemento:

```php
/**
 * Tipos disponibles: block, entity, item, stat.
 */
public readonly string $type;
```

Tipo de unidad:

```php
/**
 * Tipos disponibles: amount, distance, time.
 */
public readonly string $unitType;
```

Nombre de usuario para el que se realiza la consulta:

```php
public readonly string $username;
```

UUID del usuario para el que se realiza la consulta:

```php
public readonly string $uuid;
```

## Excepciones utilizadas

```php
use Josantonius\MinecraftServerPlayerStat\Exceptions\WrongTermException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\StatsNotFoundException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnknownUsernameException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\MinecraftServerException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnreadableDirectoryException;
use Josantonius\MinecraftServerPlayerStat\Exceptions\UnwriteableDirectoryException;
```

## Uso

Ejemplo de uso para esta biblioteca:

### Obtener las estadísticas del jugador sobre bloques

```php
use Josantonius\MinecraftServerPlayerStat\MinecraftServer;

$minecraftServer = new MinecraftServer(
    version:     '1.17.1',
    language:    'it_it',
    logsPath:    '/minecraft/logs',
    statsPath:   '/minecraft/saves/world/stats',
    storagePath: '/data/storage',
);

$stat = $minecraftServer->getPlayerStat('Aguilar11235813', 'Blocco Di Diamante');

echo "{$stat->username} ha raccolto {$stat->pickedUp} blocchi di diamante.";

// Aguilar11235813 ha raccolto 8 blocchi di diamanti.
```

**`MinecraftPlayerStat $stat`**

```php
object(Josantonius\MinecraftServerPlayerStat\MinecraftPlayerStat) {
   'broken'     => NULL,
   'crafted'    => NULL,
   'custom'     => NULL,
   'dropped'    => NULL,
   'killed'     => NULL,
   'killedBy'   => NULL,
   'mined'      => 8,
   'pickedUp'   => 8,
   'used'       => NULL,
   'key'        => 'diamond_block',
   'prettyTerm' => 'Blocco di diamante',
   'term'       => 'Blocco Di Diamante',
   'type'       => 'block',
   'unitType'   => 'amount',
   'username'   => 'Aguilar11235813',
   'uuid'       => '18f154fe-3678-37e9-9b77-185e0bfe446d',
}
```

### Obtener las estadísticas del jugador sobre distancia

```php
use Josantonius\MinecraftServerPlayerStat\MinecraftServer;

$minecraftServer = new MinecraftServer(
    version:     '1.19.1',
    language:    'es_es',
    logsPath:    '/minecraft/logs',
    statsPath:   '/minecraft/saves/world/stats',
    storagePath: '/data/storage',
);

$stat = $minecraftServer->getPlayerStat('Armadillo', 'Distancia Volada');

echo "{$stat->username} voló una distancia de " . cmToKm($stat->custom) . ' kilómetros.';

// Armadillo voló una distancia de 6 kilómetros.
```

**`MinecraftPlayerStat $stat`**

```php
object(Josantonius\MinecraftServerPlayerStat\MinecraftPlayerStat) {
   'broken'     => NULL,
   'crafted'    => NULL,
   'custom'     => 585888, // centímetros
   'dropped'    => NULL,
   'killed'     => NULL,
   'killedBy'   => NULL,
   'mined'      => NULL,
   'pickedUp'   => NULL,
   'used'       => NULL,
   'key'        => 'fly_one_cm',
   'prettyTerm' => 'Distancia volada',
   'term'       => 'Distancia Volada',
   'type'       => 'stat',
   'unitType'   => 'distance',
   'username'   => 'Armadillo',
   'uuid'       => '14e55460-c753-31f2-bd0a-c305e2ff34b5',
}
```

### Obtener las estadísticas del jugador sobre entidades

```php
use Josantonius\MinecraftServerPlayerStat\MinecraftServer;

$minecraftServer = new MinecraftServer(
    version:     '1.17',
    language:    'en_us',
    logsPath:    '/minecraft/logs',
    statsPath:   '/minecraft/saves/world/stats',
    storagePath: '/data/storage',
);

$stat = $minecraftServer->getPlayerStat('KrakenBite', 'zombie');

echo "{$stat->username} was killed {$stat->killedBy} times by a {$stat->term}.";

// KrakenBite was killed 2 times by a zombie.
```

**`MinecraftPlayerStat $stat`**

```php
object(Josantonius\MinecraftServerPlayerStat\MinecraftPlayerStat) {
   'broken'     => NULL,
   'crafted'    => NULL,
   'custom'     => NULL,
   'dropped'    => NULL,
   'killed'     => 8,
   'killedBy'   => 2,
   'mined'      => NULL,
   'pickedUp'   => NULL,
   'used'       => NULL,
   'key'        => 'zombie',
   'prettyTerm' => 'Zombie',
   'term'       => 'zombie',
   'type'       => 'entity',
   'unitType'   => 'amount',
   'username'   => 'KrakenBite',
   'uuid'       => '5cd5d2e7-9b3a-3f06-befb-34f7a81b14c6',
}
```

### Obtener las estadísticas del jugador sobre objetos

```php
use Josantonius\MinecraftServerPlayerStat\MinecraftServer;

$minecraftServer = new MinecraftServer(
    version:     '1.18.1',
    language:    'fr_fr',
    logsPath:    '/minecraft/logs',
    statsPath:   '/minecraft/saves/world/stats',
    storagePath: '/data/storage',
);

$stat = $minecraftServer->getPlayerStat('Tweedlex', 'HACHE ON BOIS');

echo "{$stat->username} a utilisé une " . strtolower($stat->term) . " {$stat->used} fois.";

// Tweedlex a utilisé une hache en bois 111 fois.
```

**`MinecraftPlayerStat $stat`**

```php
object(Josantonius\MinecraftServerPlayerStat\MinecraftPlayerStat) {
   'broken'     => 8,
   'crafted'    => 8,
   'custom'     => NULL,
   'dropped'    => NULL,
   'killed'     => NULL,
   'killedBy'   => NULL,
   'mined'      => NULL,
   'pickedUp'   => NULL,
   'used'       => 111,
   'key'        => 'wooden_axe',
   'prettyTerm' => 'Hache en bois',
   'term'       => 'HACHE ON BOIS',
   'type'       => 'item',
   'unitType'   => 'amount',
   'username'   => 'Tweedlex',
   'uuid'       => '8cb86072-3472-3b86-90f2-1e11b7188197',
}
```

### Obtener la lista de estadísticas disponibles

```php
use Josantonius\MinecraftServerPlayerStat\MinecraftServer;

$minecraftServer = new MinecraftServer(
    version:     '1.17.1',
    language:    'en_us',
    logsPath:    '/minecraft/logs',
    statsPath:   '/minecraft/saves/world/stats',
    storagePath: '/data/storage',
);

$terms = $minecraftServer->getAvailableStats();
```

**`$terms`**

```php
[
    /* ... */

    'zombie spawn egg' => [
        'key' => 'zombie_spawn_egg',
        'pretty_term' => 'Zombie Spawn Egg',
        'type' => 'item',
        'unit_type' => 'amount',
    ],
    'zombie villager' => [
        'key' => 'zombie_villager',
        'pretty_term' => 'Zombie Villager',
        'type' => 'entity',
        'unit_type' => 'amount',
    ],
    'zombie villager spawn egg' => [
        'key' => 'zombie_villager_spawn_egg',
        'pretty_term' => 'Zombie Villager Spawn Egg',
        'type' => 'item',
        'unit_type' => 'amount',
    ],
    'zombie wall head' => [
        'key' => 'zombie_wall_head',
        'pretty_term' => 'Zombie Wall Head',
        'type' => 'block',
        'unit_type' => 'amount',
    ],
    'zombified piglin' => [
        'key' => 'zombified_piglin',
        'pretty_term' => 'Zombified Piglin',
        'type' => 'entity',
        'unit_type' => 'amount',
    ],
    'zombified piglin spawn egg' => [
        'key' => 'zombified_piglin_spawn_egg',
        'pretty_term' => 'Zombified Piglin Spawn Egg',
        'type' => 'item',
        'unit_type' => 'amount',
    ],
]
```

### Obtener la lista de jugadores del servidor

```php
use Josantonius\MinecraftServerPlayerStat\MinecraftServer;

$minecraftServer = new MinecraftServer(
    version:     '1.17',
    language:    'en_us',
    logsPath:    '/minecraft/logs',
    statsPath:   '/minecraft/saves/world/stats',
    storagePath: '/data/storage',
);

$players = $minecraftServer->getPlayerList();
```

**`$players`**

```php
[
    /* ... */

  'armadillo'       => '14e55460-c753-31f2-bd0a-c305e2ff34b5',
  'aguilar11235813' => '18f154fe-3678-37e9-9b77-185e0bfe446d',
  'krakenbite'      => '5cd5d2e7-9b3a-3f06-befb-34f7a81b14c6',
  'tweedlex'        => '8cb86072-3472-3b86-90f2-1e11b7188197',
  'spook'           => '8d5b923d-37fb-38d1-8a6a-a17cd5ccf768',
]
```

## Tests

Para ejecutar las [pruebas](tests) necesitarás [Composer](http://getcomposer.org/download/)
y seguir los siguientes pasos:

```console
git clone https://github.com/josantonius/php-minecraft-server-player-stat.git
```

```console
cd PHP-MimeType
```

```console
composer install
```

Ejecutar pruebas unitarias con [PHPUnit](https://phpunit.de/):

```console
composer phpunit
```

Ejecutar pruebas de estándares de código con [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer):

```console
composer phpcs
```

Ejecutar pruebas con [PHP Mess Detector](https://phpmd.org/) para detectar inconsistencias
en el estilo de codificación:

```console
composer phpmd
```

Ejecutar todas las pruebas anteriores:

```console
composer tests
```

## Tareas pendientes

- [ ] Añadir nueva funcionalidad
- [ ] Mejorar pruebas
- [ ] Mejorar documentación
- [ ] Mejorar la traducción al inglés en el archivo README
- [ ] Refactorizar código para las reglas de estilo de código deshabilitadas
(ver [phpmd.xml](phpmd.xml) y [phpcs.xml](phpcs.xml))

## Registro de Cambios

Los cambios detallados de cada versión se documentan en las
[notas de la misma](https://github.com/josantonius/php-minecraft-server-player-stat/releases).

## Contribuir

Por favor, asegúrate de leer la [Guía de contribución](CONTRIBUTING.md) antes de hacer un
_pull request_, comenzar una discusión o reportar un _issue_.

¡Gracias por [colaborar](https://github.com/josantonius/php-minecraft-server-player-stat/graphs/contributors)! :heart:

## Patrocinar

Si este proyecto te ayuda a reducir el tiempo de desarrollo,
[puedes patrocinarme](https://github.com/josantonius/lang/es-ES/README.md#patrocinar)
para apoyar mi trabajo :blush:

## Licencia

Este repositorio tiene una licencia [MIT License](LICENSE).

Copyright © 2021-2023, [Josantonius](https://github.com/josantonius/lang/es-ES/README.md#contacto)
