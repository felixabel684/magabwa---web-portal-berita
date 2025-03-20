<?php

namespace App\Http\Controllers;

use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAdvertisement;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $articles = ArticleNews::with('category')
            ->where('is_featured', 'not_featured') // mengambil yang is_featured = not_featured
            ->latest() // mengambil yang paling terakhir di upload (terbaru)
            ->take(3) // hanya mengambil 3 item saja
            ->get();

        $featured_articles = ArticleNews::with('category')
            ->where('is_featured', 'featured')
            ->inRandomOrder() // mengambil data secara random
            ->take(3)
            ->get();

        $authors = Author::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        $featured_articles = ArticleNews::whereHas('category')
            ->where('is_featured', 'featured')
            ->inRandomOrder() // mengambil data secara random
            ->take(3)
            ->get();

        $entertainment_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertainment');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(3)
            ->get();

        $entertainment_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertainment');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        $business_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        $business_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        $automotive_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
            ->where('is_featured', 'not_featured')
            ->latest()
            ->take(6)
            ->get();

        $automotive_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
            ->where('is_featured', 'featured')
            ->inRandomOrder()
            ->first();

        return view('front.index', compact(
            'categories',
            'authors',
            'articles',
            'featured_articles',
            'bannerads',
            'automotive_articles',
            'automotive_featured_articles',
            'business_articles',
            'business_featured_articles',
            'entertainment_articles',
            'entertainment_featured_articles'
        ));
    }

    public function category (Category $category) {
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        return view ('front.category', compact('categories', 'category', 'bannerads'));
    }

    public function author (Author $author) {
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        return view('front.author', compact('categories', 'author', 'bannerads'));
    }

    public function search(Request $request) {
        $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
        ]);

        $categories = Category::all();

        $keyword = $request->keyword;

        $articles = ArticleNews::with(['category', 'author'])
        ->where('name', 'like', '%' . $keyword . '%')->paginate(6);

        return view('front.search', compact('articles', 'keyword', 'categories'));
    }

    public function details(ArticleNews $articleNews) {
        $categories = Category::all();

        $articles = ArticleNews::with('category', 'author')
            ->where('is_featured', 'not_featured') 
            ->where('id', '!=', $articleNews->id)
            ->latest() 
            ->take(3) 
            ->get();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'banner')
            ->inRandomOrder()
            ->first();

        $squareads = BannerAdvertisement::where('is_active', 'active')
            ->where('type', 'square')
            ->inRandomOrder()
            ->take(2)
            ->get();

        if($squareads->count() < 2) {
            $squareads_1 = $squareads->first();
            $squareads_2 = $squareads->first();
        } else {
            $squareads_1 = $squareads->fget(0);
            $squareads_2 = $squareads->fget(1);
        }

        $author_news = ArticleNews::where('author_id', $articleNews->author_id)
        ->where('id', '!=', $articleNews->id)
        ->inRandomOrder()
        ->get();

        return view('front.details', compact('squareads_1', 'squareads_2' ,'articleNews', 'categories', 'bannerads', 'articles', 'author_news'));
    }
}
