<?php

namespace wrdickson\hms;

$f3->route('GET /space-types', function ($f3) {
  print json_encode(SpaceTypes::get_space_types());
});