<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DailyPageviews;
use App\Models\Post;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        if (Auth::user()->group_id == 5) return redirect('/')->with('warning', 'You are not allowed to access the page.');
        $this->data["page_title"] = "Dashboard - " . get_option('ak_app_title');
        $this->data["todays_posts"] = Post::where('status_id', '!=', 4)->where('published_at', 'like', date('Y-m-d') . '%')->count();
        $this->data["total_posts"] = Post::where('status_id', '!=', 4)->count();
        $this->data["total_views"] = DailyPageviews::isTodays()->select(['total'])->first();
        $sql_posts = "select a.id, a.slug, a.title, a.total_views as views, b.name as category_name, b.slug as category_slug,
        (select sum(total_views) from posts where status_id != 4) as total_views
        from posts a
        left join categories b on (a.category_id = b.id)
        where a.status_id != 4
        order by a.total_views desc limit 10";
        $this->data["top_stories"] = DB::select($sql_posts);
        $sql_authors = "select a.id, a.first_name, a.last_name, a.display_name, a.thumbnail,
        (select sum(total_views) from posts where id in (select post_id from post_authors where author_id = a.id)) as views,
        (select sum(total_views) from posts where status_id != 4) as total_views
        from users a
        order by views desc limit 10";
        $page_views = DailyPageviews::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])->get();

        $this->data["graph"] = $this->clean_page_views($page_views, NULL, NULL);
        $this->data["top_authors"] = DB::select($sql_authors);
        return view('backend.pages.homepage.index', $this->data);
    }



    private function clean_page_views($data, $start_date = NULL, $end_date = NULL)
    {
        $day_one = $start_date ?? date('Y-m-01');
        $start_date = new DateTime($start_date ?? date('Y-m-01'));
        $end_date = new DateTime($end_date ?? date('Y-m-t'));

        $days = $end_date->diff($start_date)->format("%a");
        $days_array = [];
        $data_array = [];

        for ($i = 0; $i <= $days; $i++) {
            $views = 0;

            $currentDay = date("Y-m-d", strtotime($day_one . " + " . $i . " day "));
            foreach ($data as $d) {
                if ($d->date == $currentDay) {
                    $views = (int) $d->total;
                    break;
                }
            }

            array_push($days_array, date('d', strtotime($currentDay)));
            array_push($data_array, $views);
        }

        return json_encode(["days" => $days_array, "data" => $data_array]);
    }
}