<?php 

require __DIR__ . '/vendor/autoload.php';

class Model extends \Pixie\QueryBuilder\QueryBuilderHandler {

  public $table;
  public $primaryKey = 'id';
  public $query;
  public $relationships = [];

  function __construct() {
  //  Create DB connection
    $config = array(
                'driver'    => 'mysql', // Db driver
                'host'      => $GLOBALS['db_host'],
                'database'  => $GLOBALS['db_database'],
                'username'  => $GLOBALS['db_username'],
                'password'  => $GLOBALS['db_password']
            );
    $connection = new \Pixie\Connection('mysql', $config);
  //  Create QueryBuilder instance
    parent::__construct($connection);
  //  Set the table name based on the children called Class
    $this->from($this->table);
  }

  //  Override QueryBuilder get method to convert results to the called Class
  public function get() {
    $results = parent::get();
    $className = get_called_class();
    foreach ($results as $key => $result) {
      $result = static::cast($result, $className);
      $result->loadRelationships();
      $results[$key] = $result;
    }
    return $results;
  }

  //  Override QueryBuilder first method to convert results to the called Class
  public function first() {
    $result = parent::first();
    $className = get_called_class();
    return static::cast($result, $className);
  }

  private function loadRelationships() {
    foreach ($this->relationships as $relationship) {
      $relationshipInstance = new $relationship['relationshipClass']();
      $relationshipInstance->where($relationshipInstance->primaryKey, $this->$relationship['foreignKey']);
      switch ($relationship['relationshipType']) {
        case 'has_one':
          $this->$relationship['relationshipName'] = $relationshipInstance->first();
          break;
        case 'has_many':
          $this->$relationship['relationshipName'] = $relationshipInstance->get();
          break;        
      }  
    }    
  }

  /**
   * Cast an object into a different class.
   *
   * @param object $object The object to cast.
   * @param string $class The class to cast the object into.
   * @return object
   */
  private static function cast($object, $class) {
    if( !is_object($object) ) 
      throw new InvalidArgumentException('$object must be an object.');
    if( !is_string($class) )
      throw new InvalidArgumentException('$class must be a string.');
    if( !class_exists($class) )
      throw new InvalidArgumentException(sprintf('Unknown class: %s.', $class));
    /**
     * This is a beautifully ugly hack.
     *
     * First, we serialize our object, which turns it into a string, allowing
     * us to muck about with it using standard string manipulation methods.
     *
     * Then, we use preg_replace to change it's defined type to the class
     * we're casting it to, and then serialize the string back into an
     * object.
     */
    return unserialize(
      preg_replace(
        '/^O:\d+:"[^"]++"/', 
        'O:'.strlen($class).':"'.$class.'"',
        serialize($object)
      )
    );
  }

}

class Aircraft extends Model {

  public $table = 'aircraft';

}

class Airline extends Model {

  public $table = 'airline';

}

class Flight extends Model {

  public $table = 'flight';
  public $primaryKey = 'id';
  public $relationships = [
    [
      'relationshipName' => 'airline',
      'relationshipClass' => 'Airline',
      'relationshipType' => 'has_one',
      'foreignKey' => 'airline_id'
    ],
  ];

}

class Airport extends Model {

  public $table = 'airport';

  public static function getAirportsDropdown() {
    $airports = new self();
    $airports = $airports->get();
    $options = [];
    foreach ($airports as $key => $airport) {
      $options[$airport->id] = "(".$airport->id.") " .$airport->name;
    }
    return $options;
  }

}