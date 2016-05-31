<?php
require_once("admin.php");
require_once("../../stadfensystem/database.php");
require_once("../../stadfensystem/statistics.php");
require_once("../../stadfensystem/messages.php");
$page_id = "page-exporting";

?>

<?php include("header.php"); ?>

<style>

h2 {
	border-bottom: 2px solid #92b5be; color:#92b5be; font-size: 2.5em; font-weight: 300; padding-bottom: 10px; margin: 0px;
}

</style>

<script>


function onClickMonthReport()
{
	var month = document.getElementById('selectmonth').value;
	var year = document.getElementById('selectyear').value;
	
	window.location = "/monthreport.php?year="+year+"&month="+month;
}


</script>


<?php

$months = array
(
	1 => "Januari",
	2 => "Februari",
	3 => "Mars",
	4 => "April",
	5 => "Maj",
	6 => "Juni",
	7 => "Juli",
	8 => "Augusti",
	9 => "September",
	10 => "Oktober",
	11=> "November",
	12 => "December",
);

$years = range(2010,date("Y"));

$selected_month = intval(date("m")) -1;
if($selected_month == 0)
	$selected_month = 12;
$selected_year = intval(date("Y")) - ($selected_month == 12 ? 1 : 0); //If the last month was december, move back one year



?>

<div style="width: 900px; margin: 0 auto;">
    <div>
        <h1 style="font-weight: 300; font-size: 3em;">Exportering</h1>
    </div>
    
    <div style="display: block; overflow: hidden;">
    	<div>
            <canvas id="bar-chart" width="1100" height="120"></canvas>
        </div>
    </div> 
       
    <br />
    <div>
    	<h2>Exportering av månadsrapport</h2>
    </div>
    <div>
   
    	<table class="list business-customers-list">
        	<tbody>
            	<tr>
                	<td class="bold">Månad:</td>
                    <td>
                    	<select id="selectmonth">
                        	<?php
							
							$month_indexes = array_keys($months);
							for($i = 0; $i < count($month_indexes); $i++)
							{
								echo('<option '. ($month_indexes[$i]==$selected_month ? 'selected="selected"' :'') .' value="'.$month_indexes[$i].'">'. $months[$month_indexes[$i]] .'</option>');
							}
							
							?>
                        </select>
                    </td>
                </tr>
                <tr>
                	<td class="bold">År:</td>
                    <td>
                    	<select id="selectyear">
                        	<?php
							
							for($i = 0; $i < count($years); $i++)
							{
								echo('<option '. ($years[$i]==$selected_year ? 'selected="selected"' :'') .' value="'.$years[$i].'">'. $years[$i] .'</option>');
							}
							
							?>
                        </select>
                    </td>
                </tr>
                <tr><td><a class="btn btn-default btn-medium" href="javascript:onClickMonthReport();" role="button">Exportera</a></td><td></td></tr>
            </tbody>
        </table>
    </div>
    <br /><br />
    
    <div>
        
    </div>

    
    
    
    
    
</div>

</div>


<?php include("footer.php"); ?>