<?php
require __DIR__ . '/vendor/autoload.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XML- Assignment 1</title>
    <style>
        .form-control{
            margin-top: 4px;
        }

        .label-mid{
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .ui-datepicker-calendar {
            display: none;
        }

        .ui-datepicker-month {
            display: none;
        }

        .ui-datepicker-next,.ui-datepicker-prev {
            display:none;
        }

    </style>
    <?php include('navigation.php') ?>
</head>
<body onload="loadDoc()">
    <h1 style="text-align: center;">Manage CD Collection</h1>
    <form action="#">
    <div class="form-group col-12" style="display: flex; flex-direction: column; padding: 50px 25%;">
        <div style="display: flex;justify-content: space-around;">
            <div class="col-3 label-mid"><label for="cdName">CD Title</label></div>
            <div class="col-9"><input type="text" id="cdName" spellcheck="false" class="form-control" value="Into XML" required></div>
        </div>
        <div style="display: flex;justify-content: space-around;">
            <div class="col-3 label-mid"><label for="artist">Artist</label></div>
            <div class="col-9"><input type="text" id="artist" spellcheck="false" class="form-control" value="MNK" required></div>
        </div>
        <div style="display: flex;justify-content: space-around;">
            <div class="col-3 label-mid"><label for="country">Country</label></div>
            <div class="col-9"><input type="text" id="country" spellcheck="false" class="form-control" value="SL" required></div>
        </div>
        <div style="display: flex;justify-content: space-around;">
            <div class="col-3 label-mid"><label for="company">Company</label></div>
            <div class="col-9"><input type="text" id="company" spellcheck="false" class="form-control" value="UOK" required></div>
        </div>
        <div style="display: flex;justify-content: space-around;">
            <div class="col-3 label-mid"><label for="price">Price ($)</label></div>
            <div class="col-9"><input type="text" id="price" spellcheck="false"class="form-control" value="10.2" required></div>
        </div>
        <div style="display: flex;justify-content: space-around;">
            <div class="col-3 label-mid"><label for="year">Year</label></div>
            <div class="col-9"><input type="text" id="datepicker" spellcheck="false"class="form-control" value="1326" required></div>
        </div>
        <div class="col-12" style="text-align: end;margin-top: 10px;"><button id="btn-submit" class="btn btn-primary" style="width: 150px;" onclick="addXML()">Add</Address></button></div>
    </div>
</form>
    <div class="text-center">
        <button class="btn btn-warning text-center m-1" onclick="update('fileWrite.php'); update('addToDB.php');">Add Changes to DB and XML File</button>
    </div>
<div style="display: flex;padding: 50px 25%;" class="table-responsive-lg">
<table id="cd-table" class="table table-striped table-hover">
    <thead class="thead-dark">
    
</thead>
<tbody>

</tbody>
</table>
</div>
<script src="scripts.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>