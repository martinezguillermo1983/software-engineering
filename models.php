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
    if( is_object($result) ) {
      $className = get_called_class();
      return static::cast($result, $className);      
    }
    return $result;
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
    [
      'relationshipName' => 'departure_airport',
      'relationshipClass' => 'Airport',
      'relationshipType' => 'has_one',
      'foreignKey' => 'departure_airport_id'
    ],
    [
      'relationshipName' => 'destination_airport',
      'relationshipClass' => 'Airport',
      'relationshipType' => 'has_one',
      'foreignKey' => 'destination_airport_id'
    ],
  ];

  public function getCheapestReturnFlight($seat_type) {
    $returnFlight = new Flight();
    return $returnFlight
      ->where('departure_airport_id', $this->destination_airport_id)
      ->where('destination_airport_id', $this->departure_airport_id)
      ->orderBy('price_'.$seat_type, 'asc')
      ->first();
  }

  public function getReturnFlights($seat_type) {
    $returnFlights = new Flight();
    $results = $returnFlights
      ->where('departure_airport_id', $this->destination_airport_id)
      ->where('destination_airport_id', $this->departure_airport_id)
      ->orderBy('price_'.$seat_type, 'asc')
      ->get();
  //  Add prices to current flight
    foreach ($results as $key => $returnFlight) {
      $results[$key]->price_economy += $this->price_economy;
      $results[$key]->price_business += $this->price_business;      
    }
    return $results;
  }
}

class Airport extends Model {

  public $table = 'airport';
  public $relationships = [
    [
      'relationshipName' => 'address',
      'relationshipClass' => 'Address',
      'relationshipType' => 'has_one',
      'foreignKey' => 'address_id'
    ],
  ];

  public static function getAirportsDropdown() {
    $airports = new self();
    $airports = $airports->get();
    $options = [];
    foreach ($airports as $key => $airport) {
      $options[$airport->id] = $airport->address->city->name . ' (' . $airport->id . ')';
    }
    return $options;
  }

}

class Address extends Model {

  public $table = 'address';
  public $relationships = [
    [
      'relationshipName' => 'city',
      'relationshipClass' => 'City',
      'relationshipType' => 'has_one',
      'foreignKey' => 'city_id'
    ],
  ];
}

class City extends Model {

  public $table = 'city';
  public $relationships = [
    [
      'relationshipName' => 'province_state',
      'relationshipClass' => 'ProvinceState',
      'relationshipType' => 'has_one',
      'foreignKey' => 'province_state_id'
    ],
  ];

}

class ProvinceState extends Model {

  public $table = 'province_state';
  public $relationships = [
    [
      'relationshipName' => 'country',
      'relationshipClass' => 'Country',
      'relationshipType' => 'has_one',
      'foreignKey' => 'country_id'
    ],
  ];
}

class Country extends Model {

  public $table = 'country';

}

class Customer extends Model {

  public $table = 'customer';
  public $relationships = [
    [
      'relationshipName' => 'address',
      'relationshipClass' => 'Address',
      'relationshipType' => 'has_one',
      'foreignKey' => 'address_id'
    ],
  ];

}

class Reservation extends Model {

  public $table = 'reservation';

  public function getCustomers() {
    $customer = new Customer();
    return $customer
      ->join('reservation_customer', 'reservation_customer.customer_id', '=', 'id')
      ->where('reservation_customer.reservation_id', $this->id)
      ->orderBy('reservation_customer.order', 'asc')
      ->get();
  }

  public function getFlights() {
    $flight = new Flight();
    return $flight
      ->join('reservation_flight', 'reservation_flight.flight_id', '=', 'id')
      ->where('reservation_flight.reservation_id', $this->id)
      ->orderBy('reservation_flight.order', 'asc')
      ->get();
  }
}

class ReservationCustomer extends Model {

  public $table = 'reservation_customer';

}

class ReservationFlight extends Model {

  public $table = 'reservation_flight';

}