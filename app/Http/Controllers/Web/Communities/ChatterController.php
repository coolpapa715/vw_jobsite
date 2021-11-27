<?php

namespace App\Http\Controllers\Web\Communities;

use Auth;
use App\Helpers\ArrayHelper;
use App\Helpers\UrlGen;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Chatter_Models;
use App\Models\Chatter_Discussion;
use App\Models\Chatter_Category;
use App\Models\Chatter_Vote;

use App\Http\Controllers\Web\FrontController;
use Torann\LaravelMetaTags\Facades\MetaTag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class ChatterController extends FrontController
{
    public function index(Request $request, $slug = '')
    {
        $pagination_results = config('chatter.paginate.num_of_results');
        $filter = $request->query('filter');

        $discussions = Chatter_Discussion::with([
            'user',
            'post',
            'postsCount',
            'votesCount',
            'votes',
            'lastpost.user',
            'category',
        ])
            ->sortable()
            ->where('chatter_discussion.title', 'like', '%' . $filter . '%')
            ->paginate($pagination_results);

        $category_id = 0;
        if (isset($slug)) {
            $category = Chatter_Category::where('slug', '=', $slug)->first();
            if (isset($category->id)) {
                $discussions = Chatter_Discussion::with([
                    'user',
                    'post',
                    'postsCount',
                    'votesCount',
                    'lastpost.user',
                    'category',
                ])
                    ->where('chatter_category_id', '=', $category->id)
                    ->sortable()
                    ->where(
                        'chatter_discussion.title',
                        'like',
                        '%' . $filter . '%'
                    )
                    ->paginate($pagination_results);
                $category_id = $category->id;
            }
        }

        $categories = Chatter_Category::all();
        $chatter_editor = config('chatter.editor');

        // Dynamically register markdown service provider
        \App::register('GrahamCampbell\Markdown\MarkdownServiceProvider');

        return view(
            'communities.home',
            compact(
                'discussions',
                'categories',
                'chatter_editor',
                'slug',
                'filter',
                'category_id'
            )
        );
    }

    public function vote(Request $request)
    {
        if (!Auth::check()) {
            $chatter_alert = [
                'chatter_alert_type' => 'danger',
                'chatter_alert' => 'You have to Login.',
            ];

            return redirect('/' . config('chatter.routes.home'))
                ->with($chatter_alert)
                ->withInput();
        } else {
            $voter = Chatter_Vote::where('chatter_discussion_id', $request->id)
                ->where('user_id', Auth::user()->id)
                ->first();
            if (empty($voter) && $voter == null) {
                Chatter_Vote::create([
                    'chatter_discussion_id' => $request->id,
                    'user_id' => Auth::user()->id,
                ]);
            } else {
                $chatter_alert = [
                    'chatter_alert_type' => 'danger',
                    'chatter_alert' => 'You have already voted.',
                ];
                return redirect('/' . config('chatter.routes.home'))
                    ->with($chatter_alert)
                    ->withInput();
            }
            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert' => 'Successfully voted.',
            ];
            return redirect('/' . config('chatter.routes.home'))
                ->with($chatter_alert)
                ->withInput();
        }
    }

    public function login()
    {
        if (!Auth::check()) {
            return \Redirect::to(
                '/' .
                    config('chatter.routes.login') .
                    '?redirect=' .
                    config('chatter.routes.home')
            )->with(
                'flash_message',
                'Please create an account before posting.'
            );
        }
    }

    public function register()
    {
        if (!Auth::check()) {
            return \Redirect::to(
                '/' .
                    config('chatter.routes.register') .
                    '?redirect=' .
                    config('chatter.routes.home')
            )->with('flash_message', 'Please register for an account.');
        }
    }
}
