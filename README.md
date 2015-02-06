## Base ORM

Simple ORM in PHP

#### Install

Include both `Base.php` and `Collection.php` or install [the composer package](https://packagist.org/packages/erusev/base)

#### Use

```php
# create a database connection
$Base = new \Base\Base('mysql:host=localhost;dbname=example', 'username', 'password');

# read user #123
$Base->readItem('user', 123);
# update the username of user #123
$Base->updateItem('user', 123, ['username' => 'john.doe']);
# create a user
$Base->createItem('user', ['username' => 'james.smith', 'email' => 'james@example.com']);

# read all users
$Base->find('user')->read();
# read all users represented with an id and an email
$Base->find('user')->read('id, email');
# read the first user
$Base->find('user')->readRecord();
# read the first user represented with a username and a token
$Base->find('user')->readRecord('username, token');
# read the first field of the first user
$Base->find('user')->readField();
# read username of the first user
$Base->find('user')->readField('username');
# read the first field of all users
$Base->find('user')->readFields();
# read full name of all users
$Base->find('user')->readFields('CONCAT(firstName, " ", lastName) as name');
# update all users
$Base->find('user')->update(['isDeleted' => 1]);
# count all users
$Base->find('user')->count();

# read all users with a lastName of "Doe"
$Base->find('user')->where('lastName = ?', ['Doe'])->read();
# read all users with a lastName of "Doe"
$Base->find('user')->whereEqual('lastName', 'Doe')->read();
# read all users with a lastName that is not "Doe"
$Base->find('user')->whereNotEqual('lastName', 'Doe')->read();
# read all users with a lastName of either "Doe" or "Smith"
$Base->find('user')->whereIn('lastName', ['Doe', 'Smith'])->read();
# read all users with a lastName that is neither "Doe" or "Smith"
$Base->find('user')->whereNotIn('lastName', ['Doe', 'Smith'])->read();
# read all users with no lastName
$Base->find('user')->whereNull('lastName')->read();
# read all users with a lastName
$Base->find('user')->whereNotNull('lastName')->read();
# read all users in descending order
$Base->find('user')->order('id DESC')->read();
# read all users in descending order
$Base->find('user')->orderDesc('id')->read();
# read all users in ascending order
$Base->find('user')->orderAsc('id')->read();
# read the first user
$Base->find('user')->limit('1')->read();
# read the third dozen of users
$Base->find('user')->limit('24, 12')->read();

# read all users that have a featured post
$Base->find('user')->has('post')->whereEqual('post.isFeatured', 1)->read();
# read all posts of "john.doe"
$Base->find('post')->belongsTo('user')->whereEqual('user.username', 'john.doe')->read();
# read all posts that are tagged with "php"
$Base->find('post')->hasAndBelongsTo('tag')->whereEqual('tag.name', 'php')->read();

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
