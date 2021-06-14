<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Image;
use App\Models\Tag;

use Illuminate\Support\Facades\Validator;

class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Blog::with('tags')->with('user')->with('image')->orderBy('id', 'ASC')->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        Validator::make($request->all(), [
            'user' => 'required',
            'image_alt' => 'required',
            'title' => 'required|unique:blogs,title',
            'tags' => 'required',
            'content' => 'required',
            'image' => 'required',
        ])->validate();
        
        $blog = Blog::create(
            [
                'author' => $request->input('user'),
                'title' => $request->input('title'),
                'content' => $request->input('content')
            ]
        );


        $imagePath = $request->file('image')->store('blog_images');

        Image::create(
            [
                'path' => $imagePath,
                'alt' => $request->input('image_alt'),
                'blog_id' => $blog->id,
            ]
        );

        $tags = explode(',', $request->input('tags'));
        
        foreach ($tags as $tag) {
            Tag::create([
                'tag' => $tag,
                'blog_id' => $blog->id
            ]);
        }

        return $blog;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Blog::with('tags')->with('user')->with('image')->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'user' => 'required',
            'image_alt' => 'required',
            'title' => 'required',
            'tags' => 'required',
            'content' => 'required',
        ])->validate();

        $blog = Blog::find($id);

        $blog->author = $request->input('user');
        $blog->title = $request->input('title');
        $blog->content = $request->input('content');

        $blog->save();

        // $imagePath = $request->file('image')->store('blog_images');

        // Image::find('blog_id', $id);
        
        // (
        //     [
        //         'path' => $imagePath,
        //         'alt' => $request->input('image_alt'),
        //         'blog_id' => $blog->id,
        //     ]
        // );

        // $tags = explode(',', $request->input('tags'));
        
        // foreach ($tags as $tag) {
        //     Tag::findOrCreate([
        //         'tag' => $tag,
        //         'blog_id' => $blog->id
        //     ]);
        // }

        return $blog;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Image::where('blog_id', $id)->delete();
        Tag::where('blog_id', $id)->delete();
        return Blog::find($id)->delete();
    }
}
