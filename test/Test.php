<?php

class Test extends PHPUnit_Framework_TestCase
{
    function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $Base = new \Base\Base('mysql:host=localhost;dbname=test', 'root');

        $this->Base = $Base;
    }

    private $Base;

    # ~

    protected function setUp()
    {
        $this->Base->pdo()->beginTransaction();
    }

    protected function tearDown()
    {
        $this->Base->pdo()->rollBack();
    }

    #
    # Record
    #

    function testPreparedStatement()
    {
        $impactedRecordCount = $this->Base->update('UPDATE user SET lastName = ?', array('Smith'));

        $this->assertEquals(2, $impactedRecordCount);

        $Records = $this->Base->read('SELECT * FROM user');

        $ExpectedRecords = array(
            array(
                'id' => '1',
                'username' => 'john.doe',
                'firstName' => 'John',
                'lastName' => 'Smith',
            ),
            array(
                'id' => '2',
                'username' => 'jane.doe',
                'firstName' => 'Jane',
                'lastName' => 'Smith',
            ),
        );

        $this->assertEquals($ExpectedRecords, $Records);
    }

    function testCollection()
    {
        $impactedRecordCount = $this->Base->find('user')->update(array(
            'lastName' => 'Smith',
        ));

        $this->assertEquals(2, $impactedRecordCount);

        $Records = $this->Base->find('user')->read();

        $ExpectedRecords = array(
            array(
                'id' => '1',
                'username' => 'john.doe',
                'firstName' => 'John',
                'lastName' => 'Smith',
            ),
            array(
                'id' => '2',
                'username' => 'jane.doe',
                'firstName' => 'Jane',
                'lastName' => 'Smith',
            ),
        );

        $this->assertEquals($ExpectedRecords, $Records);
    }

    function testCollectionRecord()
    {
        $Record = $this->Base->find('user')->readRecord();

        $ExpectedRecord = array(
            'id' => '1',
            'username' => 'john.doe',
            'firstName' => 'John',
            'lastName' => 'Doe',
        );

        $this->assertEquals($ExpectedRecord, $Record);

        $Record = $this->Base->find('user')->readRecord('id, CONCAT(firstName, " ", lastName) as name');

        $ExpectedRecord = array(
            'id' => '1',
            'name' => 'John Doe',
        );

        $this->assertEquals($ExpectedRecord, $Record);
    }

    function testCollectionField()
    {
        $field = $this->Base->find('user')->readField();

        $this->assertEquals('1', $field);

        $field = $this->Base->find('user')->readField('COUNT(*)');

        $this->assertEquals('2', $field);
    }

    function testCollectionCount()
    {
        $field = $this->Base->find('user')->count();

        $this->assertEquals(2, $field);
    }

    function testCollectionFields()
    {
        $fields = $this->Base->find('user')->readFields();

        $this->assertEquals(array(1, 2), $fields);

        $fields = $this->Base->find('user')->readFields('firstName');

        $this->assertEquals(array('John', 'Jane'), $fields);
    }

    function testCollectionWhereClause()
    {
        $fields = $this->Base->find('user')
            ->where('id = 2')
            ->readFields('firstName');

        $this->assertEquals(array('Jane'), $fields);

        $fields = $this->Base->find('user')
            ->whereEqual('id', 2)
            ->readFields('firstName');

        $this->assertEquals(array('Jane'), $fields);

        $fields = $this->Base->find('user')
            ->whereNotEqual('id', 2)
            ->readFields('firstName');

        $this->assertEquals(array('John'), $fields);

        $fields = $this->Base->find('post')
            ->whereIn('id', array(1, 3))
            ->readFields();

        $this->assertEquals(array(1, 3), $fields);

        $fields = $this->Base->find('post')
            ->whereNotIn('id', array(1, 3))
            ->readFields();

        $this->assertEquals(array(2, 4), $fields);
    }

    function testCollectionOrderClause()
    {
        $fields = $this->Base->find('post')
            ->order('id DESC')
            ->readFields();

        $this->assertEquals(array(4, 3, 2, 1), $fields);

        $fields = $this->Base->find('post')
            ->orderAsc('id')
            ->readFields();

        $this->assertEquals(array(1, 2, 3, 4), $fields);

        $fields = $this->Base->find('post')
            ->orderDesc('id')
            ->readFields();

        $this->assertEquals(array(4, 3, 2, 1), $fields);
    }

    function testCollectionLimitClause()
    {
        $fields = $this->Base->find('post')
            ->limit(2)
            ->readFields();

        $this->assertEquals(array(1, 2), $fields);

        $fields = $this->Base->find('post')
            ->limit('2, 2')
            ->readFields();

        $this->assertEquals(array(3, 4), $fields);
    }

    function testCollectionRelationshipClause()
    {
        $fields = $this->Base->find('user')
            ->has('post')
            ->whereEqual('post.title', 'Third Post')
            ->readFields('username');

        $this->assertEquals(array('jane.doe'), $fields);

        $fields = $this->Base->find('post')
            ->belongsTo('user')
            ->whereEqual('user.username', 'john.doe')
            ->readFields('title');

        $this->assertEquals(array('First Post', 'Second Post'), $fields);

        $fields = $this->Base->find('post')
            ->hasAndBelongsTo('tag')
            ->whereEqual('tag.name', 'life')
            ->readFields('title');

        $this->assertEquals(array('Fourth Post'), $fields);
    }

    function testCollectionClauseCombinations()
    {
        $fields = $this->Base->find('post')
            ->whereNotEqual('id', 2)
            ->whereNotIn('id', array(1, 3))
            ->readFields();

        $this->assertEquals(array(4), $fields);

        $fields = $this->Base->find('post')
            ->whereIn('id', array(1, 2))
            ->orderDesc('id')
            ->readFields();

        $this->assertEquals(array(2, 1), $fields);

        $fields = $this->Base->find('post')
            ->whereIn('id', array(1, 2, 3))
            ->orderDesc('id')
            ->limit(2)
            ->readFields();

        $this->assertEquals(array(3, 2), $fields);

        $fields = $this->Base->find('post')
            ->whereIn('id', array(1, 2, 3))
            ->orderDesc('id')
            ->limit('1, 2')
            ->readFields();

        $this->assertEquals(array(2, 1), $fields);
    }

    function testItem()
    {
        $result = $this->Base->updateItem('user', 1, array(
            'firstName' => 'J',
            'lastName' => 'D',
        ));

        $this->assertEquals(1, $result);

        $Item = $this->Base->readItem('user', 1);

        $ExpectedRecord = array(
            'id' => '1',
            'username' => 'john.doe',
            'firstName' => 'J',
            'lastName' => 'D',
        );

        $this->assertEquals($ExpectedRecord, $Item);

        $result = $this->Base->createItem('user', array(
            'id' => '3',
            'username' => 'james.smith',
            'firstName' => 'James',
            'lastName' => 'Smith',
        ));

        $this->assertEquals(3, $result);

        $Item = $this->Base->readItem('user', 3);

        $ExpectedRecord = array(
            'id' => '3',
            'username' => 'james.smith',
            'firstName' => 'James',
            'lastName' => 'Smith',
        );

        $this->assertEquals($ExpectedRecord, $Item);
    }
}
