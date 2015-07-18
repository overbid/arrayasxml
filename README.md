# ArrayAsXml
ArrayAsXml is a simple PHP class that converts array to XML.

Based on [SimpleXML](http://php.net/manual/en/book.simplexml.php).


### Installation

via [Composer](https://getcomposer.org/)

```bash
composer require overbid/arrayasxml
```

### Usage

#### Load the library

```php
require 'vendor/autoload.php';
use Overbid\ArrayAsXml;
```

#### Set custom configuration:

```php
$arrayAsXml = new ArrayAsXml();
$arrayAsXml->setEncoding('TIS-620');  //default UTF-8
$arrayAsXml->setRootName('main');     //defaul root
```

#### Creat XML

```php
echo $arrayAsXml->save($data);
```