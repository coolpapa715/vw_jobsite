<?php
/**
 *  Yurii- Yuskove
 
 */

namespace App\Http\Controllers\Admin;
use App\Models\Chatter_Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Larapen\Admin\app\Http\Controllers\PanelController;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Cache;
use Redirect;

class CommunityController extends PanelController
{
    public function index()
    {
        $category_list = Chatter_Category::all();

        return view(
            'vendor.admin.pages.community.list',
            compact('category_list')
        );
    }

    public function create_category()
    {
        return view('vendor.admin.pages.community.create_category');
    }

    // edit
    public function edit_category(Request $request)
    {
        $category = Chatter_Category::where('id', $request->id)->first();

        return view(
            'vendor.admin.pages.community.edit_category',
            compact('category')
        );
    }
    //insert new
    public function store_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|numeric|min:1',
            'name' => 'required|string|max:40',
            'slug' => 'required|string|max:50',
            'color' => 'required',
        ]);

        if ($validator->passes()) {
            // If validation is empty inserting the post
            $values = [
                'order' => $request->order,
                'name' => $request->name,
                'slug' => $request->slug,
                'color' => $request->color,
            ];
            Chatter_Category::create($values);

            return redirect('/admin/community/')->with(
                'success',
                'New chatter category type added successfully.'
            );
        }

        return Redirect::back()->withErrors($validator->errors()->all());
    }

    public function update_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|numeric|min:1',
            'name' => 'required|string|max:40',
            'slug' => 'required|string|max:50',
            'color' => 'required',
        ]);

        if ($validator->passes()) {
            Chatter_Category::where('id', $request->id)->update([
                'order' => $request->order,
                'name' => $request->name,
                'slug' => $request->slug,
                'color' => $request->color,
            ]);

            return redirect('/admin/community/')->with(
                'success',
                'Chatter category updated successfully.'
            );
        }
        return Redirect::back()->withErrors($validator->errors()->all());
    }

    public function delete_category(Request $request)
    {
        Chatter_Category::where('id', $request->id)->delete();

        return redirect('/admin/community')->with(
            'success',
            'Chatter Category deleted successfully.'
        );
    }
}
