<?php
define("ADMIN", true);
require_once("../common/lib.php");
require_once("../common/define.php");
define("TITLE_ELEMENT", $pms_texts['DASHBOARD']);

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}elseif($_SESSION['user']['type'] == "registered"){
    unset($_SESSION['user']);
    $_SESSION['msg_error'][] = "Access denied.";
    header("Location: login.php");
    exit();
}

require_once("includes/fn_module.php"); ?>
<!DOCTYPE html>
<head>
    <?php include("includes/inc_header_common.php"); ?>
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-YG5881J2D5"></script> 
<script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-YG5881J2D5'); </script>
</head>
<body>
    <div id="wrapper">
        <?php include("includes/inc_top.php"); ?>
        <div id="page-wrapper">
            <div class="page-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h1><i class="fas fa-fw fa-tachometer-alt"></i> <?php echo $pms_texts['DASHBOARD']; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="alert-container">
                    <div class="alert alert-success alert-dismissable"></div>
                    <div class="alert alert-warning alert-dismissable"></div>
                    <div class="alert alert-danger alert-dismissable"></div>
                </div>
                <div class="row" id="dashboard">
                    <?php
                    if($pms_db !== false){
                        foreach($modules as $module){

                            $title = $module->getTitle();
                            $name = $module->getName();
                            $dir = $module->getDir();
                            $dates = $module->isDates();
                            $count = 0;
                            $last_date = "";
                            $rights = $module->getPermissions($_SESSION['user']['type']);
                            
                            if($module->isDashboard() && !in_array("no_access", $rights) && !empty($rights)){
                                $query = "SELECT count(id) AS nb";
                                if($dates) $query .= ", MAX(add_date) AS last_add_date, MAX(edit_date) AS last_add_date";
                                $query .= " FROM pm_".$name." WHERE 1";
                                if($module->isMultilingual()) $query .= " AND lang = ".PMS_DEFAULT_LANG;
                                
                                if(!in_array($_SESSION['user']['type'], array("administrator", "manager", "editor")) && pms_db_column_exists($pms_db, "pm_".$name, "users"))
                                    $query .= " AND users REGEXP '(^|,)".$_SESSION['user']['id']."(,|$)'";
                                
                                $result = @$pms_db->query($query);
                                if($result !== false && $pms_db->last_row_count() > 0){
                                    $row = $result->fetch();
                                    $count = $row[0];
                                    if($dates){
                                        $last_add_date = (!is_null($row[1])) ? $row[1] : 0;
                                        $last_edit_date = (!is_null($row[2])) ? $row[2] : 0;
                                        
                                        $last_date = max($last_edit_date, $last_add_date);
                                        $last_date = ($last_date == 0) ? "" : date("Y-m-d g:ia", $last_date);
                                    } ?>
                                    
                                    <div class="dashboard-entry col-lg-3 col-md-4 col-sm-6">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <div class="huge"><i class="fas fa-fw fa-<?php echo $module->getIcon(); ?>"></i></div>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge"><?php echo $count; ?></div>
                                                        <h3 class="mt0"><?php echo $title; ?></h3>
                                                        <?php
                                                        if($last_date != ""){
                                                            echo "<i class=\"fas fa-fw fa-clock\"></i> <small>".$last_date."</small>";
                                                        }else echo "<small>&nbsp;</small>"; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="<?php echo $dir; ?>/index.php?view=list">
                                                <div class="panel-footer">
                                                    <span class="pull-left"><?php echo $pms_texts['SHOW']; ?></span>
                                                    <span class="pull-right"><i class="fas fa-fw fa-chevron-circle-right"></i></span>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </a>
                                            <a href="<?php echo $dir; ?>/index.php?view=form&id=0">
                                                <div class="panel-footer">
                                                    <span class="pull-left"><?php echo $pms_texts['ADD']; ?></span>
                                                    <span class="pull-right"><i class="fas fa-fw fa-plus-circle"></i></span>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$_SESSION['msg_error'] = array();
$_SESSION['msg_success'] = array();
$_SESSION['msg_notice'] = array(); ?>
