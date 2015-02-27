## Base ORM

The popular ORMs seem complex. They might make sense for some complex projects, but most projects are simple. Meet Base. It's a simple ORM in PHP. I've been using it quite a lot over the years. I thought it's about time I share it with the world.

### Features

- Simple
- Intuitive
- Independent
- Secure
- Based on [PDO](http://php.net/manual/en/book.pdo.php)
- Tested in 5.3, 5.4, 5.5, 5.6 and [HHVM](http://hhvm.com/)

### Installation

Include both `Base.php` and `Collection.php` or install [the composer package](https://packagist.org/packages/erusev/base).

### Examples

Connect to a database:
```php
# the constructor takes the same parameters as the PDO constructor
$Base = new \Base\Base('mysql:host=localhost;dbname=example', 'username', 'password');
```

Work with individual records:
```php
# read user 1
$Base->readItem('user', 1);
# update the username of user 1
$Base->updateItem('user', 1, ['username' => 'john.doe']);
# create a user
$Base->createItem('user', ['username' => 'jane.doe', 'email' => 'jane@example.com']);
# delete user 1
$Base->deleteItem('user', 1);
```

Work with multiple records:
```php
# read all users
$Base->find('user')->read();
# read the user with the highest reputation
$Base->find('user')->limit(1)->orderDesc('reputation')->readRecord();
# update is_verified field of users 1 and 2
$Base->find('user')->whereIn('id', [1, 2])->update(['is_verified' => 1]);
# count users that don't have a location
$Base->find('user')->whereNull('location')->count();
# delete posts that are more than a month old
$Base->find('post')->where('created_at <= DATE_SUB(NOW(),INTERVAL 1 MONTH)')->delete();
```

Use relationships:
```php
# read the users that have a featured post
$Base->find('user')->has('post')->whereEqual('post.is_featured', 1)->read();
# read the posts of user 1
$Base->find('post')->belongsTo('user')->whereEqual('user.id', 1)->read();
# read the posts that are tagged "php"
$Base->find('post')->hasAndBelongsTo('tag')->whereEqual('tag.name', 'php')->read();
```

Execute queries:
```php
# read all users
$Base->read('SELECT * FROM user');
# read user 1
$Base->readRecord('SELECT * FROM user WHERE id = ?', [1]);
# read the username of user 1
$Base->readField('SELECT username FROM user WHERE id = ?', [1]);
# read all usernames
$Base->readFields('SELECT username FROM user');
# update all users
$Base->update('UPDATE INTO user SET is_verified = ?', [1]);
```

### Requirements

Table names must be singular - e.g. `user` instead of `users`.

### Status

[![Build Status](http://img.shields.io/travis/erusev/base.svg?style=flat-square)](https://travis-ci.org/erusev/base)
[![Latest Stable Version](http://img.shields.io/packagist/v/erusev/base.svg?style=flat-square)](https://packagist.org/packages/erusev/base)
