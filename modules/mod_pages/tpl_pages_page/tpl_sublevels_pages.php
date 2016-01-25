<? if(count($arr)!=0): ?>
<ul>
<? foreach ($arr as $row): ?>
    <li>
        <a href="<?=$row['link'];?>" class="sub_levels"><?=stripslashes($row['pname']);?></a>
    </li>
<? endforeach; ?>
</ul>
<? endif; ?>