<?php

namespace App\Http\Controllers\Web\Communities;

use Auth;
use Carbon\Carbon;
use App\Events\ChatterAfterNewResponse;
use App\Events\ChatterBeforeNewResponse;

use App\Models\Chatter_Models;
use App\Models\Chatter_Category;
use App\Models\Chatter_Post;

use App\Mail\ChatterDiscussionUpdated;

use Illuminate\Http\Request;
use App\Http\Controllers\Web\FrontController;
use Illuminate\Support\Facades\Mail;

use Validator;
use Event;

class ChatterPostController extends FrontController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $total = 10;
        $offset = 0;
        if ($request->total) {
            $total = $request->total;
        }
        if ($request->offset) {
            $offset = $request->offset;
        }
        $posts = Chatter_Models::post()
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->take($total)
            ->offset($offset)
            ->get();

        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $stripped_tags_body = ['body' => strip_tags($request->body)];
        $validator = Validator::make($stripped_tags_body, [
            'body' => 'required|min:10',
        ]);

        // Event::fire(new ChatterBeforeNewResponse($request, $validator));
        // if (function_exists('chatter_before_new_response')) {
        //     chatter_before_new_response($request, $validator);
        // }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if (config('chatter.security.limit_time_between_posts')) {
            if ($this->notEnoughTimeBetweenPosts()) {
                $minute_copy =
                    config('chatter.security.time_between_posts') == 1
                        ? ' minute'
                        : ' minutes';
                $chatter_alert = [
                    'chatter_alert_type' => 'danger',
                    'chatter_alert' =>
                        'In order to prevent spam, please allow at least ' .
                        config('chatter.security.time_between_posts') .
                        $minute_copy .
                        ' in between submitting content.',
                ];

                return back()
                    ->with($chatter_alert)
                    ->withInput();
            }
        }

        $request->request->add(['user_id' => Auth::user()->id]);

        if (config('chatter.editor') == 'simplemde'):
            $request->request->add(['markdown' => 1]);
        endif;

        $new_post = Chatter_Models::post()->create($request->all());

        $discussion = Chatter_Models::discussion()->find(
            $request->chatter_discussion_id
        );

        $category = Chatter_Models::category()->find(
            $discussion->chatter_category_id
        );
        if (!isset($category->slug)) {
            $category = Chatter_Models::category()->first();
        }

        if ($new_post->id) {
            // Event::fire(new ChatterAfterNewResponse($request));
            // if (function_exists('chatter_after_new_response')) {
            //     chatter_after_new_response($request);
            // }

            // if email notifications are enabled
            if (config('chatter.email.enabled')) {
                // Send email notifications about new post
                $this->sendEmailNotifications($new_post->discussion);
            }

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert' =>
                    'Response successfully submitted to ' .
                    config('chatter.titles.discussion') .
                    '.',
            ];

            return redirect(
                '/' .
                    config('chatter.routes.home') .
                    '/' .
                    config('chatter.routes.discussion') .
                    '/' .
                    $category->slug .
                    '/' .
                    $discussion->slug
            )->with($chatter_alert);
        } else {
            $chatter_alert = [
                'chatter_alert_type' => 'danger',
                'chatter_alert' =>
                    'Sorry, there seems to have been a problem submitting your response.',
            ];

            return redirect(
                '/' .
                    config('chatter.routes.home') .
                    '/' .
                    config('chatter.routes.discussion') .
                    '/' .
                    $category->slug .
                    '/' .
                    $discussion->slug
            )->with($chatter_alert);
        }
    }

    private function notEnoughTimeBetweenPosts()
    {
        $user = Auth::user();

        $past = Carbon::now()->subMinutes(
            config('chatter.security.time_between_posts')
        );

        $last_post = Chatter_Models::post()
            ->where('user_id', '=', $user->id)
            ->where('created_at', '>=', $past)
            ->first();

        if (isset($last_post)) {
            return true;
        }

        return false;
    }

    private function sendEmailNotifications($discussion)
    {
        $users = $discussion->users->except(Auth::user()->id);
        foreach ($users as $user) {
            Mail::to($user)->queue(new ChatterDiscussionUpdated($discussion));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $stripped_tags_body = ['body' => strip_tags($request->body)];
        $validator = Validator::make($stripped_tags_body, [
            'body' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $post = Chatter_Models::post()->find($id);
        if (!Auth::guest() && Auth::user()->id == $post->user_id) {
            $post->body = strip_tags($request->body);
            $post->save();

            $discussion = Chatter_Models::discussion()->find(
                $post->chatter_discussion_id
            );

            $category = Chatter_Models::category()->find(
                $discussion->chatter_category_id
            );
            if (!isset($category->slug)) {
                $category = Chatter_Models::category()->first();
            }

            $chatter_alert = [
                'chatter_alert_type' => 'success',
                'chatter_alert' =>
                    'Successfully updated the ' .
                    config('chatter.titles.discussion') .
                    '.',
            ];

            return redirect(
                '/' .
                    config('chatter.routes.home') .
                    '/' .
                    config('chatter.routes.discussion') .
                    '/' .
                    $category->slug .
                    '/' .
                    $discussion->slug
            )->with($chatter_alert);
        } else {
            $chatter_alert = [
                'chatter_alert_type' => 'danger',
                'chatter_alert' =>
                    'Nah ah ah... Could not update your response. Make sure you\'re not doing anything shady.',
            ];

            return redirect('/' . config('chatter.routes.home'))->with(
                $chatter_alert
            );
        }
    }

    /**
     * Delete post.
     *
     * @param string $id
     * @param  \Illuminate\Http\Request
     *
     * @return \Illuminate\Routing\Redirect
     */
    public function destroy($id, Request $request)
    {
        $post = Chatter_Models::post()
            ->with('discussion')
            ->findOrFail($id);

        if ($request->user()->id !== (int) $post->user_id) {
            return redirect('/' . config('chatter.routes.home'))->with([
                'chatter_alert_type' => 'danger',
                'chatter_alert' =>
                    'Nah ah ah... Could not delete the response. Make sure you\'re not doing anything shady.',
            ]);
        }

        if (
            $post->discussion
                ->posts()
                ->oldest()
                ->first()->id === $post->id
        ) {
            $post->discussion->posts()->delete();
            $post->discussion()->delete();

            return redirect('/' . config('chatter.routes.home'))->with([
                'chatter_alert_type' => 'success',
                'chatter_alert' =>
                    'Successfully deleted the response and ' .
                    strtolower(config('chatter.titles.discussion')) .
                    '.',
            ]);
        }

        $post->delete();

        $url =
            '/' .
            config('chatter.routes.home') .
            '/' .
            config('chatter.routes.discussion') .
            '/' .
            $post->discussion->category->slug .
            '/' .
            $post->discussion->slug;

        return redirect($url)->with([
            'chatter_alert_type' => 'success',
            'chatter_alert' =>
                'Successfully deleted the response from the ' .
                config('chatter.titles.discussion') .
                '.',
        ]);
    }

    /**
     * emoji post.
     *
     * @param string $id
     * @param  \Illuminate\Http\Request
     *
     * @return \Illuminate\Routing\Redirect
     */
    public function post_emoji($id, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(
                ['type' => 'success', 'msg' => 'You have to login'],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        $validator = Validator::make($request->all(), [
            'post_id' => 'required|numeric',
            'body_content' => 'required|min:10',
            'chatter_category_id' => 'required',
        ]);
        $body = html_entity_decode($request->my_body);

        // Event::fire(new ChatterBeforeNewDiscussion($request, $validator));
        // if (function_exists('chatter_before_new_discussion')) {
        //     chatter_before_new_discussion($request, $validator);
        // }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $user_id = Auth::user()->id;

        $post = Chatter_Post::with('discussion')->findOrFail($id);

        if ($request->user()->id !== (int) $post->user_id) {
            return redirect('/' . config('chatter.routes.home'))->with([
                'chatter_alert_type' => 'danger',
                'chatter_alert' =>
                    'Nah ah ah... Could not delete the response. Make sure you\'re not doing anything shady.',
            ]);
        }

        if (
            $post->discussion
                ->posts()
                ->oldest()
                ->first()->id === $post->id
        ) {
            $post->discussion->posts()->delete();
            $post->discussion()->delete();

            return redirect('/' . config('chatter.routes.home'))->with([
                'chatter_alert_type' => 'success',
                'chatter_alert' =>
                    'Successfully deleted the response and ' .
                    strtolower(config('chatter.titles.discussion')) .
                    '.',
            ]);
        }

        $post->delete();

        $url =
            '/' .
            config('chatter.routes.home') .
            '/' .
            config('chatter.routes.discussion') .
            '/' .
            $post->discussion->category->slug .
            '/' .
            $post->discussion->slug;

        return redirect($url)->with([
            'chatter_alert_type' => 'success',
            'chatter_alert' =>
                'Successfully deleted the response from the ' .
                config('chatter.titles.discussion') .
                '.',
        ]);
    }
}
