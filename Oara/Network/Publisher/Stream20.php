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
 * @category   Oara_Network_Publisher_Stream20
 * @copyright  Fubra Limited
 * @version    Release: 01.00
 *
 */
class Oara_Network_Publisher_Stream20 extends Oara_Network {
	/**
	 * Export client.
	 * @var Oara_Curl_Access
	 */
	private $_client = null;
	/**
	 * Merchants Export Parameters
	 * @var array
	 */
	private $_exportMerchantParameters = null;
	/**
	 * Transaction Export Parameters
	 * @var array
	 */
	private $_exportTransactionParameters = null;
	/**
	 * Overview Export Parameters
	 * @var array
	 */
	private $_exportOverviewParameters = null;
	/**
	 * Date Format, it's different in some accounts
	 * @var string
	 */
	private $_dateFormat = null;
	/**
	 *
	 * Credentials
	 * @var array
	 */
	private $_credentials = null;
	/**
	 * Constructor and Login
	 * @param $tradeDoubler
	 * @return Oara_Network_Publisher_Td_Export
	 */
	public function __construct($credentials) {

		$this->_credentials = $credentials;

		self::login();

		$this->_exportMerchantParameters = array(new Oara_Curl_Parameter('reportName', 'aAffiliateMyProgramsReport'),
			new Oara_Curl_Parameter('tabMenuName', ''),
			new Oara_Curl_Parameter('isPostBack', ''),
			new Oara_Curl_Parameter('showAdvanced', 'true'),
			new Oara_Curl_Parameter('showFavorite', 'false'),
			new Oara_Curl_Parameter('run_as_organization_id', ''),
			new Oara_Curl_Parameter('minRelativeIntervalStartTime', '0'),
			new Oara_Curl_Parameter('maxIntervalSize', '0'),
			new Oara_Curl_Parameter('interval', 'MONTHS'),
			new Oara_Curl_Parameter('reportPrograms', ''),
			new Oara_Curl_Parameter('reportTitleTextKey', 'REPORT3_SERVICE_REPORTS_AAFFILIATEMYPROGRAMSREPORT_TITLE'),
			new Oara_Curl_Parameter('setColumns', 'true'),
			new Oara_Curl_Parameter('latestDayToExecute', '0'),
			new Oara_Curl_Parameter('affiliateId', ''),
			new Oara_Curl_Parameter('includeWarningColumn', 'true'),
			new Oara_Curl_Parameter('sortBy', 'orderDefault'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('columns', 'programId'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('columns', 'affiliateId'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('columns', 'applicationDate'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('columns', 'status'),
			new Oara_Curl_Parameter('autoCheckbox', 'useMetricColumn'),
			new Oara_Curl_Parameter('customKeyMetricCount', '0'),
			new Oara_Curl_Parameter('metric1.name', ''),
			new Oara_Curl_Parameter('metric1.midFactor', ''),
			new Oara_Curl_Parameter('metric1.midOperator', '/'),
			new Oara_Curl_Parameter('metric1.columnName1', 'programId'),
			new Oara_Curl_Parameter('metric1.operator1', '/'),
			new Oara_Curl_Parameter('metric1.columnName2', 'programId'),
			new Oara_Curl_Parameter('metric1.lastOperator', '/'),
			new Oara_Curl_Parameter('metric1.factor', ''),
			new Oara_Curl_Parameter('metric1.summaryType', 'NONE'),
			new Oara_Curl_Parameter('format', 'CSV'),
			new Oara_Curl_Parameter('separator', ','),
			new Oara_Curl_Parameter('dateType', '0'),
			new Oara_Curl_Parameter('favoriteId', ''),
			new Oara_Curl_Parameter('favoriteName', ''),
			new Oara_Curl_Parameter('favoriteDescription', '')
		);

		$this->_exportTransactionParameters = array(new Oara_Curl_Parameter('reportName', 'aAffiliateEventBreakdownReport'),
			new Oara_Curl_Parameter('columns', 'programId'),
			new Oara_Curl_Parameter('columns', 'timeOfVisit'),
			new Oara_Curl_Parameter('columns', 'timeOfEvent'),
			new Oara_Curl_Parameter('columns', 'timeInSession'),
			new Oara_Curl_Parameter('columns', 'lastModified'),
			new Oara_Curl_Parameter('columns', 'epi1'),
			new Oara_Curl_Parameter('columns', 'eventName'),
			new Oara_Curl_Parameter('columns', 'pendingStatus'),
			new Oara_Curl_Parameter('columns', 'siteName'),
			new Oara_Curl_Parameter('columns', 'graphicalElementName'),
			new Oara_Curl_Parameter('columns', 'graphicalElementId'),
			new Oara_Curl_Parameter('columns', 'productName'),
			new Oara_Curl_Parameter('columns', 'productNrOf'),
			new Oara_Curl_Parameter('columns', 'productValue'),
			new Oara_Curl_Parameter('columns', 'affiliateCommission'),
			new Oara_Curl_Parameter('columns', 'link'),
			new Oara_Curl_Parameter('columns', 'leadNR'),
			new Oara_Curl_Parameter('columns', 'orderNR'),
			new Oara_Curl_Parameter('columns', 'pendingReason'),
			new Oara_Curl_Parameter('columns', 'orderValue'),
			new Oara_Curl_Parameter('isPostBack', ''),
			new Oara_Curl_Parameter('metric1.lastOperator', '/'),
			new Oara_Curl_Parameter('interval', ''),
			new Oara_Curl_Parameter('favoriteDescription', ''),
			new Oara_Curl_Parameter('event_id', '0'),
			new Oara_Curl_Parameter('pending_status', '1'),
			new Oara_Curl_Parameter('run_as_organization_id', ''),
			new Oara_Curl_Parameter('minRelativeIntervalStartTime', '0'),
			new Oara_Curl_Parameter('includeWarningColumn', 'true'),
			new Oara_Curl_Parameter('metric1.summaryType', 'NONE'),
			new Oara_Curl_Parameter('metric1.operator1', '/'),
			new Oara_Curl_Parameter('latestDayToExecute', '0'),
			new Oara_Curl_Parameter('showAdvanced', 'true'),
			new Oara_Curl_Parameter('breakdownOption', '1'),
			new Oara_Curl_Parameter('metric1.midFactor', ''),
			new Oara_Curl_Parameter('reportTitleTextKey', 'REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE'),
			new Oara_Curl_Parameter('setColumns', 'true'),
			new Oara_Curl_Parameter('metric1.columnName1', 'orderValue'),
			new Oara_Curl_Parameter('metric1.columnName2', 'orderValue'),
			new Oara_Curl_Parameter('reportPrograms', ''),
			new Oara_Curl_Parameter('metric1.midOperator', '/'),
			new Oara_Curl_Parameter('dateSelectionType', '1'),
			new Oara_Curl_Parameter('favoriteName', ''),
			new Oara_Curl_Parameter('affiliateId', ''),
			new Oara_Curl_Parameter('dateType', '1'),
			new Oara_Curl_Parameter('period', 'custom_period'),
			new Oara_Curl_Parameter('tabMenuName', ''),
			new Oara_Curl_Parameter('maxIntervalSize', '0'),
			new Oara_Curl_Parameter('favoriteId', ''),
			new Oara_Curl_Parameter('sortBy', 'timeOfEvent'),
			new Oara_Curl_Parameter('metric1.name', ''),
			new Oara_Curl_Parameter('customKeyMetricCount', '0'),
			new Oara_Curl_Parameter('metric1.factor', ''),
			new Oara_Curl_Parameter('showFavorite', 'false'),
			new Oara_Curl_Parameter('separator', ','),
			new Oara_Curl_Parameter('format', 'CSV')
		);

		$this->_exportOverviewParameters = array(new Oara_Curl_Parameter('reportName', 'aAffiliateProgramOverviewReport'),
			new Oara_Curl_Parameter('tabMenuName', ''),
			new Oara_Curl_Parameter('isPostBack', ''),
			new Oara_Curl_Parameter('showAdvanced', 'true'),
			new Oara_Curl_Parameter('showFavorite', 'false'),
			new Oara_Curl_Parameter('run_as_organization_id', ''),
			new Oara_Curl_Parameter('minRelativeIntervalStartTime', '0'),
			new Oara_Curl_Parameter('maxIntervalSize', '12'),
			new Oara_Curl_Parameter('interval', 'MONTHS'),
			new Oara_Curl_Parameter('reportPrograms', ''),
			new Oara_Curl_Parameter('reportTitleTextKey', 'REPORT3_SERVICE_REPORTS_AAFFILIATEPROGRAMOVERVIEWREPORT_TITLE'),
			new Oara_Curl_Parameter('setColumns', 'true'),
			new Oara_Curl_Parameter('latestDayToExecute', '0'),
			new Oara_Curl_Parameter('programTypeId', ''),
			new Oara_Curl_Parameter('includeWarningColumn', 'true'),
			new Oara_Curl_Parameter('programId', ''),
			new Oara_Curl_Parameter('period', 'custom_period'),
			new Oara_Curl_Parameter('columns', 'programId'),
			new Oara_Curl_Parameter('columns', 'impNrOf'),
			new Oara_Curl_Parameter('columns', 'clickNrOf'),
			new Oara_Curl_Parameter('autoCheckbox', 'columns'),
			new Oara_Curl_Parameter('autoCheckbox', 'useMetricColumn'),
			new Oara_Curl_Parameter('customKeyMetricCount', '0'),
			new Oara_Curl_Parameter('metric1.name', ''),
			new Oara_Curl_Parameter('metric1.midFactor', ''),
			new Oara_Curl_Parameter('metric1.midOperator', '/'),
			new Oara_Curl_Parameter('metric1.columnName1', 'programId'),
			new Oara_Curl_Parameter('metric1.operator1', '/'),
			new Oara_Curl_Parameter('metric1.columnName2', 'programId'),
			new Oara_Curl_Parameter('metric1.lastOperator', '/'),
			new Oara_Curl_Parameter('metric1.factor', ''),
			new Oara_Curl_Parameter('metric1.summaryType', 'NONE'),
			new Oara_Curl_Parameter('format', 'CSV'),
			new Oara_Curl_Parameter('separator', ';'),
			new Oara_Curl_Parameter('dateType', '1'),
			new Oara_Curl_Parameter('favoriteId', ''),
			new Oara_Curl_Parameter('favoriteName', ''),
			new Oara_Curl_Parameter('favoriteDescription', '')
		);
	}
	/**
	 *
	 * Login into the web interface
	 */
	private function login() {
		$user = $this->_credentials['user'];
		$password = $this->_credentials['password'];
		$loginUrl = 'http://publisher.tradedoubler.com/pan/login';

		$valuesLogin = array(new Oara_Curl_Parameter('j_username', $user),
			new Oara_Curl_Parameter('j_password', $password)
		);

		$this->_client = new Oara_Curl_Access($loginUrl, $valuesLogin, $this->_credentials);

	}
	/**
	 * Check the connection
	 */
	public function checkConnection() {
		$connection = false;

		$urls = array();
		$urls[] = new Oara_Curl_Request('http://publisher.tradedoubler.com/pan/aReport3Selection.action?reportName=aAffiliateProgramOverviewReport', array());
		$exportReport = $this->_client->get($urls);
		if (preg_match("/\(([a-zA-Z]{0,2}[\/\.][a-zA-Z]{0,2}[\/\.][a-zA-Z]{0,2})\)/", $exportReport[0], $match)) {
			$this->_dateFormat = $match[1];
		}

		if ($this->_dateFormat != null) {
			$connection = true;
		}
		return $connection;
	}
	/**
	 * It returns the Merchant CVS report.
	 * @return $exportReport
	 */
	private function getExportMerchantReport($content) {
		$merchantReport = self::formatCsv($content);

		$exportData = str_getcsv($merchantReport, "\r\n");
		$merchantReportList = Array();
		$num = count($exportData);
		for ($i = 3; $i < $num; $i++) {
			$merchantExportArray = str_getcsv($exportData[$i], ",");

			if ($merchantExportArray[2] != '' && $merchantExportArray[4] != '') {
				$merchantReportList[$merchantExportArray[4]] = $merchantExportArray[2];
			}
		}
		return $merchantReportList;
	}
	/**
	 *
	 * Format Csv
	 * @param unknown_type $csv
	 */
	private function formatCsv($csv) {
		preg_match_all("/\"([^\"]+?)\",/", $csv, $matches);
		foreach ($matches[1] as $match) {
			if (preg_match("/,/", $match)) {
				$rep = preg_replace("/,/", "", $match);
				$csv = str_replace($match, $rep, $csv);
				$match = $rep;
			}
			if (preg_match("/\n/", $match)) {
				$rep = preg_replace("/\n/", "", $match);
				$csv = str_replace($match, $rep, $csv);
			}
		}
		return $csv;
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Oara/Network/Oara_Network_Publisher_Base#getMerchantList()
	 */
	public function getMerchantList() {
		$merchants = array();

		$obj = array();
		$obj['cid'] = 1;
		$obj['name'] = "Stream20";
		$merchants[] = $obj;

		return $merchants;
	}

	/**
	 * (non-PHPdoc)
	 * @see Oara_Network::getTransactionList()
	 */
	public function getTransactionList($merchantList = null, Zend_Date $dStartDate = null, Zend_Date $dEndDate = null, $merchantMap = null) {
		$totalTransactions = Array();
		$filter = new Zend_Filter_LocalizedToNormalized(array('precision' => 2));
		self::login();

		$valuesFormExport = Oara_Utilities::cloneArray($this->_exportTransactionParameters);
		$valuesFormExport[] = new Oara_Curl_Parameter('startDate', self::formatDate($dStartDate));
		$valuesFormExport[] = new Oara_Curl_Parameter('endDate', self::formatDate($dEndDate));
		$urls = array();
		$urls[] = new Oara_Curl_Request('http://publisher.tradedoubler.com/pan/aReport3Internal.action?', $valuesFormExport);
		$exportReport = $this->_client->get($urls);
		$exportReport[0] = self::checkReportError($exportReport[0], $urls[0]);
		$exportData = str_getcsv($exportReport[0], "\r\n");
		$num = count($exportData);
		for ($i = 2; $i < $num - 1; $i++) {

			$transactionExportArray = str_getcsv($exportData[$i], ",");

			if (!isset($transactionExportArray[2])) {
				throw new Exception('Problem getting transaction\n\n');
			}

			if ($transactionExportArray[0] !== '') {

				$transaction = Array();
				$transaction['merchantId'] = 1;
				$transactionDate = self::toDate($transactionExportArray[4]);
				$transaction['date'] = $transactionDate->toString("yyyy-MM-dd HH:mm:ss");
				if ($transactionExportArray[8] != '') {
					$transaction['unique_id'] = $transactionExportArray[8];
				} else
					if ($transactionExportArray[7] != '') {
						$transaction['unique_id'] = $transactionExportArray[7];
					} else {
						throw new Exception("No Identifier");
					}
				if ($transactionExportArray[9] != '') {
					$transaction['custom_id'] = $transactionExportArray[9];
				}

				if ($transactionExportArray[11] == 'A') {
					$transaction['status'] = Oara_Utilities::STATUS_CONFIRMED;
				} else
					if ($transactionExportArray[11] == 'P') {
						$transaction['status'] = Oara_Utilities::STATUS_PENDING;
					} else
						if ($transactionExportArray[11] == 'D') {
							$transaction['status'] = Oara_Utilities::STATUS_DECLINED;
						}

				if ($transactionExportArray[18] != '') {
					$transaction['amount'] = Oara_Utilities::parseDouble($transactionExportArray[18]);
				} else {
					$transaction['amount'] = Oara_Utilities::parseDouble($transactionExportArray[19]);
				}

				$transaction['commission'] = Oara_Utilities::parseDouble($transactionExportArray[19]);
				$totalTransactions[] = $transaction;
			}
		}
		return $totalTransactions;
	}

	public function checkReportError($content, $request, $try = 0) {

		if (preg_match("/\/report\/published\/aAffiliateEventBreakdownReport/", $content, $matches)) {
			//report too big, we have to download it and read it
			if (preg_match("/(\/report\/published\/(aAffiliateEventBreakdownReport(.*))\.zip)/", $content, $matches)) {

				$file = "http://publisher.tradedoubler.com".$matches[0];
				$newfile = realpath ( dirname ( COOKIES_BASE_DIR ) ) . '/pdf/'.$matches[2].'.zip';

				if (!copy($file, $newfile)) {
					throw new Exception('Failing copying the zip file \n\n');
				}
				$zip = new ZipArchive();
				if ($zip->open($newfile, ZIPARCHIVE::CREATE) !== TRUE) {
					throw new Exception('Cannot open zip file \n\n');
				}
				$zip->extractTo(realpath ( dirname ( COOKIES_BASE_DIR ) ) . '/pdf/');
				$zip->close();

				$unzipFilePath = realpath ( dirname ( COOKIES_BASE_DIR ) ) . '/pdf/'.$matches[2];
				$fileContent = file_get_contents($unzipFilePath);
				unlink($newfile);
				unlink($unzipFilePath);
				return $fileContent;
			}

			throw new Exception('Report too big \n\n');

		} else
			if (preg_match("/ error/", $content, $matches)) {
				$urls = array();
				$urls[] = $request;
				$exportReport = $this->_client->get($urls);
				$try++;
				if ($try < 5) {
					return self::checkReportError($exportReport[0], $request, $try);
				} else {
					throw new Exception('Problem checking report\n\n');
				}

			} else {
				return $content;
			}

	}

	/**
	 * (non-PHPdoc)
	 * @see Oara/Network/Oara_Network_Publisher_Base#getPaymentHistory()
	 */
	public function getPaymentHistory() {
		$filter = new Zend_Filter_LocalizedToNormalized(array('precision' => 2));
		$paymentHistory = array();

		$urls = array();
		$urls[] = new Oara_Curl_Request('http://publisher.tradedoubler.com/pan/reportSelection/Payment?', array());
		$exportReport = $this->_client->get($urls);
		/*** load the html into the object ***/
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		$doc->validateOnParse = true;
		$doc->loadHTML($exportReport[0]);
		$selectList = $doc->getElementsByTagName('select');
		$paymentSelect = null;
		if ($selectList->length > 0) {
			// looking for the payments select
			$it = 0;
			while ($it < $selectList->length) {
				$selectName = $selectList->item($it)->attributes->getNamedItem('name')->nodeValue;
				if ($selectName == 'payment_id') {
					$paymentSelect = $selectList->item($it);
					break;
				}
				$it++;
			}
			if ($paymentSelect != null) {
				$paymentLines = $paymentSelect->childNodes;
				for ($i = 0; $i < $paymentLines->length; $i++) {
					$pid = $paymentLines->item($i)->attributes->getNamedItem("value")->nodeValue;
					if (is_numeric($pid)) {
						$obj = array();

						$paymentLine = $paymentLines->item($i)->nodeValue;
						$paymentLine = htmlentities($paymentLine);
						$paymentLine = str_replace("&Acirc;&nbsp;", "", $paymentLine);
						$paymentLine = html_entity_decode($paymentLine);

						$date = self::toDate(substr($paymentLine, 0, 10));

						$obj['date'] = $date->toString("yyyy-MM-dd HH:mm:ss");
						$obj['pid'] = $pid;
						$obj['method'] = 'BACS';
						if (preg_match("/[-+]?[0-9]*,?[0-9]*\.?[0-9]+/", substr($paymentLine, 10), $matches)) {
							$obj['value'] = $filter->filter($matches[0]);
						} else {
							throw new Exception("Problem reading payments");
						}

						$paymentHistory[] = $obj;
					}
				}
			}
		}
		return $paymentHistory;
	}

	/**
	 *
	 * It returns the transactions for a payment
	 * @param int $paymentId
	 */
	public function paymentTransactions($paymentId, $merchantList, $startDate) {
		$transactionList = array();

		$urls = array();
		$valuesFormExport = array();
		$valuesFormExport[] = new Oara_Curl_Parameter('popup', 'true');
		$valuesFormExport[] = new Oara_Curl_Parameter('payment_id', $paymentId);
		$urls[] = new Oara_Curl_Request('http://publisher.tradedoubler.com/pan/reports/Payment.html?', $valuesFormExport);
		$exportReport = $this->_client->get($urls);
		$dom = new Zend_Dom_Query($exportReport[0]);
		$results = $dom->query('a');

		$urls = array();
		foreach ($results as $result) {
			$url = $result->getAttribute('href');
			$urls[] = new Oara_Curl_Request("http://publisher.tradedoubler.com".$url."&format=CSV", array());
		}
		$exportReportList = $this->_client->get($urls);
		foreach ($exportReportList as $exportReport) {
			$exportReportData = str_getcsv($exportReport, "\r\n");
			$num = count($exportReportData);
			for ($i = 2; $i < $num - 1; $i++) {
				$transactionArray = str_getcsv($exportReportData[$i], ";");
				if ($transactionArray[8] != '') {
					$transactionList[] = $transactionArray[8];
				} else
					if ($transactionArray[7] != '') {
						$transactionList[] = $transactionArray[7];
					} else {
						throw new Exception("No Identifier");
					}
			}
		}

		return $transactionList;
	}

	/**
	 *
	 * Add Dates in a certain format to the criteriaList
	 * @param array $criteriaList
	 * @param array $dateArray
	 * @throws Exception
	 */
	private function formatDate($date) {
		$dateString = "";
		if ($this->_dateFormat == 'dd/MM/yy') {
			$dateString = $date->toString('dd/MM/yyyy');
		} else
			if ($this->_dateFormat == 'M/d/yy') {
				$dateString = $date->toString('M/d/yy');
			} else
				if ($this->_dateFormat == 'd/MM/yy') {
					$dateString = $date->toString('d/MM/yy');
				} else
					if ($this->_dateFormat == 'tt.MM.uu') {
						$dateString = $date->toString('dd.MM.yy');
					} else {
						throw new Exception("\n Date Format not supported ".$this->_dateFormat."\n");
					}
		return $dateString;
	}
	/**
	 *
	 * Date String to Object
	 * @param unknown_type $dateString
	 * @throws Exception
	 */
	private function toDate($dateString) {
		$transactionDate = null;
		if ($this->_dateFormat == 'dd/MM/yy') {
			$transactionDate = new Zend_Date(trim($dateString), "dd/MM/yy HH:mm:ss");
		} else
			if ($this->_dateFormat == 'M/d/yy') {
				$transactionDate = new Zend_Date(trim($dateString), "M/d/yy HH:mm:ss");
			} else
				if ($this->_dateFormat == 'd/MM/yy') {
					$transactionDate = new Zend_Date(trim($dateString), "d/MM/yy HH:mm:ss");
				} else
					if ($this->_dateFormat == 'tt.MM.uu') {
						$transactionDate = new Zend_Date(trim($dateString), "dd.MM.yy HH:mm:ss");
					} else {
						throw new Exception("\n Date Format not supported ".$this->_dateFormat."\n");
					}
		return $transactionDate;
	}
}
