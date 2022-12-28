<?php

namespace wrdickson\hms;

//  require config for db connection info and jwt salt stuff
require 'config/config.php';

//  1. require source classes
require 'lib/Auth.php';
require 'lib/Account.php';
require 'lib/F3Auth.php';
require 'lib/DataConnector.php';
require 'lib/PaymentTypes.php';
require 'lib/Customer.php';
require 'lib/Customers.php';
require 'lib/Folios.php';
require 'lib/Folio.php';
require 'lib/Payments.php';
require 'lib/Payment.php';
require 'lib/RootSpace.php';
require 'lib/RootSpaces.php';
require 'lib/SaleTypes.php';
require 'lib/SpaceTypes.php';
require 'lib/TaxType.php';
require 'lib/TaxTypes.php';
require 'lib/Validate.php';
require 'lib/Reservation.php';
require 'lib/Reservations.php';
require 'lib/SaleTypeGroups.php';

//  2. require route fragments
require 'route_fragments/auth.php';
//  this is bullshit dev/debug routes
require 'route_fragments/dev.php';
require 'route_fragments/reservations.php';
require 'route_fragments/root_spaces.php';
require 'route_fragments/sale_types.php';
require 'route_fragments/sale_type_groups.php';
require 'route_fragments/tax_types.php';
require 'route_fragments/payment_types.php';
require 'route_fragments/customers.php';
require 'route_fragments/folios.php';
require 'route_fragments/payments.php';
require 'route_fragments/space_types.php';
