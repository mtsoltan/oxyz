<?php
namespace App;

use App\Exception\DatabaseException;

class Database extends \PDO
{
    private $serializeObjects = false;
    protected $default;
    protected $initial;
    protected $destroy;
    protected $debug;
    protected $useBuffered;
    /** @var \PDOStatement $currentStatement */
    protected $currentStatement;
    protected $queries = [];
    /** @var \PDOStatement[] $preparedStatements */
    protected $preparedStatements = [];
    protected $boundValues = [];
    protected $resultSets = [];

    public function __construct($di, $debug) {
        $c = $di['config'];
        $this->default = $c['database.default'];
        $this->initial = $c['database.initial'];
        $this->destroy = $c['database.destroy'];
        $dsn = sprintf($c['database.dsn'], $c['database.name'], $c['database.port']);
        $this->debug = $debug;
        parent::__construct($dsn, $c['database.username'], $c['database.password'], array(
            self::ATTR_PERSISTENT => false,
            self::ATTR_ERRMODE => self::ERRMODE_EXCEPTION,
            self::MYSQL_ATTR_INIT_COMMAND => "set time_zone = '+00:00';",
            self::ATTR_EMULATE_PREPARES => false, // emulated prepares ignore param hinting when binding
            self::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", // Allows utf8 characters.
        ));
        $this->useBuffered =  array(self::MYSQL_ATTR_USE_BUFFERED_QUERY => true);
        if (!$this->query("SHOW TABLES")->fetchAll()) {
            $this->initialize();
        }
        $this->query('SET session wait_timeout=28800')->fetchAll();
        $this->query('SET session interactive_timeout=28800')->fetchAll();
    }

    /**
     * @param bool $query
     * @throws DatabaseException
     */
    public function dbDie($query = false) {
        $endable = $query ? $this->queries : $this->preparedStatements;
        throw new DatabaseException($this->errorInfo() . ' :: ' . end($endable));
    }

    public function query($q) {
        if (strpos($q, ';') !== false) {
            foreach (explode(';', $q) as $qShard) {
                $this->query($qShard);
            }
            return $this;
        }
        if (!strlen(trim($q))) return $this;
        $this->queries[] = $q;
        $this->currentStatement = parent::query($q);
        return $this;
    }

    public function prepare($q, $options = null) {
        $this->preparedStatements[] = $q;
        if (is_null($options)) $options = $this->useBuffered;
        else $options = array_merge($options, $this->useBuffered);
        $this->currentStatement = parent::prepare($q, $options);
        return $this;
    }

    public function bindValue($sql_param, $value, $type = NULL) {
        if (is_object($value) || is_array($value)) {
            if ($this->serializeObjects) $value = serialize($value);
            else throw new DatabaseException('Attempting to bind an object or array.');
        }
        if (is_null($type)) $type = self::PARAM_STR;
        if (!$this->currentStatement) $this->dbDie();
        if ($this->debug) {
            $this->boundValues[count($this->preparedStatements)][] = "$sql_param = $value";
        }
        if ($value === false) $value = 0;
        if ($value === true) $value = 1;
        if (ctype_digit("$value")) {
            $type = self::PARAM_INT;
        }
        if (is_array($value)) {
            $value = serialize($value);
            $type = self::PARAM_STR;
        }
        $this->currentStatement->bindValue($sql_param, $value, $type);
        return $this;
    }

    public function execute() {
        if (!$this->currentStatement) $this->dbDie();
        $this->currentStatement->execute();
        return $this;
    }

    public function fetchAll($fetchType = NULL, $finalize = true) {
        if (is_null($fetchType)) $fetchType = self::FETCH_ASSOC;
        if (!$this->currentStatement) $this->dbDie(true);
        $rv = $this->currentStatement->fetchAll($fetchType);
        if ($finalize) {
            $this->currentStatement->closeCursor();
            $this->currentStatement = NULL;
        }
        if ($this->debug) $this->resultSets[] = $rv !== false ? $rv : array();
        return $rv;
    }

    public function fetch($fetchType = NULL) {
        if (is_null($fetchType)) $fetchType = self::FETCH_ASSOC;
        if (!$this->currentStatement) $this->dbDie(true);
        $rv = $this->currentStatement->fetch($fetchType);
        if ($this->debug) $this->resultSets[] = $rv !== false ? array($rv) : array();
        return $rv;
    }

    /**
     * Resets the database. Be very careful when calling this.
     * @throws DatabaseException
     */
    public function reset() {
        $this->destroy();
        $this->initialize();
    }

    /**
     * Private function to destroy the databse.
     * @see Database::reset()
     */
    private function destroy() {
        $destroy = file_get_contents($this->destroy);
        $this->exec($destroy);
    }

    /**
     * Initializes the database, should be called once if the tables don't exist.
     * @throws DatabaseException
     */
    private function initialize() {
        ini_set('max_execution_time', 300);
        $initial = file_get_contents($this->initial);
        $default = '';
        if (is_file($this->default)) {
            $default = file_get_contents($this->default);
        }
        if (is_dir($this->default)) {
            foreach (scandir($this->default) as $file) {
                $fname = $this->default . DIRECTORY_SEPARATOR . $file;
                if (is_file($fname)) {
                    $default .= file_get_contents($fname);
                }
            }
        }
        $this->exec($initial);
        if ($default) {
            // We will execute statements one by one, to be able to handle errors in a better manner.
            $defaultArray = explode(';', $default);
            bdump($defaultArray);
            foreach ($defaultArray as $dae) {
                if (trim($dae) && $this->exec($dae) === false) { // This should have an unhandled throw on $this->exec()
                    throw new DatabaseException($dae); // Thus, this die statement should never execute.
                }
            }
        }
    }
}
