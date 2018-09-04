<?php

namespace App\Model;

abstract class Model
{
    /** @var \App\Database */
    protected $db;
    /** @var callable $builder */
    protected $builder;
    /** @var \Slim\Container */
    protected $di;

    const STATE_DISABLED     = 0;
    const STATE_ENABLED      = 1;

    const SORT_ASC =  1;
    const SORT_DESC = 0;

    public function __construct($di, $builder) {
        $this->db = $di['db'];
        $this->di = $di;
        $this->builder = $builder;
    }

    protected function getDatabase() {
        return $this->db;
    }

    abstract public function getTableName();

    private function selectOrDeleteByEntityData($data, $limit = 0, $offset = 0, $sort = null, $delete = false) {
        if (is_null($data) || $data === array() || !(is_array($data))) {
            return null;
        }

        $table = $this->getTableName();

        $query = $delete ? 'DELETE FROM ' : 'SELECT * FROM ';

        $whereStr = implode(' AND ', array_map(function($col) { return "`$col` = :$col"; }, array_keys($data)));

        $query = "$query `$table` WHERE $whereStr";
        if ($limit > 0 && !$delete) {
            $query .= ' LIMIT :xLimit';
            $data = array_merge($data, array('xLimit' => $limit));
        }
        if ($offset > 0 && !$delete) {
            $query .= ' OFFSET :xOffset';
            $data = array_merge($data, array('xOffset' => $offset));
        }
        if (!is_null($sort)) {
            $query .= ' ORDER BY `' . $sort[0] . ($sort[1] ? '` ASC' : '` DESC');
        }
        $this->getDatabase()->prepare($query);

        foreach ($data as $param => $value) {
            $this->getDatabase()->bindValue(':'.$param, $value);
        }
        $this->getDatabase()->execute();

        if ($delete) {
            return true;
        }

        $rows = $this->getDatabase()->fetchAll();

        if (!$rows) {
            return array();
        }

        $builder = $this->builder;
        return array_map(function($row) use ($builder) {
            return $builder($row, $this, $this->di);
        }, $rows);
    }

    public function getByEntityData($data, $limit = 0, $offset = 0, $sort = null) {
        return $this->selectOrDeleteByEntityData($data, $limit, $offset, $sort, false);
    }

    public function deleteByEntityData($data) {
        return $this->selectOrDeleteByEntityData($data, 0, 0, null, true);
    }

    public function getById($id) {
        if (!ctype_digit("$id")) {
            return null;
        }

        return $this->getByEntityData(['id' => $id], 1)[0];
    }

    public function getEntitiesFromIds($ids) {
        if (!$ids) {
            return array();
        }

        return array_map(function ($id) {
            return $this->getById($id);
        }, $ids);
    }

    public function getAll() {
        $table = $this->getTableName();
        $query = "SELECT * FROM `$table` WHERE 1=1";
        $rows = $this->getDatabase()->prepare($query)->execute()->fetchAll();
        if(!$rows) return array();
        $builder = $this->builder;
        return array_map(function($row) use ($builder) {
            return $builder($row, $this, $this->di);
        }, $rows);
    }

    public function getEnabled() {
        return $this->getByEntityData(['state' => self::STATE_ENABLED]);
    }

    public function createEntity($arr) {
        if (!is_array($arr)) {
            throw new \InvalidArgumentException("createEntity expects array as argument");
        }

        $builder = $this->builder;
        $entity = $builder($arr, $this, $this->di);
        $entity->setNew();
        return $entity;
    }

    public function save(\App\Entity\Entity $entity) {
        $table = $this->getTableName();
        $entity = clone $entity;

        if ($entity->isNew()) {
            $values = $entity->getSaveableData();

            $values = array_merge($values, array(
                'create_timestamp' => time(),
                'update_timestamp' => 0,
            ));

            $cols = $vals = '';
            if ($values) {
                $cols = '`'.implode('`, `', array_keys($values)).'`';
                $vals = ':'.implode(', :', array_keys($values));
            }

            // this query is fine even if $cols/$vals are empty
            $sql = "INSERT INTO `$table` ($cols) VALUES ($vals)";
            $this->getDatabase()->prepare($sql);
            foreach($values as $param => $value)
            {
                $this->getDatabase()->bindValue(':'.$param, $value);
            }
            $this->getDatabase()->execute();

            $id = $this->getDatabase()->lastInsertId();
            if (is_null($id)) {
                throw new \InvalidArgumentException("Unable to save entity - failed to retrieve id after save");
            }

            return  $this->getById($id);
        }

        if ($entity->hasChanged()) {
            $id = $entity->id;
            if (!ctype_digit("$id")) {
                throw new \InvalidArgumentException("Unable to save entity - primary key not set");
            }

            $values = $entity->getChangedValues();

            if (count($values) == 0) {
                throw new \InvalidArgumentException('Unable to save entity - nothing was changed, but marked as changed');
            }

            $values = array_merge($values, array(
                'update_timestamp' => time(),
            ));

            if (array_key_exists('id', $values)) {
                throw new \InvalidArgumentException("Unable to save entity - primary key was changed");
            }

            $cols = array_keys($values);
            $cols = array_map(function($col) { return "`$col` = :$col"; }, $cols);
            $sql = "UPDATE `$table` SET " . implode(', ', $cols) . " WHERE `id` = :id";

            // add id columns for the query execution
            $values = array_merge($values, ['id' => $id]);

            $this->getDatabase()->prepare($sql);
            foreach($values as $param => $value)
            {
                $this->getDatabase()->bindValue(':'.$param, $value);
            }
            $this->getDatabase()->execute();

            return $this->getById($id);
        }

        return $entity;
    }

    public function deleteById($id) {
        if (!ctype_digit("$id")) {
            return false;
        }

        return $this->deleteByEntityData(['id' => $id]);
    }

    public function customQuery($sql, $binds) {
        $query = 'SELECT * FROM `' . $this->getTableName() . ' WHERE ' . $sql;
        $this->getDatabase()->prepare($query);
        foreach ($binds as $param => $value) {
            $this->getDatabase()->bindValue(':'.$param, $value);
        }
        $this->getDatabase()->execute();
        $rows = $this->getDatabase()->fetchAll();
        if (!$rows) {
            return array();
        }
        $builder = $this->builder;
        return array_map(function($row) use ($builder) {
            return $builder($row, $this, $this->di);
        }, $rows);
    }
}
