<?php 

require __DIR__ . '/vendor/autoload.php';

$departure_airport_id = $_POST['departure_airport_id'];
$destination_airport_id = $_POST['destination_airport_id'];

$results = findFlights($departure_airport_id, $destination_airport_id);

foreach ($results as $key => $result) {
  print $result->airline_id . ' ' . $result->id . '<br>';
}




function findFlights($departure_airport_id, $destination_airport_id) {
  $flight = new Flight();
  $results = $flight->query->where('departure_airport_id','=',$departure_airport_id)->where('destination_airport_id','=',$destination_airport_id)->get();
  return $results;
}







class Model {

  public $table;
  public $query;

  function __construct() {
    // $className = get_called_class();
    $config = array(
                'driver'    => 'mysql', // Db driver
                'host'      => 'localhost',
                'database'  => 'clientserver',
                'username'  => 'root',
                'password'  => '',
                // 'charset'   => 'utf8', // Optional
                // 'collation' => 'utf8_unicode_ci', // Optional
                // // 'prefix'    => 'cb_', // Table prefix, optional
                // 'options'   => array( // PDO constructor options, optional
                //     PDO::ATTR_TIMEOUT => 5,
                //     PDO::ATTR_EMULATE_PREPARES => false,
                // ),
            );

    new \Pixie\Connection('mysql', $config, 'QB');
    $this->query = QB::table($this->table);
  }

}

class Aircraft extends Model {

  public $table = 'aircraft';

}

class Flight extends Model {

  public $table = 'flight';

}