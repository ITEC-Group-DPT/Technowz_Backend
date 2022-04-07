<?php
include '../api/apiheader.php';
include '../classes/User.php';

$user = new User($conn);
$header = getallheaders();
if (isset($_GET['command']))
{
    if ($_GET['command'] == 'getLeaderboardData')
    {
        $limit = isset($_GET['limit']) ? $_GET['limit'] : null;

        $res = $user->getLeaderBoardData($limit);
        if ($res['isSuccess'])
        {
            successApi($res['data']);
        }
        else
        {
            failApi("isLoading");
        }
    }
    else if ($_GET['command'] == 'getVisitedUsers')
    {
        if ($_GET['each'] == 'year')
        {
            $res = $user->getVisitedUsers('year');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'week')
        {
            $res = $user->getVisitedUsers('week');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'month')
        {
            $res = $user->getVisitedUsers('month');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'day')
        {
            $res = $user->getVisitedUsers('day');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
    }
    else if ($_GET['command'] == 'getActiveUsers')
    {
        if ($_GET['each'] == 'year')
        {
            $res = $user->getActiveUsers('year');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'week')
        {
            $res = $user->getActiveUsers('week');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'month')
        {
            $res = $user->getActiveUsers('month');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'day')
        {
            $res = $user->getActiveUsers('day');
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
    }
    else if ($_GET['command'] = 'getChartsData')
    {
        if ($_GET['each'] == 'year')
        {
            $res = $user->getChartsData('year');
            if ($res["isSuccess"])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'week')
        {
            $res = $user->getChartsData('week');
            if ($res["isSuccess"])
            {
                successApi(json_encode($res['data']));
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'month')
        {
            $res = $user->getChartsData('month');
            if ($res["isSuccess"])
            {
                successApi(json_encode($res['data']));
            }
            else
            {
                failApi("isLoading");
            }
        }
        else if ($_GET['each'] == 'day')
        {
            $res = $user->getChartsData('day');
            if ($res["isSuccess"])
            {
                successApi(json_encode($res['data']));
            }
            else
            {
                failApi("isLoading");
            }
        }
    }
}
