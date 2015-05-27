<?php echo Asset::js('jquery-1.9.0.min.js');
echo Asset::js('bootstrap.js');
?>
<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if($page == 'main'): echo 'class="active"'; endif; ?>>
                <?php echo Html::anchor("main", '<i class="icon-home"></i> '.__("HOME")); ?>
            </li>
            <li class="dropdown <?php if($page == 'settings'): echo 'active'; endif; ?>">
                <a class="dropdown-toggle" rel="test" id="sLabel" role="button" data-toggle="dropdown" data-target="#" href="#">
                    <?php echo '<i class="icon-wrench"></i> Settings'; ?>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="sLabel">
                    <li>
                        <?php echo Html::anchor('blog', '<i class="icon-th-large"></i> Blogs'); ?>
                    </li>
                    <li>
                        <?php echo Html::anchor('archive', '<i class="icon-refresh"></i> Archive pages'); ?>
                    </li>
<!--                     <li>
                        <?php // echo Html::anchor('articles', '<i class="icon-random"></i> Articles'); ?>
                    </li>-->
<!--                    <li>
                        <?php // echo Html::anchor('settings/profile', '<i class="icon-eye-open"></i> Profils'); ?>
                    </li>
                    <li>
                        <?php // echo Html::anchor('settings/auth_options', '<i class="icon-globe"></i> Autorizācijas iestatījumi'); ?>
                    </li>-->
                </ul>
            </li>
            <li <?php if($page == 'articles'): echo 'class="active"'; endif; ?>>
                <?php //if(Auth::has_access('overview.index')) 
                    echo Html::anchor("article", '<i class="icon-random"></i> Articles ');
                ?>
            </li>  
            <li <?php if($page == 'converter'): echo 'class="active"'; endif; ?>>
                <?php //if(Auth::has_access('overview.index')) 
                    echo Html::anchor("converter", '<i class="icon-globe"></i> Converter ');
                ?>
            </li>  
             <li <?php if($page == 'counter'): echo 'class="active"'; endif; ?>>
                <?php //if(Auth::has_access('overview.index')) 
                    echo Html::anchor("converter/inDictionaryCheck", '<i class="icon-globe"></i> inDictionaryCheck ');
                ?>
            </li>
        </ul>
    
    </div>
</div>
