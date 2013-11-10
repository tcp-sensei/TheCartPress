<?php
/**
* This file is part of TheCartPress.
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
RelEntities::createTable();

require_once( TCP_DAOS_FOLDER . 'Addresses.class.php' );
Addresses::createTable();

require_once( TCP_DAOS_FOLDER . 'Taxes.class.php' );
Taxes::createTable();

require_once( TCP_DAOS_FOLDER . 'TaxRates.class.php' );
TaxRates::createTable();
TaxRates::initData();

require_once( TCP_DAOS_FOLDER . 'Countries.class.php' );
TCPCountries::createTable();
TCPCountries::initData();

require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
Orders::createTable();

require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
OrdersDetails::createTable();

require_once( TCP_DAOS_FOLDER . 'OrdersCosts.class.php' );
OrdersCosts::createTable();

require_once( TCP_DAOS_FOLDER . 'OrdersMeta.class.php' );
OrdersMeta::createTable();

require_once( TCP_DAOS_FOLDER . 'OrdersDetailsMeta.class.php' );
OrdersDetailsMeta::createTable();

require_once( TCP_DAOS_FOLDER . 'OrdersCostsMeta.class.php' );
OrdersCostsMeta::createTable();

require_once( TCP_DAOS_FOLDER . 'Currencies.class.php' );
Currencies::createTable();
Currencies::initData();
?>
