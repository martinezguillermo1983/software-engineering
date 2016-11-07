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
  // Validate form
  $errors = null;
  if ($departure_date !== null && empty($departure_date))
    $errors[] = 'Please select a departure date';
  if ($flight_type == 'round_trip') {
    if ($return_date !== null && empty($return_date))
      $errors[] = 'Please select a return date';
    if ($departure_date > $return_date)
      $errors[] = 'Return date must be greater than departure date';    
  }

  //  Search flights
  if ($errors === null && $departure_airport_id && $destination_airport_id && $departure_date) {
    $flight = new Flight();
    $results = $flight->where('departure_airport_id','=',$departure_airport_id)->where('destination_airport_id','=',$destination_airport_id)->orderBy('price_'.$seat_type,'asc')->get();    
  //  Populate results and parameters
  //  Add cheapest return flight if roundtrip and recalculate prices
    if ($flight_type == 'round_trip') {
      foreach ($results as $key => $result) {
        $results[$key]->cheapest_return_flight = $result->getCheapestReturnFlight($seat_type);
        $results[$key]->price_economy += $results[$key]->cheapest_return_flight->price_economy;
        $results[$key]->price_business += $results[$key]->cheapest_return_flight->price_business;
      }
    }
    $variables['results'] = $results;
  }
  $variables['departure_airport_id'] = $departure_airport_id;
  $variables['destination_airport_id'] = $destination_airport_id;
  $variables['seat_type'] = $seat_type;
  $variables['flight_type'] = $flight_type;
  $variables['departure_date'] = $departure_date;
  $variables['return_date'] = $return_date;
  $variables['passengers_number'] = $passengers_number;
  $variables['error'] = $errors[0];
  //  Render view
  render("root/root.html", $variables);
}