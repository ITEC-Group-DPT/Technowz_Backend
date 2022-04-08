<?php
include '../api/apiheader.php';
include '../classes/User.php';

$user = new User($conn);
$header = getallheaders();
if (isset($_GET['command']))
{
    if ($_GET['command'] == 'getLeaderboardData')
    {
        if (isset($_GET['each']))
        {
            $limit = isset($_GET['limit']) ? $_GET['limit'] : null;
            $res = $user->getLeaderBoardData($limit,$_GET['each']);
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("An error has occcured");
            }
        }
    }
    else if ($_GET['command'] == 'getVisitedUsers')
    {
        if (isset($_GET['each']))
        {
            $res = $user->getVisitedUsers($_GET['each']);
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("An error has occcured");
            }
        }
    }
    else if ($_GET['command'] == 'getActiveUsers')
    {
        if (isset($_GET['each']))
        {
            $res = $user->getActiveUsers($_GET['each']);
            if ($res['isSuccess'])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("An error has occcured");
            }
        }
    }
    else if ($_GET['command'] = 'getChartsData')
    {
        if (isset($_GET['each']))
        {
            $res = $user->getChartsData($_GET['each']);
            if ($res["isSuccess"])
            {
                successApi($res['data']);
            }
            else
            {
                failApi("An error has occcured");
            }
        }
    }
}
