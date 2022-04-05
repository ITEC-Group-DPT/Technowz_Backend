<?php
    include '../api/apiheader.php';
    include '../classes/User.php';

    $user = new User($conn);
    $header = getallheaders();
    if(isset($_GET['command'])){
        if($_GET['command'] == 'getLeaderboardData'){
            if($user->getLeaderboardData()){
                $res = $user->getLeaderBoardData();
                if($res['isSuccess']){
                    successApi($res['data']);
                }else{
                    failApi("isLoading");
                }
            }
        }else if($_GET['command'] == 'getChartData'){
            if($_GET['each'] == 'year'){
                if($user->getChartData('year')){
                    $res = $user->getChartData('year');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'week'){
                if($user->getChartData('week')){
                    $res = $user->getChartData('week');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'month'){
                if($user->getChartData('month')){
                    $res = $user->getChartData('month');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'day'){
                if($user->getChartData('day')){
                    $res = $user->getChartData('day');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }
        }
    }

?>