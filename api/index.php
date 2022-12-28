<?php

namespace wrdickson\hms;

//  getcher friggin composer packages . . .
require 'vendor/autoload.php';

//  instantiate $f3 before loading fragments
//  this makes $f3 pretty global but wtf . . .
$f3 = \Base::instance();

//  init
//  load up route_fragments and lib classes
//  route_fragments need $f3
require 'src/index_loader.php';

//  start the router
$f3->run();
