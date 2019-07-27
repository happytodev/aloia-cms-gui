<?php

namespace FlatFileCms\GUI\Controllers;

use FlatFileCms\GUI\Requests\CreatePageRequest;
use FlatFileCms\GUI\Requests\UpdatePageRequest;
use FlatFileCms\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\View\View as ViewResponse;

class PagesController extends Controller
{

    /**
     * Show all pages
     *
     * @return ViewResponse
     */
    public function index(): ViewResponse
    {
        $this->setTitle("Manage pages");

        return View::make('flatfilecmsgui::pages.index', [
            'articles' => Page::all()
                ->map(function (Page $page) {
                    return [
                        'title' => $page->title(),
                        'image' => $page->thumbnail(),
                        'slug' => $page->slug(),
                        'isPublished' => $page->isPublished(),
                        'isHomepage' => $page->isHomepage(),
                    ];
                })
                ->sortByDesc('title')
                ->values()
        ]);
    }

    /**
     * Create a new article
     *
     * @return ViewResponse
     */
    public function create(): ViewResponse
    {
        $this->setTitle("Create a new page");

        $request = Request::capture();

        return View::make('flatfilecmsgui::pages.create', [
            'template_name' => 'flatfilecmsgui::templates.default',
            'file_type' => $request->has('file_type') ? $request->get('file_type') : 'html',
        ]);
    }

    /**
     * Save the article to file
     *
     * @param CreatePageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreatePageRequest $request)
    {
        $request->save();

        return Redirect::route('pages.index')
            ->with('create_article', true);
    }

    /**
     * Edit the article for the given slug
     *
     * @param string $slug
     * @return ViewResponse
     */
    public function edit(string $slug): ViewResponse
    {
        $this->setTitle("Edit a page");

        $page = Page::forSlug($slug);

        return View::make('flatfilecmsgui::pages.edit', [
            'page_resource' => $page,
            'file_type' => pathinfo($page->filename(), PATHINFO_EXTENSION)
        ]);
    }

    /**
     * Save the changes to the article to files
     *
     * @param UpdatePageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePageRequest $request)
    {
        $request->save();

        return Redirect::route('pages.index')
            ->with('updated_article', true);
    }

}