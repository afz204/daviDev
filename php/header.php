<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
<!--    <link rel="icon" href="../../../../favicon.ico">-->

    <title>Bunga Davi</title>

    <!-- Bootstrap core CSS -->
    <link href="<?=URL?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=URL?>assets/vendors/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?=URL?>assets/css/offcanvas.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=URL?>assets/vendors/dataTables/css/dataTables.bootstrap4.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?=URL?>assets/vendors/datetime-picker4/css/bootstrap-datetimepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?=URL?>assets/vendors/daterangepicker/daterangepicker.css"/>
    <?php if($menu == 'bd' OR $menu == 'order' OR $menu == 'payment'){ ?>
        <link rel="stylesheet" type="text/css" href="<?=URL?>assets/vendors/select2/select2.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?=URL?>assets/vendors/select2/select2-bootstrap4.min.css"/>
        <link href="<?=URL?>assets/vendors/krajeee/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="<?=URL?>assets/vendors/krajeee/themes/explorer-fa/theme.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="<?=URL?>assets/vendors/lightbox/ekko-lightbox.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="<?=URL?>assets/vendors/lightbox/ekko-main.css" media="all" rel="stylesheet" type="text/css"/>
    <?php } ?>
    <style type="text/css">
        .bootstrap-datetimepicker-widget{
            z-index : 9;
        }
    </style>
</head>

<body class="bg-light">
    <?php require_once 'navigation.php'; ?>
<main role="main" class="container-fluid2">