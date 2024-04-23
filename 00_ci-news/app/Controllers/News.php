<?php 

namespace App\Controllers;

use App\Models\NewsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class News extends BaseController {
    public function index() {
        $model = model(NewsModel::class);

        $data = [
            'news' => $model->getNews(),
            'title' => 'News archive'
        ];

        echo view('templates/header', $data);
        echo view('news/index');
        echo view('templates/footer');
    }

    public function show($slug = null) {
        $model = model(NewsModel::class);

        $data['news'] = $model->getNews($slug);

        if (empty($data['news'])) {
            throw new PageNotFoundException('Cannot find the news item' . $slug);
        }

        $data['title'] = $data['news']['title'];

        echo view('templates/header', $data);
        echo view('news/view');
        echo view('templates/footer');

    }

    public function new() {
        helper('form');

        echo view('templates/header', ['title' => 'Create a news item']);
        echo view('news/create');
        echo view('templates/footer');
    }

    public function create() {
        helper('form');

        $data = $this->request->getPost(['title', 'body']);

        if (! $this->validateData($data, [
            'title' => 'required|max_length[255]|min_length[3]',
            'body' => 'required|max_length[5000]|min_length[10]'
        ])) {

            return $this->new();
            
        }
        $post = $this->validator->getValidated();

        $model = model(NewsModel::class);

        $model->save([
            'title' => $post['title'],
            'slug' => url_title($post['title'], '-', true),
            'body' => $post['body']
        ]);

        echo view('templates/header', ['title' => 'Create a news item']);
        echo view('news/success');
        echo view('templates/footer');
    }
}