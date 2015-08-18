<?php

require_once('flyValidate.php');

class flyRSS{

var $encoding ="";
var $title ="";
var $link ="";
var $description ="";
var $language ="";
var $copyright ="";
var $webmaster ="";
var $generator ="flyRSS, fastcomet php toolkit";
var $skipHours ="";
var $skipDays ="";

var $itemDefaultTitle = "No title";

var $aItems =array();


	/**
	 * Class constructor
	 */
	function flyRSS($title =''){
		if(!empty($title)) $this->title = $title;
		
		if(empty($this->title)) $this->title =$_SERVER['SERVER_NAME'];
		if(empty($this->link)) $this->link ="http://".$_SERVER['SERVER_NAME']."/";
	}
	
	
	/**
	 * Add rss item.
	 * Elements: title, link, guid, date, description
	 * date - PHP time, or RFC date string
	 * @mixed $aElements
	 */
	function addItem($aElements){
		if(!is_array($aElements)) $aElements = array('description'=>$aElements);
		if(empty($aElements['title']))
			$aElements['title'] = $this->itemDefaultTitle;
		if(empty($aElements['link']))
			$aElements['link'] = "http://".$_SERVER['SERVER_NAME']."/";
		if(empty($aElements['guid']))	
			$aElements['guid'] = $aElements['link'];
		if(empty($aElements['description']))
			$aElements['description'] = "";
		if(empty($aElements['date']))
			$aElements['date'] = time();
		if(is_int($aElements['date']))
			$aElements['date'] = date('r', $aElements['date']);
			
		$this->aItems[] = $aElements;	
	}
	
	
	function get(){
		$aItems = $this->aItems;
		foreach($aItems as $k=>$item){
			$item['title'] = strip_tags($item['title']);
			$item['description'] = htmlentities($item['description']);
		}
		$xml  = '<?xml version="1.0"'; 
		$xml .= (empty($this->encoding)) ? "?>" : ' encoding="'.$this->encoding.'"?>';
		$xml .= "\r\n";
		$xml .= '<rss version="2.0">' ."\r\n";
		$xml .= '<channel>' ."\r\n";
		$xml .= '<title>'.$this->title.'</title>' ."\r\n";
		$xml .= '<link>'.$this->link.'</link>' ."\r\n";
		$xml .= '<description>'.$this->description.'</description>' ."\r\n";
		if(!empty($this->language))
			$xml .= '<language>'.$this->language.'</language>' ."\r\n";
		if(!empty($this->copyright))
			$xml .= '<copyright>'.$this->copyright.'</copyright>' ."\r\n";
		if(!empty($this->webmaster))
			$xml .= '<WebMaster>'.$this->webmaster.'</WebMaster>' ."\r\n";
		if(!empty($this->generator))
			$xml .= '<generator>'.$this->generator.'</generator>' ."\r\n";
		if(!empty($this->skipHours))
			$xml .= '<skipHours>'.$this->skipHours.'</skipHours>' ."\r\n";	
		if(!empty($this->skipDays))
			$xml .= '<skipDays>'.$this->skipDays.'</skipDays>' ."\r\n";
		$xml .= '<lastBuildDate>'.date('r').'</lastBuildDate>' ."\r\n";	
		foreach($aItems as $k=>$item){
			$xml .= "<item>" ."\r\n";
			$xml .= "<title>$item[title]</title>" ."\r\n";
			$xml .= "<link>$item[link]</link>" ."\r\n";
			$xml .= "<guid>$item[guid]</guid>" ."\r\n";
			$xml .= "<pubDate>$item[date]</pubDate>" ."\r\n";
			$xml .= "<description><![CDATA[" ."\r\n";
			$xml .= $item['description'] ."]]>\r\n";
			$xml .= "</description>" ."\r\n";
			$xml .= "</item>" ."\r\n";
		}
		$xml .= '</channel>' ."\r\n";
		$xml .= '</rss>';
		
		return $xml;
	}

}

?>