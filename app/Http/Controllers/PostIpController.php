<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostIpController extends Controller
{
    public function index()
    {
        $result = $this->getDatabaseData();

        $resultArray = collect($result)->groupBy('ip')
            ->values()
            ->map(function ($item) {
                return [
                    'ip' => $item->first()->ip,
                    'users' => $item->pluck('login')->unique(),
                ];
            })
            ->filter(function($item) {
                return !empty($item['users']);
            });

        return $resultArray;
    }

    /**
     * Get
     *
     * @return \Illuminate\Support\Collection
     */
    private function getDatabaseData()
    {
        $result = DB::select(DB::raw('
            SELECT posts.user_ip as ip, users.login as login
            FROM posts
              JOIN users
                ON users.id = posts.user_id
              JOIN (
                SELECT user_ip
                FROM (
                       SELECT user_ip, COUNT(*) as users_ip_count
                       FROM posts
                       GROUP BY user_ip
                 ) as counted_ips
                WHERE users_ip_count > 1
                ) as result_table
              ON result_table.user_ip = posts.user_ip
            ORDER BY posts.user_ip
        '));

        return collect($result);
    }
}
