<?php

$JSON                 = file_get_contents("https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&pageToken=".$_POST['nextPageToken']."&videoId=".$_POST['videoID']."&key=".$_POST['key']."");
		$comment      = json_decode($JSON);

		$html = '';
        $arr  = [];

		foreach($comment->items as $row){
               
               $html .=  '<div class="media">
                    <a class="pull-left" href="#">
                        <img class="media-object img-circle" src="'.$row->snippet->topLevelComment->snippet->authorProfileImageUrl.'" alt="">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading">'.$row->snippet->topLevelComment->snippet->authorDisplayName;

                            $publishedAt = $row->snippet->topLevelComment->snippet->publishedAt;
                            $html .= '<small>'.date('M d, Y',strtotime($publishedAt)).' at '.date('H:i:s',strtotime($publishedAt)).'</small>
                        </h4>
                        '.$row->snippet->topLevelComment->snippet->textDisplay.'
                        <p><i class="fa fa-thumbs-o-up"></i> '.number_format($row->snippet->topLevelComment->snippet->likeCount,0,"",".").' </p>';

                         if(isset($row->replies)) {
                            foreach($row->replies->comments as $row_replies){
                        
                       
                        $html .= '<div class="media">
                            <a class="pull-left" href="#">
                                <img class="media-object img-circle" src="'.$row_replies->snippet->authorProfileImageUrl.'" alt="">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading">'.$row_replies->snippet->authorDisplayName;
                                     $publishedAt = $row_replies->snippet->publishedAt;
                                    $html .= '<small>'.date('M d, Y',strtotime($publishedAt)).' at '.date('H:i:s',strtotime($publishedAt)).'</small>
                                </h4>
                                '.$row_replies->snippet->textDisplay.'
                                <p><i class="fa fa-thumbs-o-up"></i> '.number_format($row_replies->snippet->likeCount,0,"",".").' </p>
                            </div>
                        </div>';
                      
                         } } 
                    $html .= '</div>
                </div>';
                
                }

		$arr['showComment']   = $html;
		
		if($_POST['moreComment'] < 100){
			$moreComment          = 0;
			$arr['nextPageToken'] = null;
		} else {
			$moreComment          = $_POST['moreComment']-100;
			$arr['nextPageToken'] = $comment->nextPageToken;
		}

		$arr['moreComment']     = $moreComment;
		$arr['moreCommentShow'] = number_format($moreComment, 0, '', '.');

		print json_encode($arr);

?>