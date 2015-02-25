## Base ORM

Because most projects don't need complicated ORMs.

### Features

- Simple
- Intuitive
- Independent
- Secure
- Based on [PDO](http://php.net/manual/en/book.pdo.php)
- Tested in 5.3, 5.4, 5.5, 5.6 and [HHVM](http://hhvm.com/)

### Installation

Include both `Base.php` and `Collection.php` or install [the composer package](https://packagist.org/packages/erusev/base)

### Example

Connect to a database:
```php
# the constructor takes the same parameters as the PDO constructor
$Base = new \Base\Base('mysql:host=localhost;dbname=example', 'username', 'password');
```

Handle records:
```php
# read user #123
$Base->readItem('user', 123);
```
```php
# update the username of the same user
$Base->updateItem('user', 123, ['username' => 'john.doe']);
```
```php
# create another user
$Base->createItem('user', ['username' => 'jane.doe', 'email' => 'jane@example.com']);
```
```php
# delete user #123
$Base->deleteItem('user', 123);
```

Handle collections:
```php
# read all users
$Base->find('user')->read();
```
```php
# read the user with the highest reputation
$Base->find('user')->limit(1)->orderDesc('reputation')->readRecord();
```
```php
# update is_verified field of users #1 and #2
$Base->find('user')
  ->whereIn('id', [1, 2])
  ->update(['is_verified' => 1]);
```
```php
# count users that don't have a location
$Base->find('user')
  ->whereNull('location')
  ->count();
```
```php
# delete users that are not verified and have been created more than a month ago
$Base->find('user')
  ->where('is_verified = 0 AND created_at <= DATE_SUB(NOW(),INTERVAL 1 MONTH)')
  ->delete();
```

Handle relationships:
```php
# read the users that have a featured post
$Base->find('user')
  ->has('post')
  ->whereEqual('post.is_featured', 1)
  ->read();
```
```php
# read the last post of user #1
$Base->find('post')
  ->belongsTo('user')
  ->whereEqual('user.id', 1)
  ->orderDesc('post.id')
  ->readRecord();
```
```php
# read the titles of the posts that have a "php" label
$Base->find('post')
  ->hasAndBelongsTo('label')
  ->whereEqual('label.name', 'php')
  ->readFields('title');
```

Execute queries:
```php
# read all users
$Base->read('SELECT * FROM user');
```
```php
# read user #123
$Base->readRecord('SELECT * FROM user WHERE id = ?', [123]);
```
```php
# read the username of user #123
$Base->readField('SELECT username FROM user WHERE id = ?', [123]);
```
```php
# read all usernames
$Base->readFields('SELECT username FROM user');
```
```php
# update all users
$Base->update('UPDATE INTO user SET is_verified = ?', [1]);
```

### Requirements

In order for the methods that handle relationships to work, table names should be singular (e.g. `user` instead of `users`).

### Status

[![Build Status](http://img.shields.io/travis/erusev/base.svg?style=flat-square)](https://travis-ci.org/erusev/base)
[![Latest Stable Version](http://img.shields.io/packagist/v/erusev/base.svg?style=flat-square)](https://packagist.org/packages/erusev/base)
