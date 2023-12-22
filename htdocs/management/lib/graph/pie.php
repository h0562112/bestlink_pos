<?php
require_once ('../../../tool/jpgraph/jpgraph.php');
require_once ('../../../tool/jpgraph/jpgraph_pie.php');

parse_str(urldecode($_SERVER['QUERY_STRING']),$array);

$data = preg_split('/,/',$array['money']);

// A new pie graph
$graph = new PieGraph(600,450);
$graph->SetShadow();

// Title setup
$graph->title->Set($array['title']);
$graph->title->SetFont(FF_CHINESE,FS_NORMAL);
$graph->legend->SetFont(FF_CHINESE,FS_NORMAL);

// Setup the pie plot
$p1 = new PiePlot($data);
$p1->SetLegends(preg_split('/,/',$array['name']));
// Adjust size and position of plot
$p1->SetSize(0.35);
$p1->SetCenter(0.4,0.52);

// Setup slice labels and move them into the plot
$p1->value->SetFont(FF_CHINESE,FS_NORMAL);
$p1->value->SetColor("darkred");

// Explode all slices
$p1->ExplodeAll(10);

// Add drop shadow
$p1->SetShadow();

$graph->legend->Pos(0.05,0.2);

// Finally add the plot
$graph->Add($p1);

// ... and stroke it
$graph->Stroke();
?>