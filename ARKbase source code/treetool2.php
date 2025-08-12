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
   {"name": "PTM", "children": [{"name": "Acetylation", "children": [{"name": "THRB", "children": []}, {"name": "NR1D2", "children": []}, {"name": "RORC", "children": []}, {"name": "NR1H3", "children": []}, {"name": "NR1H2", "children": []}, {"name": "NR1H4", "children": []}, {"name": "HNF4A", "children": []}, {"name": "RXRA", "children": []}, {"name": "NR2C2", "children": []}, {"name": "ESR1", "children": []}, {"name": "ESRRA", "children": []}, {"name": "NR3C1", "children": []}, {"name": "AR", "children": []}, {"name": "NR0B2", "children": []}, {"name": "THRA", "children": []}, {"name": "NR1D1", "children": []}, {"name": "RORA", "children": []}, {"name": "RORB", "children": []}, {"name": "NR1I2", "children": []}, {"name": "RXRB", "children": []}, {"name": "NR2C1", "children": []}, {"name": "NR2F1", "children": []}, {"name": "ESRRB", "children": []}, {"name": "ESRRG", "children": []}, {"name": "NR3C2", "children": []}, {"name": "PGR", "children": []}, {"name": "NR4A1", "children": []}, {"name": "NR5A1", "children": []}, {"name": "NR5A2", "children": []}, {"name": "RXRG", "children": []}]}, {"name": "Glycosylation", "children": [{"name": "ESR1", "children": []}, {"name": "RARB", "children": []}, {"name": "PPARG", "children": []}, {"name": "RORA", "children": []}, {"name": "NR1H3", "children": []}, {"name": "NR1H4", "children": []}, {"name": "NR2C2", "children": []}, {"name": "NR2C1", "children": []}, {"name": "NR3C1", "children": []}, {"name": "NR4A3", "children": []}]}, {"name": "Methylation", "children": [{"name": "NR1H4", "children": []}, {"name": "HNF4A", "children": []}, {"name": "ESR1", "children": []}, {"name": "AR", "children": []}, {"name": "NR0B2", "children": []}, {"name": "RORA", "children": []}, {"name": "NR1H2", "children": []}, {"name": "RXRB", "children": []}, {"name": "RXRG", "children": []}, {"name": "NR2F1", "children": []}, {"name": "NR4A3", "children": []}, {"name": "THRA", "children": []}, {"name": "THRB", "children": []}, {"name": "RARA", "children": []}, {"name": "RARB", "children": []}, {"name": "RARG", "children": []}, {"name": "PPARA", "children": []}, {"name": "PPARD", "children": []}, {"name": "PPARG", "children": []}, {"name": "NR1D1", "children": []}, {"name": "NR1D2", "children": []}, {"name": "RORB", "children": []}, {"name": "RORC", "children": []}, {"name": "NR1H3", "children": []}, {"name": "VDR", "children": []}, {"name": "NR1I2", "children": []}, {"name": "NR1I3", "children": []}, {"name": "RXRA", "children": []}, {"name": "NR2C2", "children": []}, {"name": "NR2C1", "children": []}, {"name": "NR2E1", "children": []}, {"name": "NR2E3", "children": []}, {"name": "NR2F2", "children": []}, {"name": "NR2F6", "children": []}, {"name": "ESR2", "children": []}, {"name": "ESRRA", "children": []}, {"name": "ESRRB", "children": []}, {"name": "ESRRG", "children": []}, {"name": "NR3C1", "children": []}, {"name": "NR3C2", "children": []}, {"name": "PGR", "children": []}, {"name": "NR4A2", "children": []}, {"name": "NR4A1", "children": []}, {"name": "NR5A1", "children": []}, {"name": "NR5A2", "children": []}, {"name": "NR6A1", "children": []}, {"name": "NR0B1", "children": []}, {"name": "HNF4G", "children": []}]}, {"name": "Palmitoylation", "children": [{"name": "ESR1", "children": []}, {"name": "RARA", "children": []}]}, {"name": "Phosphorylation", "children": [{"name": "THRB", "children": []}, {"name": "RARA", "children": []}, {"name": "RARG", "children": []}, {"name": "PPARA", "children": []}, {"name": "PPARG", "children": []}, {"name": "NR1D1", "children": []}, {"name": "RORA", "children": []}, {"name": "NR1H3", "children": []}, {"name": "NR1H4", "children": []}, {"name": "VDR", "children": []}, {"name": "NR1I2", "children": []}, {"name": "NR1I3", "children": []}, {"name": "HNF4A", "children": []}, {"name": "RXRA", "children": []}, {"name": "NR2E1", "children": []}, {"name": "ESR1", "children": []}, {"name": "ESR2", "children": []}, {"name": "ESRRA", "children": []}, {"name": "ESRRG", "children": []}, {"name": "NR3C1", "children": []}, {"name": "NR3C2", "children": []}, {"name": "PGR", "children": []}, {"name": "AR", "children": []}, {"name": "NR4A2", "children": []}, {"name": "NR4A1", "children": []}, {"name": "NR5A2", "children": []}, {"name": "NR0B2", "children": []}, {"name": "THRA", "children": []}, {"name": "RARB", "children": []}, {"name": "PPARD", "children": []}, {"name": "NR1D2", "children": []}, {"name": "RORB", "children": []}, {"name": "RORC", "children": []}, {"name": "NR1H2", "children": []}, {"name": "RXRB", "children": []}, {"name": "RXRG", "children": []}, {"name": "NR2C2", "children": []}, {"name": "NR2C1", "children": []}, {"name": "NR2E3", "children": []}, {"name": "NR2F1", "children": []}, {"name": "NR2F2", "children": []}, {"name": "NR2F6", "children": []}, {"name": "ESRRB", "children": []}, {"name": "NR4A3", "children": []}, {"name": "NR5A1", "children": []}, {"name": "NR6A1", "children": []}, {"name": "NR0B1", "children": []}, {"name": "HNF4G", "children": []}]}, {"name": "Sumoylation", "children": [{"name": "THRA", "children": []}, {"name": "THRB", "children": []}, {"name": "RARA", "children": []}, {"name": "PPARA", "children": []}, {"name": "PPARG", "children": []}, {"name": "RORA", "children": []}, {"name": "RORC", "children": []}, {"name": "NR1H3", "children": []}, {"name": "NR1H2", "children": []}, {"name": "NR1H4", "children": []}, {"name": "VDR", "children": []}, {"name": "RXRA", "children": []}, {"name": "ESR1", "children": []}, {"name": "ESRRA", "children": []}, {"name": "NR3C1", "children": []}, {"name": "PGR", "children": []}, {"name": "AR", "children": []}, {"name": "NR4A1", "children": []}, {"name": "NR5A2", "children": []}, {"name": "RARG", "children": []}, {"name": "NR2C2", "children": []}, {"name": "NR2C1", "children": []}, {"name": "NR4A2", "children": []}, {"name": "NR5A1", "children": []}, {"name": "PPARD", "children": []}, {"name": "NR1D1", "children": []}, {"name": "NR1D2", "children": []}, {"name": "RXRB", "children": []}, {"name": "RXRG", "children": []}, {"name": "NR2F2", "children": []}, {"name": "ESRRB", "children": []}, {"name": "ESRRG", "children": []}, {"name": "NR3C2", "children": []}, {"name": "NR4A3", "children": []}, {"name": "NR0B1", "children": []}, {"name": "NR1I2", "children": []}, {"name": "HNF4A", "children": []}]}, {"name": "Ubiquitination", "children": [{"name": "PPARG", "children": []}, {"name": "NR3C1", "children": []}, {"name": "AR", "children": []}, {"name": "THRA", "children": []}, {"name": "NR1D1", "children": []}, {"name": "NR1D2", "children": []}, {"name": "VDR", "children": []}, {"name": "HNF4A", "children": []}, {"name": "RXRB", "children": []}, {"name": "NR2F1", "children": []}, {"name": "NR2F2", "children": []}, {"name": "NR2F6", "children": []}, {"name": "ESR1", "children": []}, {"name": "PGR", "children": []}, {"name": "NR4A1", "children": []}, {"name": "NR5A1", "children": []}, {"name": "RARA", "children": []}, {"name": "RARB", "children": []}, {"name": "RXRA", "children": []}, {"name": "RXRG", "children": []}, {"name": "NR2C2", "children": []}, {"name": "NR2C1", "children": []}, {"name": "ESRRA", "children": []}, {"name": "ESRRG", "children": []}, {"name": "NR4A2", "children": []}, {"name": "NR4A3", "children": []}, {"name": "NR0B1", "children": []}, {"name": "NR1H4", "children": []}]}, {"name": "Polyubiquitination", "children": [{"name": "PPARD", "children": []}, {"name": "NR4A1", "children": []}]}, {"name": "Monomethylation", "children": [{"name": "RORA", "children": []}]}, {"name": "O-glcNAcylation", "children": [{"name": "NR1H4", "children": []}]}, {"name": "Neddylation", "children": [{"name": "ESR1", "children": []}, {"name": "ESR2", "children": []}]}, {"name": "S-nitrosylation", "children": [{"name": "ESR1", "children": []}]}, {"name": "Thiol oxidation", "children": [{"name": "ESR1", "children": []}]}, {"name": "Monoubiquitination", "children": [{"name": "NR0B1", "children": []}]}]}
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
d3.select(self.frameElement).style("height", "400px");

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

// In treetool2.php, modify the click function:
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
    } else {
        // Create a form and submit it programmatically to make a POST request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'rora.php';
        
        // Create hidden input field for the keyword
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'keyword';
        input.value = d.name;
        
        // Append input to form and form to document
        form.appendChild(input);
        document.body.appendChild(form);
        
        // Submit the form
        form.submit();
    }
}

</script>

<?php include 'footer.php'?>
