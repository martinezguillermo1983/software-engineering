<?php

function getThankYou() {
  $variables = [];
  $reservation_id = getInputParameter('reservation');
  $reservation = new Reservation();
  $variables['reservation'] = $reservation->find($reservation_id);
  $variables['customers'] = $variables['reservation']->getCustomers();
  $variables['flights'] = $variables['reservation']->getFlights();
  //  Render view
  render("thank-you/thank-you.html", $variables);
}

