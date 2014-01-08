# Bandar

[![Build Status](https://travis-ci.org/yani-/bandar.png?branch=develop)](https://travis-ci.org/yani-/bandar)
[![Code Coverage](https://scrutinizer-ci.com/g/yani-/bandar/badges/coverage.png?s=c40636c2454c21b02833b845177f72708ac66d80)](https://scrutinizer-ci.com/g/yani-/bandar/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/yani-/bandar/badges/quality-score.png?s=dafa1ca61bb087fd0ed911b8376a01920fe2477f)](https://scrutinizer-ci.com/g/yani-/bandar/)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/yani-/bandar/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
[![Latest Stable Version](https://poser.pugx.org/bandar/bandar/v/stable.png)](https://packagist.org/packages/bandar/bandar)
[![Total Downloads](https://poser.pugx.org/bandar/bandar/downloads.png)](https://packagist.org/packages/bandar/bandar)

Fully tested minimalistic PHP template engine. Include the class, set your templates location and start rendering.

### Requirements
PHP v5.2 and up. Tested on PHP v5.2, v5.3, v5.4, v5.5

### Usage
```php
// example.php
require_once 'lib/Bandar.php';
define(
    'BANDAR_TEMPLATES_PATH',
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'views'
);
Bandar::render('users/list', array('users' => array('name' => 'John Smith')));
```
```php
// views/users/list.php
<ul>
<?php
foreach ($users as $user) {
    echo '<li>' . $user['name'] . '</li>';
}
?>
</ul>
```
Run it
```bash
php example.php
```

#### Page with sidebar
```php
// example-with-sidebar.php
require_once 'lib/Bandar.php';
define(
    'BANDAR_TEMPLATES_PATH',
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'views'
);
$sidebar = Bandar::getTemplateContent('common/sidebar');
Bandar::render(
    'users/list',
    array(
        'users'   => array(array('name' => 'John Smith')),
        'sidebar' => $sidebar
    )
);
```
```php
// views/users/list.php
<div class="container">
    <div class="left">
        <ul>
        <?php
        foreach ($users as $user) {
            echo '<li>' . $user['name'] . '</li>';
        }
        ?>
        </ul>
    </div>
    <div class="right">
        <?php echo $sidebar; ?>
    </div>
</div>
```
```php
// views/common/sidebar.php
Sidebar content
```
Run it
```bash
php example-with-sidebar.php
```

### Tests
Coverage reports are stored inside the coverage folder. The goal is to always have 100% coverage.
```bash
phpunit
```

### Documentation
```bash
phpdoc -f lib/Bandar.php
```

### Contributing
For code guidelines refer to `.editorconfig`. This project is following PEAR code standard - http://pear.php.net/manual/en/standards.php
The project is following Vincent Driessen's branching model aka git flow - http://nvie.com/git-model/
Make sure to submit your pull requests against the **develop** branch

### License
MIT
