<?php

class lib_db
{

    private $db;
    private $trans_ing = false;
    private $db_err;
    private $model;
    private $fields = array();
    private static $dbInstance;

    public function getInstance()
    {
        if (is_null(self::$dbInstance)) {
            self::$dbInstance = $this->_connect();
        }
        return self::$dbInstance;
    }

    private function _connect()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT;
        for ($i = 0; $i < 3; $i++) {
            try {
                $this->db = new PDO($dsn, DB_USER, DB_PASS, array(
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_AUTOCOMMIT => true,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                ));
                return $this->db;
            } catch (Exception $e) {
                if ($i >= 2) throw new Exception('连接数据库失败，请重试');
            }
        }
    }

    public function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT;
        for ($i = 0; $i < 3; $i++) {
            try {
                $this->db = new PDO($dsn, DB_USER, DB_PASS, array(
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_AUTOCOMMIT => true,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                ));
                break;
            } catch (Exception $e) {
                if ($i >= 2) throw new Exception('连接数据库失败，请重试');
            }
        }
    }

    protected function setModel()
    {

    }

    protected function setTable()
    {

    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getError()
    {
        return $this->db_err;
    }

    //插入
    public function insert($table, $data)
    {
        $fields = '';
        $values = '';
        $realValues = array();
        foreach ($data as $k => $v) {
            if ($v === null) {
                continue;
            }
            $fields .= '`' . $k . '`,';
            $values .= ":{$k},";
            $realValues[":{$k}"] = strval($v);
        }
        $sql = 'INSERT INTO `' . $table . '` (' . substr($fields, 0, -1) . ') VALUES (' . substr($values, 0, -1) . ')';
        $pre = $this->db->prepare($sql);
        if (!$pre) {
            return false;
        }
        $result = $pre->execute($realValues);
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    //更新
    public function update($table, $data, $where = array())
    {
        $fields = '';
        $realValues = array();
        foreach ($data as $k => $v) {
            if ($v === null) {
                continue;
            }
            $fields .= ",`{$k}`=:{$k}";
            $realValues[":{$k}"] = strval($v);
        }
        $fields = substr($fields, 1, strlen($fields) - 1);

        $whereSql = '';
        $whereValues = array();
        foreach ($where as $k => $v) {
            $whereSql .= " AND `{$k}`=:{$k}_1";
            $whereValues[":{$k}_1"] = strval($v);
        }
        $realValues = array_merge($whereValues, $realValues);
        $sql = "UPDATE `{$table}` SET {$fields} WHERE 1 {$whereSql}";
        $pre = $this->db->prepare($sql);
        if (!$pre) {
            return false;
        }
        try {
            $ret = $pre->execute($realValues);
            return $ret;
        } catch (Exception $e) {
            $this->db_err = $e->getMessage();
            return false;
        }
    }

    //查询
    public function search($table, $where = array(), $fields = "*", $order = "", $limit = 10000)
    {
        $values = array();
        $whereSql = "";
        foreach ($where as $k => $v) {
            if ($v === null) {
                continue;
            }
            $whereSql .= " AND `{$k}`=:{$k}";
            $values[":{$k}"] = $v;
        }
        $sql = "SELECT {$fields} FROM {$table} WHERE 1 {$whereSql} {$order} LIMIT {$limit}";
        $pre = $this->db->prepare($sql);
        if (!$pre) {
            return false;
        }
        try {
            $pre->execute($values);
            return $pre->fetchAll();
        } catch (Exception $e) {
            $this->db_err = $e->getMessage();
            return false;
        }
    }

    //单条
    public function find($table, $where = array(), $fields = "*", $order = '')
    {
        $values = array();
        $whereSql = "";
        foreach ($where as $k => $v) {
            if ($v === null) {
                continue;
            }
            $whereSql .= " AND `{$k}`=:{$k}";
            $values[":{$k}"] = $v;
        }
        $sql = "SELECT {$fields} FROM {$table} WHERE 1 {$whereSql} {$order} LIMIT 1";
        $pre = $this->db->prepare($sql);
        if (!$pre) {
            return false;
        }
        try {
            $pre->execute($values);
            $ret = $pre->fetchAll();
            if (count($ret) > 0) {
                return $ret[0];
            }
            return $ret;
        } catch (Exception $e) {
            $this->db_err = $e->getMessage();
            return false;
        }
    }

    public function delete($table, $where = array(), $order = "", $count = 10000)
    {
        $values = array();
        $whereSql = "";
        foreach ($where as $k => $v) {
            if ($v === null) {
                continue;
            }
            $whereSql .= " AND `{$k}`=:{$k}";
            $values[":{$k}"] = $v;
        }
        $sql = "DELETE FROM {$table} WHERE 1 {$whereSql} LIMIT {$count}";
        $pre = $this->db->prepare($sql);
        if (!$pre) {
            return false;
        }
        try {
            return $pre->execute($values);
        } catch (Exception $e) {
            $this->db_err = $e->getMessage();
            return false;
        }
    }

    public function query($sql, $return)
    {
        try {
            switch ($return) {
                case 0:
                    $result = $this->db->exec($sql);
                    break;
                case 1:
                    $this->db->exec($sql);
                    $result = $this->db->lastInsertId();
                    break;
                case 2:
                case 3:
                    $query = $this->db->query($sql);
                    $action = $return === 2 ? 'fetch' : 'fetchAll';
                    $result = call_user_func_array(array($query, $action), array(PDO::FETCH_ASSOC));
                    $query->closeCursor();
                    break;
                default:
                    throw new Exception('_UNKNOW_RETURN_: ' . $return);
            }
            return $result;
        } catch (Exception $e) {
            $data = array(
                'TYPE' => 'MYSQL',
                'SQL' => $sql,
            );
            core::logger($data);
            core::logger($e);
            if ($this->trans_ing) {
                throw new Exception('执行数据库操作失败，请重试');
            } else {
                core::error('执行数据库操作失败，请重试');
            }
        }
    }

    public function transaction($command)
    {
        try {
            switch ($command) {
                case 'begin':
                    $this->db->beginTransaction();
                    $this->trans_ing = true;
                    break;

                case 'commit':
                    $this->db->commit();
                    $this->trans_ing = false;
                    break;

                case 'rollBack':
                    $this->db->rollBack();
                    break;

                default:
            }
        } catch (Exception $e) {
            $data = array(
                'TYPE' => 'MYSQL',
                'SQL' => $sql,
            );
            core::logger($data);
            if ($this->trans_ing) {
                throw new Exception('执行数据库操作失败，请重试');
            } else {
                core::error('执行数据库操作失败，请重试');
            }
        }
    }

    public function __destruct()
    {
        $this->db = null;
    }

}