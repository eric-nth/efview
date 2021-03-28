<?php
require('./config.php');
if (empty($_REQUEST['path'])) {
    redirect('./share.php?path=/');
    exit();
} else {
    $path = $_REQUEST['path'];
}

if ($_REQUEST['content_only'] != "true") {
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <?php } ?>
  <title><?php echo $site_title.' - '.$path;?></title>
  <?php if ($_REQUEST['content_only'] != "true") { ?>
  <style>
      .content_wrap {
          background-color: #f2f2f2;
      }
      .pace{-webkit-pointer-events:none;pointer-events:none;-webkit-user-select:none;-moz-user-select:none;user-select:none}.pace-inactive{display:none}.pace .pace-progress{background:#41dbb4;position:fixed;z-index:2000;top:0;right:100%;width:100%;height:2px}
  </style>
  <link rel="stylesheet" href="https://www.layuicdn.com/layui/css/layui.css">
  <script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js" type="text/javascript"></script>
  <script src="https://cdn.staticfile.org/pace/1.2.4/pace.min.js" type="text/javascript"></script>
</head>

<body>
<script>var errorcode = 0;</script>
<!--Page Content-->
<div class="nav_wrap" id="nav">
    <ul class="layui-nav layui-bg-<?php echo $theme_color;?>" lay-filter="">
        <li class="layui-nav-item">
          <b><?php echo $site_title;?></b>
        </li>
    </ul>
</div>
<div class="content_wrap" id="entry_content">
<?php } ?>
    <p style="color:#777">&nbsp;<object data='svgs/caret-circle-right.svg' type='image/svg+xml' width=11 height=11></object>当前位置: <?php
            $pathparts = explode("/",$path.'/');
            $wholepath = '';
            for ($i = 0; $i < count($pathparts) - 1; $i++) {
                $wholepath .= $pathparts[$i];
                $wholepath .= '/';
                if ($pathparts[$i] == '' && $i != 0) {
                    continue;
                }
                echo "<a href='share.php?path=$wholepath' style='color:#777'>/".($i == 0 ? "根目录" : $pathparts[$i])."</a>";
            }
            
        ?></p>
    <div class="filelist_wrap" id="filelist">
        <table class="filelist_table layui-table">
            <colgroup><col width="800"><col width="80"><col width="100"><col></colgroup>
            <thead>
                <tr>
                    <th>文件名称</th>
                    <th>大小</th>
                    <th>类型</th>
                    <th>更改时间</th>
                </tr>
            </thead>
            <tbody>
        <?php
        if(!is_dir($storage_dir.$path)) {
            if (!file_exists($storage_dir.$path)) {
                echo "<script>errorcode = 404;</script>";
                echo "<tr><td>404 Not Found</td><td></td><td></td><td></td></tr>";
                header("HTTP/1.1 404 Not Found");
            } else {
                $refererUrl = parse_url($_SERVER['HTTP_REFERER']);
                if ($refererUrl['host'] == $site_domain) {
                    redirect($storage_dir.$path);
                } else {
                    $filesize = filesize($storage_dir.$path);
                        if ($filesize > 1179648) {
                            $filesize_display = sprintf("%.1fMB",$filesize/1024.0/1024.0);
                        } else if ($filesize > 1152) {
                            $filesize_display = sprintf("%.1fKB",$filesize/1024.0);
                        } else {
                            $filesize_display = sprintf("%d",$filesize);
                        }
                        echo "<tr><td><object data='svgs/file-alt.svg' type='image/svg+xml' width=12 height=12></object>&nbsp;<a class='filelink' href='$storage_dir$path' target='_blank'>$path</a></td><td>".$filesize_display."</td><td>文件</td><td>".date("Y-m-d H:i:s",filemtime($storage_dir.$path))."</td></tr>";
                }
            }
        } else {
            $handler = opendir($storage_dir.$path);
            while (($filename = readdir($handler)) !== false) {
                if ($filename != "." && $filename != "..") {
                    if (!is_dir($storage_dir.$path.$filename)) {
                        $filesize = filesize($storage_dir.$path.$filename);
                        if ($filesize > 1179648) {
                            $filesize_display = sprintf("%.1fMB",$filesize/1024.0/1024.0);
                        } else if ($filesize > 1152) {
                            $filesize_display = sprintf("%.1fKB",$filesize/1024.0);
                        } else {
                            $filesize_display = sprintf("%d",$filesize);
                        }
                        echo "<tr><td><object data='svgs/file-alt.svg' type='image/svg+xml' width=12 height=12></object>&nbsp;<a class='filelink' href='share.php?path=$path$filename' target='_blank'>$filename</a></td><td>".$filesize_display."</td><td>文件</td><td>".date("Y-m-d H:i:s",filemtime($storage_dir.$path.$filename))."</td></tr>";
                    } else {
                        echo "<tr><td><object data='svgs/folder.svg' type='image/svg+xml' width=12 height=12></object>&nbsp;<a class='filelink' href='share.php?path=$path$filename/'>$filename</a></td><td>-</td><td>文件夹</td><td>".date("Y-m-d H:i:s",filemtime($storage_dir.$path.$filename))."</td></tr>";
                    }
                }
            }
            closedir($handler);
        }
        ?>
        </table>
    </div>
<?php    if ($_REQUEST['content_only'] != "true") {?>
</div>

<!--Layui Module-->
<script src="https://www.layuicdn.com/layui/layui.js"></script>
<script>
var element = layui.element;
layui.use(['layer', 'form'], function(){
  var layer = layui.layer
  ,form = layui.form;
    if (errorcode === 404) {
        layer.open({title: '404 Not Found',content: '请求的文件不存在！',yes: function(){window.location.replace('share.php?path=/');}});
    }
    if (errorcode === 403) {
        layer.open({title: '403 Forbidden',content: '您没有权限访问该文件！',yes: function(){window.location.replace('share.php?path=/');}});
    }
});
</script>

<!--Ajax Loading Module-->
<script>/*
$(document).ready(function(){
    $("a").click(function(){
        $("div#entry_content").load($(this).attr('jump-url')+"&content_only=true");
    });
});*/
   $("a[target!=_blank]").on("click",function(){
      var url = $(this).attr("href") + "&content_only=true";
      var url_noc = $(this).attr("href");
      $.ajax({
          url:url,
          type:"get",
         //加载前
          beforeSend:function(){
                $('div#entry_content').fadeTo(200,0.2);
            },
          complete:function(){ 
                $('div#entry_content').fadeTo(300,1);
            }
            ,
          success:function(message){
                var ajaxtitle1 = $(message).find("title").text();
                var ajaxtitle2 = "<?php echo $site_title;?>";
                if (ajaxtitle1 == "") {
                    window.document.title = ajaxtitle2;
                }
                else {
                    window.document.title = ajaxtitle1;
                }
                $("div#entry_content").html(message);
                var state = {title:window.document.title,url:window.location.href};
                history.pushState(state,'',url_noc);
            },
           error: function() {
                $("div#entry_content").html("Folder Content Load Error"); 
            } , 
                });  
      return false; 
   })
</script>

</body>
</html>
<?php
}
?>