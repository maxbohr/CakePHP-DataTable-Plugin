CakePHP DataTable Plugin
=========================

CakePHP DataTable Plugin, geared towards MongoDB Collection.

Usage:

* Clone/Download this git repository into app/Plugin directory.
* Rename it to app/Plugin/DataTable
* Load it in bootstrap.php 

```php
CakePlugin::load('DataTable');
```
* Copy and rename model_User.php.sample to app/Config/model_User.php
* In app/UsersController.php, create a blank index() function.
* Update View/Users/index.ctp as below

```php
<div class="well">
    <h2> Users </h2>
</div>

<div>
    <?= $this->element(
        'data_table',
        array('model' => 'User'),
        array('plugin' => 'DataTable')
    ) ?>
</div>
```

You may use this with any controller / model, just update the 'model' parameter in the element accordingly.


