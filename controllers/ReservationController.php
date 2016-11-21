<?php

function getReservation($error=null) {
  $variables = [];
  //  Get search parameters
  $flight_id = getInputParameter('flight_id');
  $return_flight_id = getInputParameter('return_flight_id');
  $return_date = getInputParameter('return_date');
  $passengers_number = getInputParameter('passengers_number');
  $seat_type = getInputParameter('seat_type');
  $flight_type = getInputParameter('flight_type');
  $departure_date = getInputParameter('departure_date');
  //  Get flight
  $flight = new Flight();
  $flight = $flight->find($flight_id); 
  if (!empty($flight)) {   
    //  Populate result and parameters
    if ($flight_type == 'round_trip') {
    //  If round trip, get return flight
      $returnFlight = new Flight();
      $returnFlight = $returnFlight->find($return_flight_id); 
      $variables['return_flight'] = $returnFlight;
    }
    $variables['flight'] = $flight;
  } else {
    getNotFound();
  }
  $variables['seat_type'] = $seat_type;
  $variables['flight_type'] = $flight_type;
  $variables['departure_date'] = $departure_date;
  $variables['return_date'] = $return_date;
  $variables['passengers_number'] = $passengers_number;
  if ($error) $variables['error'] = $error;
  //  Render view
  render("reservation/reservation.html", $variables);
}

function postReservation() {
  $variables = [];
  //  Get search parameters
  $flight_id = getInputParameter('flight_id');
  $return_flight_id = getInputParameter('return_flight_id');
  $return_date = getInputParameter('return_date');
  $passengers_number = getInputParameter('passengers_number');
  $seat_type = getInputParameter('seat_type');
  $flight_type = getInputParameter('flight_type');
  $departure_date = getInputParameter('departure_date');
  $customers = getInputParameter('customer');


  // Validate form
  $errors = null;
  // Unique email
  $customer = new Customer();
  $customer = $customer->where('email', $customers[0]['email'])->first();
  if ($customer) {
    $errors[] = 'A customer with that email address already exists';
  }

  if (!empty($errors)) {
    //  Render view
    return getReservation($errors[0]);
  }


  // Save reservation
  $reservation = new Reservation();
  $reservationId = $reservation->insert([
    'seat_type' => $seat_type,
  ]);  
  // Save customers
  foreach ($customers as $key => $customerInput) {
    if ($key == 0) {
      // Save main Customer
      $address = new Address();
      $addressId = $address->insert([
        'address' => $customerInput['address'],
        'postal_code' => $customerInput['postal_code'],
        'city_id' => $customerInput['city_id']
      ]);
      $customer = new Customer();
      $pass = password_hash($customerInput['password'], PASSWORD_DEFAULT);
      $customerId = $customer->insert([
        'first_name' => $customerInput['first_name'],
        'last_name' => $customerInput['last_name'],
        'email' => $customerInput['email'],
        'address_id' => $addressId,
        'phone' => $customerInput['phone'],
        'password' => $pass
      ]);      
    } else {
      // Save secondary Customers
      $customer = new Customer();
      $customerId = $customer->insert([
        'first_name' => $customerInput['first_name'],
        'last_name' => $customerInput['last_name']
      ]);
    }
    // Save ReservationCustomer relationship
    $reservationCustomer = new ReservationCustomer();
    $reservationCustomerId = $reservationCustomer->insert([
      'reservation_id' => $reservationId,
      'customer_id' => $customerId,
      'order' => $key
    ]);  
  }

  // Save ReservationFlight relationship
  // Outbound Flight
  $date = new DateTime($departure_date);
  $reservationFlight = new ReservationFlight();
  $reservationFlightId = $reservationFlight->insert([
    'reservation_id' => $reservationId,
    'flight_id' => $flight_id,
    'departure_date' => $date->format('Y-m-d'),
    'order' => 0
  ]);
  if ($return_flight_id) {
    // Return Flight
    $date = new DateTime($return_date);
    $reservationFlight = new ReservationFlight();
    $reservationFlightId = $reservationFlight->insert([
      'reservation_id' => $reservationId,
      'flight_id' => $return_flight_id,
      'departure_date' => $date->format('Y-m-d'),
      'order' => 1
    ]);     
  }
 
  //  Redirect to thank-you page
  header("Location: ".$GLOBALS['baseUrl']."thank-you?reservation=".$reservationId);
}

