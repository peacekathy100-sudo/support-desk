<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KnowledgeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.client');
    }

    /**
     * Display knowledge base
     */
    public function index(Request $request): View
    {
        $categories = KnowledgeCategory::where('is_active', true)->orderBy('order')->get();
        $query = KnowledgeArticle::query();

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%")
                  ->orWhere('search_tags', 'like', "%{$request->search}%");
            });
        }

        $articles = $query->orderByDesc('is_featured')
                          ->orderByDesc('created_at')
                          ->paginate(12);

        return view('client.knowledge.index', [
            'articles' => $articles,
            'categories' => $categories,
            'selectedCategory' => $request->category_id,
            'searchTerm' => $request->search,
        ]);
    }

    /**
     * Display article details
     */
    public function show(KnowledgeArticle $article): View
    {
        $article->incrementViews();
        
        $relatedArticles = KnowledgeArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->limit(3)
            ->get();

        return view('client.knowledge.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
