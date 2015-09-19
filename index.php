<?php 

    if(isset($_GET['videoID'])){
        $videoID = $_GET['videoID']; 
    } else{
        $videoID = 'CTe-ZRwnoxo'; 
    }
   
    $key     = '';

    $JSON_Video = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id={$videoID}&key={$key}&part=snippet,contentDetails,statistics,status");
    $video = json_decode($JSON_Video);

    if($video->pageInfo->totalResults != 1){
        echo 'Video Not Found<br />';
        echo '<a href="index.php">Go back</a>';
        exit;
    }
          
    $JSON_Comment = file_get_contents("https://www.googleapis.com/youtube/v3/commentThreads?part=snippet%2Creplies&maxResults=100&videoId={$videoID}&key={$key}");
    $comment = json_decode($JSON_Comment);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Simple YOUtube API v3</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/blog-post.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
            #load { height: 100%; width: 100%; }
            #load {
               position    : fixed;
               z-index     : 99; /* or higher if necessary */
               top         : 0;
               left        : 0;
               overflow    : hidden;
               text-indent : 100%;
               font-size   : 0;
               opacity     : 0.6;
               background  : #E0E0E0  url('load.gif') center no-repeat;
            }
    </style>
</head>
<body>
    <div id="load"></div>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="">Simple YOUtube API v3</a>
            </div>
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <!-- Blog Post Content Column -->
            <div class="col-lg-8">
                <!-- Blog Post -->
                <!-- Title -->
                <h1><?= $video->items[0]->snippet->title ?></h1>
                <!-- Author -->
                <hr>
                <? $publishedAt = $video->items[0]->snippet->publishedAt; ?>
                <!-- Date/Time -->
                <p><span class="glyphicon glyphicon-time"></span> Posted on <?= date('M d, Y',strtotime($publishedAt)); ?> | <?= date('H:i:s',strtotime($publishedAt)); ?></p>
                <hr>
                <div class="embed-responsive embed-responsive-16by9">
                  <iframe class="embed-responsive-item" src="http://www.youtube.com/embed/<?= $video->items[0]->id; ?>"></iframe>
                </div>
                <hr>
                <p><i class="fa fa-eye"></i> <?= number_format($video->items[0]->statistics->viewCount,0,"","."); ?> <i class="fa fa-thumbs-o-up"></i> <?= number_format($video->items[0]->statistics->likeCount,0,"","."); ?> <i class="fa fa-thumbs-o-down"></i> <?= number_format($video->items[0]->statistics->dislikeCount,0,"","."); ?> </p>
                <!-- Post Content -->
                <p><?= $video->items[0]->snippet->description ?></p>
                <hr>
                <!-- Blog Comments -->
                <!-- Comments Form -->
                <div class="well">
                    <h4><?= number_format($video->items[0]->statistics->commentCount,0,"","."); ?> Comments</h4>
                </div>
                <hr>
                <!-- Posted Comments -->
                <!-- Comment -->
                <?
                foreach($comment->items as $row){
                ?>
                <div class="media">
                    <a class="pull-left" href="#">
                        <img class="media-object img-circle" src="<?= $row->snippet->topLevelComment->snippet->authorProfileImageUrl ?>" alt="">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading"><?= $row->snippet->topLevelComment->snippet->authorDisplayName; ?>
                            <? $publishedAt = $row->snippet->topLevelComment->snippet->publishedAt; ?>
                            <small><?= date('M d, Y',strtotime($publishedAt)); ?> at <?= date('H:i:s',strtotime($publishedAt)); ?></small>
                        </h4>
                        <?= $row->snippet->topLevelComment->snippet->textDisplay; ?>
                        <p><i class="fa fa-thumbs-o-up"></i> <?= number_format($row->snippet->topLevelComment->snippet->likeCount,0,"","."); ?> </p>
                        <? if(isset($row->replies)) {
                            foreach($row->replies->comments as $row_replies){
                        ?>
                        <!-- Nested Comment -->
                        <div class="media">
                            <a class="pull-left" href="#">
                                <img class="media-object img-circle" src="<?= $row_replies->snippet->authorProfileImageUrl; ?>" alt="">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading"><?= $row_replies->snippet->authorDisplayName; ?>
                                    <? $publishedAt = $row_replies->snippet->publishedAt; ?>
                                    <small><?= date('M d, Y',strtotime($publishedAt)); ?> at <?= date('H:i:s',strtotime($publishedAt)); ?></small>
                                </h4>
                                <?= $row_replies->snippet->textDisplay; ?>
                                <p><i class="fa fa-thumbs-o-up"></i> <?= number_format($row_replies->snippet->likeCount,0,"","."); ?> </p>
                            </div>
                        </div>
                        <!-- End Nested Comment -->
                        <? } } ?>
                    </div>
                </div>
                <?
                }
                
                if(isset($comment->nextPageToken)){
                $moreComment = $video->items[0]->statistics->commentCount-100;
                ?>
                <br />
                <div id="showComment"></div>
                <a style="text-decoration:none;cursor:pointer" id="showMoreComment"><div class="alert alert-info"> <center> SHOW MORE <span id="moreCommentShow"><?= number_format($moreComment, 0, '', '.') ?></span> COMMENTS </center></div></a>
                <input type="hidden" id="nextPageToken" value="<?= $comment->nextPageToken; ?>">
                <input type="hidden" id="videoID" value="<?= $video->items[0]->id; ?>">
                <input type="hidden" id="moreComment" value="<?= $moreComment; ?>">
                <input type="hidden" id="key" value="<?= $key; ?>">
                <?
                }
                ?>

            </div>

            <!-- Blog Sidebar Widgets Column -->
            <div class="col-md-4">

                <!-- Blog Search Well -->
                <div class="well">
                    <h4>Video Search</h4>
                    <form action="" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="videoID" placeholder="Enter Youtube Video ID">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">
                                <span class="glyphicon glyphicon-search"></span>
                        </button>
                        </span>
                    </div>
                    </form>
                    <!-- /.input-group -->
                </div>

            </div>

        </div>
        <!-- /.row -->

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright Simple YOUtube API v3 &copy; 2015</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
  
    $("#load").hide();

    $(document).on('click','#showMoreComment',function(){
        $("#load").show();

        var dataString = { 
                  nextPageToken : $('#nextPageToken').val(),
                  videoID : $('#videoID').val(),
                  moreComment : $('#moreComment').val(),
                  key : $('#key').val(),
                };

            $.ajax({
                type: "POST",
                url: "load_more.php",
                data: dataString,
                dataType: "json",
                cache : false,
                success: function(data){
                  $("#showComment").prepend(data.showComment);
                  $("#nextPageToken").val(data.nextPageToken);
                  $("#moreComment").val(data.moreComment);
                  $("#moreCommentShow").html(data.moreCommentShow);
                  if(data.moreComment == 0){
                    $('#showMoreComment').attr('id','noMoreComment').html('<div class="alert alert-success"><center>NO MORE COMMENTS</center></div>');
                  }
                  $("#load").hide();
              
                } ,error: function(xhr, status, error) {
                  alert(error);
                },
            });

    });

   

});
</script>

</body>

</html>
