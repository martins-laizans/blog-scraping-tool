<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php //echo $title; ?>Parser</title>
        <link rel="shortcut icon" href="/faviocon.ico"  type="image/x-icon"  />
        <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'></script>
	<?php echo Asset::css('bootstrap.css'); 
            echo Asset::css('donofo.css'); 
        ?>
</head>
<body>
	<div class="container">
                <div class="span12" id="header">
                        <h1><?php // echo $title; ?></h1>
                        <aside id="auth">	    
                            <div class="pull-left">
                                <?php if(isset($navigation)) echo $navigation; ?>
                            </div>
                        </aside>
                </div>
                <div class="span12" id="messages">
                        <?php if (Session::get_flash('success')): ?>
                            <div class="alert-message text-success">
                                    <p>
                                    <?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
                                    </p>
                            </div>

                        <?php endif; ?>
                        <?php if (Session::get_flash('error')): ?>
                            <div class="alert-message text-error">
                                    <p>
                                    <?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
                                    </p>
                            </div>
                        <?php endif; ?>
                </div>

                <div class="span12">
                    <?php echo $content; ?>
                </div>
		
           
		<footer class="span12">
                    <hr> 
                        <?php //if(Auth::has_access('overview.index')){ ?>
                            
                            <p >Page rendered in {exec_time}s using {mem_usage}mb of memory. Render time <?php echo date('H:i:s'); ?></p>
                        <?php //} ?>
                            <p class="pull-left">
                                
                                <?php //$users_online = Model_Log::user_count_specified_min_interval(); 
                                    //echo (($users_online>1)? $users_online.' cilvēki':' tikai Tu');
                                ?>
                            </p>
                            <p class="pull-right">
                                © 2014 Mārtiņš Laizāns
                            </p>
		</footer>
</body>
</html>
