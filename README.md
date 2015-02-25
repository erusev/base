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

Connection to a database:
```php
# the constructor takes the same parameters as the PDO constructor
$Base = new \Base\Base('mysql:host=localhost;dbname=example', 'username', 'password');
```

Read / update / create / delete records:
```php
# read user #123
$Base->readItem('user', 123);
# update the username of the same user
$Base->updateItem('user', 123, ['username' => 'john.doe']);
# create another user
$Base->createItem('user', ['username' => 'jane.doe', 'email' => 'jane@example.com']);
# delete user #123
$Base->deleteItem('user', 123);
```

Read / update / count / delete collections:
```php
# read all users
$Base->find('user')->read();
# read the user with the highest reputation
$Base->find('user')->limit(1)->orderDesc('reputation')->readRecord();
# update is_verified field of users #1 and #2
$Base->find('user')->whereIn('id', [1, 2])->update(['is_verified' => 1]);
# count users that don't have a location
$Base->find('user')->whereNull('location')->count();
# delete accounts that are not verified and more than a month old
$Base->find('user')->where('is_verified = 0 AND created_at <= DATE_SUB(NOW(),INTERVAL 1 MONTH)')->delete();
```

Handle relationships:
```php
# read all users that have a featured post
$Base->find('user')->has('post')->whereEqual('post.is_featured', 1)->read();
# read the last post of user #123
$Base->find('post')->belongsTo('user')->whereEqual('user.id', 123)->orderDesc('post.id')->readRecord();
# read the titles of the posts that have a "php" tag
$Base->find('post')->hasAndBelongsTo('tag')->whereEqual('tag.name', 'php')->readFields('title');
```

Execute queries:
```php
# read all users
$Base->read('SELECT * FROM user');
# read user #123
$Base->readRecord('SELECT * FROM user WHERE id = ?', [123]);
# read the username of user #123
$Base->readField('SELECT username FROM user WHERE id = ?', [123]);
# read all usernames
$Base->readFields('SELECT username FROM user');
# update all users
$Base->update('UPDATE INTO user SET is_verified = ?', [1]);
```

### Requirements

In order for the methods that handle relationships to work, table names should be singular (e.g. `user` instead of `users`).

### Status

[![Build Status](http://img.shields.io/travis/erusev/base.svg?style=flat-square)](https://travis-ci.org/erusev/base)
[![Latest Stable Version](http://img.shields.io/packagist/v/erusev/base.svg?style=flat-square)](https://packagist.org/packages/erusev/base)
