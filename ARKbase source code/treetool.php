<!DOCTYPE HTML>
<!-- Website Template by freewebsitetemplates.com -->

<?php include 'header.php'?>

<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Tree Tool</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>

<style>
    .node {
        cursor: pointer;
    }

    .node circle {
        fill: #c23333;
        stroke: steelblue;
        stroke-width: 3px;
    }

    .node text {
        font: 15px sans-serif;
    }

    .link {
        fill: none;
        stroke: #ccc;
        stroke-width: 2.5px;
    }
</style>

<script src="./d3.min.js"></script>

<script>
// Get the JSON data from the script tag
var treeData = [
   {"name": "Nuclear Receptors", "children": [{"name": "Endocrine", "children": [{"name": "TRα", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "TRβ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "TRβ1", "children": []}, {"name": "RARα", "children": [{"name": "Methylation"}, {"name": "Palmitoylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "RARβ", "children": [{"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Ubiquitination"}]}, {"name": "RARγ", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "ERα", "children": [{"name": "Acetylation"}, {"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Palmitoylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}, {"name": "Neddylation"}, {"name": "S-nitrosylation"}, {"name": "Thiol oxidation"}]}, {"name": "ERβ", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}]}, {"name": "GR", "children": [{"name": "Acetylation"}, {"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "MR", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "PR", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "AR", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "VDR", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}]}, {"name": "Adopted Orphan", "children": [{"name": "LXRβ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "LXRα", "children": [{"name": "Acetylation"}, {"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "RXRα", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "RXRβ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "RXRγ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "CAR", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}]}, {"name": "PXR", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "FXR", "children": [{"name": "Acetylation"}, {"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "O-glcNAcylation"}, {"name": "Ubiquitination"}]}, {"name": "Rev-ErbAα", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "Rev-ErbAβ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "PPARα", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "PPARγ", "children": [{"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "PPARγ1", "children": []}, {"name": "PPARγ2", "children": []}, {"name": "PPARβ\δ", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Polyubiquitination"}]}]}, {"name": "Orphan", "children": [{"name": "RORα", "children": [{"name": "Acetylation"}, {"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Monomethylation"}]}, {"name": "RORα1", "children": []}, {"name": "RORα4", "children": []}, {"name": "RORβ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}]}, {"name": "RORγ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "RORγt", "children": []}, {"name": "HNF4α", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Ubiquitination"}, {"name": "Sumoylation"}]}, {"name": "HNF4γ", "children": [{"name": "Phosphorylation"}, {"name": "Methylation"}]}, {"name": "TR2", "children": [{"name": "Acetylation"}, {"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "TR4", "children": [{"name": "Acetylation"}, {"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "TLX", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}]}, {"name": "PNR", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}]}, {"name": "COUP-TFI", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Ubiquitination"}]}, {"name": "COUP-TFII", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "EAR2", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Ubiquitination"}]}, {"name": "ERRα", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "ERRα1", "children": []}, {"name": "ERRβ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "ERRγ", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "NURR1", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "NGF1B", "children": []}, {"name": "NUR77", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}, {"name": "Polyubiquitination"}]}, {"name": "NOR1", "children": [{"name": "Glycosylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "SF1", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}]}, {"name": "LRH-1", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}]}, {"name": "DAX-1", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}, {"name": "Sumoylation"}, {"name": "Ubiquitination"}, {"name": "Monoubiquitination"}]}, {"name": "SHP", "children": [{"name": "Acetylation"}, {"name": "Methylation"}, {"name": "Phosphorylation"}]}, {"name": "GCNF", "children": [{"name": "Methylation"}, {"name": "Phosphorylation"}]}]}]}
];

// ************** Generate the tree diagram *****************
var margin = {top: 20, right: 20, bottom: 20, left: 20},
    width = window.innerWidth - margin.right - margin.left,
    height = window.innerHeight - margin.top - margin.bottom;

var i = 0,
    duration = 750,
    root;

var tree = d3.layout.tree()
    .size([height, width]);

var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });

var svg = d3.select("body").append("svg")
    .attr("width", window.innerWidth + margin.right + margin.left)
    .attr("height", window.innerHeight + margin.top + margin.bottom)
  .append("g")
  .attr("transform", "translate(" + (width / 4) + "," + margin.top + ")");

root = treeData[0];
root.x0 = height / 2;
root.y0 = 0;

// Collapse levels function
function collapseLevels(node, level) {
  if (node.children && level <= 3) {
    node._children = node.children;
    node.children = null;
    if (node._children) {
      node._children.forEach(function(child) {
        collapseLevels(child, level + 1);
      });
    }
  }
}

// Call the function on the root node to collapse levels
collapseLevels(root, 1);

update(root);
d3.select(self.frameElement).style("height", "800px");

function update(source) {
  var nodes = tree.nodes(root).reverse(),
      links = tree.links(nodes);

  nodes.forEach(function(d) { d.y = d.depth * 200; });

  var node = svg.selectAll("g.node")
      .data(nodes, function(d) { return d.id || (d.id = ++i); });

  var nodeEnter = node.enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
      .on("click", click);

  nodeEnter.append("circle")
      .attr("r", 1e-6)
      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

  nodeEnter.append("text")
      .attr("x", function(d) { return d.children || d._children ? -13 : 13; })
      .attr("dy", ".35em")
      .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
      .text(function(d) { return d.name; })
      .style("fill-opacity", 1e-6);

  var nodeUpdate = node.transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

  nodeUpdate.select("circle")
      .attr("r", 10)
      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

  nodeUpdate.select("text")
      .style("fill-opacity", 1);

  var nodeExit = node.exit().transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
      .remove();

  nodeExit.select("circle")
      .attr("r", 1e-6);

  nodeExit.select("text")
      .style("fill-opacity", 1e-6);

  var link = svg.selectAll("path.link")
      .data(links, function(d) { return d.target.id; });

  link.enter().insert("path", "g")
      .attr("class", "link")
      .attr("d", function(d) {
        var o = {x: source.x0, y: source.y0};
        return diagonal({source: o, target: o});
      });

  link.transition()
      .duration(duration)
      .attr("d", diagonal);

  link.exit().transition()
      .duration(duration)
      .attr("d", function(d) {
        var o = {x: source.x, y: source.y};
        return diagonal({source: o, target: o});
      })
      .remove();

  nodes.forEach(function(d) {
    d.x0 = d.x;
    d.y0 = d.y;
  });
}

// Toggle children on click.
function click(d) {
  if (d.children || d._children) {
    if (d.children) {
      d._children = d.children;
      d.children = null;
    } else {
      d.children = d._children;
      d._children = null;
    }
    update(d);
  } //else {
    // Redirect to rora.php with the selected node's name as a query parameter
   // window.location.href = "rora.php?search=" + encodeURIComponent(d.name);
  //}
}
</script>

<?php include 'footer.php'?>
