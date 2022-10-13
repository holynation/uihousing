<?php
$address = 'Abraham Lincol road, University of Ibadan';
$office = "Koladaisi Building";
$phone = '08100000000';
$dateTimeNow = date("Y-m-d h:i:s"); 

?>
<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<title> Applicant Form </title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style type="text/css">
 body {
   /*background-image: url(images/result_online_backdrop.png);*/
   font-family:Verdana, Arial, Helvetica, sans-serif;
}
 .heading { margin:0; padding: 2px 0; font-size: 14px; font-weight: bold; color: #000;}
 .header { color: #FFFFFF; font-weight: bold; font-size:12px; background-color: rgba(31, 111, 224,.99);}
 table {width:100%; background-color: #fff; font-family:Verdana, Arial, Helvetica, sansserif;font-size:12px;}

 .tableHeader {border-bottom:1px dotted green;font-size:14px; padding-bottom: 0}
 .tableLabel {font-size:9px; padding: 0 0 5px 5px}

 .tableData {border-bottom:1px dotted rgba(31, 111, 224,.99); padding: 1px 0 0 0}
 .results .tableData {text-align:center; padding: 0 5px}
 .results {text-align:left; padding: 0 5px;}
 .table1Data {border-bottom:1px dotted rgba(31, 111, 224,.99); padding-bottom: 0; padding: 12px 0 0 0}
 .table1DataHeader {padding-top: 12px}
 .tableDataHeader {border-bottom:1px dotted rgba(31, 111, 224,.99); padding-bottom: 0}
 .tableData {border-bottom:1px dotted rgba(31, 111, 224,.99);border-left:1px solid rgba(31, 111, 224,.99); paddingbottom: 0}

 #control {float:right; font-size:15px; margin: 2px 0 0 0;}
div#page {margin: 0 auto; width: auto;} /* use this to control the width of the print page */
</style>
</head>

<body>
    <div id="page">
        <table width="750px">
            <tr>
                <td style="background-color: rgba(31, 111, 224,.99);text-align: center; color:#fff;">
                    <img src="<?= base_url('assets/img/housing_logo.jpeg'); ?>" alt="company logo" width="120" height="120" style="float: left;">
                    <h1>University of Ibadan <br/> Senior Staff Housing Committee </h1>
                    <address>Address: <?= $address; ?><br />Phone Number: <?= $phone; ?> </address>
                </td>
            </tr>
            <tr>
                <td class="heading">Application Form Printed on <?= $dateTimeNow; ?>
                <div id="control"><a href="#" onClick="window.print()">Print</a></div></td>
            </tr>
        </table>
        <table width="750" border="0" style="border: 2px solid rgba(31, 111, 224,.99);border-bottom:0 solid rgba(31, 111, 224,.99);" cellpadding="4px">
            <tr bgcolor="rgba(31, 111, 224,.99)">
                <td colspan="4" class="header">Personal Information</td>
            </tr>
            <tr>
                <td width="212" class="tableHeader"><strong><?= $allocation->staff->surname ?></strong></td>
                <td widht="196" class="tableHeader"><strong><?= $allocation->staff->firstname; ?></strong></td>
                <td widht="224" class="tableHeader"><strong><?= $allocation->staff->othername; ?></strong></td>
            </tr> 
            <tr>
                <td width="212" class="tableLabel">Surname</td>
                <td width="196" class="tableLabel">Firstname</td>
                <td width="224" class="tableLabel">Othernames</td>
            </tr>
        </table>

        <table width="750" border="0" cellpadding="6" cellspacing="0" style="border: 2px solid rgba(31, 111, 224,.99);">
            <tr>
                <td class="table1DataHeader" width="110"><strong>Staff Number:</strong></td>
                <td class="table1Data" width="450"><?= deleteSpecialChar(@$allocation->staff->occupant_num); ?></td>
                <td rowspan="17" align="center" valign="middle" style="border-left:2px solid rgba(31, 111, 224,.99);border-top: 1px solid #ffffff;">
                    <img src="<?= $allocation->staff->staff_path; ?>" alt="staff image" width="150">
                </td>
            </tr>
            <tr>
                <td class="table1DataHeader"><strong>Department:</strong></td>
                <td class="table1Data"><?= @$allocation->staff->staff_department->departments->name; ?></td>
            </tr>
            <tr>
                <td class="table1DataHeader"><strong>Designation:</strong></td>
                <td class="table1Data"><?= @$allocation->staff->designation->designation_name; ?></td>
            </tr>
            <tr>
                <td class="table1DataHeader"><strong>Office Address:</strong></td>
                <td class="table1Data"><?= $office; ?></td>
            </tr>
            <tr>
                <td class="table1DataHeader"><strong>Confirmation Date:</strong></td>
                <td class="table1Data"><?= dateFormatter($allocation->date_modified); ?></td>
            </tr>
            <tr>
                <td class="table1DataHeader"><strong>Date Applied :</strong></td>
                <td class="table1Data"><?= dateFormatter($allocation->date_created); ?></td>
            </tr>
            <tr>
                <td class="table1DataHeader"> <strong>Status:</strong></td>
                <td class="table1Data"><?= strtoupper($allocation->applicant_status); ?></td>
            </tr>
            <tr>
                <td class="table1DataHeader"> <strong>Staff Status:</strong></td>
                <td class="table1Data"><?= @$allocation->staff->title->name; ?></td>
            </tr>
            <tr>
                <td class="table1DataHeader"><strong>Address:</strong></td>
                <td class="table1Data"><?= $allocation->address; ?></td>
            </tr>
                <td class="table1DataHeader"><strong>Accommodation:</strong></td>
                <td class="table1Data"><?= $allocation->category->category_name; ?></td>
            </tr>
        </table>
        <!-- this is the certificate -->
        <table width="750" border="0"  cellpadding="4" class="results" style="border:2px solid
        rgba(31, 111, 224,.99);">
            <tr>
                <td>
                    <div style="margin-top: 24px;">______________________________________________ <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Director & Sign/Date</b>
                    </div>
                    <div style="float:right;margin-top:-30px;">______________________________________________ <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Supervisor & Sign/Date</b>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>