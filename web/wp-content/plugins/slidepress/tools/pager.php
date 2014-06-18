<?php

class Pager
{
	
	
	function Pager()
	{
		$this->link = $_SERVER['PHP_SELF']."?page=ssp_show_admin_managegallery";
	}
	
function findStart($limit) {
	if ((!isset($_GET['pagenum'])) || ($_GET['pagenum'] == "1")) {
    	$start = 0;
    	$_GET['page'] = 1;
    } else {
       	$start = ($_GET['pagenum']-1) * $limit;
    }
	return $start;
}

  /*
   * int findPages (int count, int limit)
   * Returns the number of pages needed based on a count and a limit
   */
function findPages($count, $limit) {
     $pages = (($count % $limit) == 0) ? $count / $limit : floor($count / $limit) + 1; 

     return $pages;
} 

/*
* string pageList (int curpage, int pages)
* Returns a list of pages in the format of "« < [pages] > »"
**/
function pageList($curpage, $pages, $count, $limit, $start)
{
	$lastpage = $start + $limit;
	$firstpage = $start + 1;
	$page_list  = "<div class='tablenav-pages'>";
	$page_list .= "<span class='displaying-num'>";
	$page_list .= "Displaying " . $firstpage ."-" . $lastpage ." of <span class='total-type-count'>" . $count . "</span>";
	$page_list .= "</span>";
	
	# get curpage if url var isn't set
	if($curpage <= 1)
	{ 
		$curpage = 1;
	}
	
	#Previous Link
    if (($curpage-1) > 0) {
       $page_list .= "<a class='prev page-numbers' href=\"".$this->link."&pagenum=".($curpage-1)."\" title=\"Previous Page\">&laquo;</a> ";
    } else {
    //	$page_list .= "<a href=\"".$this->link."&pagenum=".($curpage-1)."\" onClick=\"return false\" class=\"disabled\" title=\"Previous Page\">Previous</a> ";  
    }

	

    /* Print the numeric page list; make the current page unlinked and bold */
    for ($i=1; $i<=$pages; $i++) {
    	if ($i == $curpage) {
         	$page_list .= "<span class='page-numbers current'>" . $i . "</span>"; 
        } else {
         	$page_list .= "<a class='page-numbers' href=\"".$this->link."&pagenum=".$i."\" title=\"Page ".$i."\">".$i."</a>";
        }
       	$page_list .= " ";
      } 

  # Next link
  if (($curpage+1) <= $pages) {
       	$page_list .= "<a class='next page-numbers' href=\"".$this->link."&pagenum=".($curpage+1)."\" title=\"Next Page\">&raquo;</a>";
     }else{
     //	$page_list .= "<a href=\"".$this->link."&pagenum=".($curpage+1)."\" onClick=\"return false\" class=\"disabled\" title=\"Next Page\">Next</a> ";
     }

  # Close page list
     $page_list .= "</div>\n"; 

     return $page_list;
}

/*
* string nextPrev (int curpage, int pages)
* Returns "Previous | Next" string for individual pagination (it's a word!)
*/
function nextPrev($curpage, $pages) {
 $next_prev  = ""; 

	if (($curpage-1) <= 0) {
   		$next_prev .= "Previous";
	} else {
   		$next_prev .= "<a href=\"".$this->link."&pagenum=".($curpage-1)."\">Previous</a>";
	} 

 		$next_prev .= " | "; 

 	if (($curpage+1) > $pages) {
   		$next_prev .= "Next";
    } else {
       	$next_prev .= "<a href=\"".$this->link."&pagenum=".($curpage+1)."\">Next</a>";
    }
     	return $next_prev;
    }
}