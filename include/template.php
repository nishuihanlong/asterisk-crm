<?php
/*
 * (C) Copyright 1999-2000 NetUSE GmbH
 *                    Kristian Koehntopp
 *
 * $Id: template.inc,v 1.12 2002/07/11 22:29:51 richardarcher Exp $
 * $Phzzy last modified at 2006/02/21 12:30 $
 * $http://www.phzzy.org
 *
 */

class Template
{
  var $classname = "Template";

  var $debug    = false;

  var $root     = ".";

  var $file     = array();

  var $varkeys  = array();

  var $varvals  = array();

  var $unknowns = "remove";

  var $halt_on_error  = "yes";

  var $last_error     = "";
  
  var $left_delimiter = "{";
  
  var $right_delimiter = "}";
  
  var $caching = FALSE;
  
  var $cache_dir = 'cache';
  
  var $cache_lifetime = 3600;
  
  var $cache_id = '';

  function Template($root = ".", $unknowns = "remove") {
    if ($this->debug & 4) {
      echo "<p><b>Template:</b> root = $root, unknowns = $unknowns</p>\n";
    }
    $this->set_root($root);
    $this->set_unknowns($unknowns);
  }


  function set_root($root) {
    if ($this->debug & 4) {
      echo "<p><b>set_root:</b> root = $root</p>\n";
    }
    if (!is_dir($root)) {
      $this->halt("set_root: $root is not a directory.");
      return false;
    }

    $this->root = $root;
    return true;
  }


  function set_unknowns($unknowns = "remove") {
    if ($this->debug & 4) {
      echo "<p><b>unknowns:</b> unknowns = $unknowns</p>\n";
    }
    $this->unknowns = $unknowns;
  }


 
  function set_file($varname, $filename = "") {
    if (!is_array($varname)) {
      if ($this->debug & 4) {
        echo "<p><b>set_file:</b> (with scalar) varname = $varname, filename = $filename</p>\n";
      }
      if ($filename == "") {
        $this->halt("set_file: For varname $varname filename is empty.");
        return false;
      }
      $this->file[$varname] = $this->filename($filename);
    } else {
      reset($varname);
      while(list($v, $f) = each($varname)) {
        if ($this->debug & 4) {
          echo "<p><b>set_file:</b> (with array) varname = $v, filename = $f</p>\n";
        }
        if ($f == "") {
          $this->halt("set_file: For varname $v filename is empty.");
          return false;
        }
        $this->file[$v] = $this->filename($f);
      }
    }
    return true;
  }


 
  function set_block($parent, $varname, $name = "") {
    if ($this->debug & 4) {
      echo "<p><b>set_block:</b> parent = $parent, varname = $varname, name = $name</p>\n";
    }
    if (!$this->loadfile($parent)) {
      $this->halt("set_block: unable to load $parent.");
      return false;
    }
    if ($name == "") {
      $name = $varname;
    }

    $str = $this->get_var($parent);
    $reg = "/[ \t]*<!--\s+BEGIN $varname\s+-->\s*?\n?(\s*.*?\n?)\s*<!--\s+END $varname\s+-->\s*?\n?/sm";
    preg_match_all($reg, $str, $m);
    $str = preg_replace($reg, $this->left_delimiter . "$name" . $this->right_delimiter, $str);
    $this->set_var($varname, $m[1][0]);
    $this->set_var($parent, $str);
    return true;
  }


  function set_var($varname, $value = "", $append = false) {
    if (!is_array($varname)) {
      if (!empty($varname)) {
        if ($this->debug & 1) {
          printf("<b>set_var:</b> (with scalar) <b>%s</b> = '%s'<br>\n", $varname, htmlentities($value));
        }
        $this->varkeys[$varname] = "/".$this->varname($varname)."/";
        if ($append && isset($this->varvals[$varname])) {
          $this->varvals[$varname] .= $value;
        } else {
          $this->varvals[$varname] = $value;
        }
      }
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        if (!empty($k)) {
          if ($this->debug & 1) {
            printf("<b>set_var:</b> (with array) <b>%s</b> = '%s'<br>\n", $k, htmlentities($v));
          }
          $this->varkeys[$k] = "/".$this->varname($k)."/";
          if ($append && isset($this->varvals[$k])) {
            $this->varvals[$k] .= $v;
          } else {
            $this->varvals[$k] = $v;
          }
        }
      }
    }
  }


 
  function clear_var($varname) {
    if (!is_array($varname)) {
      if (!empty($varname)) {
        if ($this->debug & 1) {
          printf("<b>clear_var:</b> (with scalar) <b>%s</b><br>\n", $varname);
        }
        $this->set_var($varname, "");
      }
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        if (!empty($v)) {
          if ($this->debug & 1) {
            printf("<b>clear_var:</b> (with array) <b>%s</b><br>\n", $v);
          }
          $this->set_var($v, "");
        }
      }
    }
  }


 
  function unset_var($varname) {
    if (!is_array($varname)) {
      if (!empty($varname)) {
        if ($this->debug & 1) {
          printf("<b>unset_var:</b> (with scalar) <b>%s</b><br>\n", $varname);
        }
        unset($this->varkeys[$varname]);
        unset($this->varvals[$varname]);
      }
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        if (!empty($v)) {
          if ($this->debug & 1) {
            printf("<b>unset_var:</b> (with array) <b>%s</b><br>\n", $v);
          }
          unset($this->varkeys[$v]);
          unset($this->varvals[$v]);
        }
      }
    }
  }


  function subst($varname, $append = false) {
    $varvals_quoted = array();
    if ($this->debug & 4) {
      echo "<p><b>subst:</b> varname = $varname</p>\n";
    }
    if (!$this->loadfile($varname)) {
      $this->halt("subst: unable to load $varname.");
      return false;
    }

    // quote the replacement strings to prevent bogus stripping of special chars
    reset($this->varvals);
    while(list($k, $v) = each($this->varvals)) {
      $varvals_quoted[$k] = preg_replace(array('/\\\\/', '/\$/'), array('\\\\\\\\', '\\\\$'), $v);
    }

    $str = $this->get_var($varname);
    $str = preg_replace($this->varkeys, $varvals_quoted, $str);
    return $str;
  }


 
  function psubst($varname) {
    if ($this->debug & 4) {
      echo "<p><b>psubst:</b> varname = $varname</p>\n";
    }
    print $this->subst($varname);

    return false;
  }


 
  function parse($target, $varname, $append = false) {
    if (!is_array($varname)) {
      if ($this->debug & 4) {
        echo "<p><b>parse:</b> (with scalar) target = $target, varname = $varname, append = $append</p>\n";
      }
      $str = $this->subst($varname);
      if ($append) {
        $this->set_var($target, $this->get_var($target) . $str);
      } else {
        $this->set_var($target, $str);
      }
    } else {
      reset($varname);
      while(list($i, $v) = each($varname)) {
        if ($this->debug & 4) {
          echo "<p><b>parse:</b> (with array) target = $target, i = $i, varname = $v, append = $append</p>\n";
        }
        $str = $this->subst($v);
        if ($append) {
          $this->set_var($target, $this->get_var($target) . $str);
        } else {
          $this->set_var($target, $str);
        }
      }
    }

    if ($this->debug & 4) {
      echo "<p><b>parse:</b> completed</p>\n";
    }
    return $str;
  }



  function pparse($target, $varname, $append = false) {
    if ($this->debug & 4) {
      echo "<p><b>pparse:</b> passing parameters to parse...</p>\n";
    }
    print $this->finish($this->parse($target, $varname, $append));
    return false;
  }


  function get_vars() {
    if ($this->debug & 4) {
      echo "<p><b>get_vars:</b> constructing array of vars...</p>\n";
    }
    reset($this->varkeys);
    while(list($k, $v) = each($this->varkeys)) {
      $result[$k] = $this->get_var($k);
    }
    return $result;
  }


  function get_var($varname) {
    if (!is_array($varname)) {
      if (isset($this->varvals[$varname])) {
        $str = $this->varvals[$varname];
      } else {
        $str = "";
      }
      if ($this->debug & 2) {
        printf ("<b>get_var</b> (with scalar) <b>%s</b> = '%s'<br>\n", $varname, htmlentities($str));
      }
      return $str;
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        if (isset($this->varvals[$v])) {
          $str = $this->varvals[$v];
        } else {
          $str = "";
        }
        if ($this->debug & 2) {
          printf ("<b>get_var:</b> (with array) <b>%s</b> = '%s'<br>\n", $v, htmlentities($str));
        }
        $result[$v] = $str;
      }
      return $result;
    }
  }

  function get_undefined($varname) {
    if ($this->debug & 4) {
      echo "<p><b>get_undefined:</b> varname = $varname</p>\n";
    }
    if (!$this->loadfile($varname)) {
      $this->halt("get_undefined: unable to load $varname.");
      return false;
    }

    preg_match_all("/{([^ \t\r\n}]+)}/", $this->get_var($varname), $m);
    $m = $m[1];
    if (!is_array($m)) {
      return false;
    }

    reset($m);
    while(list($k, $v) = each($m)) {
      if (!isset($this->varkeys[$v])) {
        if ($this->debug & 4) {
         echo "<p><b>get_undefined:</b> undefined: $v</p>\n";
        }
        $result[$v] = $v;
      }
    }

    if (count($result)) {
      return $result;
    } else {
      return false;
    }
  }

  function finish($str) {
    switch ($this->unknowns) {
      case "keep":
      break;

      case "remove":
        $str = preg_replace('/\[##[^ \t\r\n}]+##\]/', "", $str);
      break;

      case "comment":
        $str = preg_replace('/{([^ \t\r\n}]+)}/', "<!-- Template variable \\1 undefined -->", $str);
      break;
    }

    return $str;
  }


  function p($varname) {
  	if($this->caching) $this->save_cache($varname , $this->cache_id);
    print $this->finish($this->get_var($varname));
  }


  function get($varname) {
    return $this->finish($this->get_var($varname));
  }


  function filename($filename) {
    if ($this->debug & 4) {
      echo "<p><b>filename:</b> filename = $filename</p>\n";
    }
    if (substr($filename, 0, 1) != "/") {
      $filename = $this->root."/".$filename;
    }

    if (!file_exists($filename)) {
      $this->halt("文件:  $filename 不存在。有可能您的安装没有成功。");
    }
    return $filename;
  }


  function varname($varname) {
    return preg_quote($this->left_delimiter.$varname.$this->right_delimiter);
  }

  function loadfile($varname) {
    if ($this->debug & 4) {
      echo "<p><b>loadfile:</b> varname = $varname</p>\n";
    }

    if (!isset($this->file[$varname])) {
      // $varname does not reference a file so return
      if ($this->debug & 4) {
        echo "<p><b>loadfile:</b> varname $varname does not reference a file</p>\n";
      }
      return true;
    }

    if (isset($this->varvals[$varname])) {
      // will only be unset if varname was created with set_file and has never been loaded
      // $varname has already been loaded so return
      if ($this->debug & 4) {
        echo "<p><b>loadfile:</b> varname $varname is already loaded</p>\n";
      }
      return true;
    }
    $filename = $this->file[$varname];

    /* use @file here to avoid leaking filesystem information if there is an error */
    $str = implode("", @file($filename));
    if (empty($str)) {
      $this->halt("loadfile: While loading $varname, $filename does not exist or is empty.");
      return false;
    }

    if ($this->debug & 4) {
      printf("<b>loadfile:</b> loaded $filename into $varname<br>\n");
    }
    $this->set_var($varname, $str);

    return true;
  }

  function halt($msg) {
    $this->last_error = $msg;

    if ($this->halt_on_error != "no") {
      $this->haltmsg($msg);
    }

    if ($this->halt_on_error == "yes") {
      die("<b>程序意外终止。</b>");
    }

    return false;
  }

  function haltmsg($msg) {
    printf("<b>模板错误:</b> %s<br>\n", $msg);
  }
	
	

	function is_cached($userfile = ''){
		if (!$this->caching)
			return false;

		$cache_name = $this->cachefilename($userfile);
		
		if(!file_exists($cache_name))
			return false;
		
		if( time() - filectime($cache_name) > $this->cache_lifetime )
			return false;
		else
			return true;
  }
  
  function cachefilename($userfile = ''){
  	if($userfile != '')
  		return $this->cache_dir.'/'.urlencode($userfile).'.cache';
  	
  	return $this->cache_dir.'/'.urlencode($this->cache_id).'.cache';
  }
  
	function save_cache($varname , $cache_id , $userfile = ''){
		$str = $this->get($varname);
		
		$cache_name = $this->cachefilename($userfile);
		@unlink($cache_name);
		if($this->writetofile($cache_name , $str))
			return true;
		else
			return false;
	}
	
	function writetofile($file_name,$data,$method="w") {
    $filenum=fopen($file_name,$method);
    flock($filenum,LOCK_EX);
    $file_data=fwrite($filenum,$data);
    fclose($filenum);
    if($file_data) return true;
    else return false;
	}
	
	function readfromfile($file_name) {
    if (file_exists($file_name)) {
        $file_data=file_get_contents($file_name);
        return $file_data;
    }
	}
	

	function geturl(){
		$SELF=basename($_SERVER['PHP_SELF']);
		@$QUERY=$_SERVER["QUERY_STRING"];
		$query_page=preg_replace(array("!((&|^)page=([^&]+))|(page=)!","!(^&)|[&]{2,}|([&]$)!"),array('',''),$QUERY);
		$query_page=$SELF.($query_page?"?":"").$query_page;
		return $query_page;
	}

	function cache_p($userfile = ''){
		print $this->readfromfile($this->cachefilename($userfile));
	}
	
	function clear_cache($userfile = ''){
		$cache_name = $this->cachefilename($userfile);
		if(@unlink($cache_name)) return true;
		else return false;
	}
	
	function clear_cache_all(){
		$current_dir = @opendir($this->cache_dir);
		while($entryname = @readdir($current_dir)){
			@unlink($this->cache_dir.'/'.$entryname);
		}
		@closedir($current_dir);
	}
}
?>