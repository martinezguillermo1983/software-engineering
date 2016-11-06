<?php

function getRoot() {
  $variables = [];
  //  Get available airports
  $variables['airports'] = Airport::getAirportsDropdown();
  //  Get search parameters
  $departure_airport_id = getInputParameter('departure_airport_id');
  $destination_airport_id = getInputParameter('destination_airport_id');
  $seat_type = getInputParameter('seat_type');
  $flight_type = getInputParameter('flight_type');
  $departure_date = getInputParameter('departure_date');
  $return_date = getInputParameter('return_date');
  $passengers_number = getInputParameter('passengers_number');
  //  Search flights
  if ($departure_airport_id && $destination_airport_id && $departure_date) {
    $flight = new Flight();
    $results = $flight->where('departure_airport_id','=',$departure_airport_id)->where('destination_airport_id','=',$destination_airport_id)->orderBy('price_'.$seat_type,'asc')->get();    
    //  Populate results and parameters
    if (!empty($results)) {
      $variables['results'] = $results;
    }
  }

  $variables['departure_airport_id'] = $departure_airport_id;
  $variables['destination_airport_id'] = $destination_airport_id;
  $variables['seat_type'] = $seat_type;
  $variables['flight_type'] = $flight_type;
  $variables['departure_date'] = $departure_date;
  $variables['return_date'] = $return_date;
  $variables['passengers_number'] = $passengers_number;
  //  Render view
  render("root/root.html", $variables);
}