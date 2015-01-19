<?php
/**
 The goal of the Open Affiliate Report Aggregator (OARA) is to develop a set
 of PHP classes that can download affiliate reports from a number of affiliate networks, and store the data in a common format.

 Copyright (C) 2014  Fubra Limited
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or any later version.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.
 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

 Contact
 ------------
 Fubra Limited <support@fubra.com> , +44 (0)1252 367 200
 **/
/**
 * Export Class
 *
 * @author     Carlos Morillo Merino
 * @category   Oara_Network_Publisher_Amazon
 * @copyright  Fubra Limited
 * @version    Release: 01.00
 *
 */
class Oara_Network_Publisher_Amazon extends Oara_Network {
        /**
         * The network to use if none is specified
         */
        const NETWORK = 'us';
        
	/**
	 * Export Merchants Parameters
	 * @var array
	 */
	private $_exportMerchantParameters = null;
	/**
	 * Export Transaction Parameters
	 * @var array
	 */
	private $_exportTransactionParameters = null;
	/**
	 * Export Overview Parameters
	 * @var array
	 */
	private $_exportOverviewParameters = null;
	/**
	 * Export Payment Parameters
	 * @var array
	 */
	private $_exportPaymentParameters = null;

	private $_idBox = null;

	private $_credentials = null;
	/**
	 * Client
	 * @var unknown_type
	 */
	private $_client = null;
	/**
	 * Server Url for the Network Selected
	 */
	private $_networkServer = null;


	private $_extension = null;
	/**
	 * Constructor and Login
	 * @param $credentials
	 * @return Oara_Network_Publisher_Amazon
	 */
	public function __construct($credentials) {
            
                /*
                 * If the network was specified in the credentials file, we'll use
                 * that; otherwise, we'll use the value from the network constant.
                 * Note the use of 'static' which allows us to override the network
                 * constant in subclasses
                 */
                if (empty($credentials['network'])) {
                    $credentials['network'] = static::NETWORK;
                }
		$this->_credentials = $credentials;

		self::logIn();
		$this->_exportTransactionParameters = array(
		new Oara_Curl_Parameter('tag', ''),
		new Oara_Curl_Parameter('reportType', 'earningsReport'),
		new Oara_Curl_Parameter('program', 'all'),
		new Oara_Curl_Parameter('preSelectedPeriod', 'monthToDate'),
		new Oara_Curl_Parameter('periodType', 'exact'),
		new Oara_Curl_Parameter('submit.download_CSV.x', '106'),
		new Oara_Curl_Parameter('submit.download_CSV.y', '11'),
		new Oara_Curl_Parameter('submit.download_CSV', 'Download report (CSV)')
		);

		$this->_exportOverviewParameters = array(
		new Oara_Curl_Parameter('tag', ''),
		new Oara_Curl_Parameter('reportType', 'trendsReport'),
		new Oara_Curl_Parameter('preSelectedPeriod', 'monthToDate'),
		new Oara_Curl_Parameter('periodType', 'exact'),
		new Oara_Curl_Parameter('submit.download_CSV.x', '106'),
		new Oara_Curl_Parameter('submit.download_CSV.y', '11'),
		new Oara_Curl_Parameter('submit.download_CSV', 'Download report (CSV)')
		);

		$this->_exportPaymentParameters = array();

	}

	private function logIn() {
		$user = $this->_credentials['user'];
		$password = $this->_credentials['password'];
		$network = $this->_credentials['network'];
		$this->_httpLogin = null;
		if (isset($this->_credentials['httpLogin'])){
			$this->_httpLogin = $this->_credentials['httpLogin'];
		}
		$extension = "";
		$handle = "";
		$this->_networkServer = "";
		switch ($network) {
			case "uk":
				$this->_networkServer = "https://affiliate-program.amazon.co.uk";
				$extension = ".co.uk";
				$handle = "gb";
				break;
			case "es":
				$this->_networkServer = "https://afiliados.amazon.es";
				$extension = ".es";
				$handle = "es";
				break;
			case "us":
				$this->_networkServer = "https://affiliate-program.amazon.com";
				$extension = ".com";
				$handle = "us";
				break;
			case "ca":
				$this->_networkServer = "https://associates.amazon.ca";
				$extension = ".ca";
				$handle = "ca";
				break;
			case "de":
				$this->_networkServer = "https://partnernet.amazon.de";
				$extension = ".de";
				$handle = "de";
				break;
			case "fr":
				$this->_networkServer = "https://partenaires.amazon.fr";
				$extension = ".fr";
				$handle = "fr";
				break;
			case "it":
				$this->_networkServer = "https://programma-affiliazione.amazon.it";
				$extension = ".it";
				$handle = "it";
				break;
			case "jp":
				$this->_networkServer = "https://affiliate.amazon.co.jp";
				$extension = ".co.jp";
				$handle = "jp";
				break;
			case "cn":
				$this->_networkServer = "https://associates.amazon.cn";
				$extension = ".cn";
				$handle = "cn";
				break;
		}
		$this->_extension = $extension;
		$this->_client = new Oara_Curl_Access($this->_networkServer."/gp/associates/network/main.html", array(), $this->_credentials);

	        if (!isset($this->_credentials["cookiesDir"])) {
	            $this->_credentials["cookiesDir"] = "Oara";
	        }
	        if (!isset($this->_credentials["cookiesSubDir"])) {
	            $this->_credentials["cookiesSubDir"] = "Import";
	        }
	        if (!isset($this->_credentials["cookieName"])) {
	            $this->_credentials["cookieName"] = "default";
	        }

			$dir = COOKIES_BASE_DIR . DIRECTORY_SEPARATOR . $this->_credentials['cookiesDir'] . DIRECTORY_SEPARATOR . $this->_credentials['cookiesSubDir'] . DIRECTORY_SEPARATOR;
			$cookieName = $this->_credentials["cookieName"];
			$cookies = $dir.$cookieName.'_cookies.txt';

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $this->_networkServer);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
	        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	        $strHTML = curl_exec($ch);
	        curl_close($ch);

	        libxml_use_internal_errors(true);
	        $objDOM = new DOMDocument();
	        $objDOM->loadHTML($strHTML);

	        $objXPath = new DOMXPath($objDOM);

	        $objForm = $objXPath->query("//form[@name='sign_in']");
	        $objForm = $objForm->item(0);

	        $objInputs = $objXPath->query("//input[@type='hidden']", $objForm);

	        $arrInputs = array(
	            'username' => $user,
	            'password' => $password,
	        );
	        foreach ($objInputs as $objInput) {
	            $arrInputs[$objInput->getAttribute('name')] = $objInput->getAttribute('value');
	        }

	        $strURL = $objForm->getAttribute('action');

	        // make the actual login-request
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $strURL);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
	        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
	        curl_setopt($ch, CURLOPT_POST, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrInputs));
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	        $strHTML = curl_exec($ch);
	        curl_close($ch);
	}
	/**
	 * Check the connection
	 */
	public function checkConnection() {
		//If not login properly the construct launch an exception
		$connection = false;
		$urls = array();
		$urls[] = new Oara_Curl_Request($this->_networkServer."/gp/associates/network/main.html", array());
		$exportReport = $this->_client->get($urls);

		if (preg_match("/logout%26openid.ns/", $exportReport[0])) {
			$dom = new Zend_Dom_Query($exportReport[0]);
			$idBox = array();
			$results = $dom->query('select[name="idbox_store_id"]');
			$count = count($results);
			if ($count == 0) {
				$idBox[] = "";
			} else {
				foreach ($results as $result) {
					$optionList = $result->childNodes;
					$optionNumber = $optionList->length;
					for ($i = 0; $i < $optionNumber; $i++) {
						$idBoxName = $optionList->item($i)->attributes->getNamedItem("value")->nodeValue;
						if (!in_array($idBoxName, $idBox)) {
							$idBox[] = $idBoxName;
						}
					}
				}
			}

			$this->_idBox = $idBox;
			$connection = true;
		}

		return $connection;
	}
	/**
	 * (non-PHPdoc)
	 * @see library/Oara/Network/Oara_Network_Publisher_Interface#getMerchantList()
	 */
	public function getMerchantList() {
		$merchants = array();

		$obj = array();
		$obj['cid'] = "1";
		$obj['name'] = "Amazon";
		$obj['url'] = "www.amazon.com";
		$merchants[] = $obj;

		return $merchants;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Oara/Network/Oara_Network_Publisher_Interface#getTransactionList($aMerchantIds, $dStartDate, $dEndDate, $sTransactionStatus)
	 */
	public function getTransactionList($merchantList = null, Zend_Date $dStartDate = null, Zend_Date $dEndDate = null, $merchantMap = null) {

		$totalTransactions = array();
		foreach ($this->_idBox as $id) {

			//$dateArray = Oara_Utilities::daysOfDifference($dStartDate, $dEndDate);
			//$dateArraySize = sizeof($dateArray);


			//for ($j = 0; $j < $dateArraySize; $j++) {
			//echo "day ".$dateArray[$j]->toString("d")."\n";
			//echo round(memory_get_usage(true) / 1048576, 2)." megabytes \n";
			$try = 0;
			$done = false;
			while (!$done && $try < 5) {
				try {

					$totalTransactions = array_merge($totalTransactions, self::getTransactionReportRecursive($id, $dStartDate, $dEndDate));
					$done = true;

				} catch (Exception $e) {
					$try++;
				}
			}
			if ($try == 5) {
				throw new Exception("Couldn't get data for the date ");
			}

			//}
		}

		return $totalTransactions;
	}

	private function getTransactionReportRecursive($id, $startDate, $endDate) {
		$totalTransactions = array();
		$valuesFromExport = Oara_Utilities::cloneArray($this->_exportTransactionParameters);
		$valuesFromExport[] = new Oara_Curl_Parameter('startDay', $startDate->toString("d"));
		$valuesFromExport[] = new Oara_Curl_Parameter('startMonth', (int) $startDate->toString("M") - 1);
		$valuesFromExport[] = new Oara_Curl_Parameter('startYear', $startDate->toString("yyyy"));
		$valuesFromExport[] = new Oara_Curl_Parameter('endDay', $endDate->toString("d"));
		$valuesFromExport[] = new Oara_Curl_Parameter('endMonth', (int) $endDate->toString("M") - 1);
		$valuesFromExport[] = new Oara_Curl_Parameter('endYear', $endDate->toString("yyyy"));
		$valuesFromExport[] = new Oara_Curl_Parameter('idbox_store_id', $id);

		$urls = array();
		$urls[] = new Oara_Curl_Request($this->_networkServer."/gp/associates/network/reports/report.html?", $valuesFromExport);
		$exportReport = $this->_client->get($urls);

		if (preg_match("/DOCTYPE/", $exportReport[0])){
			return array();
		}
		$exportData = str_getcsv($exportReport[0], "\n");


		$index = 2;
		try{
			if (!isset($transactionExportArray[$index]) || !isset($transactionExportArray[5])){
				throw new Exception("No date");
			}
			$transactionExportArray = str_getcsv(str_replace("\"", "", $exportData[$index]), "\t");

			$transactionDate = new Zend_Date($transactionExportArray[5], 'MMMM d,yyyy', 'en');
		} catch (Exception $e){
			$index = 3;
		}

		$num = count($exportData);
		for ($i = $index; $i < $num; $i++) {
			$transactionExportArray = str_getcsv(str_replace("\"", "", $exportData[$i]), "\t");
			$transactionDate = new Zend_Date($transactionExportArray[5], 'MMMM d,yyyy', 'en');
			$transaction = Array();
			$transaction['merchantId'] = 1;
			if (!isset($transactionExportArray[5])) {
				throw new Exception("Request failed");
			}

			$transaction['date'] = $transactionDate->toString("yyyy-MM-dd HH:mm:ss");
			unset($transactionDate);
			if ($transactionExportArray[4] != null) {
				$transaction['custom_id'] = $transactionExportArray[4];
			}

			$transaction['status'] = Oara_Utilities::STATUS_CONFIRMED;
			$transaction['amount'] = Oara_Utilities::parseDouble($transactionExportArray[9]);
			$transaction['commission'] = Oara_Utilities::parseDouble($transactionExportArray[10]);
			$transaction['device_type'] = $transactionExportArray[11];
			$transaction['skew'] = $transactionExportArray[2];
			$transaction['title'] = $transactionExportArray[1];
			$totalTransactions[] = $transaction;

		}
		return $totalTransactions;
	}

	/**
	 * (non-PHPdoc)
	 * @see Oara/Network/Oara_Network_Publisher_Base#getPaymentHistory()
	 */
	public function getPaymentHistory() {
		$paymentHistory = array();
		foreach ($this->_idBox as $id) {
			$urls = array();
			$paymentExport = array();
			$paymentExport[] = new Oara_Curl_Parameter('idbox_store_id', $id);
			$urls[] = new Oara_Curl_Request($this->_networkServer."/gp/associates/network/your-account/payment-history.html?", $paymentExport);
			$exportReport = $this->_client->get($urls);
			$dom = new Zend_Dom_Query($exportReport[0]);
			$results = $dom->query('.paymenthistory');
			$count = count($results);
			$yearArray = array();
			if ($count == 1) {
				$paymentTable = $results->current();
				$paymentReport = self::htmlToCsv(self::DOMinnerHTML($paymentTable));
				for ($i = 2; $i < count($paymentReport) - 1; $i++) {
					$paymentExportArray = str_getcsv($paymentReport[$i], ";");

					$obj = array();
					$paymentDate = new Zend_Date($paymentExportArray[0], "M d yyyy", "en");
					$obj['date'] = $paymentDate->toString("yyyy-MM-dd HH:mm:ss");
					$obj['pid'] = ($paymentDate->toString("yyyyMMdd").substr((string) base_convert(md5($id), 16, 10), 0, 5));
					$obj['method'] = 'BACS';
					if (preg_match("/[0-9]*,?[0-9]*\.?[0-9]+/", $paymentExportArray[4], $matches)) {
						$obj['value'] = Oara_Utilities::parseDouble($matches[0]);
						$paymentHistory[] = $obj;
					}

				}
			} else {
				//throw new Exception('Problem getting the payments');
			}
		}
		return $paymentHistory;
	}
	/**
	 *
	 * Function that Convert from a table to Csv
	 * @param unknown_type $html
	 */
	private function htmlToCsv($html) {
		$html = str_replace(array("\t", "\r", "\n"), "", $html);
		$csv = "";
		$dom = new Zend_Dom_Query($html);
		$results = $dom->query('tr');
		$count = count($results); // get number of matches: 4
		foreach ($results as $result) {
			$tdList = $result->childNodes;
			$tdNumber = $tdList->length;
			for ($i = 0; $i < $tdNumber; $i++) {
				$value = $tdList->item($i)->nodeValue;
				if ($i != $tdNumber - 1) {
					$csv .= trim($value).";";
				} else {
					$csv .= trim($value);
				}
			}
			$csv .= "\n";
		}
		$exportData = str_getcsv($csv, "\n");
		return $exportData;
	}
	/**
	 *
	 * Function that returns the innet HTML code
	 * @param unknown_type $element
	 */
	private function DOMinnerHTML($element) {
		$innerHTML = "";
		$children = $element->childNodes;
		foreach ($children as $child) {
			$tmp_dom = new DOMDocument();
			$tmp_dom->appendChild($tmp_dom->importNode($child, true));
			$innerHTML .= trim($tmp_dom->saveHTML());
		}
		return $innerHTML;
	}

	/**
	 *
	 * Gets the cookies value for this network
	 * @param unknown_type $credentials
	 */
	private function readCookies($credentials) {
		$dir = COOKIES_BASE_DIR . DIRECTORY_SEPARATOR . $credentials['cookiesDir'] . DIRECTORY_SEPARATOR . $credentials['cookiesSubDir'] . DIRECTORY_SEPARATOR;
		$cookieName = $credentials["cookieName"];
		$cookies = $dir.$cookieName.'_cookies.txt';

		$aCookies = array();
		$aLines = file($cookies);
		foreach ($aLines as $line) {
			if ('#' == $line {0})
				continue;
				$arr = explode("\t", $line);
				if (isset($arr[5]) && isset($arr[6])){
					if ($arr[0] == ".amazon{$this->_extension}"){
						$aCookies[$arr[5]] = str_replace("\n", "", $arr[6]);
					}

				}

		}
		return $aCookies;
	}

}
