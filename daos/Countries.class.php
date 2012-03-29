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

class Countries {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_countries` (
		  `iso`		char(2) NOT NULL,
		  `name`	varchar(50) NOT NULL,
		  `en`		varchar(50) NOT NULL,
		  `es`		varchar(50) NOT NULL,
		  `de`		varchar(50) NOT NULL,
		  `fr`		varchar(50) NOT NULL,
		  `iso3`	char(3)		NOT NULL,
		  `code`	int(4)		NOT NULL,
		  `ue`		int(1)		NOT NULL,
		  `nue`		int(1)		NOT NULL,
		  `re`		int(1)		NOT NULL,
		  PRIMARY KEY  (`ISO`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}
	
	static function getAll( $language = 'en' ) {
		global $wpdb;
		$language = Countries::getIso( $language );
		return $wpdb->get_results( 'select iso, ' . $language . ' as name from ' . $wpdb->prefix . 'tcp_countries order by name' );
	}
	
	static function get( $iso, $language = '' ) {
		global $wpdb;
		if ( $language == '' ) {
			return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_countries where iso = %s', $iso ) );
		} else {
			$language = Countries::getIso( $language );
			return $wpdb->get_row( $wpdb->prepare( 'select iso, ' . $language . ' as name from ' . $wpdb->prefix . 'tcp_countries where iso = %s', $iso ) );
		}
	}
	
	static function getSome( $isos, $language = 'en' ) {
		global $wpdb;
		$selected_isos = '\'';
		foreach ( $isos as $iso )
			$selected_isos .= $iso . '\', \'';
		$selected_isos = substr( $selected_isos, 0, -3 );
		$language = Countries::getIso( $language );
		$res = $wpdb->get_results( 'select iso, ' . $language . ' as name from ' . $wpdb->prefix . 'tcp_countries where iso in ( ' . $selected_isos . ')' );
		return $res;
	}

	private static function getIso( $language = 'en' ) {
		if ( $language == 'en' || $language == 'es' || $language == 'de' || $language == 'fr' ) {
			return $language;
		} else {
			global $wpdb;
			$row = $wpdb->get_row( $wpdb->prepare( 'SHOW COLUMNS FROM ' . $wpdb->prefix . 'tcp_countries WHERE field = %s' ), $language );
			if ( $row ) {
				return $language;
			} else {
				return 'en';
			}
		}
	}

	static function initData() {
		global $wpdb;
		$count = $wpdb->get_var( 'select count(*) from ' . $wpdb->prefix . 'tcp_countries' );
		if ( $count == 0 ) {
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'AD\',\'ANDORRA\',\'ANDORRA\',\'ANDORRA\',\'ANDORRA\',\'ANDORRE\',\'AND\',20,0,0,1),
			 (\'AE\',\'UNITED ARAB EMIRATES\',\'UNITED ARAB EMIRATES\',\'EMIRATOS ÁRABES UNIDOS\',\'VEREINIGTE ARABISCHE EMIRATE\',\'ÉMIRATS ARABES UNIS\',\'ARE\',784,0,0,0),
			 (\'AF\',\'AFGHANISTAN\',\'AFGHANISTAN\',\'AFGHANISTÁN\',\'Afghanistan\',\'Afghanistan\',\'AFG\',4,0,0,0),
			 (\'AG\',\'ANTIGUA AND BARBUDA\',\'ANTIGUA AND BARBUDA\',\'ANTIGUA Y BARBUDA\',\'ANTIGUA UND BARBUDA\',\'ANTIGUA ET BARBUDA\',\'ATG\',28,0,0,0),
			 (\'AI\',\'ANGUILLA\',\'ANGUILLA\',\'ANGUILLA\',\'ANGUILLA\',\'ANGUILLA\',\'AIA\',660,0,0,0),
			 (\'AL\',\'ALBANIA\',\'ALBANIA\',\'ALBANIA\',\'ALBANIEN\',\'ALBANIE\',\'ALB\',8,0,0,1),
			 (\'AM\',\'ARMENIA\',\'ARMENIA\',\'ARMENIA\',\'ARMENIEN\',\'ARMÉNIE\',\'ARM\',51,0,0,0),
			 (\'AN\',\'NETHERLANDS ANTILLES\',\'NETHERLANDS ANTILLES\',\'ANTILLAS HOLANDESAS\',\'NIEDERLÄNDISCHE ANTILLEN\',\'ANTILLES NÉERLANDAISES\',\'ANT\',530,1,0,1),
			 (\'AO\',\'ANGOLA\',\'ANGOLA\',\'ANGOLA\',\'ANGOLA\',\'ANGOLA\',\'AGO\',24,0,0,0),
			 (\'AQ\',\'ANTARCTICA\',\'ANTARCTICA\',\'ANTÁRTIDA\',\'ANTARKTIS\',\'ANTARCTIQUE\',NULL,NULL,0,0,0),
			 (\'AR\',\'ARGENTINA\',\'ARGENTINA\',\'ARGENTINA\',\'ARGENTINIEN\',\'ARGENTINE\',\'ARG\',32,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'AS\',\'AMERICAN SAMOA\',\'AMERICAN SAMOA\',\'SAMOA AMERICANA\',\'AMERIKANISCHE SAMOA\',\'SAMOA AMÉRICAINES\',\'ASM\',16,0,0,0),
			 (\'AT\',\'AUSTRIA\',\'AUSTRIA\',\'AUSTRIA\',\'ÖSTERREICH\',\'AUTRICHE\',\'AUT\',40,1,0,1),
			 (\'AU\',\'AUSTRALIA\',\'AUSTRALIA\',\'AUSTRALIA\',\'AUSTRALIEN\',\'AUSTRALIE\',\'AUS\',36,0,0,0),
			 (\'AW\',\'ARUBA\',\'ARUBA\',\'ARUBA\',\'ARUBA\',\'ARUBA\',\'ABW\',533,0,0,0),
			 (\'AZ\',\'AZERBAIJAN\',\'AZERBAIJAN\',\'AZERBAIYÁN\',\'ASERBAIDSCHAN\',\'AZERBAÏDJAN\',\'AZE\',31,0,0,0),
			 (\'BA\',\'BOSNIA AND HERZEGOVINA\',\'BOSNIA AND HERZEGOVINA\',\'BOSNIA Y HERZEGOVINA\',\'BOSNIEN UND HERZEGOWINA\',\'BOSNIE-HERZÉGOVINE\',\'BIH\',70,0,0,1),
			 (\'BB\',\'BARBADOS\',\'BARBADOS\',\'BARBADOS\',\'BARBADOS\',\'BARBADE\',\'BRB\',52,0,0,0),
			 (\'BD\',\'BANGLADESH\',\'BANGLADESH\',\'BANGLADESH\',\'BANGLADESCH\',\'BANGLADESH\',\'BGD\',50,0,0,0),
			 (\'BE\',\'BELGIUM\',\'BELGIUM\',\'BÉLGICA\',\'BELGIEN\',\'BELGIQUE\',\'BEL\',56,1,0,1),
			 (\'BF\',\'BURKINA FASO\',\'BURKINA FASO\',\'BURKINA FASO\',\'BURKINA FASO\',\'BURKINA FASO\',\'BFA\',854,0,0,0),
			 (\'BG\',\'BULGARIA\',\'BULGARIA\',\'BULGARIA\',\'BULGARIEN\',\'BULGARIE\',\'BGR\',100,1,0,1);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'BH\',\'BAHRAIN\',\'BAHRAIN\',\'BAHRÉIN\',\'BAHRAIN\',\'BAHREÏN\',\'BHR\',48,0,0,0),
			 (\'BI\',\'BURUNDI\',\'BURUNDI\',\'BURUNDI\',\'BURUNDI\',\'BURUNDI\',\'BDI\',108,0,0,0),
			 (\'BJ\',\'BENIN\',\'BENIN\',\'BENIN\',\'BENIN\',\'BÉNIN\',\'BEN\',204,0,0,0),
			 (\'BM\',\'BERMUDA\',\'BERMUDA\',\'BERMUDAS\',\'BERMUDA\',\'BERMUDES\',\'BMU\',60,0,0,0),
			 (\'BN\',\'BRUNEI DARUSSALAM\',\'BRUNEI DARUSSALAM\',\'BRUNÉI\',\'BRUNEI\',\'BRUNÉI DARUSSALAM\',\'BRN\',96,0,0,0),
			 (\'BO\',\'BOLIVIA\',\'BOLIVIA\',\'BOLIVIA\',\'BOLIVIEN\',\'BOLIVIE\',\'BOL\',68,0,0,0),
			 (\'BR\',\'BRAZIL\',\'BRAZIL\',\'BRASIL\',\'BRASILIEN\',\'BRÉSIL\',\'BRA\',76,0,0,0),
			 (\'BS\',\'BAHAMAS\',\'BAHAMAS\',\'BAHAMAS\',\'BAHAMAS\',\'BAHAMAS\',\'BHS\',44,0,0,0),
			 (\'BT\',\'BHUTAN\',\'BHUTAN\',\'BHUTÁN\',\'BHUTAN\',\'BHOUTAN\',\'BTN\',64,0,0,0),
			 (\'BV\',\'BOUVET ISLAND\',\'BOUVET ISLAND\',\'ISLA BOUVET\',\'BOUVET INSEL\',\'BOUVET, ÎLE\',NULL,NULL,0,0,0),
			 (\'BW\',\'BOTSWANA\',\'BOTSWANA\',\'BOTSUANA\',\'BOTSWANA\',\'BOTSWANA\',\'BWA\',72,0,0,0),
			 (\'BY\',\'BELARUS\',\'BELARUS\',\'BIELORRUSIA\',\'WEIßRUSSLAND\',\'BÉLARUS\',\'BLR\',112,0,0,0),
			 (\'BZ\',\'BELIZE\',\'BELIZE\',\'BELICE\',\'BELIZE\',\'BELIZE\',\'BLZ\',84,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'CA\',\'CANADA\',\'CANADA\',\'CANADÁ\',\'KANADA\',\'CANADA\',\'CAN\',124,0,0,0),
			 (\'CC\',\'COCOS (KEELING) ISLANDS\',\'COCOS (KEELING) ISLANDS\',\'ISLAS COCOS\',\'KOKOSINSELN\',\'COCOS (KEELING); ÎLES\',\'CCK\',166,0,0,0),
			 (\'CD\',\'CONGO, THE DEMOCRATIC REPUBLIC OF THE\',\'CONGO, THE DEMOCRATIC REPUBLIC OF THE\',\'REPÚBLICA DEMOCRÁTICA DEL CONGO\',\'KONGO (DEM. REP.)\',\'CONGO, LA RÉPUBLIQUE DÉMOCRATIQUE DU\',\'COD\',180,0,0,0),
			 (\'CF\',\'CENTRAL AFRICAN REPUBLIC\',\'CENTRAL AFRICAN REPUBLIC\',\'REPÚBLICA CENTROAFRICANA\',\'ZENTRALAFRIKANISCHE REPUBLIK\',\'CENTRAFRICAINE, RÉPUBLIQUE\',\'CAF\',140,0,0,0),
			 (\'CG\',\'CONGO\',\'CONGO\',\'CONGO\',\'KONGO\',\'CONGO\',\'COG\',178,0,0,0),
			 (\'CH\',\'SWITZERLAND\',\'SWITZERLAND\',\'SUIZA\',\'SCHWEIZ\',\'SUISSE\',\'CHE\',756,0,1,0),
			 (\'CI\',\'COTE D`IVOIRE\',\'COTE D`IVOIRE\',\'COSTA DE MARFIL\',\'ELFENBEINKÜSTE\',\'COTE D`IVOIRE\',\'CIV\',384,0,0,0),
			 (\'CK\',\'COOK ISLANDS\',\'COOK ISLANDS\',\'ISLAS COOK\',\'COOKINSELN\',\'COOK, ÎLES\',\'COK\',184,0,0,0),
			 (\'CL\',\'CHILE\',\'CHILE\',\'CHILE\',\'CHILE\',\'CHILI\',\'CHL\',152,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'CM\',\'CAMEROON\',\'CAMEROON\',\'CAMERÚN\',\'KAMERUN\',\'CAMEROUN\',\'CMR\',120,0,0,0),
			 (\'CN\',\'CHINA\',\'CHINA\',\'CHINA\',\'CHINA\',\'CHINE\',\'CHN\',156,0,0,0),
			 (\'CO\',\'COLOMBIA\',\'COLOMBIA\',\'COLOMBIA\',\'KOLUMBIEN\',\'COLOMBIE\',\'COL\',170,0,0,0),
			 (\'CR\',\'COSTA RICA\',\'COSTA RICA\',\'COSTA RICA\',\'COSTA RICA\',\'COSTA RICA\',\'CRI\',188,0,0,0),
			 (\'CS\',\'SERBIA AND MONTENEGRO\',\'SERBIA AND MONTENEGRO\',\'SERBIA Y MONTENEGRO\',\'SERBIEN UND MONTENEGRO\',\'SERBIE-ET-MONTÉNEGRO\',NULL,NULL,0,0,1),
			 (\'CU\',\'CUBA\',\'CUBA\',\'CUBA\',\'KUBA\',\'CUBA\',\'CUB\',192,0,0,0),
			 (\'CV\',\'CAPE VERDE\',\'CAPE VERDE\',\'CABO VERDE\',\'KAP VERDE\',\'CAP-VERT\',\'CPV\',132,0,0,0),
			 (\'CX\',\'CHRISTMAS ISLAND\',\'CHRISTMAS ISLAND\',\'ISLA DE NAVIDAD\',\'CHRISTMAS INSEL\',\'CHRISTMAS, ÎLE\',\'CXR\',162,0,0,0),
			 (\'CY\',\'CYPRUS\',\'CYPRUS\',\'CHIPRE\',\'ZYPERN\',\'CHYPRE\',\'CYP\',196,0,0,0),
			 (\'CZ\',\'CZECH REPUBLIC\',\'CZECH REPUBLIC\',\'REPÚBLICA CHECA\',\'TSCHECHISCHE REPUBLIK\',\'TCHÍšQUE, RÉPUBLIQUE\',\'CZE\',203,0,0,1),
			 (\'DE\',\'GERMANY\',\'GERMANY\',\'ALEMANIA\',\'DEUTSCHLAND\',\'ALLEMAGNE\',\'DEU\',276,1,0,1);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'DJ\',\'DJIBOUTI\',\'DJIBOUTI\',\'YIBUTI\',\'DJIBOUTI\',\'DJIBOUTI\',\'DJI\',262,0,0,0),
			 (\'DK\',\'DENMARK\',\'DENMARK\',\'DINAMARCA\',\'DÄNEMARK\',\'DANEMARK\',\'DNK\',208,1,0,1),
			 (\'DM\',\'DOMINICA\',\'DOMINICA\',\'DOMINICA\',\'DOMINICA\',\'DOMINIQUE\',\'DMA\',212,0,0,0),
			 (\'DO\',\'DOMINICAN REPUBLIC\',\'DOMINICAN REPUBLIC\',\'REPÚBLICA DOMINICANA\',\'DOMINIKANISCHE REPUBLIK\',\'DOMINICAINE, RÉPUBLIQUE\',\'DOM\',214,0,0,0),
			 (\'EC\',\'ECUADOR\',\'ECUADOR\',\'ECUADOR\',\'ECUADOR\',\'ÉQUATEUR\',\'ECU\',218,0,0,0),
			 (\'EE\',\'ESTONIA\',\'ESTONIA\',\'ESTONIA\',\'ESTLAND\',\'ESTONIE\',\'EST\',233,0,0,0),
			 (\'EG\',\'EGYPT\',\'EGYPT\',\'EGIPTO\',\'ÄGYPTEN\',\'ÉGYPTE\',\'EGY\',818,0,0,0),
			 (\'EH\',\'WESTERN SAHARA\',\'WESTERN SAHARA\',\'SAHARA OCCIDENTAL\',\'WESTSAHARA\',\'SAHARA OCCIDENTAL\',\'ESH\',732,0,0,0),
			 (\'ER\',\'ERITREA\',\'ERITREA\',\'ERITREA\',\'ERITREA\',\'ÉRYTHRÉE\',\'ERI\',232,0,0,0),
			 (\'ES\',\'SPAIN\',\'SPAIN\',\'ESPAÑA\',\'SPANIEN\',\'ESPAGNE\',\'ESP\',724,1,0,1),
			 (\'ET\',\'ETHIOPIA\',\'ETHIOPIA\',\'ETIOPÍA\',\'ÄTHIOPIEN\',\'ÉTHIOPIE\',\'ETH\',231,0,0,0),
			 (\'FI\',\'FINLAND\',\'FINLAND\',\'FINLANDIA\',\'FINNLAND\',\'FINLANDE\',\'FIN\',246,0,1,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'FJ\',\'FIJI\',\'FIJI\',\'FIYI\',\'FIDSCHI\',\'FIDJI\',\'FJI\',242,0,0,0),
			 (\'FK\',\'FALKLAND ISLANDS (MALVINAS)\',\'FALKLAND ISLANDS (MALVINAS)\',\'ISLAS MALVINAS\',\'FALKLANDINSELN\',\'FALKLAND, ÎLES (MALVINAS)\',\'FLK\',238,0,0,0),
			 (\'FM\',\'MICRONESIA, FEDERATED STATES OF\',\'MICRONESIA, FEDERATED STATES OF\',\'MICRONESIA\',\'MIKRONESIEN\',\'MICRONÉSIE, ÉTATS FÉDÉRÉS DE\',\'FSM\',583,0,0,0),
			 (\'FO\',\'FAROE ISLANDS\',\'FAROE ISLANDS\',\'ISLAS FEROE\',\'FÄRÖER-INSELN\',\'FÉROÉ, ÎLES\',\'FRO\',234,0,0,0),
			 (\'FR\',\'FRANCE\',\'FRANCE\',\'FRANCIA\',\'FRANKREICH\',\'FRANCE\',\'FRA\',250,1,0,1),
			 (\'GA\',\'GABON\',\'GABON\',\'GABÓN\',\'GABUN\',\'GABON\',\'GAB\',266,0,0,0),
			 (\'GB\',\'UNITED KINGDOM\',\'UNITED KINGDOM\',\'REINO UNIDO\',\'GROßBRITANNIEN\',\'ROYAUME-UNI\',\'GBR\',826,1,0,1),
			 (\'GD\',\'GRENADA\',\'GRENADA\',\'GRANADA\',\'GRENADA\',\'GRENADE\',\'GRD\',308,0,0,0),
			 (\'GE\',\'GEORGIA\',\'GEORGIA\',\'GEORGIA\',\'GEORGIEN\',\'GÉORGIE\',\'GEO\',268,0,0,0),
			 (\'GF\',\'FRENCH GUIANA\',\'FRENCH GUIANA\',\'GUAYANA FRANCESA\',\'FRANZÖSISCH GUYANA\',\'GUYANE FRANÍ§AISE\',\'GUF\',254,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'GH\',\'GHANA\',\'GHANA\',\'GHANA\',\'GHANA\',\'GHANA\',\'GHA\',288,0,0,0),
			 (\'GI\',\'GIBRALTAR\',\'GIBRALTAR\',\'GIBRALTAR\',\'GIBRALTAR\',\'GIBRALTAR\',\'GIB\',292,1,0,1),
			 (\'GL\',\'GREENLAND\',\'GREENLAND\',\'GROENLANDIA\',\'GRÖNLAND\',\'GROENLAND\',\'GRL\',304,0,0,0),
			 (\'GM\',\'GAMBIA\',\'GAMBIA\',\'GAMBIA\',\'GAMBIA\',\'GAMBIE\',\'GMB\',270,0,0,0),
			 (\'GN\',\'GUINEA\',\'GUINEA\',\'GUINEA\',\'GUINEA\',\'GUINÉE\',\'GIN\',324,0,0,0),
			 (\'GP\',\'GUADELOUPE\',\'GUADELOUPE\',\'GUADALUPE\',\'GUADELOUPE\',\'GUADELOUPE\',\'GLP\',312,0,0,0),
			 (\'GQ\',\'EQUATORIAL GUINEA\',\'EQUATORIAL GUINEA\',\'GUINEA ECUATORIAL\',\'ÄQUATORIAL-GUINEA\',\'GUINÉE ÉQUATORIALE\',\'GNQ\',226,0,0,0),
			 (\'GR\',\'GREECE\',\'GREECE\',\'GRECIA\',\'GRIECHENLAND\',\'GRÍšCE\',\'GRC\',300,1,0,1),
			 (\'GS\',\'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS\',\'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS\',\'ISLAS GEORGIAS DEL SUR Y SANDWICH DEL SUR\',\'SÜDGEORGIEN UND DIE SÜDLICHEN SANDWICHINSELN\',\'GÉORGIE DU SUD ET LES ÎLES SANDWICH DU SUD\',\'SGS\',239,0,0,0),
			 (\'GT\',\'GUATEMALA\',\'GUATEMALA\',\'GUATEMALA\',\'GUATEMALA\',\'GUATEMALA\',\'GTM\',320,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'GU\',\'GUAM\',\'GUAM\',\'GUAM\',\'GUAM\',\'GUAM\',\'GUM\',316,0,0,0),
			 (\'GW\',\'GUINEA-BISSAU\',\'GUINEA-BISSAU\',\'GUINEA-BISSAU\',\'GUINEA-BISSAU\',\'GUINÉE-BISSAU\',\'GNB\',624,0,0,0),
			 (\'GY\',\'GUYANA\',\'GUYANA\',\'GUYANA\',\'GUYANA\',\'GUYANA\',\'GUY\',328,0,0,0),
			 (\'HK\',\'HONG KONG\',\'HONG KONG\',\'HONG KONG\',\'HONGKONG\',\'HONG-KONG\',\'HKG\',344,0,0,0),
			 (\'HM\',\'HEARD ISLAND AND MCDONALD ISLANDS\',\'HEARD ISLAND AND MCDONALD ISLANDS\',\'ISLAS HEARD Y MCDONALD\',\'HEARD- UND MCDONALD-INSELN\',\'HEARD, ÎLE ET MCDONALD, ÎLES\',\'334\',0,0,0,0),
			 (\'HN\',\'HONDURAS\',\'HONDURAS\',\'HONDURAS\',\'HONDURAS\',\'HONDURAS\',\'HND\',340,0,0,0),
			 (\'HR\',\'CROATIA\',\'CROATIA\',\'CROACIA\',\'KROATIEN\',\'CROATIE\',\'HRV\',191,1,0,1),
			 (\'HT\',\'HAITI\',\'HAITI\',\'HAITÍ\',\'HAITI\',\'HAÏTI\',\'HTI\',332,0,0,0),
			 (\'HU\',\'HUNGARY\',\'HUNGARY\',\'HUNGRÍA\',\'UNGARN\',\'HONGRIE\',\'HUN\',348,1,0,1),
			 (\'ID\',\'INDONESIA\',\'INDONESIA\',\'INDONESIA\',\'INDONESIEN\',\'INDONÉSIE\',\'IDN\',360,0,0,0),
			 (\'IE\',\'IRELAND\',\'IRELAND\',\'IRLANDA\',\'IRLAND\',\'IRLANDE\',\'IRL\',372,1,0,1),
			 (\'IL\',\'ISRAEL\',\'ISRAEL\',\'ISRAEL\',\'ISRAEL\',\'ISRAËL\',\'ISR\',376,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'IN\',\'INDIA\',\'INDIA\',\'INDIA\',\'INDIEN\',\'INDE\',\'IND\',356,0,0,0),
			 (\'IO\',\'BRITISH INDIAN OCEAN TERRITORY\',\'BRITISH INDIAN OCEAN TERRITORY\',\'TERRITORIO BRITÁNICO DEL OCÉANO ÍNDICO\',\'BRITISCHE HOHEITSGEBIETE\',\'TERRITOIRE BRITANNIQUE DE L`OCÉAN INDIEN\',\'IOT\',86,0,0,0),
			 (\'IQ\',\'IRAQ\',\'IRAQ\',\'IRAQ\',\'IRAK\',\'IRAQ\',\'IRQ\',368,0,0,0),
			 (\'IR\',\'IRAN, ISLAMIC REPUBLIC OF\',\'IRAN, ISLAMIC REPUBLIC OF\',\'IRÁN\',\'IRAN\',\'IRAN, RÉPUBLIQUE ISLAMIQUE D`\',\'IRN\',364,0,0,0),
			 (\'IS\',\'ICELAND\',\'ICELAND\',\'ISLANDIA\',\'ISLAND\',\'ISLANDE\',\'ISL\',352,0,0,0),
			 (\'IT\',\'ITALY\',\'ITALY\',\'ITALIA\',\'ITALIEN\',\'ITALIE\',\'ITA\',380,1,0,1),
			 (\'JM\',\'JAMAICA\',\'JAMAICA\',\'JAMAICA\',\'JAMAIKA\',\'JAMAÏQUE\',\'JAM\',388,0,0,0),
			 (\'JO\',\'JORDAN\',\'JORDAN\',\'JORDANIA\',\'JORDANIEN\',\'JORDANIE\',\'JOR\',400,0,0,0),
			 (\'JP\',\'JAPAN\',\'JAPAN\',\'JAPÓN\',\'JAPAN\',\'JAPON\',\'JPN\',392,0,0,0),
			 (\'KE\',\'KENYA\',\'KENYA\',\'KENIA\',\'KENIA\',\'KENYA\',\'KEN\',404,0,0,0),
			 (\'KG\',\'KYRGYZSTAN\',\'KYRGYZSTAN\',\'KIRGUISTÁN\',\'KIRGISISTAN\',\'KIRGHIZISTAN\',\'KGZ\',417,0,0,0),
			 (\'KH\',\'CAMBODIA\',\'CAMBODIA\',\'CAMBOYA\',\'KAMBODSCHA\',\'CAMBODGE\',\'KHM\',116,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'KI\',\'KIRIBATI\',\'KIRIBATI\',\'KIRIBATI\',\'KIRIBATI\',\'KIRIBATI\',\'KIR\',296,0,0,0),
			 (\'KM\',\'COMOROS\',\'COMOROS\',\'COMORAS\',\'KOMOREN\',\'COMORES\',\'COM\',174,0,0,0),
			 (\'KN\',\'SAINT KITTS AND NEVIS\',\'SAINT KITTS AND NEVIS\',\'SAN CRISTÓBAL Y NEVIS\',\'SAINT KITTS UND NEVIS\',\'SAINT-KITTS-ET-NEVIS\',\'KNA\',659,0,0,0),
			 (\'KP\',\'KOREA, DEMOCRATIC PEOPLE`S REPUBLIC OF\',\'KOREA, DEMOCRATIC PEOPLE`S REPUBLIC OF\',\'COREA DEL NORTE\',\'NORDKOREA\',\'CORÉE, RÉPUBLIQUE POPULAIRE DÉMOCRATIQUE \',\'PRK\',408,0,0,0),
			 (\'KR\',\'KOREA, REPUBLIC OF\',\'KOREA, REPUBLIC OF\',\'COREA DEL SUR\',\'SÜDKOREA\',\'CORÉE, RÉPUBLIQUE DE\',\'KOR\',410,0,0,0),
			 (\'KW\',\'KUWAIT\',\'KUWAIT\',\'KUWAIT\',\'KUWAIT\',\'KOWEIT\',\'KWT\',414,0,0,0),
			 (\'KY\',\'CAYMAN ISLANDS\',\'CAYMAN ISLANDS\',\'ISLAS CAIMÁN\',\'CAYMAN-INSELN\',\'CAÏMANES, ÎLES\',\'CYM\',136,0,0,0),
			 (\'KZ\',\'KAZAKHSTAN\',\'KAZAKHSTAN\',\'KAZAJSTÁN\',\'KASACHSTAN\',\'KAZAKHSTAN\',\'KAZ\',398,0,0,0),
			 (\'LA\',\'LAO PEOPLE`S DEMOCRATIC REPUBLIC\',\'LAO PEOPLE`S DEMOCRATIC REPUBLIC\',\'LAOS\',\'LAOS\',\'LAO, RÉPUBLIQUE DÉMOCRATIQUE POPULAIRE\',\'LAO\',418,0,0,0),
			 (\'LB\',\'LEBANON\',\'LEBANON\',\'LÍBANO\',\'LIBANON\',\'LIBAN\',\'LBN\',422,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'LC\',\'SAINT LUCIA\',\'SAINT LUCIA\',\'SANTA LUCÍA\',\'SAINT LUCIA\',\'SAINTE-LUCIE\',\'LCA\',662,0,0,0),
			 (\'LI\',\'LIECHTENSTEIN\',\'LIECHTENSTEIN\',\'LIECHTENSTEIN\',\'LIECHTENSTEIN\',\'LIECHTENSTEIN\',\'LIE\',438,1,0,1),
			 (\'LK\',\'SRI LANKA\',\'SRI LANKA\',\'SRI LANKA\',\'SRI LANKA\',\'SRI LANKA\',\'LKA\',144,0,0,0),
			 (\'LR\',\'LIBERIA\',\'LIBERIA\',\'LIBERIA\',\'LIBERIA\',\'LIBÉRIA\',\'LBR\',430,0,0,0),
			 (\'LS\',\'LESOTHO\',\'LESOTHO\',\'LESOTHO\',\'LESOTHO\',\'LESOTHO\',\'LSO\',426,0,0,0),
			 (\'LT\',\'LITHUANIA\',\'LITHUANIA\',\'LITUANIA\',\'LITAUEN\',\'LITUANIE\',\'LTU\',440,1,0,1),
			 (\'LU\',\'LUXEMBOURG\',\'LUXEMBOURG\',\'LUXEMBURGO\',\'LUXEMBURG\',\'LUXEMBOURG\',\'LUX\',442,1,0,1),
			 (\'LV\',\'LATVIA\',\'LATVIA\',\'LETONIA\',\'LETTLAND\',\'LETTONIE\',\'LVA\',428,1,0,1),
			 (\'LY\',\'LIBYAN ARAB JAMAHIRIYA\',\'LIBYAN ARAB JAMAHIRIYA\',\'LIBIA\',\'LIBYEN\',\'LIBYE\',\'LBY\',434,0,0,0),
			 (\'MA\',\'MOROCCO\',\'MOROCCO\',\'MARRUECOS\',\'MAROKKO\',\'MAROC\',\'MAR\',504,0,0,0),
			 (\'MC\',\'MONACO\',\'MONACO\',\'MÓNACO\',\'MONACO\',\'MONACO\',\'MCO\',492,1,0,1),
			 (\'MD\',\'MOLDOVA, REPUBLIC OF\',\'MOLDOVA, REPUBLIC OF\',\'MOLDAVIA\',\'MOLDAWIEN\',\'MOLDOVA, RÉPUBLIQUE DE\',\'MDA\',498,0,0,1),
			 (\'MG\',\'MADAGASCAR\',\'MADAGASCAR\',\'MADAGASCAR\',\'MADAGASKAR\',\'MADAGASCAR\',\'MDG\',450,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'MH\',\'MARSHALL ISLANDS\',\'MARSHALL ISLANDS\',\'ISLAS MARSHALL\',\'MARSHALL-INSELN\',\'MARSHALL, ÎLES\',\'MHL\',584,0,0,0),
			 (\'MK\',\'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF\',\'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF\',\'MACEDONIA\',\'MAZEDONIEN\',\'MACÉDOINE\',\'MKD\',807,0,0,1),
			 (\'ML\',\'MALI\',\'MALI\',\'MALÍ\',\'MALI\',\'MALI\',\'MLI\',466,0,0,0),
			 (\'MM\',\'MYANMAR\',\'MYANMAR\',\'MYANMAR\',\'MYANMAR\',\'MYANMAR\',\'MMR\',104,0,0,0),
			 (\'MN\',\'MONGOLIA\',\'MONGOLIA\',\'MONGOLIA\',\'MONGOLEI\',\'MONGOLIE\',\'MNG\',496,0,0,0),
			 (\'MO\',\'MACAO\',\'MACAO\',\'MACAO\',\'MACAO\',\'MACAO\',\'MAC\',446,0,0,0),
			 (\'MP\',\'NORTHERN MARIANA ISLANDS\',\'NORTHERN MARIANA ISLANDS\',\'ISLAS MARIANAS DEL NORTE\',\'NÖRDLICHE MARIANEN\',\'MARIANNES DU NORD, ÎLES\',\'MNP\',580,0,0,0),
			 (\'MQ\',\'MARTINIQUE\',\'MARTINIQUE\',\'MARTINICA\',\'MARTINIQUE\',\'MARTINIQUE\',\'MTQ\',474,0,0,0),
			 (\'MR\',\'MAURITANIA\',\'MAURITANIA\',\'MAURITANIA\',\'MAURETANIEN\',\'MAURITANIE\',\'MRT\',478,0,0,0),
			 (\'MS\',\'MONTSERRAT\',\'MONTSERRAT\',\'MONTSERRAT\',\'MONTSERRAT\',\'MONTSERRAT\',\'MSR\',500,0,0,0),
			 (\'MT\',\'MALTA\',\'MALTA\',\'MALTA\',\'MALTA\',\'MALTE\',\'MLT\',470,0,0,1);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'MU\',\'MAURITIUS\',\'MAURITIUS\',\'MAURICIO\',\'MAURITIUS\',\'MAURICE\',\'MUS\',480,0,0,0),
			 (\'MV\',\'MALDIVES\',\'MALDIVES\',\'MALDIVAS\',\'MALEDIVEN\',\'MALDIVES\',\'MDV\',462,0,0,0),
			 (\'MW\',\'MALAWI\',\'MALAWI\',\'MALAWI\',\'MALAWI\',\'MALAWI\',\'MWI\',454,0,0,0),
			 (\'MX\',\'MEXICO\',\'MEXICO\',\'MÉXICO\',\'MEXIKO\',\'MEXIQUE\',\'MEX\',484,0,0,0),
			 (\'MY\',\'MALAYSIA\',\'MALAYSIA\',\'MALASIA\',\'MALAYSIA\',\'MALAISIE\',\'MYS\',458,0,0,0),
			 (\'MZ\',\'MOZAMBIQUE\',\'MOZAMBIQUE\',\'MOZAMBIQUE\',\'MOSAMBIK\',\'MOZAMBIQUE\',\'MOZ\',508,0,0,0),
			 (\'NA\',\'NAMIBIA\',\'NAMIBIA\',\'NAMIBIA\',\'NAMIBIA\',\'NAMIBIE\',\'NAM\',516,0,0,0),
			 (\'NC\',\'NEW CALEDONIA\',\'NEW CALEDONIA\',\'NUEVA CALEDONIA\',\'NEUKALEDONIEN\',\'NOUVELLE-CALÉDONIE\',\'NCL\',540,0,0,0),
			 (\'NE\',\'NIGER\',\'NIGER\',\'NÍGER\',\'NIGER\',\'NIGER\',\'NER\',562,0,0,0),
			 (\'NF\',\'NORFOLK ISLAND\',\'NORFOLK ISLAND\',\'ISLA NORFOLK\',\'NORFOLK ISLAND\',\'NORFOK, ÎLE\',\'NFK\',574,0,0,1),
			 (\'NG\',\'NIGERIA\',\'NIGERIA\',\'NIGERIA\',\'NIGERIA\',\'NIGÉRIA\',\'NGA\',566,0,0,0),
			 (\'NI\',\'NICARAGUA\',\'NICARAGUA\',\'NICARAGUA\',\'NICARAGUA\',\'NICARAGUA\',\'NIC\',558,0,0,0),
			 (\'NL\',\'NETHERLANDS\',\'NETHERLANDS\',\'PAÍSES BAJOS\',\'NIEDERLANDE\',\'PAYS-BAS\',\'NLD\',528,1,0,1);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'NO\',\'NORWAY\',\'NORWAY\',\'NORUEGA\',\'NORWEGEN\',\'NORVÍšGE\',\'NOR\',578,0,1,0),
			 (\'NP\',\'NEPAL\',\'NEPAL\',\'NEPAL\',\'NEPAL\',\'NÉPAL\',\'NPL\',524,0,0,0),
			 (\'NR\',\'NAURU\',\'NAURU\',\'NAURU\',\'NAURU\',\'NAURU\',\'NRU\',520,0,0,0),
			 (\'NU\',\'NIUE\',\'NIUE\',\'NIUE\',\'NIUE\',\'NIUÉ\',\'NIU\',570,0,0,0),
			 (\'NZ\',\'NEW ZEALAND\',\'NEW ZEALAND\',\'NUEVA ZELANDA\',\'NEUSEELAND\',\'NOUVELLE-ZÉLANDE\',\'NZL\',554,0,0,0),
			 (\'OM\',\'OMAN\',\'OMAN\',\'OMÁN\',\'OMAN\',\'OMAN\',\'OMN\',512,0,0,0),
			 (\'PA\',\'PANAMA\',\'PANAMA\',\'PANAMÁ\',\'PANAMA\',\'PANAMA\',\'PAN\',591,0,0,0),
			 (\'PE\',\'PERU\',\'PERU\',\'PERÚ\',\'PERU\',\'PÉROU\',\'PER\',604,0,0,0),
			 (\'PF\',\'FRENCH POLYNESIA\',\'FRENCH POLYNESIA\',\'POLINESIA FRANCESA\',\'FRANZÖSISCH-POLYNESIEN\',\'POLYNÉSIE FRANÍ§AISE\',\'PYF\',258,0,0,0),
			 (\'PG\',\'PAPUA NEW GUINEA\',\'PAPUA NEW GUINEA\',\'PAPÚA NUEVA GUINEA\',\'PAPUA-NEUGUINEA\',\'PAPOUASIE-NOUVELLE-GUINÉE\',\'PNG\',598,0,0,0),
			 (\'PH\',\'PHILIPPINES\',\'PHILIPPINES\',\'FILIPINAS\',\'PHILIPPINEN\',\'PHILIPPINES\',\'PHL\',608,0,0,0),
			 (\'PK\',\'PAKISTAN\',\'PAKISTAN\',\'PAKISTÁN\',\'PAKISTAN\',\'PAKISTAN\',\'PAK\',586,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'PL\',\'POLAND\',\'POLAND\',\'POLONIA\',\'POLEN\',\'POLOGNE\',\'POL\',616,1,0,1),
			 (\'PM\',\'SAINT PIERRE AND MIQUELON\',\'SAINT PIERRE AND MIQUELON\',\'SAN PEDRO Y MIQUELÍN\',\'SAINT PIERRE UND MIQUELON\',\'SAINT-PIERRE-ET-MIQUELON\',\'SPM\',666,0,0,0),
			 (\'PN\',\'PITCAIRN\',\'PITCAIRN\',\'ISLAS PITCAIRN\',\'PITCAIRNINSELN\',\'PITCAIRN\',\'PCN\',612,0,0,0),
			 (\'PR\',\'PUERTO RICO\',\'PUERTO RICO\',\'PUERTO RICO\',\'PUERTO RICO\',\'PORTO RICO\',\'PRI\',630,0,0,0),
			 (\'PS\',\'PALESTINIAN TERRITORY, OCCUPIED\',\'PALESTINIAN TERRITORY, OCCUPIED\',\'PALESTINA\',\'PALÄSTINA\',\'PALESTINIEN OCCUPÉ, TERRITOIRE\',\'PSE\',275,0,0,0),
			 (\'PT\',\'PORTUGAL\',\'PORTUGAL\',\'PORTUGAL\',\'PORTUGAL\',\'PORTUGAL\',\'PRT\',620,1,0,1),
			 (\'PW\',\'PALAU\',\'PALAU\',\'PALAU\',\'PALAU\',\'PALAOS\',\'PLW\',585,0,0,0),
			 (\'PY\',\'PARAGUAY\',\'PARAGUAY\',\'PARAGUAY\',\'PARAGUAY\',\'PARAGUAY\',\'PRY\',600,0,0,0),
			 (\'QA\',\'QATAR\',\'QATAR\',\'QATAR\',\'KATAR\',\'QATAR\',\'QAT\',634,0,0,0),
			 (\'RE\',\'REUNION\',\'REUNION\',\'REUNIÓN\',\'REUNION\',\'RÉUNION\',\'REU\',638,0,0,0),
			 (\'RO\',\'ROMANIA\',\'ROMANIA\',\'RUMANIA\',\'RUMÄNIEN\',\'ROUMANIE\',\'ROM\',642,1,0,1);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'RU\',\'RUSSIAN FEDERATION\',\'RUSSIAN FEDERATION\',\'RUSIA\',\'RUSSLAND\',\'RUSSIE, FÉDÉRATION DE\',\'RUS\',643,0,0,1),
			 (\'RW\',\'RWANDA\',\'RWANDA\',\'RUANDA\',\'RUANDA\',\'RWANDA\',\'RWA\',646,0,0,0),
			 (\'SA\',\'SAUDI ARABIA\',\'SAUDI ARABIA\',\'ARABIA SAUDÍ­\',\'SAUDI-ARABIEN\',\'ARABIE SAOUDITE\',\'SAU\',682,0,0,0),
			 (\'SB\',\'SOLOMON ISLANDS\',\'SOLOMON ISLANDS\',\'ISLAS SALOMÓN\',\'SALOMONEN\',\'SALOMON, ÎLES\',\'SLB\',90,0,0,0),
			 (\'SC\',\'SEYCHELLES\',\'SEYCHELLES\',\'SEYCHELLES\',\'SEYCHELLEN\',\'SEYCHELLES\',\'SYC\',690,0,0,0),
			 (\'SD\',\'SUDAN\',\'SUDAN\',\'SUDÁN\',\'SUDAN\',\'SOUDAN\',\'SDN\',736,0,0,0),
			 (\'SE\',\'SWEDEN\',\'SWEDEN\',\'SUECIA\',\'SCHWEDEN\',\'SUÍšDE\',\'SWE\',752,0,1,0),
			 (\'SG\',\'SINGAPORE\',\'SINGAPORE\',\'SINGAPUR\',\'SINGAPUR\',\'SINGAPOUR\',\'SGP\',702,0,0,0),
			 (\'SH\',\'SAINT HELENA\',\'SAINT HELENA\',\'SANTA HELENA\',\'SANKT HELENA\',\'SAINTE-HÉLÍšNE\',\'SHN\',654,0,0,0),
			 (\'SI\',\'SLOVENIA\',\'SLOVENIA\',\'ESLOVENIA\',\'SLOWENIEN\',\'SLOVÉNIE\',\'SVN\',705,1,0,1),
			 (\'SJ\',\'SVALBARD AND JAN MAYEN\',\'SVALBARD AND JAN MAYEN\',\'SVALBARD Y JAN MAYEN\',\'SVALBARD UND JAN MAYEN\',\'SVALBARD ET ÎLE JAN MAYEN\',\'SJM\',744,0,0,0),
			 (\'SK\',\'SLOVAKIA\',\'SLOVAKIA\',\'ESLOVAQUIA\',\'SLOWAKEI\',\'SLOVAQUIE\',\'SVK\',703,1,0,1);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'SL\',\'SIERRA LEONE\',\'SIERRA LEONE\',\'SIERRA LEONA\',\'SIERRA LEONE\',\'SIERRA LEONE\',\'SLE\',694,0,0,0),
			 (\'SM\',\'SAN MARINO\',\'SAN MARINO\',\'SAN MARINO\',\'SAN MARINO\',\'SAINT-MARIN\',\'SMR\',674,0,0,0),
			 (\'SN\',\'SENEGAL\',\'SENEGAL\',\'SENEGAL\',\'SENEGAL\',\'SÉNÉGAL\',\'SEN\',686,0,0,0),
			 (\'SO\',\'SOMALIA\',\'SOMALIA\',\'SOMALIA\',\'SOMALIA\',\'SOMALIE\',\'SOM\',706,0,0,0),
			 (\'SR\',\'SURINAME\',\'SURINAME\',\'SURINAM\',\'SURINAM\',\'SURINAME\',\'SUR\',740,0,0,0),
			 (\'ST\',\'SAO TOME AND PRINCIPE\',\'SAO TOME AND PRINCIPE\',\'SANTO TOMÉ Y PRÍ­NCIPE\',\'SÍ£O TOMÉ UND PRÍ­NCIPE\',\'SAO TOMÉ-ET-PRINCIPE\',\'STP\',678,0,0,0),
			 (\'SV\',\'EL SALVADOR\',\'EL SALVADOR\',\'EL SALVADOR\',\'EL SALVADOR\',\'EL SALVADOR\',\'SLV\',222,0,0,0),
			 (\'SY\',\'SYRIAN ARAB REPUBLIC\',\'SYRIAN ARAB REPUBLIC\',\'SIRIA\',\'SYRIEN\',\'SYRIE\',\'SYR\',760,0,0,0),
			 (\'SZ\',\'SWAZILAND\',\'SWAZILAND\',\'SUAZILANDIA\',\'SWASILAND\',\'SWAZILAND\',\'SWZ\',748,0,1,0),
			 (\'TC\',\'TURKS AND CAICOS ISLANDS\',\'TURKS AND CAICOS ISLANDS\',\'ISLAS TURCAS Y CAICOS\',\'TURKS- UND CAICOSINSELN\',\'TURKS ET CAÏQUES, ÎLES\',\'TCA\',796,0,0,0),
			 (\'TD\',\'CHAD\',\'CHAD\',\'CHAD\',\'TSCHAD\',\'TCHAD\',\'TCD\',148,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'TF\',\'FRENCH SOUTHERN TERRITORIES\',\'FRENCH SOUTHERN TERRITORIES\',\'TERRITORIOS AUSTRALES FRANCESES\',\'FRANZÖSISCHE SÜDGEBIETE\',\'TERRES AUSTRALES FRANÍ§AISES\',\'ATF\',260,0,0,0),
			 (\'TG\',\'TOGO\',\'TOGO\',\'TOGO\',\'TOGO\',\'TOGO\',\'TGO\',768,0,0,0),
			 (\'TH\',\'THAILAND\',\'THAILAND\',\'TAILANDIA\',\'THAILAND\',\'THAÏLANDE\',\'THA\',764,0,0,0),
			 (\'TJ\',\'TAJIKISTAN\',\'TAJIKISTAN\',\'TAYIKISTÁN\',\'TADSCHIKISTAN\',\'TADJIKISTAN\',\'TJK\',762,0,0,0),
			 (\'TK\',\'TOKELAU\',\'TOKELAU\',\'TOKELAU\',\'TOKELAU\',\'TOKELAU\',\'TKL\',772,0,0,0),
			 (\'TL\',\'TIMOR-LESTE\',\'TIMOR-LESTE\',\'TIMOR ORIENTAL\',\'OST-TIMOR\',\'TIMOR-LESTE\',\'TLS\',626,0,0,0),
			 (\'TM\',\'TURKMENISTAN\',\'TURKMENISTAN\',\'TURKMENISTÁN\',\'TURKMENISTAN\',\'TURKMÉNISTAN\',\'TKM\',795,0,0,0),
			 (\'TN\',\'TUNISIA\',\'TUNISIA\',\'TÃNEZ\',\'TUNESIEN\',\'TUNISIE\',\'TUN\',788, 0, 0, 0),
			 (\'TO\',\'TONGA\',\'TONGA\',\'TONGA\',\'TONGA\',\'TONGA\',\'TON\',776 ,0 ,0 ,0 ),
			 (\'TR\',\'TURKEY\',\'TURKEY\',\'TURQUÍA\',\'TÜRKEI\',\'TURQUIE\',\'TUR\',792 ,0 ,0 ,0),
			 (\'TT\',\'TRINIDAD AND TOBAGO\',\'TRINIDAD AND TOBAGO\',\'TRINIDAD Y TOBAGO\',\'TRINIDAD UND TOBAGO\',\'TRINITÉ-ET-TOBAGO\',\'TTO\',780,0,0,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'TV\',\'TUVALU\',\'TUVALU\',\'TUVALU\',\'TUVALU\',\'TUVALU\',\'TUV\',798,0,0,0),
			 (\'TW\',\'TAIWAN, PROVINCE OF CHINA\',\'TAIWAN, PROVINCE OF CHINA\',\'TAIWÁN\',\'TAIWAN\',\'TAÏWAN, PROVINCE DE CHINA\',\'TWN\',158,0,0,0),
			 (\'TZ\',\'TANZANIA, UNITED REPUBLIC OF\',\'TANZANIA, UNITED REPUBLIC OF\',\'TANZANIA\',\'TANSANIA\',\'TANZANIE, RÉPUBLIQUE-UNIE DE\',\'TZA\',834,0,0,0),
			 (\'UA\',\'UKRAINE\',\'UKRAINE\',\'UCRANIA\',\'UKRAINE\',\'UKRAINE\',\'UKR\', 804, 0, 0, 1),
			 (\'UG\',\'UGANDA\',\'UGANDA\',\'UGANDA\',\'UGANDA\',\'OUGANDA\',\'UGA\', 800, 0, 0, 0),
			 (\'UM\',\'UNITED STATES MINOR OUTLYING ISLANDS\',\'UNITED STATES MINOR OUTLYING ISLANDS\',\'ISLAS ULTRAMARINAS DE ESTADOS UNIDOS\',\'AMERIKANISCH-OZEANIEN\',\'ÎLES MINEURES ÉLOIGNÉES DES ÉTATS-UN\',\'UMI\',581,0,0,0),
			 (\'US\',\'UNITED STATES\',\'UNITED STATES\',\'ESTADOS UNIDOS\',\'VEREINIGTE STAATEN VON AMERIKA\',\'ÉTATS-UNIS\',\'USA\',840,0,0,0),
			 (\'UY\',\'URUGUAY\',\'URUGUAY\',\'URUGUAY\',\'URUGUAY\',\'URUGUAY\',\'URY\', 858, 0, 0, 0),
			 (\'UZ\',\'UZBEKISTAN\',\'UZBEKISTAN\',\'UZBEKISTÁN\',\'USBEKISTAN\',\'OUZBÉKISTAN\',\'UZB\', 860, 0, 0, 0),
			 (\'VA\',\'HOLY SEE (VATICAN CITY STATE)\',\'HOLY SEE (VATICAN CITY STATE)\',\'CIUDAD DEL VATICANO\',\'VATIKAN\',\'SAINT-SIÍšGE (ÉTAT DE LA CITÉ DU VATICAN)\',\'VAT\',336,0,0,1);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'VC\',\'SAINT VINCENT AND THE GRENADINES\',\'SAINT VINCENT AND THE GRENADINES\',\'SAN VICENTE Y LAS GRANADINAS\',\'SAINT VINCENT UND DIE GRENADINEN\',\'SAINT-VINCENT-ET-LES-GRENADINES\',\'VCT\',670,0,0,0),
			 (\'VE\',\'VENEZUELA\',\'VENEZUELA\',\'VENEZUELA\',\'VENEZUELA\',\'VENEZUELA\',\'VEN\',862,0,0,0),
			 (\'VG\',\'VIRGIN ISLANDS, BRITISH\',\'VIRGIN ISLANDS, BRITISH\',\'ISLAS VÍ­RGENES BRITÁNICAS\',\'BRITISCHE JUNGFERNINSELN\',\'ÎLES VIERGES BRITANNIQUES\',\'VGB\',92,0,0,0),
			 (\'VI\',\'VIRGIN ISLANDS, U.S.\',\'VIRGIN ISLANDS, U.S.\',\'ISLAS VÍ­RGENES DE LOS ESTADOS UNIDOS\',\'AMERIKANISCHE JUNGFERNINSELN\',\'ÎLES VIERGES DES ÉTATS-UNIS\',\'VIR\',850,0,0,0),
			 (\'VN\',\'VIET NAM\',\'VIET NAM\',\'VIETNAM\',\'VIETNAM\',\'VIET NAM\',\'VNM\',704,0,0,0),
			 (\'VU\',\'VANUATU\',\'VANUATU\',\'VANUATU\',\'VANUATU\',\'VANUATU\',\'VUT\',548,0,0,0),
			 (\'WF\',\'WALLIS AND FUTUNA\',\'WALLIS AND FUTUNA\',\'WALLIS Y FUTUNA\',\'WALLIS UND FUTUNA\',\'WALLIS ET FUTUNA\',\'WLF\',876 ,0 ,0 ,0),
			 (\'WS\',\'SAMOA\',\'SAMOA\',\'SAMOA\',\'SAMOA\',\'SAMOA\',\'WSM\',882 ,0 ,0 ,0),
			 (\'YE\',\'YEMEN\',\'YEMEN\',\'YEMEN\',\'JEMEN\',\'YÉMEN\',\'YEM\',887 ,0 ,0 ,0);';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_countries` VALUES  (\'YT\',\'MAYOTTE\',\'MAYOTTE\',\'MAYOTTE\',\'MAYOTTE\',\'MAYOTTE\',\'MYT\',175 ,0 ,0 ,0),
			 (\'ZA\',\'SOUTH AFRICA\',\'SOUTH AFRICA\',\'SUDÁFRICA\',\'SÜDAFRIKA, REPUBLIK\',\'AFRIQUE DU SUD\',\'ZAF\',710 ,0 ,0 ,0),
			 (\'ZM\',\'ZAMBIA\',\'ZAMBIA\',\'ZAMBIA\',\'SAMBIA\',\'ZAMBIE\',\'ZMB\',894 ,0 ,0 ,0),
			 (\'ZW\',\'ZIMBABWE\',\'ZIMBABWE\',\'ZIMBABUE\',\'SIMBABWE\',\'ZIMBABWE\',\'ZWE\',716 ,0 ,0 ,0);';
			$wpdb->query( $sql );
		}
	}
}
?>
