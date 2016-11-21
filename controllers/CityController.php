<?php

function searchCity() {
  //  Get search parameters
  $city_name = getInputParameter('city_name');
  if (empty($city_name)) {
    print "[]";
    return;
  }
  //  Get cities
  $cities = new City();
  $cities = $cities
                ->where('name', 'LIKE', "$city_name%")
                ->orderBy('name', 'asc')
                ->limit(10)
                ->get();
  print json_encode($cities);
}

