<?php 
class NP_GoogleIt extends NucleusPlugin {

  function getName() {
    return 'GoogleIt';
  }
  function getAuthor() {
    return 'nakahara21';
  } 
  function getURL()
  {
    return 'http://xx.nakahara21.net/';
  }
  function getVersion() {
    return '0.51';
  }
  function getDescription() {
    return 'Google it! usage:[[keyword]] or search by wikipedia usage: {{keyword}}';
  }
  function supportsFeature($what) {
    switch($what){
    case 'SqlTablePrefix':
      return 1;
    default:
      return 0;
    }
  }

  function install() {
    $this->createOption('CreateTextG','Googleキーワードの隣りにつけるリンクテキスト(空白の場合はキーワードそのものにリンク)','text','[G]');
    $this->createOption('CreateTextW','Wikipediaキーワードの隣りにつけるリンクテキスト(空白の場合はキーワードそのものにリンク)','text','[W]');
  }

  function getEventList() {
    return array('PreItem');
  }

  function event_PreItem(&$data) {
  	if(!isset($data['item'])) return;
    $this->currentItem = $data['item'];

    // google
    $this->currentItem->body =
      preg_replace_callback("/\[\[(.*)\]\]/Us",
			    array(&$this, 'googleit'),
			    $this->currentItem->body);
    $this->currentItem->more =
      preg_replace_callback("/\[\[(.*)\]\]/Us",
			    array(&$this, 'googleit'),
			    $this->currentItem->more);

    // wikipedia
    $this->currentItem->body =
      preg_replace_callback("/\{\{(.*)\}\}/Us",
			    array(&$this, 'wikipedia'),
			    $this->currentItem->body);
    $this->currentItem->more =
      preg_replace_callback("/\{\{(.*)\}\}/Us",
			    array(&$this, 'wikipedia'),
			    $this->currentItem->more); 
  }

  function googleit($matches){
    $keyword = htmlspecialchars(strip_tags($matches[1]));
    $keyword = preg_replace('/\r\n/s',' ',$keyword);
    $keyword = mb_convert_encoding($keyword, "UTF-8", _CHARSET);
    $keyword = urlencode($keyword);
    if($this->getOption('CreateTextG') == ''){
      $text = '<a href="http://www.google.co.jp/search?ie=UTF-8&oe=UTF-8&q='.$keyword.'" target="_blank" title="Google it!">'.$matches[1].'</a>';
    }else{
      $text = '<span style="border-bottom:1px dotted;">'.$matches[1].'</span><a href="http://www.google.co.jp/search?ie=UTF-8&oe=UTF-8&q='.$keyword.'" target="_blank">'.$this->getOption('CreateTextG').'</a>';
    }
    return $text;
	
  }

  function wikipedia($matches){
    $keyword = htmlspecialchars(strip_tags($matches[1]));
    $keyword = preg_replace('/\r\n/s',' ',$keyword);
    $keyword = mb_convert_encoding($keyword, "UTF-8", _CHARSET);
    $keyword = urlencode($keyword);
    if($this->getOption('CreateTextW') == ''){
      $text = '<a href="http://ja.wikipedia.org/wiki/'.$keyword.'" target="_blank" title="ja.wikipedia.org">'.$matches[1].'</a>';
    }else{
      $text = '<span style="border-bottom:1px dotted;">'.$matches[1].'</span><a href="http://ja.wikipedia.org/wiki/'.$keyword.'" target="_blank">'.$this->getOption('CreateTextW').'</a>';
    }
    return $text;
  }
} 
?>