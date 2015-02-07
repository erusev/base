## Base ORM

Because most projects don't need complicated ORMs.

### Features

- simple
- intuitive
- independent
- secure
- compatible with [most of the popular adapters](http://php.net/manual/en/pdo.drivers.php)
- tested in PHP 5.3, 5.4, 5.5, 5.6 and hhvm

### Installation

Include both `Base.php` and `Collection.php` or install [the composer package](https://packagist.org/packages/erusev/base)

### Example

Create a connection:
```php
$Base = new \Base\Base('mysql:host=localhost;dbname=example', 'username', 'password');
```

Read / update / create records:
```php
# read user #123
$Base->readItem('user', 123);
# update the username of user #123
$Base->updateItem('user', 123, ['username' => 'john.doe']);
# create a user
$Base->createItem('user', ['username' => 'jane.doe', 'email' => 'jane@example.com']);
```

Read / update / count collections:
```php
# read all users
$Base->find('user')->read();
# read all users that have a featured post
$Base->find('user')->has('post')->whereEqual('post.isFeatured', 1)->read();
# read the email addresses of the 20 most recent users
$Base->find('user')->limit(20)->orderDesc('createdAt')->readFields('email');
# update isDeleted field of users #1 and #2
$Base->find('user')->whereIn('id', [1, 2])->update(['isDeleted' => 1]);
# count users that don't have a location
$Base->find('user')->whereNull('location')->count();
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
$Base->update('UPDATE INTO user SET updated = ?', [1]);
```
