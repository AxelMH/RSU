<?php

/**
 * Load a document from the DB using $id as its key
 * Note that the _id will be combined with "_TYPE: $docType" for the query
 * @param string $id
 * @return bool
 */
function loadById(string $id) {
    $filter = ['_id' => $id, '_TYPE' => $this->docType];
    $options = ['limit' => 1];
    $query = new MongoDB\Driver\Query($filter, $options);
    $rows = $this->dbManager->executeQuery($this->dbName . '.' . $this->dbCollection, $query);
    $rows->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

    $this->doc = $rows->toArray()[0] ?? null;

    if (!is_null($this->doc)) {
        $this->afterLoad();
    }

    return !is_null($this->doc);
}

function save() {
    // put _id and _TYPE on top of document, and Status at bottom
    $doc['_id'] = $this->doc['_id'];
    $doc['_TYPE'] = $this->doc['_TYPE'];
    unset($this->doc['_id']);
    unset($this->doc['_TYPE']);
    $doc = array_merge($doc, $this->doc);
    unset($doc['Status']);
    $doc['Status'] = $this->doc['Status'];
    $this->doc = $doc;

    $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);

    if ($isNew) {
        $bulk->insert($this->doc);
    } else {
        $bulk->update(['_id' => $this->doc['_id']], $this->doc);
    }

    try {
        $result = $this->dbManager->executeBulkWrite($this->dbName . '.' . $this->dbCollection, $bulk, $writeConcern);
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
        error_log("ERROR MongoDB Other: ", $e->getMessage());
    }

    return $result;
}
