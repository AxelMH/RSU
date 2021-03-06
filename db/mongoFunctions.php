<?php

/**
 * Save array to db on collName
 * @global type $manager
 * @param array $array
 * @param string $dbName
 * @param string $collName
 * @return type
 */
function save(array $array, string $dbName, string $collName) {
    global $manager;

    $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);

    $bulk->insert($array);

    try {
        $result = $manager->executeBulkWrite($dbName . '.' . $collName, $bulk, $writeConcern);
    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
        $result = $e->getWriteResult();

        // Check if the write concern could not be fulfilled
        if ($writeConcernError = $result->getWriteConcernError()) {
            error_log('ERROR MongoDB writeConcernError: ' . $writeConcernError->getMessage() . ' (' . $writeConcernError->getCode() . ')');
        }

        // Check if any write operations did not complete at all
        foreach ($result->getWriteErrors() as $writeError) {
            error_log("ERROR MongoDB Operation #" . $writeError->getIndex() . ' ' . $writeError->getMessage() . ' (' . $writeError->getCode() . ')');
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        error_log("ERROR MongoDB Other: " . $e->getMessage());
    }

    return $result;
}

/**
 * Update query with array
 * @global type $manager
 * @param array $array
 * @param array $query
 * @param string $dbName
 * @param string $collName
 * @return type
 */
function update(array $query, array $array, string $dbName, string $collName) {
    global $manager;

    // Create a bulk write object and add our update operation
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
    $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
    $bulk->update($query, ['$set' => $array], []);

    try {
        $result = $manager->executeBulkWrite($dbName . '.' . $collName, $bulk, $writeConcern);
    } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
        $result = $e->getWriteResult();

        // Check if the write concern could not be fulfilled
        if ($writeConcernError = $result->getWriteConcernError()) {
            error_log('ERROR MongoDB writeConcernError: ' . $writeConcernError->getMessage() . ' (' . $writeConcernError->getCode());
        }

        // Check if any write operations did not complete at all
        foreach ($result->getWriteErrors() as $writeError) {
            error_log("ERROR MongoDB Operation #" . $writeError->getIndex() . ' ' . $writeError->getMessage() . ' (' . $writeError->getCode() . ')');
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        error_log("ERROR MongoDB Other: " . $e->getMessage());
    }

    return $result;
}

/**
 * 
 * @global type $manager
 * @param array $filter
 * @param string $dbName
 * @param string $collName
 * @param array $options
 * @return array
 */
function find(array $filter, string $dbName, string $collName, array $options = []) {
    global $manager;

    $query = new MongoDB\Driver\Query($filter, $options);
    $rows = $manager->executeQuery($dbName . '.' . $collName, $query);
    $rows->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

    $doc = $rows->toArray() ?? null;

    return $doc;
}

/**
 * 
 * @global type $manager
 * @param array $filter
 * @param string $dbName
 * @param string $collName
 * @param array $options
 * @return array
 */
function findOne(array $filter, string $dbName, string $collName, array $options = []) {
    global $manager;

    $options['limit'] = 1;
    $query = new MongoDB\Driver\Query($filter, $options);
    $rows = $manager->executeQuery($dbName . '.' . $collName, $query);
    $rows->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

    $doc = $rows->toArray()[0] ?? null;

    return $doc;
}

/**
 * 
 * @global type $manager
 * @param string $field field for which we want to get the distinct values
 * @param string $dbName
 * @param string $collName
 * @param type $query
 * @return type
 */
function distinct(string $field, string $dbName, string $collName, $query = ['_id' => ['$ne' => '']]) {
    //gereric query that finds everything because im lazy 
    // and dont want to find out how to do the command with empty query
    global $manager;

    $cmd = new MongoDB\Driver\Command([
        // build the 'distinct' command
        'distinct' => $collName, // specify the collection name
        'key' => $field, // specify the field for which we want to get the distinct values
        'query' => $query // criteria to filter documents
    ]);
    $cursor = $manager->executeCommand($dbName, $cmd); // retrieve the results
    $result = current($cursor->toArray())->values; // get the distinct values as an array

    return $result;
}

function delete($query, $dbname, $collection) {
    global $manager;

    if (empty($dbname)) {
        throw new Exception(EXCEPTMSG_DBNAME);
    }
    if (empty($collection)) {
        throw new Exception(EXCEPTMSG_COLLECTION);
    }

    /* MongoDB */
    $mongoBulk = new MongoDB\Driver\BulkWrite((['ordered' => true]));
    $mongoBulk->delete($query);
    $manager->executeBulkWrite($dbname . '.' . $collection, $mongoBulk);
}
