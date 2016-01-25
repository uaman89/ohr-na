<?
$rows_count = 0;
for( $i=0; $i<$limit; $i++ ):
    if ( !isset($arr[$i]) ) continue;
    $row = $arr[$i];
    $rows_count++;
    ?>
    <div class="single-comment">
        <div class="single-comment-inner" >
            <div class="comment-content">
                <div class="comments-arrow"></div>
                <div class="comments-data-name-box">
                    <span class="comments-name"><?=$row['show_name']?></span>
                    <span class="comments-data"><?=$row['dt']?></span><br/>
                    <?
                        if ( $row['vote'] > 0 ) {
                            echo '<ul class="stars">';
                            for ($j = 1; $j < 6; $j++) {
                                if ($j == $row['vote']) echo '<li class="on"></li>';
                                else echo '<li></li>';
                            }
                            echo '</ul>';
                        }
                    ?>
                </div>
                <?php echo stripcslashes ($row['text']); ?>
            </div>
        </div>
    </div>
    <hr class="dotted-line"/>
    <?
endfor; ?>
<input type="button" class="green-button" onclick="goto_comments();return false;" value="Все отзывы" style="margin: 0 auto;"/>