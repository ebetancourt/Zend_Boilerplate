<?php
/**
 * Eb.
 *
 * @category   Eb
 * @package    Eb_Datamapper
 * @subpackage DataMapper
 * @author     Elliot Betancourt
 */

/**
 * A proof of concept and test to illustrate creating a simple DataMapper for mongo collections.
 *
 * @category   Eb
 * @package    Eb_Datamapper
 * @subpackage DataMapper
 * @author     Elliot Betancourt
 */
class Eb_DataMapper_MongoTest extends Eb_MongoDataMapper
{
    /**
     * Name of the collection in the Mongo Database
     *
     * @var string
     */
    protected $_collectionName = 'user';

} // class Eb_DataMapper_MongoTest
