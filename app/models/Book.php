<?php



class Book {

private $db; 
    public function __construct(){
        $this->db = new Database;

}
//get all books with pagination.
public function getAllBooks($limit){
    $query = 'SELECT b.id, b.title,u.username,b.date_created,group_concat(distinct a.name)  as author, group_concat(distinct c.name) as category FROM books b LEFT OUTER JOIN bookcategory bc ON bc.book_id = b.id 
    LEFT OUTER JOIN book_author ba ON ba.book_id = b.id LEFT OUTER JOIN author a ON a.id = ba.author_id 
    LEFT OUTER JOIN category c ON c.id = bc.cat_id
    LEFT OUTER JOIN users u ON u.id = b.uploaded_by
    GROUP BY b.id,b.title,u.username,b.date_created
    LIMIT :from_record,:record_per_page';

    $this->db->query($query);
    $this->db->bind(':from_record',$limit['from_record']);
    $this->db->bind(':record_per_page',$limit['records_per_page']);
    $row = $this->db->resultSet();
    return $row;
}
//get count of all books in the database
public function count(){
    $query = 'SELECT  b.id, b.title,u.username,b.date_created,group_concat(distinct a.name)  as author, group_concat(distinct c.name) as category FROM books b LEFT OUTER JOIN bookcategory bc ON bc.book_id = b.id 
    LEFT OUTER JOIN book_author ba ON ba.book_id = b.id LEFT OUTER JOIN author a ON a.id = ba.author_id 
    LEFT OUTER JOIN category c ON c.id = bc.cat_id
    LEFT OUTER JOIN users u ON u.id = b.uploaded_by
    GROUP BY b.id,b.title';

    $this->db->query($query);
    $row = $this->db->resultSet();
    $count = $this->db->rowCount();
    return $count;
}

public function getBookById($id){
    $query = 'SELECT b.id, b.title, a.name as author,c.name as category FROM books b LEFT OUTER JOIN bookcategory bc ON bc.book_id = b.id 
    LEFT OUTER JOIN book_author ba ON ba.book_id = b.id LEFT OUTER JOIN author a ON a.id = ba.author_id 
    LEFT OUTER JOIN category c ON c.id = bc.cat_id
    WHERE b.id = :id';

    $this->db->query($query);
    $this->db->bind(':id',$id);
    $row =$this->db->singleResult();
    return $row;

}

public function getBookBySearch($term,$limit){
    $this->db->query('CALL sp_search(:term,:start,:stop)');
    $this->db->bind(':term',$term);
    $this->db->bind(':start',$limit['from_record']);
    $this->db->bind(':stop',$limit['records_per_page']);
    $row = $this->db->resultSet();
    return array(
        'result'=>$row,
        'count'=>$this->db->rowCount()
    );
}

public function addBook($book){
    $query = 'INSERT INTO books (title,date_published,uploaded_by,url) values (:title,:published,:uploaded_by,:url)';

    $this->db->query($query);
    $this->db->bind(':title',$book['title']);
    $this->db->bind(':published',$book['published']);
    $this->db->bind(':uploaded_by',$book['uploaded_by']);
    $this->db->bind(':url','so');
    return $this->db->execute();
    
}



}