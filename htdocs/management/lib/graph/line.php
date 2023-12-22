<?php // content="text/plain; charset=utf-8"
require_once ('../../../tool/jpgraph/jpgraph.php');
require_once ('../../../tool/jpgraph/jpgraph_line.php');

parse_str(urldecode($_SERVER['QUERY_STRING']),$array);

if(isset($array['width'])){
	$width=$array['width'];
}
else{
	$width=750;
}
if(isset($array['height'])){
	$height=$array['height'];
}
else{
	$height=400;
}

//$l1datay = array(11,9,2,4,3,13,17);
//$l2datay = array(23,12,5,19,17,10,15);

//$datax=$gDateLocale->GetShortMonth();

// Create the graph.
$graph = new Graph($width,$height);
$graph->clearTheme();
$graph->SetScale("textlin");
$graph->SetMargin(70,60,30,90);//左、右、上、下
$graph->xaxis->SetTickLabels(preg_split('/,/',$array['xaxis']));
$graph->xaxis->SetLabelAngle(45);

$plot=array();

$graph->SetYScale(0,"lin");

// Create the linear error plot
$plot[0]=new LinePlot(preg_split('/,/',$array['yaxis1']));
$plot[0]->mark->SetWidth(4);
$plot[0]->SetColor("red");

// Add the plots to t'he graph
$graph->Add($plot[0]);
$graph->yaxis->SetColor('red');

// Create the linear error plot
$plot[1]=new LinePlot(preg_split('/,/',$array['yaxis2']));
$plot[1]->mark->SetWidth(4);
$plot[1]->SetColor("#0000ff");

// Add the plots to t'he graph
$graph->AddY(0,$plot[1]);
$graph->ynaxis[0]->SetColor('#0000ff');

$graph->title->Set($array['title']);
//$graph->xaxis->title->Set("X-title");
$graph->yaxis->title->Set("小計");
$graph->yaxis->SetTitlemargin(45);
$graph->ynaxis[0]->title->Set("來客數");
$graph->ynaxis[0]->SetTitlemargin(45);

$graph->title->SetFont(FF_CHINESE,FS_NORMAL);
$graph->yaxis->SetFont(FF_CHINESE,FS_NORMAL);
$graph->yaxis->title->SetFont(FF_CHINESE,FS_NORMAL);
$graph->ynaxis[0]->SetFont(FF_CHINESE,FS_NORMAL);
$graph->ynaxis[0]->title->SetFont(FF_CHINESE,FS_NORMAL);

$graph->xaxis->SetFont(FF_CHINESE,FS_NORMAL);


// Display the graph
$graph->Stroke();
?>
