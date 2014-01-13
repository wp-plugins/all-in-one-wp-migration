# MySQL Dump Factory

[![Build Status](https://travis-ci.org/yani-/mysqldump-factory.png?branch=master)](https://travis-ci.org/yani-/mysqldump-factory)
[![Latest Stable Version](https://poser.pugx.org/mysqldump-factory/mysqldump-factory/v/stable.png)](https://packagist.org/packages/mysqldump-factory/mysqldump-factory)
[![Total Downloads](https://poser.pugx.org/mysqldump-factory/mysqldump-factory/downloads.png)](https://packagist.org/packages/mysqldump-factory/mysqldump-factory)

MySQL Dump Factory class that creates either mysql or PDO classes

### Requirements
PHP v5.2 and up. Tested on PHP v5.2.17, v5.3, v5.4, v5.5

### Usage
```php
require_once 'lib/MysqlDumpFactory.php';
$mc = MysqlDumpFactory::makeMysqlDump('dbhost', 'dbuser', 'dbpass', 'dbname',class_exists('PDO'));
```

### Tests
Coverage reports are stored inside the coverage folder.
```bash
phpunit
```

### Contributing
For code guidelines refer to `.editorconfig`. This project is following PEAR code standard - http://pear.php.net/manual/en/standards.php
The project is following Vincent Driessen's branching model aka git flow - http://nvie.com/git-model/
Make sure to submit your pull requests against the **develop** branch

### License
MIT

### Authors
* Yani Iliev
* Bobby Angelov
