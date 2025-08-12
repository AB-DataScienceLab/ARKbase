<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Visualization</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.23.0/cytoscape.min.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Arial';
        }
        .main-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        #cy-container {
            flex-grow: 1;
            position: relative;
        }
        #cy {
            width: 100%;
            height: 100%;
            background-color: #fafafa;
        }
        #network-wait-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
        }
        #network-controls {
            flex-shrink: 0;
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-top: 1px solid #dee2e6;
        }
        .legend-color-box {
            display: inline-block; width: 15px; height: 15px;
            margin-right: 8px; vertical-align: middle; border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="main-container">
    <div id="cy-container">
        <div id="network-wait-message" class="text-center">
            <h4><span class="spinner-border text-primary" role="status"></span><span class="ms-2">Loading Network Data...</span></h4>
            <p class="text-muted mt-2">Please wait while the graph is being prepared.</p>
        </div>
        <div id="cy"></div>
    </div>

    <div id="network-controls" class="d-flex justify-content-between align-items-center">
         <div>
            <span class="me-3"><span class="legend-color-box" style="background-color: #0d6efd;"></span> Protein 1</span>
            <span class="me-3"><span class="legend-color-box" style="background-color: #dc3545;"></span> Protein 2</span>
            <span class="me-3"><span class="legend-color-box" style="background-color: #6f42c1;"></span> Both</span>
         </div>
         <div>
            <small id="layout-status" class="text-muted me-3"></small>
            <button class="btn btn-secondary btn-sm" id="reset-layout-btn"><i class="bi bi-grid-3x3-gap-fill"></i> Reset Layout (Fast)</button>
            <button class="btn btn-primary btn-sm" id="detailed-layout-btn"><i class="bi bi-bezier"></i> Recalculate Layout (Detailed)</button>
         </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const cyContainer = document.getElementById('cy');
    const waitMessage = document.getElementById('network-wait-message');
    const layoutStatus = document.getElementById('layout-status');
    const layoutButtons = [document.getElementById('reset-layout-btn'), document.getElementById('detailed-layout-btn')];
    let cy;

    const params = new URLSearchParams(window.location.search);
    const dataUrl = `get_network_data.php?${params.toString()}`;

    try {
        const response = await fetch(dataUrl);
        const data = await response.json();

        if (data.error || data.nodes.length === 0) {
            waitMessage.innerHTML = `<h4 class="text-danger">Error</h4><p>${data.error || 'No interactions found to visualize.'}</p>`;
            return;
        }
        
        waitMessage.style.display = 'none';
        
        // --- Render and Layout Functions ---
        function runLayout(layoutName) {
            if (!cy) return;

            layoutStatus.textContent = `Applying ${layoutName} layout...`;
            layoutButtons.forEach(btn => btn.disabled = true);
            
            const layoutOptions = {
                'concentric': {
                    name: 'concentric', fit: true, padding: 30, minNodeSpacing: 20,
                    concentric: node => node.degree(),
                    levelWidth: nodes => nodes.maxDegree() / 4,
                    animate: true, animationDuration: 500
                },
                'cose': {
                    name: 'cose', idealEdgeLength: 100, nodeOverlap: 20,
                    fit: true, padding: 30, randomize: true, componentSpacing: 100,
                    nodeRepulsion: 400000, edgeElasticity: 100, numIter: 1000,
                    animate: 'end', animationDuration: 500
                }
            };

            const layout = cy.layout(layoutOptions[layoutName]);
            layout.promiseOn('layoutstop').then(() => {
                layoutStatus.textContent = ``;
                layoutButtons.forEach(btn => btn.disabled = false);
            });
            layout.run();
        }

        cy = cytoscape({
            container: cyContainer,
            elements: data,
            style: [
                { selector: 'node', style: { 'label': 'data(id)', 'font-size': '12px', 'color': '#333', 'text-valign': 'bottom', 'text-halign': 'center', 'text-margin-y': '6px', 'width': '20px', 'height': '20px', 'min-zoomed-font-size': 8 } },
                { selector: "node[type='protein_1_only']", style: { 'background-color': '#0d6efd' } },
                { selector: "node[type='protein_2_only']", style: { 'background-color': '#dc3545' } },
                { selector: "node[type='protein_both']", style: { 'background-color': '#6f42c1' } },
                { selector: 'edge', style: { 'width': 2, 'line-color': '#cccccc', 'curve-style': 'bezier' } }
            ],
            layout: { name: 'preset' } // Use preset to avoid any initial layout cost
        });
        
        cy.ready(() => runLayout('concentric'));
        
        document.getElementById('reset-layout-btn').addEventListener('click', () => runLayout('concentric'));
        document.getElementById('detailed-layout-btn').addEventListener('click', () => runLayout('cose'));

    } catch (error) {
        console.error('Failed to load or render network:', error);
        waitMessage.innerHTML = `<h4 class="text-danger">Failed to Load Network</h4><p>Could not retrieve data from the server. Please ensure you are connected and try again.</p>`;
    }
});
</script>

</body>
</html>