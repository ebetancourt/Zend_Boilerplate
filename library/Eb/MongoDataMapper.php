<?php
/**
 * Eb.
 *
 * @category   Eb
 * @package    Eb
 * @subpackage Eb_MongoDataMapper
 * @author     Elliot Betancourt
 */

/**
 * Datamapper factory class.
 *
 * @category   Eb
 * @package    Eb
 * @subpackage Eb_MongoDataMapper
 * @author     Elliot Betancourt
 */
class Eb_MongoDataMapper
{
    /**
     * The constructor for this object
     **/
    public function __construct()
    {
        $this->_getCollection();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Queries the collection
     *
     * @param  array $query  Query parameters in an associative array
     * @param  array $fields (Optional) List of fields to return in an array
     * @return MongoCursor
     **/
    public function find(array $query, array $fields = array())
    {
        return $this->_collection->find($query, $fields);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Counts the number of records that match the given parameters
     *
     * @param  array $query  Query parameters in an associative array
     * @return MongoCursor
     **/
    public function count(array $query)
    {
        return $this->_collection->count($query);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Queries the collection
     *
     * @param  array $query    Query parameters in an associative array
     * @param  array $fields   (Optional) List of fields to return in an array
     * @param  array $pageSize (Optional) Number of records to pull per page (default is 30)
     * @param  array $skip     (Optional) offset for the items to pull (default is zero)
     * @return MongoCursor
     **/
    public function getPage(array $query, array $fields = array(), $pageSize = 30, $skip = 0)
    {
        return $this->_collection->find($query, $fields)->limit($pageSize)->skip($skip);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Queries the collection and returns just one
     *
     * @param  array $query  Query parameters in an associative array
     * @param  array $fields (Optional) List of fields to return in an array
     * @return array
     **/
    public function findOne(array $query, array $fields = array())
    {
        return $this->_collection->findOne($query, $fields);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Removes records from the collection
     *
     * @param  array $criteria Criteria for this removal (mongo version of the where clause)
     * @param  array $options  (Optional) List of options for this operation
     * @return mixed If safe option is sent, an array of information is returned, otherwise boolean
     **/
    public function remove(array $criteria, array $options = array())
    {
        return $this->_collection->remove($criteria, $options);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Saves an object to the collection
     *
     * @param  array $data    An associative array representing a record
     * @param  array $options (Optional) List of options for this operation
     * @return mixed If safe option is sent, an array of information is returned, otherwise boolean
     **/
    public function save(array $data, array $options = array())
    {
        return $this->_collection->save($data, $options);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Inserts an array into the collection
     *
     * @param  array $data    An associative array representing a new record
     * @param  array $options (Optional) List of options for this operation
     * @return mixed If safe option is sent, an array of information is returned, otherwise boolean
     **/
    public function insert(array $data, array $options = array())
    {
        return $this->_collection->insert($data, $options);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Updates records in the collection
     *
     * @param  array $criteria Criteria for this update (mongo version of the where clause)
     * @param  array $data     New  or updated fields and values for records matching the given criteria
     * @param  array $options  (Optional) List of options for this operation
     * @return mixed If safe option is sent, an array of information is returned, otherwise boolean
     **/
    public function update(array $criteria, array $data, array $options = array('upsert' => true, 'safe' => false))
    {
        return $this->_collection->update($criteria, array('$set' => $data), $options);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Adds new items to the nested array
     *
     * @param  array $criteria Criteria for this update (mongo version of the where clause)
     * @param  array $data     New  or updated fields and values for records matching the given criteria
     * @param  array $options  (Optional) List of options for this operation
     * @return mixed If safe option is sent, an array of information is returned, otherwise boolean
     **/
    public function addToSet(array $criteria, array $data, array $options = array('upsert' => true, 'safe' => false))
    {
        return $this->_collection->update($criteria, array('$addToSet' => $data), $options);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Deletes a value from a nested array
     *
     * @param  array $criteria Criteria for this update (mongo version of the where clause)
     * @param  array $data     New  or updated fields and values for records matching the given criteria
     * @param  array $options  (Optional) List of options for this operation
     * @return mixed If safe option is sent, an array of information is returned, otherwise boolean
     **/
    public function pull(array $criteria, array $data, array $options = array('upsert' => true, 'safe' => false))
    {
        return $this->_collection->update($criteria, array('$pull' => $data), $options);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns the collection for this object
     *
     * @return MongoCollection
     **/
    protected function _getCollection()
    {
        if (empty($this->_collection))
        {
            $this->_collection = $this->_getDatabase()->selectCollection($this->_collectionName);
            $this->_collection->setSlaveOkay(true);
        }

        return $this->_collection;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns the collection for this object
     *
     * @return MongoCollection
     **/
    protected function _ensureIndexes()
    {
        foreach ($this->_ensureIndexes as $index)
        {
            $options = (isset($index['options'])) ? $index['options'] : array();
            $fields  = (isset($index['fields'])) ? $index['fields'] : $index;
            $this->_collection->ensureIndex($fields, $options);
        }

        return $this->_collection;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns the database for this object
     *
     * @return MongoDB
     **/
    protected function _getDatabase()
    {
        if (empty($this->_database))
        {
             $this->_database = $this->_getConnection()->selectDB($this->_databaseName);
        }

        return $this->_database;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns the connection for this object
     *
     * @return Mongo
     **/
    protected function _getConnection()
    {
        if (empty($this->_connection))
        {
            $method            = $this->_connectionMethod;
            $this->_connection = Eb_Db_Connection::$method();
        }

        return $this->_connection;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Method of Backend Connection to call.
     *
     * @var string
     */
    protected $_connectionMethod = 'getMongoCache';

    /**
     * Name of the collection in the Mongo Database
     *
     * @var string
     */
    protected $_collectionName = '';

    /**
     * Name of the Mongo Database
     *
     * @var string
     */
    protected $_databaseName = 'default';

    /**
     * The Mongo connection
     *
     * @var Mongo
     */
    protected $_connection = null;

    /**
     * The database object
     *
     * @var MongoDB
     */
    protected $_database = null;

    /**
     * The collection object against which queries are run
     *
     * @var MongoCollection
     */
    protected $_collection = null;

    /**
     * An array of indexes
     *
     * @var array
     */
    protected $_ensureIndexes = array();

} // class Eb_MongoDataMapper
