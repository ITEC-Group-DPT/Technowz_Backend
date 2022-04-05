<?php
    include '../api/apiheader.php';
    include '../classes/User.php';
    include '../classes/Statistic.php';

    $header = getallheaders();

    $user = new User($conn);
    $statistic = new Statistic($conn);


    function calculateDiffPercent($object)
    {
        $curData = $object['current'];
        $pastData = $object['past'];

        $curData = floatval($curData);
        $pastData = floatval($pastData);

        if ($pastData == 0) return $curData;

        $res = ($curData - $pastData)/ $pastData;
        $format = number_format((float)$res, 2, '.', '');

        return $format;
    }

    if (isset($header['Userid'])) {

        $userID = $header['Userid'];
        $isAdmin = $user->verifyAdmin($userID);

        if ($isAdmin == false) failApi("Access-Control Denied");

        if (isset($_GET['command']) == null) failApi("Invalid request");

        $command = $_GET['command'];

        if ($command == "getOverallStatistic") {

            $orderData = $statistic->getTotalOrderData();
            $userData = $statistic->getTotalAccountNum();
            

            $summaryData = $orderData + $userData;
            successApi($summaryData);
        }

        if ($command == "getDashboardDataByTime") {
            
            if (!isset($_GET['filter'])) failApi("Invalid request");

            $filter = $_GET['filter'];

            $orderData = $statistic->getOrderDataByTime($filter);
            $visitData = $statistic->getVisitByTime($filter);


            $finalData = $orderData;
            $finalData['customer'] = $visitData;
            
            foreach ($finalData as $key => $value) {
                print($finalData[$key]['percent']);

                $finalData[$key]['percent'] = calculateDiffPercent($value); 
            }

            successApi($finalData);
        }

    }
    failApi("Access-Control Denied");
