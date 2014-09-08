<?php
error_reporting(E_ALL);
//set_time_limit(0);
include_once('simple_html_dom.php');

$html = file_get_html('http://primera.e-sim.org/militaryUnitCompanies.html?id=38');
$lang = '';


$charset = $html->find('meta[http-equiv*=content-type]', 0);
$target = array();
$query = '';

//Company Listing 

$target = $html->find('.dataTable tr');
//$noEmployee = $html->find('.dataTable tr td 3')
$i=0;
$list_company_id = array();
foreach($target as $element) {
	$comp_link = $element->find('td a');
	if($comp_link){
		foreach($comp_link as $comp_link_i){
			if(preg_match ('/company/' , $comp_link_i->href)){
			$no_employes = $element->find('td',3)->plaintext;
				if(!preg_match ('/0/' , $no_employes)){
					$id_comp = explode("company.html?id=", $comp_link_i->href);
					$list_company_id[$i++] = array(
						'id' => $id_comp[1]
					);
				}
			
		}
		}
	}
}
$html->clear();
$company_work_day = array();
for($i = 0; $i < 1 ; $i++ ){
	$company_id = 'http://primera.e-sim.org/companyWorkResults.html?id=16993';
	$html_comp = file_get_html($company_id);
	for ($j = 2; $j < 12; $j++){
		$company_work_day[$j] = $html_comp->find('#productivityTable tr td',$j)->plaintext;
	}
	$html_comp->clear();
}
$x=0;
$company_work_res = array();
for($i = 0; $i < count($list_company_id) ; $i++ ){
	$company_id = ''.'http://primera.e-sim.org/companyWorkResults.html?id='.$list_company_id[$i]["id"];
	$comp_link  = ''.'http://primera.e-sim.org/company.html?id='.$list_company_id[$i]["id"];
	$html_comp = file_get_html($company_id);
	$worker_list = $html_comp->find('#productivityTable tr td a');
	$tr_worker = $html_comp->find('#productivityTable tr');
	foreach($tr_worker as $tr_worker_i){
		if($tr_worker_i->find('td a')){
			$worker_link = $tr_worker_i->find('td a');
			$worker_id ;
			foreach($worker_link as $worker_link){
				$worker_link = $worker_link->href;
				$worker_id = explode('profile.html?id=',$worker_link);
			}
			$worker_name = $tr_worker_i->find('td',0)->plaintext;
			$skill = $tr_worker_i->find('td', 1 )->plaintext;
			$daywork = array();
			for($j = 2; $j<12 ;$j++){
				$d = $tr_worker_i->find('td' ,$j );
				if($d->find('div')){
					$daywork[$j-1] = $d->find('div',0)->plaintext;
				}
				if($d->find('img')){
					$daywork[$j-1] = '<img src="http://cdn.e-sim.org:8080/img/buttonIcons/cross-icon.png">';
				}
			}

			$company_work_res[$x] = array(
				'skill' => $skill,
				'comp_link' => $comp_link,
				'worker_link' => $worker_link,
				'worker_name' => $worker_name
			);
			$company_work_res[$x]['work_res'] = $daywork;
			$x++;
		}
	}
	$html_comp->clear();
}
$x=0;
$company_work_salary = array();
for($i = 0; $i < count($list_company_id) ; $i++ ){
	$comp_link  = ''.'http://primera.e-sim.org/company.html?id='.$list_company_id[$i]["id"];
	$html_comp = file_get_html($comp_link);
	$tr_worker = $html_comp->find('.dataTable tr ');
	foreach($tr_worker as $tr_worker_i){
		if($tr_worker_i->find('td a')){
			$worker_salary = $tr_worker_i->find('td',2)->plaintext;
			

			$company_work_res[$x] = array(
				'skill' => $company_work_res[$x]['skill'],
				'comp_link' =>$company_work_res[$x]['comp_link'],
				//'worker_id' =>$worker_id[1],
				'worker_link' => $company_work_res[$x]['worker_link'],
				'worker_name' => $company_work_res[$x]['worker_name'],
				'salary' => $worker_salary,
				'work_res'=>$company_work_res[$x]['work_res'] 
			);
			$x++;
		}
	}
	$html_comp->clear();
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html <?=$lang?>>
<head>
    <?
        if ($lang!='')
            echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
        else if ($charset)
            echo $charset;
        else 
            echo '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>';
    ?>
	<title>Daftar Wamil</title>
	<link rel="stylesheet" href="js/jquery.treeview.css" />
	<link rel="stylesheet" href="js/screen.css" />
	<style>
        .tag { color: blue; }
        .attr { color: #990033; }
		#masterTable { 
			font-size:10px;
			font-family:Arial;
			font-weight:normal;
			text-align: center;
		}
		#tableWorker {}
		table.gradienttable {
	font-family: verdana,arial,sans-serif;
	font-size:11px;
	color:#333333;
	border-width: 1px;
	border-color: #999999;
	border-collapse: collapse;
	}
		table.gradienttable th {
			padding: 0px;
			background: #d5e3e4;
			background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2Q1ZTNlNCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjQwJSIgc3RvcC1jb2xvcj0iI2NjZGVlMCIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNiM2M4Y2MiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
			background: -moz-linear-gradient(top,  #d5e3e4 0%, #ccdee0 40%, #b3c8cc 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#d5e3e4), color-stop(40%,#ccdee0), color-stop(100%,#b3c8cc));
			background: -webkit-linear-gradient(top,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
			background: -o-linear-gradient(top,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
			background: -ms-linear-gradient(top,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
			background: linear-gradient(to bottom,  #d5e3e4 0%,#ccdee0 40%,#b3c8cc 100%);
			border: 1px solid #999999;
		}
		table.gradienttable td {
			padding: 0px;
			background: #ebecda;
			background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ViZWNkYSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjQwJSIgc3RvcC1jb2xvcj0iI2UwZTBjNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNjZWNlYjciIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
			background: -moz-linear-gradient(top,  #ebecda 0%, #e0e0c6 40%, #ceceb7 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ebecda), color-stop(40%,#e0e0c6), color-stop(100%,#ceceb7));
			background: -webkit-linear-gradient(top,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
			background: -o-linear-gradient(top,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
			background: -ms-linear-gradient(top,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
			background: linear-gradient(to bottom,  #ebecda 0%,#e0e0c6 40%,#ceceb7 100%);
			border: 1px solid #999999;
		}
		table.gradienttable th p{
			margin:0px;
			padding:8px;
			border-top: 1px solid #eefafc;
			border-bottom:0px;
			border-left: 1px solid #eefafc;
			border-right:0px;
		}
		table.gradienttable td p{
			margin:0px;
			padding:8px;
			border-top: 1px solid #fcfdec;
			border-bottom:0px;
			border-left: 1px solid #fcfdec;;
			border-right:0px;
		}
    </style>
	<script src="js/jquery.js" type="text/javascript"></script>
	<script src="js/jquery.treeview.js" type="text/javascript"></script>
	<script type="text/javascript">
    $(document).ready(function(){	
        $("#html_tree").treeview({
            control:"#sidetreecontrol",
            collapsed: true,
            prerendered: true
        });
	});
    </script>
	</head>
	<body>
		<h1>
			KILL ISENG-ISENG
		</h1>
		<div id='masterTable'>
			<table class='gradienttable'>
				<tr><th>Company Link</th>
					<th>Worker Name(Link)</th>
					<th>Eco Skill</th>
					<?php for($j = 2;$j<12;$j++){?>
					<th><?php echo $company_work_day[$j]; ?></th>
					<?php }?>
					<th>Salary</th>
				</tr>
				<?php for($i=0; $i<count($company_work_res);$i++){ ?>
					<tr>
						<td><a href="<?php echo "".$company_work_res[$i]['comp_link'] ?>" target="_blank">Company</a></td>
						<td><a href="<?php echo "http://www.primera.e-sim.org/".$company_work_res[$i]['worker_link'] ?>" target="_blank"><?php echo $company_work_res[$i]['worker_name'] ?></a></td>
						<td><p><?php echo $company_work_res[$i]['skill'];?></p>
						</td>
						<?php for($j = 1; $j <=10 ; $j++){ ?>
						<td><?php echo $company_work_res[$i]['work_res'][$j];?></td>
						<?php }?>
						<td><?php echo "".$company_work_res[$i]['salary']?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
    <br>
	<table>
	
		</table>
 
</body></html>