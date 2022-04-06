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
        }else if($_GET['command'] == 'getVisitedUsers'){
            if($_GET['each'] == 'year'){
                if($user->getVisitedUsers('year')){
                    $res = $user->getVisitedUsers('year');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'week'){
                if($user->getVisitedUsers('week')){
                    $res = $user->getVisitedUsers('week');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'month'){
                if($user->getVisitedUsers('month')){
                    $res = $user->getVisitedUsers('month');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'day'){
                if($user->getVisitedUsers('day')){
                    $res = $user->getVisitedUsers('day');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }
        }else if($_GET['command'] == 'getActiveUsers'){
            if($_GET['each'] == 'year'){
                if($user->getActiveUsers('year')){
                    $res = $user->getActiveUsers('year');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'week'){
                if($user->getActiveUsers('week')){
                    $res = $user->getActiveUsers('week');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'month'){
                if($user->getActiveUsers('month')){
                    $res = $user->getActiveUsers('month');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'day'){
                if($user->getActiveUsers('day')){
                    $res = $user->getActiveUsers('day');
                    if($res['isSuccess']){
                        successApi($res['data']);
                    }else{
                        failApi("isLoading");
                    }
                }
            }
        }else if($_GET['command'] = 'getChartsData'){
            if($_GET['each'] == 'year'){
                if($user->getChartsData('year')){
                    $res = json_decode($user->getChartsData('year'), true);
                    if($res["isSuccess"]){
                        successApi(json_encode($res['data']));
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'week'){
                if($user->getChartsData('week')){
                    $res = json_decode($user->getChartsData('week'), true);
                    if($res["isSuccess"]){
                        successApi(json_encode($res['data']));
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'month'){
                if($user->getChartsData('month')){
                    $res = json_decode($user->getChartsData('month'), true);
                    if($res["isSuccess"]){
                        successApi(json_encode($res['data']));
                    }else{
                        failApi("isLoading");
                    }
                }
            }else if($_GET['each'] == 'day'){
                if($user->getChartsData('day')){
                    $res = json_decode($user->getChartsData('day'), true);
                    if($res["isSuccess"]){
                        successApi(json_encode($res['data']));
                    }else{
                        failApi("isLoading");
                    }
                }
            }
        }
    }
?>