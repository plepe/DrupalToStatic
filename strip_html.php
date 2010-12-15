<?
function process_dom($n) {
  global $strip_ids;
  global $strip_classes;

  if(($n->hasAttributes())&&
     (in_array($n->getAttribute("id"), $strip_ids)||
      in_array($n->getAttribute("class"), $strip_classes))) {
    $n->parentNode->removeChild($n);
    return;
  }

  $c=$n->firstChild;
  while($c) {
    $c_next=$c->nextSibling;

    process_dom($c);

    $c=$c_next;
  }

  return;
}

function process_html($file) {
  global $source_path;
  global $strip_path;

  print "Processing $file\n";
  $dom=new DOMDocument();
  if(!$dom->loadHTML(file_get_contents("$source_path/$file")))
    return;

  process_dom($dom);

  file_put_contents("$strip_path/$file", $dom->saveHTML());
}

function process_dir($src) {
  global $source_path;
  global $strip_path;

  $d=opendir("$source_path/$src");
  while($f=readdir($d)) {
    if(($f==".")||($f=="..")) {
    }
    elseif(is_dir("$source_path/$src/$f")) {
      if(!is_dir("$strip_path/$src/$f"))
        mkdir("$strip_path/$src/$f");

      process_dir("$src/$f");
    }
    elseif(preg_match("/\.html$/", $f)) {
      process_html("$src/$f");
    }
    else {
      copy("$source_path/$src/$f", "$strip_path/$src/$f");
    }
  }
  closedir($d);
}

process_dir("");
