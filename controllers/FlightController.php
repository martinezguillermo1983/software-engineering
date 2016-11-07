<?php

function getFlight() {
  $variables = [];
  //  Get search parameters
  $flight_id = getInputParameter('flight_id');
  $seat_type = getInputParameter('seat_type');
  $flight_type = getInputParameter('flight_type');
  $departure_date = getInputParameter('departure_date');
  $return_date = getInputParameter('return_date');
  $passengers_number = getInputParameter('passengers_number');
  //  Get flight

  $flight = new Flight();
  $result = $flight->find($flight_id); 
  if (!empty($result)) {   
    //  Populate result and parameters
    if ($flight_type == 'round_trip') {
    //  If round trip, get return flights
      $result->return_flights = $result->getReturnFlights($seat_type);

    }
    $variables['flight'] = $result;
  } else {
    getNotFound();
  }
  $variables['seat_type'] = $seat_type;
  $variables['flight_type'] = $flight_type;
  $variables['departure_date'] = $departure_date;
  $variables['return_date'] = $return_date;
  $variables['passengers_number'] = $passengers_number;
  //  Render view
  render("flight/flight.html", $variables);
}