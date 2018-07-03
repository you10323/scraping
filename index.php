<?PHP
require_once("phpQuery-onefile.php");
$companyList = [];
$url = "http://job.rikunabi.com/2019/search/pre/internship/result/?mainsub=0&pn=1";
$html = file_get_contents($url);
$pageNum = phpQuery::newDocument($html)->find(".js-p-search-resultCounter-count")->text();
// for ($num = 1; $num < $pageNum/100; $num++) {
for ($num = 1; $num < 3; $num++) {
  $url = "http://job.rikunabi.com/2019/search/pre/internship/result/?mainsub=0&pn=".$num;
  $html = file_get_contents($url);
  $array = phpQuery::newDocument($html)->find(".ts-p-_cassette-title");
  $urlArray = [];
  foreach ($array as $buf) {
      array_push($urlArray, "http://job.rikunabi.com".pq($buf)->find("a")->attr("href"));
  }

  foreach ($urlArray as $url) {
    $companyArray = [];
    $page = file_get_contents($url);
    $pageArray = phpQuery::newDocument($page);
    $companyName = pq($pageArray)->find(".ts-p-company-mainTitle")->find("a")->text();
    $date = pq($pageArray)->find(".ts-p-_internshipList-item-info-row-detail-text_place")->text();
    $trArray = pq($pageArray)->find("tr");
    foreach ($trArray as $tr) {
      $td = pq($tr)->find(".ts-p-mod-dataTable02-cell_thLowHeight")->text();
      if (preg_match("/交通費/", $td) || preg_match("/報酬/", $td)) {
        $text = pq($tr)->find(".ts-p-mod-dataTable02-cell_tdRightPd")->text();
        if (preg_match("/,/", $text) || preg_match("/円/", $text)) {
            $companyArray["name"] = str_replace(array("\r", "\n"), '', $companyName);
            $companyArray["date"] = str_replace(array("\r", "\n"), '', $date);
            $companyArray["text"] = str_replace(array("\r", "\n"), '', $text);
            $companyList[++$id] = $companyArray;
        }
      }
    }
  }
}
$companyList = json_encode($companyList, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
file_put_contents("test.json" , $companyList);
?>
