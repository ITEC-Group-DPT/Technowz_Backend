<?php
include '../api/apiheader.php';
include '../classes/CustomerStatistic.php';
include '../classes/SaleStatistic.php';

$header = getallheaders();
if (isset($header['Userid']))
{
    $userID = $header['Userid'];
}

if (isset($_GET['command']))
{
    if ($_GET['command'] == 'getSaleOverview')
    {

        $sortby = isset($_GET['sortby']) ? $_GET['sortby'] : 'month';
        if ($sortby == 'day')
        {
            $currentInterval = "DATE(now() - INTERVAL 1 {$sortby})";
            $previousInterval = "DATE(now() - INTERVAL 2 {$sortby})";
        }
        elseif ($sortby == 'month'){
            $firstdayofmonth = date('Y-m-01');
            $currentInterval = "DATE('{$firstdayofmonth}')";
            $previousInterval = "DATE('{$firstdayofmonth}' - INTERVAL 1 {$sortby})";
        }
        elseif ($sortby == 'year'){
            $firstdayofyear =  date('Y-01-01');
            $currentInterval = "DATE('{$firstdayofyear}')";
            $previousInterval = "DATE('{$firstdayofyear}' - INTERVAL 1 {$sortby})";
        }
        // $currentInterval = "DATE(now() - INTERVAL 1 {$sortby})";
        // $previousInterval = "DATE(now() - INTERVAL 2 {$sortby})";
        // $currentInterval = "date('2021-07-01 00:00:00')";
        // $previousInterval = "date('2021-06-01 00:00:00')";
        $arr = [];
        $sts = new Statistic($conn);
        $arr['saleInTime'] = $sts->getSaleInTime($currentInterval);
        $arr['itemOnSale'] = $sts->getItemOnSale($currentInterval);
        $arr['topRevenue'] = $sts->getTopRevenue($currentInterval);
        $arr['bestSeller'] = $sts->getBestSeller($currentInterval, $previousInterval);
        $arr['mostViewed'] = $sts->getMostViewed($currentInterval, $previousInterval);
        $arr['mostProfitableCate'] = $sts->getMostProfitableCate($currentInterval, $previousInterval);
        $arr['incomeByTime'] = $sts->getIncomeLineChart($sortby);

        $customerStat = new CustomerStatistic($conn);
        $arr['topCustomer'] = $customerStat->getLeaderBoardData(5, $sortby)["data"];
        successApi($arr);
        // successApi(date("Y-m-d H:i:s"));
        // 2021-07-30 09:13:51
        // $product = new Product($conn, $productID);
        // $arr = [];
        // $arr['product'] = $product->getProduct();

        // if(isset($userID)){
        //     $favorite = new Favorite($conn, $userID);
        //     $arr['favorite'] = $favorite->checkFavorite($productID);
        // }
        // else $arr['favorite'] = false;

        // if($arr['product'] != [])
        //     successApi($arr);
        // else failAPI("No product found");
    }
    else failApi("No command found!");
}
