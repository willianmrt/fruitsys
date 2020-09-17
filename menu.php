
<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="utf-8"/>
    <title>FruitSys</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />	       
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.12.4.js"></script>     
    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/function.js"></script>                 
    <script src="assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>         
    <script src="assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>    
    <script src="assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>       
    <script src="assets/plugins/sweetalert/dist/sweetalert.min.js"></script>         
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/transparent/app.css">   
    
    </head>
    <body>
        <!-- begin page-cover -->
        <div class="page-cover"></div>
        <!-- end page-cover -->

        <!-- begin #page-loader -->
        <div id="page-loader" class="fade show"><span class="spinner"></span></div>
        <!-- end #page-loader -->

        <!-- begin #page-container -->
        <div id="page-container" class="page-container fade page-sidebar-fixed page-header-fixed">
            <!-- begin #header -->
            <div id="header" class="header navbar-default">
                <!-- begin navbar-header -->
                <div class="navbar-header">
                    <a href="index.html" class="navbar-brand"><span class="navbar-logo"></span> <b>Fruit</b>sys</a>                                       
                </div>
                <!-- end navbar-header -->

                <!-- begin header-nav -->
                <ul class="navbar-nav navbar-right">                
                    <li class="dropdown navbar-user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="assets/img/user-13.jpg" alt="" />
                            <span class="d-none d-md-inline">Willian Maricato</span> <b class="caret"></b>
                        </a>                        
                    </li>
                </ul>
                <!-- end header navigation right -->
            </div>
            <!-- end #header -->

            <!-- begin #sidebar -->
            <div id="sidebar" class="sidebar">
                <!-- begin sidebar scrollbar -->
                <div data-scrollbar="true" data-height="100%">                
                    <!-- begin sidebar nav -->
                    <ul class="nav">
                        <li class="nav-header">Menu</li> 
                        <li class="has-sub">
                            <a href="product">                                
                                <i class="fa fa-cubes"></i>
                                <span>Produto</span>
                            </a>                            
                        </li>                        
                        <li class="has-sub">
                            <a href="order">                               
                                <i class="fa fa-handshake"></i>
                                <span>Vendas</span>
                            </a>                           
                        </li>                        
                    </ul>                    
                </div>                
            </div>
            <div class="sidebar-bg"></div>            
        </div>
    </body>    


