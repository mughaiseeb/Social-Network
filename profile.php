<?php
include('./Class/DB.php');
include('./Class/Login.php');
$username = "";
$verified = False;
$isFollowing = False;

if (isset($_GET['username'])) {
        if (DB::quary('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))) {
                $username = DB::quary('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
                $userid = DB::quary('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $verified = DB::quary('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
                $followerid = Login::isLoggedIn();
                if (isset($_POST['follow'])) {
                        if ($userid != $followerid) {
                                if (!DB::quary('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 6) {
                                                DB::quary('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::quary('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
                                } else {
                                        echo 'Already following!';
                                }
                                $isFollowing = True;
                        }
                }
                if (isset($_POST['unfollow'])) {
                        if ($userid != $followerid) {
                                if (DB::quary('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 6) {
                                                DB::quary('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::quary('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                                }
                                $isFollowing = False;
                        }
                }
                if (DB::quary('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                        //echo 'Already following!';
                        $isFollowing = True;
                }
                if (isset($_POST['post'])) {
                        $postbody = $_POST['postbody'];
                        $loggedInUserId = Login::isLoggedIn();
                        if (strlen($postbody) > 200 || strlen($postbody) < 1) {
                                die('Incorrect length!');
                        }
                        if ($loggedInUserId == $userid) {
                                DB::quary('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody'=>$postbody, ':userid'=>$userid));
                        } else {
                                die('Incorrect user!');
                        }
                }
                if (isset($_GET['postid'])) {
                        if (!DB::quary('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                                DB::quary('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                                DB::quary('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                        } else {
                                DB::quary('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                                DB::quary('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                        }
                }
                $dbposts = DB::quary('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
				$posts = "";
				$val1="";
				$val2="";
                foreach($dbposts as $p) {
                	if(Login::isLoggedIn()){
                	
                        if (!DB::quary('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$followerid))) {
                                $posts .= htmlspecialchars($p['body'])."

                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                               
                                	
                                       <input type='submit' name='like' value='Like'>
                                   
                                   
                                       <span>".$p['likes']." likes</span>
                                      
                                   
                                </form>
                                <hr /></br />
                                ";
                        } else {
                                $posts .= htmlspecialchars($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='unlike' value='Unlike'>
                                        <span>".$p['likes']." likes</span>
                                </form>
                                <hr /></br />
                                ";
                        }

                       }else
                       {
                       	      if (!DB::quary('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$followerid))) {
                                $posts .= htmlspecialchars($p['body'])."

                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                               
                                	
                                     
                                   
                                   
                                       <span>".$p['likes']." likes</span>
                                      
                                   
                                </form>
                                <hr /></br />
                                ";
                        } else {
                                $posts .= htmlspecialchars($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='unlike' value='Unlike'>
                                        <span>".$p['likes']." likes</span>
                                </form>
                                <hr /></br />
                                ";
                        }

                       }

                
            }
        } else {
                die('User not found!');
        }
}
?>
<h1><?php echo $username; ?>'s Profile<?php if ($verified) { echo ' - Verified'; } ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <?php
        if(Login::isLoggedIn()){
        	if ($userid != $followerid) {
                if ($isFollowing) {
                        echo '<input type="submit" name="unfollow" value="Unfollow">';
                } else {
                        echo '<input type="submit" name="follow" value="Follow">';
                }
       		 }
    }
        ?>
</form>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
<?php
	if(Login::isLoggedIn()){
       echo' <textarea name="postbody" rows="8" cols="80"></textarea>';
        echo'<input type="submit" name="post" value="Post">';
    }
?>
</form>

<div class="posts">
<?php
   echo $posts;
     ?>
</div>