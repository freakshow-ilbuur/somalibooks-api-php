<?php

class Books extends Controller
{
    private $auth;
    public function __construct()
    {
        $this->auth = new Auth;
        $this->bookModel = $this->model('Book');
    }
    public function page($page)
    {
        $book_arr = array();
        $book_arr['records'] = array();
        $book_arr['paging'] = array();
        $limit = pagination($page);

        $row = $this->bookModel->getAllBooks($limit);

        if ($row) {
            for ($i = 0; $i < sizeof($row); $i++) {
                extract($row);
                $book = array(
                    'id' => $row[$i]['id'],
                    'title' => $row[$i]['title'],
                    'author' => array($row[$i]['author']),
                    'category' => array($row[$i]['category']),
                );
                array_push($book_arr['records'], $book);
            }
            $total_rows = $this->bookModel->count();
            $page_url = URLROOT . '/books/page';
            $per_page = $limit['records_per_page'];
            $paging = getPaging($page, $total_rows, $per_page, $page_url);

            $book_arr['paging'] = $paging;
            $this->response($book_arr, true, 200);
        } else {
            $this->response(null, false, 200);
        }

    }
    public function book($id)
    {
        $res = $this->bookModel->getBookById($id);
        if ($res) {
            $this->response($res, true, 200);

        } else {
            $this->response(null, true, 200);
        }

    }

    public function search(...$term)
    {
        $page = $term[1];
        $book_arr = array();
        $book_arr['records'] = array();
        $book_arr['paging'] = array();
        $limit = pagination($page);

        $row = $this->bookModel->getBookBySearch($term[0], $limit);
        if (sizeof($row['result']) > 0) {
            for ($i = 0; $i < sizeof($row['result']); $i++) {
                extract($row);
                $book = array(
                    'id' => $row['result'][$i]['id'],
                    'title' => $row['result'][$i]['title'],
                    'author' => array($row['result'][$i]['author']),
                    'category' => array($row['result'][$i]['category']),
                    'username' => $row['result'][$i]['username'],
                    'date_created' => $row['result'][$i]['date_created'],
                );
                array_push($book_arr['records'], $book);
            }
            $total_rows = $row['count'];
            $page_url = URLROOT . '/books/search/' . $term[0];
            $per_page = $limit['records_per_page'];
            $paging = getPaging($page, $total_rows, $per_page, $page_url);

            $book_arr['paging'] = $paging;
            $this->response($book_arr, true, 200);
        } else {
            $this->response(null, true, 200);
        }
    }
    public function add()
    {
        $filters = array(

            'title' => FILTER_SANITIZE_STRING,

            'uploaded_by' => FILTER_VALIDATE_INT,

        );
        $option = array(
            'title' => array(
                'flag' => FILTER_NULL_ON_FAILURE,
            ),
            'uploaded_by' => array(
                'flag' => FILTER_NULL_ON_FAILURE,
            ),

        );

        $input = json_decode(file_get_contents("php://input"));
        $filtered = array();
        foreach ($input as $key => $value) {
            if ($key !== 'published') {
                $filtered[$key] = filter_var($value, $filters[$key], $option[$key]);
            }

        }
        $filtered['published'] = $input->published;
        $token = $this->auth->authenticate($_COOKIE['refresh_token']);
        if (!isset($token)) {
            $this->response(null, null, 401, null);

        } else {
            if ($this->bookModel->addBook($filtered)) {
                $this->response($token, true, 200, 'Added');
            } else { $this->response(null, false, 200, 'Failed to add');}

        }

    }
}
