<?php



function pagination($page){
    $records_per_page = 10;
    $from_record = ($records_per_page*$page) - $records_per_page;
    return array(
        'from_record'=>$from_record,
        'records_per_page'=>$records_per_page
    );

}

function getPaging($page,$total_rows,$records_per_page,$page_url){

    $paging_arr = array();
    $paging_arr['first'] = $page>1?  $page_url. '/1':'';


    $total_pages = ceil($total_rows/$records_per_page);


    $range = 2;

    $initial_num = $page-$range;
    $condition_num = ($page + $range) + 1;
    $paging_arr['pages'] = array();
    $page_count = 0;

    for($i=$initial_num; $i< $condition_num; $i++){
        if(($i > 0) && ($i <=$total_pages)){
            $paging_arr['pages'][$page_count]['page'] = $i;
            $paging_arr['pages'][$page_count]['url'] = $page_url.'/'.$i;
            $paging_arr['pages'][$page_count]['current_page'] = $i==$page?true:false;
            $page_count++;

        }
    }

    $paging_arr['last'] = $page< $total_pages?  $page_url. '/'.$total_pages:'';

    return $paging_arr;
}

